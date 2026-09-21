<?php
/**
 * Practical 10: Protected Student Dashboard
 */

require_once __DIR__ . '/auth_check.php';
requireAuth('student'); // Enforce student role access
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Dashboard | StudentHub CHARUSAT</title>
  <style>
    body { font-family: system-ui, sans-serif; background: #f8fafc; color: #0f172a; padding: 2rem; }
    .card { max-width: 800px; margin: 0 auto; background: white; padding: 2rem; border-radius: 8px; border: 1px solid #e2e8f0; }
    .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem; margin-bottom: 1.5rem; }
    .badge { background: #dcfce7; color: #166534; padding: 0.3rem 0.6rem; border-radius: 4px; font-weight: bold; font-size: 0.8rem; }
    .btn-logout { background: #ef4444; color: white; padding: 0.5rem 1rem; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 0.9rem; }
  </style>
</head>
<body>
  <div class="card">
    <div class="header">
      <div>
        <h1 style="color: #1e3a8a; margin: 0; font-size: 1.5rem;">Student Portal Dashboard</h1>
        <p style="color: #64748b; margin: 0; font-size: 0.9rem;">Authenticated as: <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong> (<?= htmlspecialchars($_SESSION['user_email']) ?>)</p>
      </div>
      <div>
        <span class="badge">ROLE: STUDENT</span>
        <a href="logout.php" class="btn-logout" style="margin-left: 1rem;">Logout</a>
      </div>
    </div>

    <p>Session ID: <code><?= htmlspecialchars(session_id()) ?></code></p>
    <p>Last Activity Timestamp: <code><?= date('Y-m-d H:i:s', $_SESSION['last_activity']) ?></code> (Auto-expires in 15 mins of inactivity)</p>

    <div style="background: #f1f5f9; padding: 1.5rem; border-radius: 6px; margin-top: 1.5rem;">
      <h3 style="color: #1e3a8a; margin-bottom: 0.5rem;">Security Verification Checklist</h3>
      <ul style="padding-left: 1.25rem; font-size: 0.9rem;">
        <li>Unauthorized visitors attempting direct access are redirected to <code>login.php</code>.</li>
        <li>Attempting to visit <code>admin_dashboard.php</code> will be blocked by Role-Based Access Control (RBAC).</li>
        <li>Inactive sessions auto-terminate after 15 minutes.</li>
      </ul>
    </div>
  </div>
</body>
</html>
