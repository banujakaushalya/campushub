<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

// Check role permission: super_admin, admin, or moderator
$curr_role = $_SESSION['role'] ?? 'guest';
$curr_user_id = $_SESSION['user_id'] ?? 1;
if ($curr_role !== 'super_admin' && $curr_role !== 'admin' && $curr_role !== 'moderator') {
    echo "<script>window.location.href = '../student/dashboard.php';</script>";
    exit;
}

$success_msg = "";
$error_msg = "";

// Fetch events
$events = [];
$e_res = Database::search("SELECT event_id, title FROM events ORDER BY event_date DESC");
if ($e_res && $e_res->num_rows > 0) {
    while ($row = $e_res->fetch_assoc()) {
        $events[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $raw_type = isset($_POST['media_type']) ? trim($_POST['media_type']) : 'image';
    $media_type = ($raw_type === 'video') ? 'video' : 'image';
    $event_id = isset($_POST['event_id']) ? (int)$_POST['event_id'] : 0;
    
    // Ensure valid event_id for foreign key constraint
    if ($event_id <= 0 && !empty($events)) {
        $event_id = (int)$events[0]['event_id'];
    }
    
    $file_path = "hero1.jpg";
    if (isset($_FILES['media_file']) && $_FILES['media_file']['error'] === UPLOAD_ERR_OK) {
        $file_name = time() . '_' . basename($_FILES['media_file']['name']);
        $target_dir = __DIR__ . "/../assets/images/";
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES['media_file']['tmp_name'], $target_file)) {
            $file_path = $file_name;
        }
    }

    $meta_json = json_encode(['description' => $description]);

    if (empty($title)) {
        $error_msg = "Media title is required.";
    } elseif ($event_id <= 0) {
        $error_msg = "An active event is required to link media. Please create an event first.";
    } else {
        $inserted = Database::iud(
            "INSERT INTO media (title, media_type, meta_data, events_event_id, users_user_id, file_path) VALUES (?, ?, ?, ?, ?, ?)",
            "sssiis",
            [$title, $media_type, $meta_json, $event_id, $curr_user_id, $file_path]
        );

        if ($inserted) {
            $success_msg = "Media resource published successfully!";
        } else {
            $error_msg = "Could not save media record.";
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

    .form-container {
      width: 100%;
      max-width: 680px;
      margin: 36px auto 0;
    }

    .form-card {
      background: #ffffff;
      border: 1px solid var(--line);
      border-radius: 16px;
      padding: 36px;
      box-shadow: 0 10px 30px rgba(23, 27, 42, 0.08);
    }

    .form-card h1 {
      margin: 0 0 8px;
      color: var(--ink);
      font-size: 1.6rem;
    }

    .form-card p.subtitle {
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

  <div class="form-container">
    <div class="form-card">
      <h1>Upload Campus Media Asset</h1>
      <p class="subtitle">Publish event banners, promotional flyers, or gallery albums.</p>

      <?php if (!empty($success_msg)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
      <?php endif; ?>

      <?php if (!empty($error_msg)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error_msg); ?></div>
      <?php endif; ?>

      <form action="upload_media.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
          <label for="title">Title *</label>
          <input type="text" id="title" name="title" class="form-control" placeholder="e.g. Hackathon Official Poster" required>
        </div>

        <div class="form-group">
          <label for="media_type">Media Type</label>
          <select id="media_type" name="media_type" class="form-control">
            <option value="photo">Photo / Image</option>
            <option value="video">Video Recording</option>
            <option value="audio">Audio Podcast</option>
          </select>
        </div>

        <div class="form-group">
          <label for="event_id">Link to Event</label>
          <select id="event_id" name="event_id" class="form-control">
            <option value="0">General Campus Asset</option>
            <?php foreach ($events as $ev): ?>
              <option value="<?php echo htmlspecialchars($ev['event_id']); ?>">
                <?php echo htmlspecialchars($ev['title']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label for="description">Description</label>
          <textarea id="description" name="description" class="form-control" rows="3" placeholder="Brief note about this asset..."></textarea>
        </div>

        <div class="form-group">
          <label for="media_file">Select File</label>
          <input type="file" id="media_file" name="media_file" class="form-control">
        </div>

        <button type="submit" class="btn-submit">Upload Asset</button>
      </form>
    </div>
  </div>
</main>
<?php
require_once '../includes/footer.php';
?>
