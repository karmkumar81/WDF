<?php
/**
 * Practical 10: Authentication Middleware & Session Protection
 * Course: ITUE203 - Web Development Frameworks | CHARUSAT
 */

if (session_status() === PHP_SESSION_NONE) {
    // Secure session cookie settings
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => false, // Set true in production over HTTPS
        'httponly' => true,  // Prevent JavaScript access to session cookie
        'samesite' => 'Lax'
    ]);
    session_start();
}

/**
 * Enforce authentication and role permissions
 * @param string|null $requiredRole Optional: 'student' or 'admin'
 */
function requireAuth(?string $requiredRole = null) {
    // 1. Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php?msg=" . urlencode("Please log in to access this page."));
        exit;
    }

    // 2. Session Inactivity Timeout (15 minutes = 900 seconds)
    $timeoutSeconds = 900;
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeoutSeconds)) {
        // Clear session data
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        header("Location: login.php?msg=" . urlencode("Your session expired due to inactivity. Please log in again."));
        exit;
    }
    $_SESSION['last_activity'] = time(); // Refresh activity timestamp

    // 3. Role-Based Access Control (RBAC)
    if ($requiredRole !== null && ($_SESSION['user_role'] ?? '') !== $requiredRole) {
        // User does not possess the required privilege
        if ($_SESSION['user_role'] === 'student') {
            header("Location: student_dashboard.php?error=" . urlencode("Unauthorized: Admin privileges required."));
        } else {
            header("Location: admin_dashboard.php?error=" . urlencode("Access redirected to your administrative dashboard."));
        }
        exit;
    }
}
