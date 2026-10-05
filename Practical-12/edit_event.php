<?php
/**
 * Practical 12: Edit Campus Event & Update Poster
 */

require_once __DIR__ . '/db.php';

$conn = getDB();
$id = (int)($_GET['id'] ?? 0);
$errors = [];

$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$event) {
    header("Location: index.php?error=" . urlencode("Event not found."));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $date = trim($_POST['event_date'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $seats = (int)($_POST['seats'] ?? 50);
    $status = $_POST['status'] ?? 'open';
    $posterFilename = $event['poster_image'];

    if (empty($title) || empty($category) || empty($date) || empty($location)) {
        $errors[] = "All text fields are required.";
    }

    // Check if new poster uploaded
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['poster'];
        $maxBytes = 2 * 1024 * 1024;
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if ($file['size'] > $maxBytes) {
            $errors[] = "Uploaded file exceeds 2MB limit.";
        } elseif (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            $errors[] = "Allowed extensions: JPG, PNG, WebP.";
        } else {
            $uploadDir = __DIR__ . '/uploads/';
            $newFilename = uniqid('event_', true) . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], $uploadDir . $newFilename)) {
                // Delete old poster if not default
                if ($posterFilename !== 'default_event.jpg' && file_exists($uploadDir . $posterFilename)) {
                    @unlink($uploadDir . $posterFilename);
                }
                $posterFilename = $newFilename;
            } else {
                $errors[] = "Failed to save new poster image.";
            }
        }
    }

    if (empty($errors)) {
        $updateStmt = $conn->prepare("UPDATE events SET title = ?, category = ?, event_date = ?, location = ?, seats = ?, poster_image = ?, status = ? WHERE id = ?");
        $updateStmt->bind_param("ssssissi", $title, $category, $date, $location, $seats, $posterFilename, $status, $id);
        $updateStmt->execute();
        $updateStmt->close();

        header("Location: index.php?msg=" . urlencode("Event '{$title}' updated successfully!"));
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Event | Practical 12</title>
  <style>
    body { font-family: system-ui, sans-serif; background: #f8fafc; color: #0f172a; padding: 2rem; }
    .card { max-width: 650px; margin: 0 auto; background: white; padding: 2rem; border-radius: 8px; border: 1px solid #e2e8f0; }
    .form-group { margin-bottom: 1.2rem; }
    label { display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.3rem; }
    .form-control { width: 100%; padding: 0.65rem; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; }
    .btn { background: #1e3a8a; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; }
    .alert-error { background: #fee2e2; color: #991b1b; padding: 0.75rem; border-radius: 6px; margin-bottom: 1rem; }
  </style>
</head>
<body>
  <div class="card">
    <h2 style="color: #1e3a8a; margin-bottom: 1.5rem;">Edit Event Details</h2>

    <?php if (!empty($errors)): ?>
      <div class="alert-error">
        <?php foreach ($errors as $e): ?><div>&bull; <?= htmlspecialchars($e) ?></div><?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="edit_event.php?id=<?= $id ?>" enctype="multipart/form-data">
      <div class="form-group">
        <label>Event Title *</label>
        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($event['title']) ?>" required />
      </div>

      <div class="form-group">
        <label>Category *</label>
        <select name="category" class="form-control" required>
          <option value="Technical" <?= $event['category'] === 'Technical' ? 'selected' : '' ?>>Technical</option>
          <option value="Robotics" <?= $event['category'] === 'Robotics' ? 'selected' : '' ?>>Robotics</option>
          <option value="Workshop" <?= $event['category'] === 'Workshop' ? 'selected' : '' ?>>Workshop</option>
          <option value="Sports" <?= $event['category'] === 'Sports' ? 'selected' : '' ?>>Sports</option>
          <option value="Cultural" <?= $event['category'] === 'Cultural' ? 'selected' : '' ?>>Cultural</option>
        </select>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
        <div class="form-group">
          <label>Event Date *</label>
          <input type="date" name="event_date" class="form-control" value="<?= htmlspecialchars($event['event_date']) ?>" required />
        </div>
        <div class="form-group">
          <label>Seats *</label>
          <input type="number" name="seats" class="form-control" value="<?= $event['seats'] ?>" required />
        </div>
      </div>

      <div class="form-group">
        <label>Venue / Location *</label>
        <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($event['location']) ?>" required />
      </div>

      <div class="form-group">
        <label>Status</label>
        <select name="status" class="form-control">
          <option value="open" <?= $event['status'] === 'open' ? 'selected' : '' ?>>Open</option>
          <option value="closed" <?= $event['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
          <option value="cancelled" <?= $event['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
        </select>
      </div>

      <div class="form-group">
        <label>Update Poster (Leave blank to keep existing poster: <?= htmlspecialchars($event['poster_image']) ?>)</label>
        <input type="file" name="poster" class="form-control" accept=".jpg,.jpeg,.png,.webp" />
      </div>

      <button type="submit" class="btn">Update Event</button>
      <a href="index.php" style="margin-left: 1rem; color: #64748b;">Back to List</a>
    </form>
  </div>
</body>
</html>
