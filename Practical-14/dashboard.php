<?php
/**
 * Practical 14: Admin Dashboard with RBAC, Dynamic Menu, Analytics & Audit Logging
 * Course: ITUE203 - Web Development Frameworks | CHARUSAT
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth_check.php';
checkAdminRole(); // Enforce admin-only access

$conn = getDB();
$msg = $_GET['msg'] ?? '';

// 1. Dynamic Metric Aggregations
// Student Count
$studentCountRes = $conn->query("SELECT COUNT(*) AS total FROM students");
$totalStudents = $studentCountRes->fetch_assoc()['total'] ?? 0;

// Active Events Count
$eventCountRes = $conn->query("SELECT COUNT(*) AS total FROM events WHERE status = 'open'");
$totalActiveEvents = $eventCountRes->fetch_assoc()['total'] ?? 0;

// Registrations Count
$regCountRes = $conn->query("SELECT COUNT(*) AS total FROM registrations");
$totalRegistrations = $regCountRes->fetch_assoc()['total'] ?? 0;

// User Accounts Count
$userCountRes = $conn->query("SELECT COUNT(*) AS total FROM users");
$totalUsers = $userCountRes->fetch_assoc()['total'] ?? 0;

// 2. Fetch Users for Status Toggle (Intermediate Extension)
$usersRes = $conn->query("SELECT id, name, email, role, status, created_at FROM users ORDER BY id ASC LIMIT 5");
$userList = [];
while ($u = $usersRes->fetch_assoc()) {
    $userList[] = $u;
}

// 3. Fetch Recent Registrations with Student & Event Joins
$recentRegsRes = $conn->query("
    SELECT r.id, s.name AS student_name, s.enrollment_no, e.title AS event_title, e.category, r.registered_at, r.status
    FROM registrations r
    JOIN students s ON r.student_id = s.id
    JOIN events e ON r.event_id = e.id
    ORDER BY r.registered_at DESC
    LIMIT 5
");
$recentRegistrations = [];
if ($recentRegsRes) {
    while ($reg = $recentRegsRes->fetch_assoc()) {
        $recentRegistrations[] = $reg;
    }
}

// 4. Fetch Audit Logs
$auditRes = $conn->query("
    SELECT a.id, u.name AS user_name, a.action, a.details, a.ip_address, a.created_at
    FROM audit_logs a
    LEFT JOIN users u ON a.user_id = u.id
    ORDER BY a.created_at DESC
    LIMIT 5
");
$auditLogs = [];
if ($auditRes) {
    while ($log = $auditRes->fetch_assoc()) {
        $auditLogs[] = $log;
    }
}

// 5. Department Breakdown for Analytics
$deptRes = $conn->query("SELECT department, COUNT(*) AS count FROM students GROUP BY department");
$deptStats = [];
if ($deptRes) {
    while ($d = $deptRes->fetch_assoc()) {
        $deptStats[] = $d;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Practical 14 - Admin Dashboard &amp; Audit Logs | CHARUSAT</title>
  <style>
    :root {
      --primary: #1e3a8a;
      --bg: #f8fafc;
      --surface: #ffffff;
      --text: #0f172a;
      --text-muted: #64748b;
      --border: #e2e8f0;
      --success: #16a34a;
      --danger: #dc2626;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: system-ui, sans-serif; background: var(--bg); color: var(--text); line-height: 1.6; }
    
    /* Dynamic Role-Based Navbar */
    .navbar { background: #0f172a; color: white; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; }
    .brand { font-size: 1.3rem; font-weight: bold; color: #38bdf8; text-decoration: none; }
    .nav-menu { display: flex; list-style: none; gap: 1.5rem; }
    .nav-menu a { color: #cbd5e1; text-decoration: none; font-size: 0.9rem; }
    .nav-menu a.active { color: #38bdf8; font-weight: 600; }
    
    .container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
    .alert-success { background: #dcfce7; color: #166534; padding: 0.75rem 1rem; border-radius: 6px; margin-bottom: 1.5rem; }
    
    /* Metric Cards */
    .metric-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; margin-bottom: 2rem; }
    .metric-card { background: white; border: 1px solid var(--border); border-radius: 8px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.04); }
    .metric-number { font-size: 2.2rem; font-weight: 800; color: var(--primary); margin: 0.25rem 0; }
    .metric-title { font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; }
    
    .section-card { background: white; border: 1px solid var(--border); border-radius: 8px; padding: 1.5rem; margin-bottom: 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.04); }
    h2 { font-size: 1.25rem; color: var(--primary); margin-bottom: 1rem; }
    
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid var(--border); font-size: 0.85rem; }
    th { background: #f8fafc; font-weight: 600; color: var(--primary); }
    
    .badge { padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600; }
    .badge-active { background: #dcfce7; color: #166534; }
    .badge-inactive { background: #fee2e2; color: #991b1b; }
    .btn-toggle { padding: 0.3rem 0.6rem; border-radius: 4px; font-size: 0.75rem; text-decoration: none; font-weight: 600; }
    .btn-toggle-deactivate { background: #ef4444; color: white; }
    .btn-toggle-activate { background: #16a34a; color: white; }
  </style>
</head>
<body>
  <!-- Dynamic Menu Bar Based on User Role -->
  <header class="navbar">
    <a href="dashboard.php" class="brand">StudentHub Admin</a>
    <nav>
      <ul class="nav-menu">
        <?php if ($_SESSION['user_role'] === 'admin'): ?>
          <li><a href="dashboard.php" class="active">Overview Stats</a></li>
          <li><a href="../Practical-11/index.php">Manage Students</a></li>
          <li><a href="../Practical-12/index.php">Manage Events</a></li>
          <li><a href="#audit-section">Audit Logs</a></li>
          <li><span style="color: #f43f5e; font-weight: bold;">[ADMIN ACCESS]</span></li>
        <?php else: ?>
          <li><a href="dashboard.php">My Profile</a></li>
          <li><a href="../Practical-06/index.html">Campus Events</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </header>

  <main class="container">
    <div style="margin-bottom: 1.5rem;">
      <h1 style="color: var(--primary);">Administrative Monitoring Console</h1>
      <p style="color: var(--text-muted); font-size: 0.9rem;">
        Practical 14: Dynamic Database Aggregation &bull; User Status Toggle &bull; Audit Trail
      </p>
    </div>

    <?php if (!empty($msg)): ?>
      <div class="alert-success"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <!-- 1. Real-time Aggregation Stat Cards -->
    <div class="metric-grid">
      <div class="metric-card" style="border-left: 4px solid #3b82f6;">
        <div class="metric-title">Enrolled Students</div>
        <div class="metric-number"><?= $totalStudents ?></div>
        <div style="font-size: 0.8rem; color: var(--text-muted);">Across all semesters</div>
      </div>

      <div class="metric-card" style="border-left: 4px solid #10b981;">
        <div class="metric-title">Active Events</div>
        <div class="metric-number"><?= $totalActiveEvents ?></div>
        <div style="font-size: 0.8rem; color: var(--text-muted);">Open for registration</div>
      </div>

      <div class="metric-card" style="border-left: 4px solid #f59e0b;">
        <div class="metric-title">Total Registrations</div>
        <div class="metric-number"><?= $totalRegistrations ?></div>
        <div style="font-size: 0.8rem; color: var(--text-muted);">Event participant bookings</div>
      </div>

      <div class="metric-card" style="border-left: 4px solid #8b5cf6;">
        <div class="metric-title">Portal User Accounts</div>
        <div class="metric-number"><?= $totalUsers ?></div>
        <div style="font-size: 0.8rem; color: var(--text-muted);">Students &amp; administrators</div>
      </div>
    </div>

    <!-- 2. Intermediate Extension: User Status Toggle -->
    <section class="section-card">
      <h2>User Account Management (Status Toggle with Audit Log)</h2>
      <div style="overflow-x: auto;">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email Address</th>
              <th>Role</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($userList as $u): ?>
              <tr>
                <td><?= $u['id'] ?></td>
                <td><strong><?= htmlspecialchars($u['name']) ?></strong></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= ucfirst($u['role']) ?></td>
                <td>
                  <span class="badge <?= $u['status'] === 'active' ? 'badge-active' : 'badge-inactive' ?>">
                    <?= ucfirst($u['status']) ?>
                  </span>
                </td>
                <td>
                  <a href="toggle_status.php?id=<?= $u['id'] ?>&current=<?= $u['status'] ?>" 
                     class="btn-toggle <?= $u['status'] === 'active' ? 'btn-toggle-deactivate' : 'btn-toggle-activate' ?>">
                    <?= $u['status'] === 'active' ? 'Deactivate' : 'Activate' ?>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    <!-- 3. Recent Registrations -->
    <section class="section-card">
      <h2>Recent Event Registrations</h2>
      <?php if (empty($recentRegistrations)): ?>
        <p style="color: var(--text-muted);">No event registrations recorded yet.</p>
      <?php else: ?>
        <div style="overflow-x: auto;">
          <table>
            <thead>
              <tr>
                <th>Registration ID</th>
                <th>Student</th>
                <th>Enrollment</th>
                <th>Event Title</th>
                <th>Category</th>
                <th>Booking Timestamp</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recentRegistrations as $reg): ?>
                <tr>
                  <td>#<?= $reg['id'] ?></td>
                  <td><strong><?= htmlspecialchars($reg['student_name']) ?></strong></td>
                  <td><?= htmlspecialchars($reg['enrollment_no']) ?></td>
                  <td><?= htmlspecialchars($reg['event_title']) ?></td>
                  <td><?= htmlspecialchars($reg['category']) ?></td>
                  <td><?= htmlspecialchars($reg['registered_at']) ?></td>
                  <td><span class="badge badge-active"><?= ucfirst($reg['status']) ?></span></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </section>

    <!-- 4. System Audit Trail -->
    <section class="section-card" id="audit-section">
      <h2>Administrative Audit Logs</h2>
      <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1rem;">
        Immutable audit trail capturing administrative modifications, login attempts, and status toggles.
      </p>
      <div style="overflow-x: auto;">
        <table>
          <thead>
            <tr>
              <th>Log ID</th>
              <th>Triggered By</th>
              <th>Action</th>
              <th>Details</th>
              <th>IP Address</th>
              <th>Timestamp</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($auditLogs as $log): ?>
              <tr>
                <td>#<?= $log['id'] ?></td>
                <td><?= htmlspecialchars($log['user_name'] ?? 'System') ?></td>
                <td><code><?= htmlspecialchars($log['action']) ?></code></td>
                <td><?= htmlspecialchars($log['details']) ?></td>
                <td><?= htmlspecialchars($log['ip_address']) ?></td>
                <td><?= htmlspecialchars($log['created_at']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>
</body>
</html>
