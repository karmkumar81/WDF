<?php
/**
 * Practical 11: Student Management CRUD with Search, Filter & Pagination
 * Course: ITUE203 - Web Development Frameworks | CHARUSAT
 */

require_once __DIR__ . '/db.php';

$conn = getDB();

$search = trim($_GET['search'] ?? '');
$department = trim($_GET['department'] ?? '');
$status = trim($_GET['status'] ?? '');
$msg = $_GET['msg'] ?? '';
$error = $_GET['error'] ?? '';

// Build parameterized search/filter query
$sql = "SELECT id, enrollment_no, name, email, mobile, department, semester, gpa, status FROM students WHERE 1=1";
$params = [];
$types = "";

if (!empty($search)) {
    $sql .= " AND (name LIKE ? OR email LIKE ? OR enrollment_no LIKE ?)";
    $like = "%" . $search . "%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= "sss";
}

if (!empty($department) && $department !== 'all') {
    $sql .= " AND department = ?";
    $params[] = $department;
    $types .= "s";
}

if (!empty($status) && $status !== 'all') {
    $sql .= " AND status = ?";
    $params[] = $status;
    $types .= "s";
}

$sql .= " ORDER BY id ASC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Practical 11 - Student Management CRUD | CHARUSAT</title>
  <style>
    body { font-family: system-ui, sans-serif; background: #f8fafc; color: #0f172a; padding: 2rem 1rem; }
    .container { max-width: 1100px; margin: 0 auto; background: white; padding: 2rem; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.06); }
    .header-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; }
    h1 { color: #1e3a8a; font-size: 1.7rem; margin: 0; }
    .btn { padding: 0.6rem 1.1rem; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.9rem; cursor: pointer; border: none; }
    .btn-primary { background: #1e3a8a; color: white; }
    .btn-success { background: #16a34a; color: white; }
    .btn-danger { background: #dc2626; color: white; }
    .btn-edit { background: #0284c7; color: white; padding: 0.3rem 0.6rem; font-size: 0.8rem; border-radius: 4px; }
    .btn-del { background: #ef4444; color: white; padding: 0.3rem 0.6rem; font-size: 0.8rem; border-radius: 4px; }
    
    .filter-bar { display: flex; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1.5rem; background: #f1f5f9; padding: 1rem; border-radius: 6px; }
    .filter-input { padding: 0.55rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 0.9rem; }
    
    table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
    th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #e2e8f0; font-size: 0.85rem; }
    th { background: #f8fafc; color: #1e3a8a; font-weight: 600; }
    .status-active { color: #16a34a; font-weight: bold; }
    .status-inactive { color: #dc2626; font-weight: bold; }
    
    .alert-success { background: #dcfce7; color: #166534; padding: 0.75rem 1rem; border-radius: 6px; margin-bottom: 1rem; }
    .alert-error { background: #fee2e2; color: #991b1b; padding: 0.75rem 1rem; border-radius: 6px; margin-bottom: 1rem; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header-bar">
      <div>
        <h1>Student Records Management</h1>
        <p style="color: #64748b; font-size: 0.85rem; margin-top: 0.25rem;">Practical 11: MySQLi CRUD, Search, Filter &amp; Soft Delete</p>
      </div>
      <div>
        <a href="add_student.php" class="btn btn-primary">+ Add New Student</a>
        <a href="export_csv.php?search=<?= urlencode($search) ?>&department=<?= urlencode($department) ?>" class="btn btn-success" style="margin-left: 0.5rem;">Export CSV</a>
      </div>
    </div>

    <?php if (!empty($msg)): ?>
      <div class="alert-success"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
      <div class="alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Search & Filter Controls -->
    <form method="GET" action="index.php" class="filter-bar">
      <input type="text" name="search" class="filter-input" style="flex: 2; min-width: 200px;" placeholder="Search name, email, or enrollment..." value="<?= htmlspecialchars($search) ?>" />

      <select name="department" class="filter-input">
        <option value="all">All Departments</option>
        <option value="IT" <?= $department === 'IT' ? 'selected' : '' ?>>IT (CSPIT)</option>
        <option value="CSE" <?= $department === 'CSE' ? 'selected' : '' ?>>CSE (CSPIT)</option>
        <option value="CE" <?= $department === 'CE' ? 'selected' : '' ?>>CE (DEPSTAR)</option>
        <option value="AIML" <?= $department === 'AIML' ? 'selected' : '' ?>>AIML</option>
      </select>

      <select name="status" class="filter-input">
        <option value="all">All Statuses</option>
        <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active Only</option>
        <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive Only</option>
      </select>

      <button type="submit" class="btn btn-primary" style="padding: 0.55rem 1rem;">Filter</button>
      <a href="index.php" class="btn" style="background: #cbd5e1; color: #0f172a; padding: 0.55rem 1rem;">Reset</a>
    </form>

    <div style="overflow-x: auto;">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Enrollment</th>
            <th>Student Name</th>
            <th>Email Address</th>
            <th>Mobile</th>
            <th>Dept</th>
            <th>Sem</th>
            <th>GPA</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($students)): ?>
            <tr><td colspan="10" style="text-align: center; color: #64748b; padding: 2rem;">No matching student records found.</td></tr>
          <?php else: ?>
            <?php foreach ($students as $s): ?>
              <tr>
                <td><?= $s['id'] ?></td>
                <td><strong><?= htmlspecialchars($s['enrollment_no']) ?></strong></td>
                <td><?= htmlspecialchars($s['name']) ?></td>
                <td><?= htmlspecialchars($s['email']) ?></td>
                <td><?= htmlspecialchars($s['mobile']) ?></td>
                <td><?= htmlspecialchars($s['department']) ?></td>
                <td><?= $s['semester'] ?></td>
                <td><?= $s['gpa'] ?></td>
                <td>
                  <span class="<?= $s['status'] === 'active' ? 'status-active' : 'status-inactive' ?>">
                    <?= ucfirst($s['status']) ?>
                  </span>
                </td>
                <td>
                  <a href="edit_student.php?id=<?= $s['id'] ?>" class="btn-edit">Edit</a>
                  <a href="delete_student.php?id=<?= $s['id'] ?>" class="btn-del" onclick="return confirm('Are you sure you want to delete or deactivate student <?= htmlspecialchars($s['name']) ?>?');">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
