<?php
/**
 * Practical 10: Secure Logout Handler
 * Destroys session, unsets cookies, and redirects safely to login.
 */

require_once __DIR__ . '/db.php';

session_start();

// Clear remember token in DB if present
if (isset($_SESSION['user_id'])) {
    $conn = getDB();
    $stmt = $conn->prepare("UPDATE users SET remember_token = NULL WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();
}

// 1. Unset all session variables
$_SESSION = [];

// 2. Clear Session Cookie from browser
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Clear remember me cookie
if (isset($_COOKIE['studenthub_remember'])) {
    setcookie('studenthub_remember', '', time() - 3600, '/');
}

// 4. Destroy session
session_destroy();

header("Location: login.php?msg=" . urlencode("You have logged out securely."));
exit;
