<?php
/**
 * Practical 11: Delete Student Record (Soft Delete / Hard Delete)
 */

require_once __DIR__ . '/db.php';

$id = (int)($_GET['id'] ?? 0);
$mode = $_GET['mode'] ?? 'soft'; // Default to safe soft delete

if ($id > 0) {
    $conn = getDB();

    if ($mode === 'hard') {
        // Permanent delete
        $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $msg = "Student record permanently deleted.";
    } else {
        // Advanced Extension: Soft Delete using status column
        $stmt = $conn->prepare("UPDATE students SET status = 'inactive' WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $msg = "Student status updated to inactive (Soft Deleted).";
    }

    header("Location: index.php?msg=" . urlencode($msg));
    exit;
}

header("Location: index.php");
exit;
