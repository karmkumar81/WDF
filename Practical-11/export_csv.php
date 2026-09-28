<?php
/**
 * Practical 11: Export Student Records to CSV
 */

require_once __DIR__ . '/db.php';

$conn = getDB();

$search = trim($_GET['search'] ?? '');
$department = trim($_GET['department'] ?? '');

$sql = "SELECT enrollment_no, name, email, mobile, department, semester, gpa, status, created_at FROM students WHERE 1=1";
$params = [];
$types = "";

if (!empty($search)) {
    $sql .= " AND (name LIKE ? OR email LIKE ? OR enrollment_no LIKE ?)";
    $like = "%" . $search . "%";
    $params[] = $like; $params[] = $like; $params[] = $like;
    $types .= "sss";
}

if (!empty($department) && $department !== 'all') {
    $sql .= " AND department = ?";
    $params[] = $department;
    $types .= "s";
}

$sql .= " ORDER BY enrollment_no ASC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$filename = "studenthub_students_" . date('Ymd_His') . ".csv";

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

$output = fopen('php://output', 'w');
fputcsv($output, ['Enrollment No', 'Full Name', 'Email Address', 'Mobile', 'Department', 'Semester', 'GPA', 'Status', 'Registered Date']);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
$stmt->close();
exit;
