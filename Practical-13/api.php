<?php
/**
 * Practical 13: RESTful PHP JSON API for Asynchronous CRUD Operations
 * Course: ITUE203 - Web Development Frameworks | CHARUSAT
 */

header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$conn = getDB();

/**
 * Centralized JSON Response Helper
 */
function sendResponse(int $statusCode, bool $success, $data = null, ?string $message = null) {
    http_response_code($statusCode);
    echo json_encode([
        'success'   => $success,
        'status'    => $statusCode,
        'message'   => $message,
        'data'      => $data,
        'timestamp' => date('c')
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    exit;
}

// -------------------------------------------------------------
// 1. GET: Fetch Students (List or Single) with Optional Search
// -------------------------------------------------------------
if ($method === 'GET') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
    $search = trim($_GET['search'] ?? '');

    if ($id !== null) {
        $stmt = $conn->prepare("SELECT id, enrollment_no, name, email, mobile, department, semester, gpa, status FROM students WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $student = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($student) {
            sendResponse(200, true, $student, "Student record retrieved successfully.");
        } else {
            sendResponse(404, false, null, "Student record not found with ID {$id}.");
        }
    } else {
        $sql = "SELECT id, enrollment_no, name, email, mobile, department, semester, gpa, status FROM students WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($search)) {
            $sql .= " AND (name LIKE ? OR email LIKE ? OR enrollment_no LIKE ?)";
            $like = "%" . $search . "%";
            $params = [$like, $like, $like];
            $types = "sss";
        }
        $sql .= " ORDER BY id DESC";

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

        sendResponse(200, true, $students, "Found " . count($students) . " student records.");
    }
}

// -------------------------------------------------------------
// 2. POST: Create New Student
// -------------------------------------------------------------
elseif ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST; // Fallback to standard form-urlencoded
    }

    $name = trim($input['name'] ?? '');
    $enrollment = strtoupper(trim($input['enrollment_no'] ?? ''));
    $email = filter_var(trim($input['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $mobile = trim($input['mobile'] ?? '');
    $department = trim($input['department'] ?? '');
    $semester = (int)($input['semester'] ?? 3);
    $gpa = (float)($input['gpa'] ?? 8.5);

    // Backend Input Validation
    $errors = [];
    if (empty($name)) $errors[] = "Full Name is required.";
    if (empty($enrollment)) $errors[] = "Enrollment Number is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
    if (!preg_match("/^[6-9]\d{9}$/", $mobile)) $errors[] = "Valid 10-digit mobile number starting 6-9 is required.";
    if (empty($department)) $errors[] = "Department must be selected.";

    if (!empty($errors)) {
        sendResponse(400, false, ['errors' => $errors], "Validation failed.");
    }

    try {
        $stmt = $conn->prepare("INSERT INTO students (enrollment_no, name, email, mobile, department, semester, gpa, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'active')");
        $stmt->bind_param("sssssid", $enrollment, $name, $email, $mobile, $department, $semester, $gpa);
        $stmt->execute();
        $newId = $conn->insert_id;
        $stmt->close();

        sendResponse(201, true, ['id' => $newId, 'enrollment_no' => $enrollment, 'name' => $name], "Student created successfully.");
    } catch (mysqli_sql_exception $e) {
        if ($conn->errno === 1062) {
            sendResponse(409, false, null, "Conflict: Enrollment number or email already exists.");
        } else {
            sendResponse(500, false, null, "Database error: " . $e->getMessage());
        }
    }
}

// -------------------------------------------------------------
// 3. PUT: Update Existing Student Record
// -------------------------------------------------------------
elseif ($method === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = isset($input['id']) ? (int)$input['id'] : 0;

    if ($id <= 0) {
        sendResponse(400, false, null, "A valid Student ID is required for update.");
    }

    $name = trim($input['name'] ?? '');
    $mobile = trim($input['mobile'] ?? '');
    $department = trim($input['department'] ?? '');
    $semester = (int)($input['semester'] ?? 3);
    $gpa = (float)($input['gpa'] ?? 8.5);
    $status = $input['status'] ?? 'active';

    if (empty($name) || empty($mobile) || empty($department)) {
        sendResponse(400, false, null, "Name, mobile, and department cannot be blank.");
    }

    try {
        $stmt = $conn->prepare("UPDATE students SET name = ?, mobile = ?, department = ?, semester = ?, gpa = ?, status = ? WHERE id = ?");
        $stmt->bind_param("sssidsi", $name, $mobile, $department, $semester, $gpa, $status, $id);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();

        sendResponse(200, true, ['id' => $id, 'updated' => true], "Student record updated successfully.");
    } catch (mysqli_sql_exception $e) {
        sendResponse(500, false, null, "Failed to update: " . $e->getMessage());
    }
}

// -------------------------------------------------------------
// 4. DELETE: Remove Student Record
// -------------------------------------------------------------
elseif ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($input['id']) ? (int)$input['id'] : 0);

    if ($id <= 0) {
        sendResponse(400, false, null, "Valid Student ID required for deletion.");
    }

    try {
        $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        sendResponse(200, true, ['id' => $id, 'deleted' => true], "Student record deleted successfully.");
    } catch (mysqli_sql_exception $e) {
        sendResponse(500, false, null, "Delete failed: " . $e->getMessage());
    }
} else {
    sendResponse(405, false, null, "Method {$method} not allowed.");
}
