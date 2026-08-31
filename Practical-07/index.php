<?php
/**
 * Practical 7: PHP Form Processing with Server-Side Validation, CSV/JSON Storage, and Record Display
 * Course: ITUE203 - Web Development Frameworks | CHARUSAT
 */

session_start();

// Ensure CSRF token exists in session
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Data storage paths
$dataDir = __DIR__ . '/data';
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0777, true);
}
$csvFile = $dataDir . '/submissions.csv';
$jsonFile = $dataDir . '/submissions.json';

// Variables for form state
$errors = [];
$successMessage = '';
$name = '';
$email = '';
$mobile = '';
$department = '';
$message = '';

// Whitelist of valid departments
$validDepartments = ['IT', 'CSE', 'CE', 'AIML'];

// Process Form Submission via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Verify CSRF Token
    $submittedToken = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $submittedToken)) {
        $errors['csrf'] = 'Invalid or expired CSRF security token.';
    }

    // 2. Retrieve, Trim & Sanitize Inputs
    $name = trim($_POST['name'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $mobile = trim($_POST['mobile'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // 3. Server-Side Validation
    // Name validation
    if (empty($name)) {
        $errors['name'] = 'Full Name is required.';
    } elseif (!preg_match("/^[A-Za-z\s]{3,50}$/", $name)) {
        $errors['name'] = 'Name must be 3-50 characters containing only letters and spaces.';
    }

    // Email validation
    if (empty($email)) {
        $errors['email'] = 'Email address is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please provide a valid email format.';
    }

    // Mobile number validation
    if (empty($mobile)) {
        $errors['mobile'] = 'Mobile number is required.';
    } elseif (!preg_match("/^[6-9]\d{9}$/", $mobile)) {
        $errors['mobile'] = 'Mobile number must be 10 digits starting with 6, 7, 8, or 9.';
    }

    // Department validation
    if (empty($department) || !in_array($department, $validDepartments, true)) {
        $errors['department'] = 'Please select a valid academic department from the list.';
    }

    // Message validation
    if (empty($message)) {
        $errors['message'] = 'Message inquiry cannot be blank.';
    } elseif (strlen($message) < 10) {
        $errors['message'] = 'Message must be at least 10 characters long.';
    }

    // 4. Safe File Writing if No Errors
    if (empty($errors)) {
        $timestamp = date('Y-m-d H:i:s');
        $sanitizedName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $sanitizedMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

        $record = [
            'timestamp'  => $timestamp,
            'name'       => $sanitizedName,
            'email'      => $email,
            'mobile'     => $mobile,
            'department' => $department,
            'message'    => $sanitizedMessage
        ];

        // A. Write to CSV with File Locking
        $csvHandle = fopen($csvFile, 'a');
        if ($csvHandle) {
            flock($csvHandle, LOCK_EX); // Exclusive lock to prevent race conditions
            // Add CSV header if file is empty
            if (filesize($csvFile) === 0) {
                fputcsv($csvHandle, ['Timestamp', 'Full Name', 'Email', 'Mobile', 'Department', 'Message']);
            }
            fputcsv($csvHandle, array_values($record));
            flock($csvHandle, LOCK_UN);
            fclose($csvHandle);
        }

        // B. Write to JSON with File Locking
        $existingJson = [];
        if (file_exists($jsonFile) && filesize($jsonFile) > 0) {
            $jsonContent = file_get_contents($jsonFile);
            $decoded = json_decode($jsonContent, true);
            if (is_array($decoded)) {
                $existingJson = $decoded;
            }
        }
        $existingJson[] = $record;
        file_put_contents($jsonFile, json_encode($existingJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);

        $successMessage = 'Your inquiry was processed, validated, and stored successfully in CSV and JSON formats!';

        // Reset form fields and refresh CSRF token
        $name = $email = $mobile = $department = $message = '';
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

// Read Stored JSON Records for Display (Intermediate Extension)
$storedRecords = [];
if (file_exists($jsonFile) && filesize($jsonFile) > 0) {
    $raw = file_get_contents($jsonFile);
    $parsed = json_decode($raw, true);
    if (is_array($parsed)) {
        $storedRecords = array_reverse($parsed); // Latest first
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Practical 7 - PHP Form Processing &amp; Safe File Storage | CHARUSAT</title>
  <style>
    :root {
      --primary: #1e3a8a;
      --primary-hover: #1d4ed8;
      --bg: #f8fafc;
      --surface: #ffffff;
      --text: #0f172a;
      --text-muted: #64748b;
      --border: #cbd5e1;
      --error: #dc2626;
      --success: #16a34a;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      background-color: var(--bg);
      color: var(--text);
      line-height: 1.6;
      padding: 2rem 1rem;
    }
    .wrapper { max-width: 900px; margin: 0 auto; }
    header { text-align: center; margin-bottom: 2rem; }
    header h1 { color: var(--primary); font-size: 2rem; margin-bottom: 0.5rem; }
    header p { color: var(--text-muted); }
    
    .card {
      background: var(--surface);
      border-radius: 8px;
      border: 1px solid #e2e8f0;
      padding: 2rem;
      box-shadow: 0 4px 6px -1px rgba(0,0,0,0.06);
      margin-bottom: 2.5rem;
    }
    
    .alert-success {
      background: #dcfce7;
      border: 1px solid #86efac;
      color: #166534;
      padding: 1rem;
      border-radius: 6px;
      margin-bottom: 1.5rem;
      font-weight: 500;
    }
    .alert-error {
      background: #fef2f2;
      border: 1px solid #fecaca;
      color: #991b1b;
      padding: 1rem;
      border-radius: 6px;
      margin-bottom: 1.5rem;
      font-weight: 500;
    }
    
    .form-group { margin-bottom: 1.25rem; }
    label { display: block; font-weight: 600; font-size: 0.9rem; margin-bottom: 0.35rem; }
    .form-control {
      width: 100%;
      padding: 0.7rem 0.85rem;
      border: 1px solid var(--border);
      border-radius: 6px;
      font-size: 0.95rem;
      font-family: inherit;
    }
    .form-control:focus { outline: 2px solid var(--primary); border-color: var(--primary); }
    .field-error { color: var(--error); font-size: 0.8rem; margin-top: 0.25rem; font-weight: 500; }
    
    .btn {
      background: var(--primary);
      color: white;
      border: none;
      padding: 0.75rem 1.5rem;
      border-radius: 6px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
    }
    .btn:hover { background: var(--primary-hover); }
    
    table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
    th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #e2e8f0; font-size: 0.9rem; }
    th { background: #f1f5f9; color: var(--primary); font-weight: 600; }
    .table-container { overflow-x: auto; }
  </style>
</head>
<body>
  <div class="wrapper">
    <header>
      <h1>StudentHub PHP Form Processing</h1>
      <p>Practical 7: Server-Side Sanitization, Validation, File Locking, &amp; CSV/JSON Storage</p>
    </header>

    <div class="card">
      <h2 style="color: var(--primary); margin-bottom: 1.25rem;">Submit Campus Inquiry</h2>

      <?php if (!empty($successMessage)): ?>
        <div class="alert-success"><?= htmlspecialchars($successMessage) ?></div>
      <?php endif; ?>

      <?php if (!empty($errors['csrf'])): ?>
        <div class="alert-error"><?= htmlspecialchars($errors['csrf']) ?></div>
      <?php endif; ?>

      <form action="index.php" method="POST" novalidate>
        <!-- CSRF Hidden Token -->
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>" />

        <div class="form-group">
          <label for="name">Full Name <span style="color: red;">*</span></label>
          <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($name) ?>" placeholder="e.g. Alice Patel" required />
          <?php if (isset($errors['name'])): ?>
            <div class="field-error"><?= htmlspecialchars($errors['name']) ?></div>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label for="email">Student Email Address <span style="color: red;">*</span></label>
          <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" placeholder="e.g. alice@charusat.edu.in" required />
          <?php if (isset($errors['email'])): ?>
            <div class="field-error"><?= htmlspecialchars($errors['email']) ?></div>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label for="mobile">Mobile Number <span style="color: red;">*</span></label>
          <input type="tel" id="mobile" name="mobile" class="form-control" value="<?= htmlspecialchars($mobile) ?>" placeholder="10-digit number starting with 6-9" required />
          <?php if (isset($errors['mobile'])): ?>
            <div class="field-error"><?= htmlspecialchars($errors['mobile']) ?></div>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label for="department">Department <span style="color: red;">*</span></label>
          <select id="department" name="department" class="form-control" required>
            <option value="">-- Choose Department --</option>
            <option value="IT" <?= $department === 'IT' ? 'selected' : '' ?>>Information Technology (CSPIT)</option>
            <option value="CSE" <?= $department === 'CSE' ? 'selected' : '' ?>>Computer Science &amp; Engg (CSPIT)</option>
            <option value="CE" <?= $department === 'CE' ? 'selected' : '' ?>>Computer Engineering (DEPSTAR)</option>
            <option value="AIML" <?= $department === 'AIML' ? 'selected' : '' ?>>AI &amp; Machine Learning</option>
          </select>
          <?php if (isset($errors['department'])): ?>
            <div class="field-error"><?= htmlspecialchars($errors['department']) ?></div>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label for="message">Inquiry / Feedback Details <span style="color: red;">*</span></label>
          <textarea id="message" name="message" class="form-control" rows="4" placeholder="Minimum 10 characters..." required><?= htmlspecialchars($message) ?></textarea>
          <?php if (isset($errors['message'])): ?>
            <div class="field-error"><?= htmlspecialchars($errors['message']) ?></div>
          <?php endif; ?>
        </div>

        <button type="submit" class="btn">Submit via HTTP POST</button>
      </form>
    </div>

    <!-- Stored Records Display (Intermediate Extension) -->
    <div class="card">
      <h2 style="color: var(--primary); margin-bottom: 0.5rem;">Stored Inquiries (Loaded from JSON / CSV)</h2>
      <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1rem;">
        Total Records: <?= count($storedRecords) ?> | Stored safely on disk using <code>flock(LOCK_EX)</code>
      </p>

      <?php if (empty($storedRecords)): ?>
        <p style="color: var(--text-muted); font-style: italic;">No records submitted yet. Submit the form above to generate entries.</p>
      <?php else: ?>
        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>Timestamp</th>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Dept</th>
                <th>Message</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($storedRecords as $row): ?>
                <tr>
                  <td><?= htmlspecialchars($row['timestamp']) ?></td>
                  <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                  <td><?= htmlspecialchars($row['email']) ?></td>
                  <td><?= htmlspecialchars($row['mobile']) ?></td>
                  <td><?= htmlspecialchars($row['department']) ?></td>
                  <td><?= htmlspecialchars($row['message']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
