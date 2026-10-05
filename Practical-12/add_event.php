<?php
/**
 * Practical 12: Add Campus Event with File Upload Validation
 */

require_once __DIR__ . '/db.php';

$errors = [];
$title = $category = $date = $location = '';
$seats = 50;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $date = trim($_POST['event_date'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $seats = (int)($_POST['seats'] ?? 50);
    $posterFilename = 'default_event.jpg';

    if (empty($title)) $errors[] = "Event title is required.";
    if (empty($category)) $errors[] = "Category is required.";
    if (empty($date)) $errors[] = "Event date is required.";
    if (empty($location)) $errors[] = "Venue location is required.";

    // File Upload Validation (Type, Size, Extension)
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['poster'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "File upload failed with error code: " . $file['error'];
        } else {
            // Check file size (Max 2MB = 2 * 1024 * 1024 bytes)
            $maxBytes = 2 * 1024 * 1024;
            if ($file['size'] > $maxBytes) {
                $errors[] = "File size exceeds 2MB limit. (Current: " . round($file['size'] / 1024 / 1024, 2) . " MB)";
            }

            // Check file extension
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
            if (!in_array($ext, $allowedExts, true)) {
                $errors[] = "Invalid file type. Only JPG, PNG, and WebP images are permitted.";
            }

            // Check MIME type via finfo
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
            if (!in_array($mime, $allowedMimes, true)) {
                $errors[] = "Invalid MIME type detected ({$mime}). Upload must be an authentic image.";
            }

            // Safe image renaming (Advanced Extension)
            if (empty($errors)) {
                $uploadDir = __DIR__ . '/uploads/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                
                $posterFilename = uniqid('event_', true) . '.' . $ext;
                $targetPath = $uploadDir . $posterFilename;

                if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                    $errors[] = "Failed to store uploaded file on the server.";
                }
            }
        }
    }

    if (empty($errors)) {
        $conn = getDB();
        $stmt = $conn->prepare("INSERT INTO events (title, category, event_date, location, seats, poster_image, status) VALUES (?, ?, ?, ?, ?, ?, 'open')");
        $stmt->bind_param("ssssis", $title, $category, $date, $location, $seats, $posterFilename);
        $stmt->execute();
        $stmt->close();

        header("Location: index.php?msg=" . urlencode("Event '{$title}' created successfully!"));
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add Event | Practical 12</title>
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
    <h2 style="color: #1e3a8a; margin-bottom: 1.5rem;">Create New Campus Event</h2>

    <?php if (!empty($errors)): ?>
      <div class="alert-error">
        <?php foreach ($errors as $e): ?><div>&bull; <?= htmlspecialchars($e) ?></div><?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="add_event.php" enctype="multipart/form-data">
      <div class="form-group">
        <label>Event Title *</label>
        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($title) ?>" placeholder="e.g. AI & Robotics Symposium" required />
      </div>

      <div class="form-group">
        <label>Category *</label>
        <select name="category" class="form-control" required>
          <option value="">-- Choose Category --</option>
          <option value="Technical">Technical</option>
          <option value="Robotics">Robotics</option>
          <option value="Workshop">Workshop</option>
          <option value="Sports">Sports</option>
          <option value="Cultural">Cultural</option>
        </select>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
        <div class="form-group">
          <label>Event Date *</label>
          <input type="date" name="event_date" class="form-control" value="<?= htmlspecialchars($date) ?>" required />
        </div>
        <div class="form-group">
          <label>Available Seats *</label>
          <input type="number" name="seats" class="form-control" value="<?= $seats ?>" min="1" max="1000" required />
        </div>
      </div>

      <div class="form-group">
        <label>Venue / Location *</label>
        <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($location) ?>" placeholder="e.g. CSPIT Seminar Hall" required />
      </div>

      <div class="form-group">
        <label>Upload Event Poster (Max 2MB | JPG, PNG, WebP)</label>
        <input type="file" name="poster" class="form-control" accept=".jpg,.jpeg,.png,.webp" />
      </div>

      <button type="submit" class="btn">Publish Event</button>
      <a href="index.php" style="margin-left: 1rem; color: #64748b;">Cancel</a>
    </form>
  </div>
</body>
</html>
