<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

// Check role permission: super_admin, admin, or moderator
$curr_role = $_SESSION['role'] ?? 'guest';
if ($curr_role !== 'super_admin' && $curr_role !== 'admin' && $curr_role !== 'moderator') {
    echo "<script>window.location.href = '../student/dashboard.php';</script>";
    exit;
}

$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$success_msg = "";
$error_msg = "";

// Fetch existing event
$event = null;
if ($event_id > 0) {
    $res = Database::search("SELECT * FROM events WHERE event_id = ? LIMIT 1", "i", [$event_id]);
    if ($res && $res->num_rows > 0) {
        $event = $res->fetch_assoc();
    }
}

if (!$event) {
    echo "<script>window.location.href = 'manage_events.php';</script>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $category = isset($_POST['category']) ? trim($_POST['category']) : 'Workshop';
    $club_name = isset($_POST['club_name']) ? trim($_POST['club_name']) : 'CampusHub General';
    $event_date = isset($_POST['event_date']) ? trim($_POST['event_date']) : '';
    $event_start_time = isset($_POST['event_start_time']) && !empty($_POST['event_start_time']) ? trim($_POST['event_start_time']) : (isset($_POST['event_time']) ? trim($_POST['event_time']) : '09:00:00');
    $event_end_time = isset($_POST['event_end_time']) && !empty($_POST['event_end_time']) ? trim($_POST['event_end_time']) : '17:00:00';
    $location = isset($_POST['location']) ? trim($_POST['location']) : '';
    $seats = isset($_POST['seats']) ? (int)$_POST['seats'] : (isset($_POST['max_participants']) ? (int)$_POST['max_participants'] : 100);
    $status = isset($_POST['status']) && in_array($_POST['status'], ['display', 'hide']) ? $_POST['status'] : 'display';
    $img_path = isset($_POST['img_path']) && !empty(trim($_POST['img_path'])) ? trim($_POST['img_path']) : ($event['img_path'] ?? 'assets/images/events/tech_coding_work_shop.jpg');
    $tags = isset($_POST['tags']) ? trim($_POST['tags']) : '';

    // Handle image upload if provided
    if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
        $img_name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($_FILES['event_image']['name']));
        $target_dir = __DIR__ . '/../assets/images/events/';
        if (!is_dir($target_dir)) {
            @mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . $img_name;
        if (move_uploaded_file($_FILES['event_image']['tmp_name'], $target_file)) {
            $img_path = 'assets/images/events/' . $img_name;
        }
    }

    $tags_arr = array_values(array_filter(array_map('trim', explode(',', $tags))));
    $meta_data = [
        'category' => $category,
        'location' => $location,
        'club_name' => $club_name,
        'tags' => $tags_arr
    ];
    $meta_json = json_encode($meta_data);

    if (empty($title) || empty($description) || empty($event_date) || empty($location)) {
        $error_msg = "Please fill in all mandatory fields (Title, Description, Date, Location).";
    } else {
        $updated = Database::iud(
            "UPDATE events SET title = ?, description = ?, event_date = ?, event_start_time = ?, event_end_time = ?, img_path = ?, meta_data = ?, status = ?, seats = ? WHERE event_id = ?",
            "ssssssssii",
            [$title, $description, $event_date, $event_start_time, $event_end_time, $img_path, $meta_json, $status, $seats, $event_id]
        );

        if ($updated) {
            $success_msg = "Event updated successfully!";
            // Refresh event data
            $res = Database::search("SELECT * FROM events WHERE event_id = ? LIMIT 1", "i", [$event_id]);
            if ($res && $res->num_rows > 0) {
                $event = $res->fetch_assoc();
            }
        } else {
            $error_msg = "Failed to update event.";
        }
    }
}

// Decode metadata for display in form
$meta_arr = !empty($event['meta_data']) ? json_decode($event['meta_data'], true) : [];
$event_category = $meta_arr['category'] ?? ($event['category'] ?? 'Workshop');
$event_club_name = $meta_arr['club_name'] ?? ($event['club_name'] ?? 'CampusHub General');
$event_location = $meta_arr['location'] ?? ($event['location'] ?? 'Main Auditorium');
$existing_tags_arr = $meta_arr['tags'] ?? [];
$existing_tags_str = is_array($existing_tags_arr) ? implode(', ', $existing_tags_arr) : (string)$existing_tags_arr;
$event_seats = isset($event['seats']) ? (int)$event['seats'] : (isset($event['max_participants']) ? (int)$event['max_participants'] : 100);
$event_status = $event['status'] ?? 'display';
$event_img_path = $event['img_path'] ?? 'assets/images/events/tech_coding_work_shop.jpg';
$event_start_time = !empty($event['event_start_time']) ? $event['event_start_time'] : (!empty($event['event_time']) ? $event['event_time'] : '09:00:00');
$event_end_time = !empty($event['event_end_time']) ? $event['event_end_time'] : '17:00:00';
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
      max-width: 760px;
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

    .form-row-2 {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
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

    @media (max-width: 600px) {
      .form-row-2 {
        grid-template-columns: 1fr;
      }
    }
  </style>

  <div class="form-container">
    <div class="form-card">
      <h1>Edit Event</h1>
      <p class="subtitle">Modify event schedule, location, capacity, or details.</p>

      <?php if (!empty($success_msg)): ?>
        <div class="alert alert-success">
          <?php echo htmlspecialchars($success_msg); ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($error_msg)): ?>
        <div class="alert alert-error">
          <?php echo htmlspecialchars($error_msg); ?>
        </div>
      <?php endif; ?>

      <form action="edit_event.php?id=<?php echo htmlspecialchars($event_id); ?>" method="POST" enctype="multipart/form-data">
        <div class="form-group">
          <label for="title">Event Title *</label>
          <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($event['title']); ?>" required>
        </div>

        <div class="form-row-2">
          <div class="form-group">
            <label for="category">Category</label>
            <select id="category" name="category" class="form-control">
              <option value="Workshop" <?php echo ($event_category === 'Workshop') ? 'selected' : ''; ?>>Workshop</option>
              <option value="Competition" <?php echo ($event_category === 'Competition') ? 'selected' : ''; ?>>Competition</option>
              <option value="Sports" <?php echo ($event_category === 'Sports') ? 'selected' : ''; ?>>Sports</option>
              <option value="Cultural" <?php echo ($event_category === 'Cultural') ? 'selected' : ''; ?>>Cultural</option>
              <option value="Career" <?php echo ($event_category === 'Career') ? 'selected' : ''; ?>>Career</option>
            </select>
          </div>

          <div class="form-group">
            <label for="club_name">Organizing Club / Society</label>
            <input type="text" id="club_name" name="club_name" class="form-control" value="<?php echo htmlspecialchars($event_club_name); ?>" required>
          </div>
        </div>

        <div class="form-row-2">
          <div class="form-group">
            <label for="event_date">Event Date *</label>
            <input type="date" id="event_date" name="event_date" class="form-control" value="<?php echo htmlspecialchars($event['event_date']); ?>" required>
          </div>

          <div class="form-group">
            <label for="event_start_time">Event Start Time *</label>
            <input type="time" id="event_start_time" name="event_start_time" class="form-control" value="<?php echo htmlspecialchars(substr($event_start_time, 0, 5)); ?>" required>
          </div>
        </div>

        <div class="form-row-2">
          <div class="form-group">
            <label for="event_end_time">Event End Time *</label>
            <input type="time" id="event_end_time" name="event_end_time" class="form-control" value="<?php echo htmlspecialchars(substr($event_end_time, 0, 5)); ?>" required>
          </div>

          <div class="form-group">
            <label for="location">Location / Venue *</label>
            <input type="text" id="location" name="location" class="form-control" value="<?php echo htmlspecialchars($event_location); ?>" required>
          </div>
        </div>

        <div class="form-row-2">
          <div class="form-group">
            <label for="seats">Max Participant Seats</label>
            <input type="number" id="seats" name="seats" class="form-control" value="<?php echo htmlspecialchars($event_seats); ?>" min="1">
          </div>

          <div class="form-group">
            <label for="status">Event Status</label>
            <select id="status" name="status" class="form-control">
              <option value="display" <?php echo ($event_status === 'display' || $event_status === 'upcoming') ? 'selected' : ''; ?>>Display</option>
              <option value="hide" <?php echo ($event_status === 'hide' || $event_status === 'cancelled' || $event_status === 'completed') ? 'selected' : ''; ?>>Hide</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label for="img_path">Banner Image Path / URL</label>
          <input type="text" id="img_path" name="img_path" class="form-control" value="<?php echo htmlspecialchars($event_img_path); ?>">
        </div>

        <div class="form-group">
          <label for="event_image">Or Upload New Banner Image</label>
          <input type="file" id="event_image" name="event_image" class="form-control" accept="image/*">
        </div>

        <div class="form-group">
          <label for="description">Event Description *</label>
          <textarea id="description" name="description" class="form-control" rows="4" required><?php echo htmlspecialchars($event['description']); ?></textarea>
        </div>

        <div class="form-group">
          <label for="tags">Hashtags / Search Keywords (Comma-separated)</label>
          <input type="text" id="tags" name="tags" class="form-control" placeholder="#coding, #hackathon" value="<?php echo htmlspecialchars($existing_tags_str); ?>">
        </div>

        <button type="submit" class="btn-submit">Save Event Changes</button>
        <a href="manage_events.php" style="display: block; text-align: center; margin-top: 14px; color: var(--ink); text-decoration: none; font-weight: bold; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Cancel & Return</a>
      </form>
    </div>
  </div>
</main>
<?php
require_once '../includes/footer.php';
?>
