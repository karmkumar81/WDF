<?php
/**
 * Practical 8: Database Connection Diagnostic & Prepared Statement Test Page
 * Course: ITUE203 - Web Development Frameworks | CHARUSAT
 */

require_once __DIR__ . '/db.php';

$pdoStatus = ['status' => false, 'message' => ''];
$mysqliStatus = ['status' => false, 'message' => ''];
$testQueryResults = [];

// 1. Test PDO Connection
try {
    $pdo = getPDOConnection();
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total_students FROM `students`");
    $stmt->execute();
    $row = $stmt->fetch();
    $pdoStatus['status'] = true;
    $pdoStatus['message'] = "PDO Connected successfully! Total seeded students: " . $row['total_students'];
} catch (Exception $e) {
    $pdoStatus['status'] = false;
    $pdoStatus['message'] = $e->getMessage();
}

// 2. Test MySQLi Connection
try {
    $mysqli = getMySQLiConnection();
    $stmt = $mysqli->prepare("SELECT `id`, `title`, `category`, `seats` FROM `events` LIMIT 3");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($r = $result->fetch_assoc()) {
        $testQueryResults[] = $r;
    }
    $mysqliStatus['status'] = true;
    $mysqliStatus['message'] = "MySQLi Connected successfully! Events retrieved: " . count($testQueryResults);
} catch (Exception $e) {
    $mysqliStatus['status'] = false;
    $mysqliStatus['message'] = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Practical 8 - Database Connection Test | CHARUSAT</title>
  <style>
    body { font-family: system-ui, sans-serif; background: #f8fafc; color: #0f172a; padding: 2rem; }
    .card { background: white; max-width: 750px; margin: 0 auto; padding: 2rem; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.06); }
    h1 { color: #1e3a8a; margin-bottom: 0.5rem; }
    .badge-success { background: #dcfce7; color: #166534; padding: 0.5rem 1rem; border-radius: 6px; font-weight: bold; display: inline-block; }
    .badge-fail { background: #fee2e2; color: #991b1b; padding: 0.5rem 1rem; border-radius: 6px; font-weight: bold; display: inline-block; }
    .test-box { margin: 1.25rem 0; padding: 1rem; border: 1px solid #e2e8f0; border-radius: 6px; }
    table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
    th, td { padding: 0.5rem; text-align: left; border-bottom: 1px solid #e2e8f0; }
    th { background: #f1f5f9; }
  </style>
</head>
<body>
  <div class="card">
    <h1>Database Connection Status</h1>
    <p style="color: #64748b; margin-bottom: 1.5rem;">Diagnostic check for MySQLi and PDO prepared statement connectors.</p>

    <div class="test-box">
      <h3>1. PHP Data Objects (PDO) Connection</h3>
      <div style="margin-top: 0.5rem;">
        <?php if ($pdoStatus['status']): ?>
          <span class="badge-success">&#10004; <?= htmlspecialchars($pdoStatus['message']) ?></span>
        <?php else: ?>
          <span class="badge-fail">&#10008; <?= htmlspecialchars($pdoStatus['message']) ?></span>
        <?php endif; ?>
      </div>
    </div>

    <div class="test-box">
      <h3>2. MySQLi (Object-Oriented) Connection</h3>
      <div style="margin-top: 0.5rem;">
        <?php if ($mysqliStatus['status']): ?>
          <span class="badge-success">&#10004; <?= htmlspecialchars($mysqliStatus['message']) ?></span>
        <?php else: ?>
          <span class="badge-fail">&#10008; <?= htmlspecialchars($mysqliStatus['message']) ?></span>
        <?php endif; ?>
      </div>
    </div>

    <?php if (!empty($testQueryResults)): ?>
      <div class="test-box">
        <h3>Sample Events Queried via Prepared Statement</h3>
        <table>
          <thead><tr><th>ID</th><th>Title</th><th>Category</th><th>Available Seats</th></tr></thead>
          <tbody>
            <?php foreach ($testQueryResults as $event): ?>
              <tr>
                <td><?= $event['id'] ?></td>
                <td><?= htmlspecialchars($event['title']) ?></td>
                <td><?= htmlspecialchars($event['category']) ?></td>
                <td><?= $event['seats'] ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
