<?php
/**
 * Practical 10: Protected Administrator Dashboard
 */

require_once __DIR__ . '/auth_check.php';
requireAuth('admin'); // Enforce admin role access
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard | StudentHub CHARUSAT</title>
  <style>
    body { font-family: system-ui, sans-serif; background: #0f172a; color: #f8fafc; padding: 2rem; }
    .card { max-width: 800px; margin: 0 auto; background: #1e293b; padding: 2rem; border-radius: 8px; border: 1px solid #334155; }
    .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #334155; padding-bottom: 1rem; margin-bottom: 1.5rem; }
    .badge { background: #fee2e2; color: #991b1b; padding: 0.3rem 0.6rem; border-radius: 4px; font-weight: bold; font-size: 0.8rem; }
    .btn-logout { background: #f43f5e; color: white; padding: 0.5rem 1rem; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 0.9rem; }
  </style>
</head>
<body>
  <div class="card">
    <div class="header">
      <div>
        <h1 style="color: #38bdf8; margin: 0; font-size: 1.5rem;">Administrative Console</h1>
        <p style="color: #94a3b8; margin: 0; font-size: 0.9rem;">Admin User: <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong> (<?= htmlspecialchars($_SESSION['user_email']) ?>)</p>
      </div>
      <div>
        <span class="badge">ROLE: ADMIN</span>
        <a href="logout.php" class="btn-logout" style="margin-left: 1rem;">Logout</a>
      </div>
    </div>

    <p>Session ID: <code><?= htmlspecialchars(session_id()) ?></code></p>
    <p>Admin Privilege Level: <strong>Full System Access</strong></p>

    <div style="background: #0f172a; padding: 1.5rem; border-radius: 6px; margin-top: 1.5rem; border: 1px solid #334155;">
      <h3 style="color: #38bdf8; margin-bottom: 0.5rem;">Role-Based Access Enforcement</h3>
      <p style="font-size: 0.9rem; color: #cbd5e1;">Students attempting to navigate to this page are immediately redirected to their student dashboard with an authorization failure warning.</p>
    </div>
  </div>
</body>
</html>
