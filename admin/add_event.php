<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

$success_msg = "";
$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $category = isset($_POST['category']) ? trim($_POST['category']) : 'Workshop';
    $club_name = isset($_POST['club_name']) ? trim($_POST['club_name']) : 'CampusHub General';
    $event_date = isset($_POST['event_date']) ? trim($_POST['event_date']) : '';
    $event_time = isset($_POST['event_time']) ? trim($_POST['event_time']) : '09:00:00';
    $location = isset($_POST['location']) ? trim($_POST['location']) : '';
    $max_participants = isset($_POST['max_participants']) ? (int)$_POST['max_participants'] : 50;

    if (empty($title) || empty($description) || empty($event_date) || empty($location)) {
        $error_msg = "Please fill in all mandatory fields (Title, Description, Date, Location).";
    } else {
        $inserted = Database::iud(
            "INSERT INTO events (title, description, category, club_name, event_date, event_time, location, max_participants, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'upcoming')",
            "sssssssi",
            [$title, $description, $category, $club_name, $event_date, $event_time, $location, $max_participants]
        );

        if ($inserted) {
            $success_msg = "New event successfully created!";
        } else {
            $error_msg = "Failed to create event. Please verify inputs.";
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
      <h1>Create New Campus Event</h1>
      <p class="subtitle">Publish workshops, hackathons, and tournaments to the student portal.</p>

      <?php if (!empty($success_msg)): ?>
        <div class="alert alert-success">
          <?php echo htmlspecialchars($success_msg); ?>
        </div>
        <a href="manage_events.php" style="display: inline-block; margin-bottom: 20px; color: var(--coral); font-weight: bold; text-decoration: none;">&larr; Return to Event List</a>
      <?php endif; ?>

      <?php if (!empty($error_msg)): ?>
        <div class="alert alert-error">
          <?php echo htmlspecialchars($error_msg); ?>
        </div>
      <?php endif; ?>

      <form action="add_event.php" method="POST">
        <div class="form-group">
          <label for="title">Event Title *</label>
          <input type="text" id="title" name="title" class="form-control" placeholder="e.g. AI & Robotics Innovation Workshop" required>
        </div>

        <div class="form-row-2">
          <div class="form-group">
            <label for="category">Category</label>
            <select id="category" name="category" class="form-control">
              <option value="Workshop">Workshop</option>
              <option value="Competition">Competition</option>
              <option value="Sports">Sports</option>
              <option value="Cultural">Cultural</option>
              <option value="Career">Career</option>
            </select>
          </div>

          <div class="form-group">
            <label for="club_name">Organizing Club / Society</label>
            <input type="text" id="club_name" name="club_name" class="form-control" placeholder="e.g. Computing Society" required>
          </div>
        </div>

        <div class="form-row-2">
          <div class="form-group">
            <label for="event_date">Event Date *</label>
            <input type="date" id="event_date" name="event_date" class="form-control" required>
          </div>

          <div class="form-group">
            <label for="event_time">Event Time *</label>
            <input type="time" id="event_time" name="event_time" class="form-control" value="09:00" required>
          </div>
        </div>

        <div class="form-row-2">
          <div class="form-group">
            <label for="location">Location / Venue *</label>
            <input type="text" id="location" name="location" class="form-control" placeholder="e.g. Main Auditorium" required>
          </div>

          <div class="form-group">
            <label for="max_participants">Max Participant Seats</label>
            <input type="number" id="max_participants" name="max_participants" class="form-control" value="50" min="1">
          </div>
        </div>

        <div class="form-group">
          <label for="description">Event Description *</label>
          <textarea id="description" name="description" class="form-control" rows="4" placeholder="Full details, agenda, and guidelines for students..." required></textarea>
        </div>

        <button type="submit" class="btn-submit">Publish Event</button>
      </form>
    </div>
  </div>
</main>
<?php
require_once '../includes/footer.php';
?>
