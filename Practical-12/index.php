<?php
/**
 * Practical 12: Event Management CRUD with Poster Upload & File Validation
 * Course: ITUE203 - Web Development Frameworks | CHARUSAT
 */

require_once __DIR__ . '/db.php';

$conn = getDB();

$category = trim($_GET['category'] ?? '');
$status = trim($_GET['status'] ?? '');
$msg = $_GET['msg'] ?? '';
$error = $_GET['error'] ?? '';

$sql = "SELECT id, title, category, event_date, location, seats, poster_image, status FROM events WHERE 1=1";
$params = [];
$types = "";

if (!empty($category) && $category !== 'all') {
    $sql .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

if (!empty($status) && $status !== 'all') {
    $sql .= " AND status = ?";
    $params[] = $status;
    $types .= "s";
}

$sql .= " ORDER BY event_date ASC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Practical 12 - Event Management &amp; Poster Upload | CHARUSAT</title>
  <style>
    body { font-family: system-ui, sans-serif; background: #f8fafc; color: #0f172a; padding: 2rem 1rem; }
    .container { max-width: 1100px; margin: 0 auto; background: white; padding: 2rem; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.06); }
    .header-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; }
    h1 { color: #1e3a8a; font-size: 1.7rem; margin: 0; }
    .btn { padding: 0.6rem 1.1rem; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.9rem; cursor: pointer; border: none; }
    .btn-primary { background: #1e3a8a; color: white; }
    .btn-edit { background: #0284c7; color: white; padding: 0.3rem 0.6rem; font-size: 0.8rem; border-radius: 4px; }
    .btn-del { background: #ef4444; color: white; padding: 0.3rem 0.6rem; font-size: 0.8rem; border-radius: 4px; }
    
    .filter-bar { display: flex; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1.5rem; background: #f1f5f9; padding: 1rem; border-radius: 6px; }
    .filter-input { padding: 0.55rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 0.9rem; }
    
    table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
    th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #e2e8f0; font-size: 0.85rem; vertical-align: middle; }
    th { background: #f8fafc; color: #1e3a8a; font-weight: 600; }
    .poster-thumb { width: 60px; height: 45px; object-fit: cover; border-radius: 4px; border: 1px solid #cbd5e1; background: #e2e8f0; }
    .status-open { color: #16a34a; font-weight: bold; }
    .status-closed { color: #dc2626; font-weight: bold; }
    
    .alert-success { background: #dcfce7; color: #166534; padding: 0.75rem 1rem; border-radius: 6px; margin-bottom: 1rem; }
    .alert-error { background: #fee2e2; color: #991b1b; padding: 0.75rem 1rem; border-radius: 6px; margin-bottom: 1rem; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header-bar">
      <div>
        <h1>Campus Events Management</h1>
        <p style="color: #64748b; font-size: 0.85rem; margin-top: 0.25rem;">Practical 12: Admin Event CRUD with Validated Poster Uploads</p>
      </div>
      <div>
        <a href="add_event.php" class="btn btn-primary">+ Add New Event</a>
      </div>
    </div>

    <?php if (!empty($msg)): ?>
      <div class="alert-success"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
      <div class="alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Category & Status Filter Bar (Intermediate Extension) -->
    <form method="GET" action="index.php" class="filter-bar">
      <select name="category" class="filter-input">
        <option value="all">All Categories</option>
        <option value="Technical" <?= $category === 'Technical' ? 'selected' : '' ?>>Technical</option>
        <option value="Robotics" <?= $category === 'Robotics' ? 'selected' : '' ?>>Robotics</option>
        <option value="Workshop" <?= $category === 'Workshop' ? 'selected' : '' ?>>Workshop</option>
        <option value="Sports" <?= $category === 'Sports' ? 'selected' : '' ?>>Sports</option>
      </select>

      <select name="status" class="filter-input">
        <option value="all">All Statuses</option>
        <option value="open" <?= $status === 'open' ? 'selected' : '' ?>>Open for Registration</option>
        <option value="closed" <?= $status === 'closed' ? 'selected' : '' ?>>Closed</option>
      </select>

      <button type="submit" class="btn btn-primary" style="padding: 0.55rem 1rem;">Filter Events</button>
      <a href="index.php" class="btn" style="background: #cbd5e1; color: #0f172a; padding: 0.55rem 1rem;">Reset</a>
    </form>

    <div style="overflow-x: auto;">
      <table>
        <thead>
          <tr>
            <th>Poster</th>
            <th>Event Title</th>
            <th>Category</th>
            <th>Date</th>
            <th>Location</th>
            <th>Available Seats</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($events)): ?>
            <tr><td colspan="8" style="text-align: center; color: #64748b; padding: 2rem;">No matching campus events found.</td></tr>
          <?php else: ?>
            <?php foreach ($events as $ev): ?>
              <tr>
                <td>
                  <?php 
                    $posterFile = $ev['poster_image'];
                    $posterPath = __DIR__ . '/uploads/' . $posterFile;
                    $src = file_exists($posterPath) && $posterFile !== 'default_event.jpg' ? 'uploads/' . htmlspecialchars($posterFile) : 'https://placehold.co/120x90/1e3a8a/white?text=Poster';
                  ?>
                  <img src="<?= $src ?>" alt="Poster" class="poster-thumb" />
                </td>
                <td><strong><?= htmlspecialchars($ev['title']) ?></strong></td>
                <td><?= htmlspecialchars($ev['category']) ?></td>
                <td><?= htmlspecialchars($ev['event_date']) ?></td>
                <td><?= htmlspecialchars($ev['location']) ?></td>
                <td><?= $ev['seats'] ?></td>
                <td>
                  <span class="<?= $ev['status'] === 'open' ? 'status-open' : 'status-closed' ?>">
                    <?= ucfirst($ev['status']) ?>
                  </span>
                </td>
                <td>
                  <a href="edit_event.php?id=<?= $ev['id'] ?>" class="btn-edit">Edit</a>
                  <a href="delete_event.php?id=<?= $ev['id'] ?>" class="btn-del" onclick="return confirm('Are you sure you want to delete event <?= htmlspecialchars($ev['title']) ?>?');">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
