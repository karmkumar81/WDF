<?php
/**
 * Practical 12: Delete Event & Unlink Poster Image
 */

require_once __DIR__ . '/db.php';

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $conn = getDB();

    // Fetch poster filename to safely delete from disk
    $stmt = $conn->prepare("SELECT poster_image FROM events WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($row) {
        $posterFile = $row['poster_image'];
        $uploadDir = __DIR__ . '/uploads/';
        if ($posterFile !== 'default_event.jpg' && file_exists($uploadDir . $posterFile)) {
            @unlink($uploadDir . $posterFile);
        }

        $delStmt = $conn->prepare("DELETE FROM events WHERE id = ?");
        $delStmt->bind_param("i", $id);
        $delStmt->execute();
        $delStmt->close();

        header("Location: index.php?msg=" . urlencode("Event and poster removed successfully."));
        exit;
    }
}

header("Location: index.php");
exit;
