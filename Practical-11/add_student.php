<?php
/**
 * Practical 11: Add Student Record (Create Operation)
 */

require_once __DIR__ . '/db.php';

$errors = [];
$name = $enrollment = $email = $mobile = $department = '';
$semester = 3;
$gpa = 8.5;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $enrollment = strtoupper(trim($_POST['enrollment_no'] ?? ''));
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $mobile = trim($_POST['mobile'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $semester = (int)($_POST['semester'] ?? 3);
    $gpa = (float)($_POST['gpa'] ?? 8.5);

    if (empty($name)) $errors[] = "Full Name is required.";
    if (empty($enrollment)) $errors[] = "Enrollment Number is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
    if (!preg_match("/^[6-9]\d{9}$/", $mobile)) $errors[] = "Valid 10-digit mobile number starting 6-9 is required.";
    if (empty($department)) $errors[] = "Please select a department.";

    if (empty($errors)) {
        $conn = getDB();
        try {
            $stmt = $conn->prepare("INSERT INTO students (enrollment_no, name, email, mobile, department, semester, gpa, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'active')");
            $stmt->bind_param("sssssid", $enrollment, $name, $email, $mobile, $department, $semester, $gpa);
            $stmt->execute();
            $stmt->close();

            header("Location: index.php?msg=" . urlencode("Student {$name} ({$enrollment}) added successfully!"));
            exit;
        } catch (mysqli_sql_exception $e) {
            if ($conn->errno === 1062) {
                $errors[] = "Duplicate Error: Enrollment number or email already exists.";
            } else {
                $errors[] = "Database Error: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add Student | Practical 11</title>
  <style>
    body { font-family: system-ui, sans-serif; background: #f8fafc; color: #0f172a; padding: 2rem; }
    .card { max-width: 600px; margin: 0 auto; background: white; padding: 2rem; border-radius: 8px; border: 1px solid #e2e8f0; }
    .form-group { margin-bottom: 1.2rem; }
    label { display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.3rem; }
    .form-control { width: 100%; padding: 0.65rem; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; }
    .btn { background: #1e3a8a; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; }
    .alert-error { background: #fee2e2; color: #991b1b; padding: 0.75rem; border-radius: 6px; margin-bottom: 1rem; }
  </style>
</head>
<body>
  <div class="card">
    <h2 style="color: #1e3a8a; margin-bottom: 1.5rem;">Add New Student Record</h2>

    <?php if (!empty($errors)): ?>
      <div class="alert-error">
        <?php foreach ($errors as $e): ?><div>&bull; <?= htmlspecialchars($e) ?></div><?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="add_student.php">
      <div class="form-group">
        <label>Full Name *</label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($name) ?>" required />
      </div>

      <div class="form-group">
        <label>Enrollment Number *</label>
        <input type="text" name="enrollment_no" class="form-control" value="<?= htmlspecialchars($enrollment) ?>" placeholder="e.g. 24IT021" required />
      </div>

      <div class="form-group">
        <label>Email Address *</label>
        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required />
      </div>

      <div class="form-group">
        <label>Mobile Number *</label>
        <input type="tel" name="mobile" class="form-control" value="<?= htmlspecialchars($mobile) ?>" required />
      </div>

      <div class="form-group">
        <label>Department *</label>
        <select name="department" class="form-control" required>
          <option value="">-- Select --</option>
          <option value="IT">IT (CSPIT)</option>
          <option value="CSE">CSE (CSPIT)</option>
          <option value="CE">CE (DEPSTAR)</option>
          <option value="AIML">AIML</option>
        </select>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
        <div class="form-group">
          <label>Semester</label>
          <input type="number" name="semester" class="form-control" value="3" min="1" max="8" />
        </div>
        <div class="form-group">
          <label>GPA</label>
          <input type="number" step="0.01" name="gpa" class="form-control" value="8.50" min="0" max="10" />
        </div>
      </div>

      <button type="submit" class="btn">Save Student Record</button>
      <a href="index.php" style="margin-left: 1rem; color: #64748b;">Cancel</a>
    </form>
  </div>
</body>
</html>
