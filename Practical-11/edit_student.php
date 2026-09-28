<?php
/**
 * Practical 11: Edit Student Record (Update Operation)
 */

require_once __DIR__ . '/db.php';

$conn = getDB();
$id = (int)($_GET['id'] ?? 0);
$errors = [];

// Fetch existing record
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$student) {
    header("Location: index.php?error=" . urlencode("Student record not found."));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $semester = (int)($_POST['semester'] ?? 3);
    $gpa = (float)($_POST['gpa'] ?? 8.5);
    $status = $_POST['status'] ?? 'active';

    if (empty($name)) $errors[] = "Full Name is required.";
    if (!preg_match("/^[6-9]\d{9}$/", $mobile)) $errors[] = "Valid 10-digit mobile number required.";

    if (empty($errors)) {
        $updateStmt = $conn->prepare("UPDATE students SET name = ?, mobile = ?, department = ?, semester = ?, gpa = ?, status = ? WHERE id = ?");
        $updateStmt->bind_param("sssidsi", $name, $mobile, $department, $semester, $gpa, $status, $id);
        $updateStmt->execute();
        $updateStmt->close();

        header("Location: index.php?msg=" . urlencode("Student record for {$student['enrollment_no']} updated successfully!"));
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Student | Practical 11</title>
  <style>
    body { font-family: system-ui, sans-serif; background: #f8fafc; color: #0f172a; padding: 2rem; }
    .card { max-width: 600px; margin: 0 auto; background: white; padding: 2rem; border-radius: 8px; border: 1px solid #e2e8f0; }
    .form-group { margin-bottom: 1.2rem; }
    label { display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.3rem; }
    .form-control { width: 100%; padding: 0.65rem; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; }
    .btn { background: #1e3a8a; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; }
  </style>
</head>
<body>
  <div class="card">
    <h2 style="color: #1e3a8a; margin-bottom: 0.5rem;">Edit Student Record</h2>
    <p style="color: #64748b; margin-bottom: 1.5rem;">Enrollment No: <strong><?= htmlspecialchars($student['enrollment_no']) ?></strong></p>

    <form method="POST" action="edit_student.php?id=<?= $id ?>">
      <div class="form-group">
        <label>Full Name *</label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($student['name']) ?>" required />
      </div>

      <div class="form-group">
        <label>Email Address (Read-only)</label>
        <input type="email" class="form-control" value="<?= htmlspecialchars($student['email']) ?>" readonly style="background: #f1f5f9;" />
      </div>

      <div class="form-group">
        <label>Mobile Number *</label>
        <input type="tel" name="mobile" class="form-control" value="<?= htmlspecialchars($student['mobile']) ?>" required />
      </div>

      <div class="form-group">
        <label>Department *</label>
        <select name="department" class="form-control" required>
          <option value="IT" <?= $student['department'] === 'IT' ? 'selected' : '' ?>>IT (CSPIT)</option>
          <option value="CSE" <?= $student['department'] === 'CSE' ? 'selected' : '' ?>>CSE (CSPIT)</option>
          <option value="CE" <?= $student['department'] === 'CE' ? 'selected' : '' ?>>CE (DEPSTAR)</option>
          <option value="AIML" <?= $student['department'] === 'AIML' ? 'selected' : '' ?>>AIML</option>
        </select>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
        <div class="form-group">
          <label>Semester</label>
          <input type="number" name="semester" class="form-control" value="<?= $student['semester'] ?>" min="1" max="8" />
        </div>
        <div class="form-group">
          <label>GPA</label>
          <input type="number" step="0.01" name="gpa" class="form-control" value="<?= $student['gpa'] ?>" min="0" max="10" />
        </div>
      </div>

      <div class="form-group">
        <label>Record Status</label>
        <select name="status" class="form-control">
          <option value="active" <?= $student['status'] === 'active' ? 'selected' : '' ?>>Active</option>
          <option value="inactive" <?= $student['status'] === 'inactive' ? 'selected' : '' ?>>Inactive (Soft-Deleted)</option>
        </select>
      </div>

      <button type="submit" class="btn">Update Student</button>
      <a href="index.php" style="margin-left: 1rem; color: #64748b;">Back to List</a>
    </form>
  </div>
</body>
</html>
