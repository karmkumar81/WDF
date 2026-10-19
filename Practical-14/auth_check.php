<?php
/**
 * Practical 14: Authentication & RBAC Enforcer
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Auto-seed admin session for standalone practical testing if not logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['user_name'] = "Administrative Officer";
    $_SESSION['user_email'] = "admin@charusat.edu.in";
    $_SESSION['user_role'] = "admin";
}

function checkAdminRole() {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
        die("<div style='font-family:sans-serif; padding:2rem; background:#fee2e2; color:#991b1b;'>
            <h2>403 Forbidden: Administrator Access Required</h2>
            <p>Your account lacks the privileges required to access the Administrative Dashboard.</p>
        </div>");
    }
}
