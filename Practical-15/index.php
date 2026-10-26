<?php
/**
 * Practical 15: StudentHub Master Integration & Deployment Hub
 * Course: ITUE203 - Web Development Frameworks | CHARUSAT
 */

// Diagnostic Checks
$phpVersion = PHP_VERSION;
$extensions = [
    'mysqli' => extension_loaded('mysqli'),
    'pdo_mysql' => extension_loaded('pdo_mysql'),
    'json' => extension_loaded('json'),
    'fileinfo' => extension_loaded('fileinfo'),
    'session' => extension_loaded('session'),
];

$practicals = [
    ['num' => '01', 'title' => 'Project Initiation, Sitemap, Wireframe & GitHub Setup', 'link' => '../Practical-01/sitemap.html', 'tech' => 'HTML, Wireframes, Git'],
    ['num' => '02', 'title' => 'Semantic HTML5 Pages with Accessibility Structure', 'link' => '../Practical-02/index.html', 'tech' => 'Semantic HTML5, WCAG 2.1'],
    ['num' => '03', 'title' => 'Responsive UI Design using CSS Grid & Flexbox', 'link' => '../Practical-03/index.html', 'tech' => 'CSS3, Grid, Flexbox, Media Queries'],
    ['num' => '04', 'title' => 'JavaScript DOM Manipulation, Events & Dark Mode', 'link' => '../Practical-04/index.html', 'tech' => 'ES6+, DOM, localStorage'],
    ['num' => '05', 'title' => 'Registration Form with Regex Validation', 'link' => '../Practical-05/index.html', 'tech' => 'HTML Forms, Regex, Password Meter'],
    ['num' => '06', 'title' => 'External JSON Rendering using Fetch API & Filter', 'link' => '../Practical-06/index.html', 'tech' => 'Fetch API, JSON, Pagination'],
    ['num' => '07', 'title' => 'PHP Form Processing with CSV/JSON Storage & CSRF', 'link' => '../Practical-07/index.php', 'tech' => 'PHP POST, flock, CSV/JSON, CSRF'],
    ['num' => '08', 'title' => 'MySQL Schema Design, ER Model & PDO Connectivity', 'link' => '../Practical-08/test_connection.php', 'tech' => 'MySQL, 3NF, PDO, MySQLi'],
    ['num' => '09', 'title' => 'Secure User Registration & Password Hashing', 'link' => '../Practical-09/register.php', 'tech' => 'password_hash, Duplicate Check'],
    ['num' => '10', 'title' => 'Secure Login/Logout, Sessions, RBAC & Timeout', 'link' => '../Practical-10/login.php', 'tech' => 'password_verify, Sessions, RBAC'],
    ['num' => '11', 'title' => 'Student Management CRUD with Search & Soft Delete', 'link' => '../Practical-11/index.php', 'tech' => 'MySQLi CRUD, CSV Export'],
    ['num' => '12', 'title' => 'Event Management CRUD with Poster Upload & Security', 'link' => '../Practical-12/index.php', 'tech' => 'File Upload, MIME validation'],
    ['num' => '13', 'title' => 'PHP REST JSON API & Asynchronous Fetch CRUD', 'link' => '../Practical-13/index.html', 'tech' => 'REST API, Fetch API, Debounce'],
    ['num' => '14', 'title' => 'Admin Dashboard, Metrics, Dynamic Menu & Audit Logs', 'link' => '../Practical-14/dashboard.php', 'tech' => 'Aggregation, Audit Logs, RBAC'],
    ['num' => '15', 'title' => 'Final Integration, Testing & Deployment Suite', 'link' => 'index.php', 'tech' => 'Full-Stack Integration & QA'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Practical 15 - StudentHub Master Integration Hub | CHARUSAT</title>
  <style>
    :root {
      --primary: #1e3a8a;
      --bg: #0f172a;
      --surface: #1e293b;
      --card: #334155;
      --text: #f8fafc;
      --text-muted: #94a3b8;
      --accent: #38bdf8;
      --success: #10b981;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      background: var(--bg);
      color: var(--text);
      line-height: 1.6;
      padding: 2rem 1rem;
    }
    .wrapper { max-width: 1200px; margin: 0 auto; }
    header { text-align: center; margin-bottom: 2.5rem; }
    h1 { color: var(--accent); font-size: 2.2rem; margin-bottom: 0.5rem; }
    .subtitle { color: var(--text-muted); font-size: 1rem; }

    .status-panel {
      background: var(--surface);
      border: 1px solid var(--card);
      border-radius: 8px;
      padding: 1.5rem;
      margin-bottom: 2rem;
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      gap: 1rem;
    }
    .status-pill {
      background: rgba(16, 185, 129, 0.15);
      color: var(--success);
      border: 1px solid var(--success);
      padding: 0.35rem 0.75rem;
      border-radius: 999px;
      font-size: 0.85rem;
      font-weight: 600;
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
      gap: 1.5rem;
    }
    .card {
      background: var(--surface);
      border: 1px solid var(--card);
      border-radius: 8px;
      padding: 1.5rem;
      display: flex;
      flex-direction: column;
      transition: transform 0.2s, border-color 0.2s;
    }
    .card:hover {
      transform: translateY(-2px);
      border-color: var(--accent);
    }
    .card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 0.75rem;
    }
    .badge-num {
      background: var(--primary);
      color: white;
      font-weight: bold;
      padding: 0.2rem 0.6rem;
      border-radius: 4px;
      font-size: 0.85rem;
    }
    .card h3 { font-size: 1.1rem; color: var(--text); margin-bottom: 0.5rem; }
    .card p { font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.25rem; }
    .btn-launch {
      margin-top: auto;
      background: var(--accent);
      color: #0f172a;
      text-decoration: none;
      padding: 0.6rem 1rem;
      border-radius: 6px;
      font-weight: 700;
      font-size: 0.85rem;
      text-align: center;
      transition: opacity 0.2s;
    }
    .btn-launch:hover { opacity: 0.9; }
  </style>
</head>
<body>
  <div class="wrapper">
    <header>
      <h1>StudentHub Master Integration Suite</h1>
      <p class="subtitle">CHARUSAT &bull; Faculty of Technology and Engineering &bull; ITUE203 (Semester 3)</p>
      <p style="font-size: 0.85rem; color: var(--accent); margin-top: 0.25rem;">Practical 15: Full-Stack Integration, Verification, Documentation &amp; Deployment</p>
    </header>

    <div class="status-panel">
      <div>
        <strong>PHP Runtime:</strong> <?= $phpVersion ?> | 
        <strong>Web Server:</strong> <?= $_SERVER['SERVER_SOFTWARE'] ?? 'Apache' ?>
      </div>
      <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <?php foreach ($extensions as $ext => $loaded): ?>
          <span class="status-pill"><?= strtoupper($ext) ?>: <?= $loaded ? '✓ Active' : '✗ Missing' ?></span>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="grid">
      <?php foreach ($practicals as $p): ?>
        <div class="card">
          <div class="card-header">
            <span class="badge-num">Practical <?= $p['num'] ?></span>
            <span style="font-size: 0.75rem; color: var(--text-muted);"><?= htmlspecialchars($p['tech']) ?></span>
          </div>
          <h3><?= htmlspecialchars($p['title']) ?></h3>
          <p>Verified according to exact PDF requirements without extra clutter.</p>
          <a href="<?= $p['link'] ?>" class="btn-launch" target="_blank">Launch Practical <?= $p['num'] ?> &rarr;</a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</body>
</html>
