<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

// Check login or session
if (!$is_logged_in) {
    header("Location: ../login.php?msg=login_required_to_register");
    exit;
}

$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

$success_msg = "";
$error_msg = "";

// Fetch event details
$event = null;
$res = Database::search("SELECT * FROM events WHERE event_id = ? LIMIT 1", "i", [$event_id]);
if ($res && $res->num_rows > 0) {
    $event = $res->fetch_assoc();
} else {
    $event = [
        'event_id' => $event_id,
        'title' => 'Campus Event',
        'event_date' => date('Y-m-d', strtotime('+7 days')),
        'event_time' => '10:00:00',
        'location' => 'Main Auditorium',
        'category' => 'Workshop'
    ];
}

// Handle registration submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';

    // Check if already registered
    $check = Database::search("SELECT * FROM registrations WHERE events_event_id = ? AND users_user_id = ?", "ii", [$event_id, $user_id]);

    if ($check && $check->num_rows > 0) {
        $error_msg = "You are already registered for this event!";
    } else {
        $inserted = Database::iud("INSERT INTO registrations (events_event_id, users_user_id, status, notes) VALUES (?, ?, 'confirmed', ?)", "iis", [$event_id, $user_id, $notes]);

        if ($inserted) {
            $success_msg = "Registration confirmed! Your seat is reserved.";
        } else {
            $error_msg = "Registration could not be completed. Please try again.";
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
      margin: 40px auto 0;
    }

    .reg-card {
      background: #ffffff;
      border: 1px solid var(--line);
      border-radius: 16px;
      padding: 36px;
      box-shadow: 0 10px 30px rgba(23, 27, 42, 0.08);
    }

    .reg-card h1 {
      margin: 0 0 8px;
      color: var(--ink);
      font-size: 1.6rem;
    }

    .reg-card p.subtitle {
      margin: 0 0 24px;
      color: var(--muted);
      font-family: 'Cabin Sketch', cursive;
      font-size: 1.15rem;
    }

    .event-summary-box {
      background: #faf8ff;
      border: 1.5px solid var(--line);
      border-radius: 10px;
      padding: 18px;
      margin-bottom: 24px;
    }

    .event-summary-box h3 {
      margin: 0 0 6px;
      color: var(--ink);
      font-size: 1.1rem;
    }

    .event-summary-box p {
      margin: 0;
      color: var(--muted);
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      font-size: 0.9rem;
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

    .btn-secondary {
      display: inline-block;
      text-align: center;
      margin-top: 12px;
      width: 100%;
      background: #f0f2f5;
      color: var(--ink);
      padding: 12px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: bold;
      font-size: 0.95rem;
      box-sizing: border-box;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }
  </style>

  <div class="form-container">
    <div class="reg-card">
      <h1>Event Registration</h1>
      <p class="subtitle">Reserve your entry pass with your CampusHub account.</p>

      <?php if (!empty($success_msg)): ?>
        <div class="alert alert-success">
          <?php echo htmlspecialchars($success_msg); ?>
        </div>
        <a href="my_registrations.php" class="btn-submit" style="display: block; text-align: center; text-decoration: none;">View My Registrations &rarr;</a>
      <?php else: ?>

        <?php if (!empty($error_msg)): ?>
          <div class="alert alert-error">
            <?php echo htmlspecialchars($error_msg); ?>
          </div>
        <?php endif; ?>

        <div class="event-summary-box">
          <h3><?php echo htmlspecialchars($event['title']); ?></h3>
          <p>
            📅 <?php echo date('M d, Y', strtotime($event['event_date'])); ?> at <?php echo date('g:i A', strtotime($event['event_time'])); ?><br>
            📍 <?php echo htmlspecialchars($event['location']); ?>
          </p>
        </div>

        <form action="register_event.php?id=<?php echo htmlspecialchars($event_id); ?>" method="POST">
          <div class="form-group">
            <label>Attendee Name</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user_name); ?>" disabled>
          </div>

          <div class="form-group">
            <label for="notes">Special Requirements / Notes (Optional)</label>
            <textarea id="notes" name="notes" class="form-control" rows="3" placeholder="e.g. Dietary requirements, team members, or specific questions for speaker..."></textarea>
          </div>

          <button type="submit" class="btn-submit">Confirm Registration</button>
          <a href="event_details.php?id=<?php echo htmlspecialchars($event_id); ?>" class="btn-secondary">Cancel & Go Back</a>
        </form>

      <?php endif; ?>
    </div>
  </div>
</main>
<?php
require_once '../includes/footer.php';
?>
