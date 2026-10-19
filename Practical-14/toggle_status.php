<?php
/**
 * Practical 14: User Active/Inactive Status Toggle with Audit Logging
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth_check.php';
checkAdminRole();

$userId = (int)($_GET['id'] ?? 0);
$currentStatus = $_GET['current'] ?? 'active';

if ($userId > 0) {
    $conn = getDB();
    $newStatus = ($currentStatus === 'active') ? 'inactive' : 'active';

    // 1. Update user status
    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $newStatus, $userId);
    $stmt->execute();
    $stmt->close();

    // 2. Insert into audit_logs table
    $adminId = $_SESSION['user_id'];
    $action = "USER_STATUS_CHANGE";
    $details = "Admin changed User ID {$userId} status from {$currentStatus} to {$newStatus}";
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

    $auditStmt = $conn->prepare("INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
    $auditStmt->bind_param("isss", $adminId, $action, $details, $ip);
    $auditStmt->execute();
    $auditStmt->close();

    header("Location: dashboard.php?msg=" . urlencode("User ID {$userId} status changed to {$newStatus}."));
    exit;
}

header("Location: dashboard.php");
exit;
