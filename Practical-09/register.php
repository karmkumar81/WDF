<?php
/**
 * Practical 9: Secure User Registration with MySQLi, Duplicate Check & password_hash()
 * Course: ITUE203 - Web Development Frameworks | CHARUSAT
 */

session_start();
require_once __DIR__ . '/db.php';

// Generate CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];
$successMessage = '';
$name = '';
$email = '';
$mobile = '';
$enrollment = '';
$department = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Verify CSRF
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        $errors['csrf'] = 'Invalid CSRF token.';
    }

    // 2. Fetch and Sanitize Inputs
    $name = trim($_POST['name'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $mobile = trim($_POST['mobile'] ?? '');
    $enrollment = strtoupper(trim($_POST['enrollment'] ?? ''));
    $department = trim($_POST['department'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // 3. Backend Server-Side Validation
    if (empty($name) || !preg_match("/^[A-Za-z\s]{3,50}$/", $name)) {
        $errors['name'] = 'Full Name must be 3-50 letters only.';
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'A valid email address is required.';
    }

    if (empty($mobile) || !preg_match("/^[6-9]\d{9}$/", $mobile)) {
        $errors['mobile'] = 'Mobile number must be 10 digits starting with 6-9.';
    }

    if (empty($enrollment) || !preg_match("/^[0-9]{2}[A-Za-z]{2,4}[0-9]{3}$/", $enrollment)) {
        $errors['enrollment'] = 'Enrollment No must follow format like 24IT001 or 24CSE042.';
    }

    if (empty($department)) {
        $errors['department'] = 'Please select a department.';
    }

    if (strlen($password) < 8 || !preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/", $password)) {
        $errors['password'] = 'Password must be at least 8 chars with uppercase, lowercase, and a number.';
    }

    if ($password !== $confirmPassword) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    // 4. Check Duplicate Email and Enrollment No using MySQLi Prepared Statements
    if (empty($errors)) {
        $conn = getDB();

        // Check duplicate email in users table
        $stmtEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmtEmail->bind_param("s", $email);
        $stmtEmail->execute();
        $stmtEmail->store_result();
        if ($stmtEmail->num_rows > 0) {
            $errors['email'] = 'This email is already registered in the system.';
        }
        $stmtEmail->close();

        // Check duplicate enrollment in students table
        $stmtEnroll = $conn->prepare("SELECT id FROM students WHERE enrollment_no = ?");
        $stmtEnroll->bind_param("s", $enrollment);
        $stmtEnroll->execute();
        $stmtEnroll->store_result();
        if ($stmtEnroll->num_rows > 0) {
            $errors['enrollment'] = 'This enrollment number is already registered.';
        }
        $stmtEnroll->close();
    }

    // 5. Password Hashing & Database Insertion (with Transaction)
    if (empty($errors)) {
        $conn = getDB();
        $conn->begin_transaction();

        try {
            // Secure Password Hashing
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $role = 'student';

            // Insert into users
            $stmtUser = $conn->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
            $stmtUser->bind_param("ssss", $name, $email, $passwordHash, $role);
            $stmtUser->execute();
            $userId = $conn->insert_id;
            $stmtUser->close();

            // Insert into students
            $sem = 3;
            $stmtStudent = $conn->prepare("INSERT INTO students (user_id, enrollment_no, name, email, mobile, department, semester) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmtStudent->bind_param("isssssi", $userId, $enrollment, $name, $email, $mobile, $department, $sem);
            $stmtStudent->execute();
            $stmtStudent->close();

            // Audit log insertion
            $action = 'USER_REGISTRATION';
            $details = "Registered user {$email} with student ID {$enrollment}";
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $stmtAudit = $conn->prepare("INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
            $stmtAudit->bind_param("isss", $userId, $action, $details, $ip);
            $stmtAudit->execute();
            $stmtAudit->close();

            $conn->commit();
            $successMessage = "Registration successful! Account created for {$name} ({$enrollment}). You may now log in.";

            // Reset fields
            $name = $email = $mobile = $enrollment = $department = '';
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Exception $e) {
            $conn->rollback();
            $errors['general'] = "Registration failed due to server error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Practical 9 - Secure User Registration | CHARUSAT</title>
  <style>
    body { font-family: system-ui, sans-serif; background: #f8fafc; color: #0f172a; padding: 2rem 1rem; }
    .card { max-width: 650px; margin: 0 auto; background: white; padding: 2rem; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.06); }
    h1 { color: #1e3a8a; margin-bottom: 0.5rem; text-align: center; }
    .subtitle { text-align: center; color: #64748b; font-size: 0.9rem; margin-bottom: 1.5rem; }
    .form-group { margin-bottom: 1.2rem; }
    label { display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.3rem; }
    .form-control { width: 100%; padding: 0.65rem 0.8rem; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; }
    .form-control:focus { outline: 2px solid #1e3a8a; border-color: #1e3a8a; }
    .error-msg { color: #dc2626; font-size: 0.8rem; margin-top: 0.25rem; font-weight: 500; }
    .alert-success { background: #dcfce7; color: #166534; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; font-weight: 500; border: 1px solid #86efac; }
    .alert-error { background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; font-weight: 500; border: 1px solid #fecaca; }
    .btn { width: 100%; background: #1e3a8a; color: white; padding: 0.8rem; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 1rem; }
    .btn:hover { background: #1d4ed8; }
  </style>
</head>
<body>
  <div class="card">
    <h1>Secure Student Registration</h1>
    <p class="subtitle">Practical 9: Prepared Statements &bull; Duplicate Check &bull; password_hash()</p>

    <?php if (!empty($successMessage)): ?>
      <div class="alert-success"><?= htmlspecialchars($successMessage) ?></div>
    <?php endif; ?>

    <?php if (!empty($errors['general'])): ?>
      <div class="alert-error"><?= htmlspecialchars($errors['general']) ?></div>
    <?php endif; ?>

    <form action="register.php" method="POST" novalidate>
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>" />

      <div class="form-group">
        <label for="name">Full Name <span style="color:red;">*</span></label>
        <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($name) ?>" placeholder="e.g. Alice Patel" required />
        <?php if (isset($errors['name'])): ?><div class="error-msg"><?= htmlspecialchars($errors['name']) ?></div><?php endif; ?>
      </div>

      <div class="form-group">
        <label for="enrollment">Enrollment Number <span style="color:red;">*</span></label>
        <input type="text" id="enrollment" name="enrollment" class="form-control" value="<?= htmlspecialchars($enrollment) ?>" placeholder="e.g. 24IT001" required />
        <?php if (isset($errors['enrollment'])): ?><div class="error-msg"><?= htmlspecialchars($errors['enrollment']) ?></div><?php endif; ?>
      </div>

      <div class="form-group">
        <label for="email">Email Address <span style="color:red;">*</span></label>
        <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" placeholder="student@charusat.edu.in" required />
        <?php if (isset($errors['email'])): ?><div class="error-msg"><?= htmlspecialchars($errors['email']) ?></div><?php endif; ?>
      </div>

      <div class="form-group">
        <label for="mobile">Mobile Number <span style="color:red;">*</span></label>
        <input type="tel" id="mobile" name="mobile" class="form-control" value="<?= htmlspecialchars($mobile) ?>" placeholder="10-digit number" required />
        <?php if (isset($errors['mobile'])): ?><div class="error-msg"><?= htmlspecialchars($errors['mobile']) ?></div><?php endif; ?>
      </div>

      <div class="form-group">
        <label for="department">Department <span style="color:red;">*</span></label>
        <select id="department" name="department" class="form-control" required>
          <option value="">-- Select Department --</option>
          <option value="IT" <?= $department === 'IT' ? 'selected' : '' ?>>Information Technology (CSPIT)</option>
          <option value="CSE" <?= $department === 'CSE' ? 'selected' : '' ?>>Computer Science &amp; Engg (CSPIT)</option>
          <option value="CE" <?= $department === 'CE' ? 'selected' : '' ?>>Computer Engineering (DEPSTAR)</option>
        </select>
        <?php if (isset($errors['department'])): ?><div class="error-msg"><?= htmlspecialchars($errors['department']) ?></div><?php endif; ?>
      </div>

      <div class="form-group">
        <label for="password">Password <span style="color:red;">*</span></label>
        <input type="password" id="password" name="password" class="form-control" placeholder="Min 8 chars, 1 uppercase, 1 lowercase, 1 number" required />
        <?php if (isset($errors['password'])): ?><div class="error-msg"><?= htmlspecialchars($errors['password']) ?></div><?php endif; ?>
      </div>

      <div class="form-group">
        <label for="confirm_password">Confirm Password <span style="color:red;">*</span></label>
        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required />
        <?php if (isset($errors['confirm_password'])): ?><div class="error-msg"><?= htmlspecialchars($errors['confirm_password']) ?></div><?php endif; ?>
      </div>

      <button type="submit" class="btn">Register Secure Account</button>
    </form>
  </div>
</body>
</html>
