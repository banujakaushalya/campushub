<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 2;
$success_msg = "";
$error_msg = "";

// Fetch upcoming or recent events for select dropdown
$events = [];
$e_res = Database::search("SELECT event_id, title FROM events ORDER BY event_date DESC LIMIT 10");
if ($e_res && $e_res->num_rows > 0) {
    while ($row = $e_res->fetch_assoc()) {
        $events[] = $row;
    }
}

// Handle media upload
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $event_id = isset($_POST['event_id']) ? (int)$_POST['event_id'] : 0;
    
    if ($event_id <= 0 && !empty($events)) {
        $event_id = (int)$events[0]['event_id'];
    }
    
    $file_path = "tech_coding_work_shop.jpg";
    
    // Check if file was uploaded
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $file_name = time() . '_' . basename($_FILES['photo']['name']);
        $target_dir = __DIR__ . "/../assets/images/events/";
        if (!is_dir($target_dir)) {
            @mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
            $file_path = $file_name;
        }
    }

    $meta_json = json_encode(['description' => $description]);

    if (empty($title)) {
        $error_msg = "Please provide a photo title.";
    } elseif ($event_id <= 0) {
        $error_msg = "An active event is required to upload a photo. Please select an event.";
    } else {
        $inserted = Database::iud(
            "INSERT INTO media (title, media_type, meta_data, events_event_id, users_user_id, file_path) VALUES (?, 'image', ?, ?, ?, ?)",
            "ssiis",
            [$title, $meta_json, $event_id, $user_id, $file_path]
        );

        if ($inserted) {
            $success_msg = "Photo successfully submitted to the campus gallery!";
        } else {
            $error_msg = "Could not save photo to database. Please check your inputs.";
        }
    }
}
?>
<main class="page-container">
  <style>
    .page-container {
      --ink: #171b2a;
      --muted: #687083;
      --line: #e4e7ed;
      --coral: #FF6B4A;
      width: 100%;
      padding: 0 24px 64px;
      box-sizing: border-box;
    }

    .upload-container {
      width: 100%;
      max-width: 680px;
      margin: 40px auto 0;
    }

    .upload-card {
      background: #ffffff;
      border: 1px solid var(--line);
      border-radius: 16px;
      padding: 36px;
      box-shadow: 0 10px 30px rgba(23, 27, 42, 0.08);
    }

    .upload-card h1 {
      margin: 0 0 8px;
      color: var(--ink);
      font-size: 1.6rem;
    }

    .upload-card p.subtitle {
      margin: 0 0 24px;
      color: var(--muted);
      font-family: 'Cabin Sketch', cursive;
      font-size: 1.15rem;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 700;
      color: var(--ink);
      font-size: 0.9rem;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .form-control {
      width: 100%;
      padding: 12px 14px;
      border: 1.5px solid var(--line);
      border-radius: 8px;
      font-size: 0.95rem;
      color: var(--ink);
      box-sizing: border-box;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .form-control:focus {
      outline: none;
      border-color: var(--coral);
      box-shadow: 0 0 0 3px rgba(255, 107, 74, 0.15);
    }

    .file-drop-zone {
      border: 2px dashed var(--line);
      border-radius: 12px;
      padding: 32px 20px;
      text-align: center;
      background: #faf8ff;
      cursor: pointer;
      transition: border-color 0.2s, background-color 0.2s;
    }

    .file-drop-zone:hover {
      border-color: var(--coral);
      background: #fff5f2;
    }

    .file-drop-zone input[type="file"] {
      display: none;
    }

    .upload-icon {
      font-size: 2.5rem;
      margin-bottom: 8px;
    }

    .upload-text {
      color: var(--ink);
      font-weight: bold;
      font-size: 0.95rem;
      margin-bottom: 4px;
    }

    .upload-hint {
      color: var(--muted);
      font-size: 0.82rem;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .btn-submit {
      width: 100%;
      background-color: var(--coral);
      color: #ffffff;
      padding: 14px;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      font-size: 1rem;
      cursor: pointer;
      box-shadow: 0 4px 12px rgba(255, 107, 74, 0.35);
      transition: background-color 0.2s;
    }

    .btn-submit:hover {
      background-color: #e55a3a;
    }

    .alert {
      padding: 14px 18px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      font-size: 0.92rem;
      font-weight: 600;
    }

    .alert-success {
      background-color: #e6f7ec;
      color: #1b7a3e;
      border: 1px solid #c2ebd0;
    }

    .alert-error {
      background-color: #ffeaea;
      color: #c92a2a;
      border: 1px solid #fcc2c2;
    }
  </style>

  <div class="upload-container">
    <div class="upload-card">
      <h1>Upload Activity Photo</h1>
      <p class="subtitle">Share your event captures and club memories with CampusHub.</p>

      <?php if (!empty($success_msg)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
      <?php endif; ?>

      <?php if (!empty($error_msg)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error_msg); ?></div>
      <?php endif; ?>

      <form action="upload_photo.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
          <label for="title">Photo Title *</label>
          <input type="text" id="title" name="title" class="form-control" placeholder="e.g. Hackathon Team Alpha Presentation" required>
        </div>

        <div class="form-group">
          <label for="event_id">Associated Event</label>
          <select id="event_id" name="event_id" class="form-control">
            <option value="0">General Campus Activity</option>
            <?php foreach ($events as $ev): ?>
              <option value="<?php echo htmlspecialchars($ev['event_id']); ?>">
                <?php echo htmlspecialchars($ev['title']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label for="description">Caption / Description</label>
          <textarea id="description" name="description" class="form-control" rows="3" placeholder="Brief note about this photo..."></textarea>
        </div>

        <div class="form-group">
          <label>Select Photo File</label>
          <label class="file-drop-zone" for="photo">
            <div class="upload-icon">📷</div>
            <div class="upload-text" id="file-name-display">Click to choose image file (JPG, PNG)</div>
            <div class="upload-hint">Supported formats: JPG, PNG, WEBP (Max 5MB)</div>
            <input type="file" id="photo" name="photo" accept="image/*" onchange="document.getElementById('file-name-display').innerText = this.files[0] ? this.files[0].name : 'Click to choose image file';">
          </label>
        </div>

        <button type="submit" class="btn-submit">Upload to Gallery</button>
      </form>
    </div>
  </div>
</main>
<?php
require_once '../includes/footer.php';
?>
