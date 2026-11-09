<?php
require_once 'includes/header.php';
require_once 'includes/db.php';

// Check login or session
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?msg=login_required_to_register");
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
    $meta = !empty($event['meta_data']) ? json_decode($event['meta_data'], true) : [];
    $event['category'] = $meta['category'] ?? ($event['category'] ?? 'Workshop');
    $event['location'] = $meta['location'] ?? ($event['location'] ?? 'Main Auditorium');
    $event['club_name'] = $meta['club_name'] ?? ($event['club_name'] ?? 'CampusHub');
    $event['event_start_time'] = !empty($event['event_start_time']) ? $event['event_start_time'] : (!empty($event['event_time']) ? $event['event_time'] : '10:00:00');
    $event['event_end_time'] = !empty($event['event_end_time']) ? $event['event_end_time'] : '18:00:00';
    $event['event_time'] = $event['event_start_time'];
    $event_seats = isset($event['seats']) && is_numeric($event['seats']) ? (int)$event['seats'] : (isset($event['max_participants']) ? (int)$event['max_participants'] : 100);
    $event['seats'] = $event_seats;
    $event['max_participants'] = $event_seats;
} else {
    $event = [
        'event_id' => $event_id,
        'title' => 'Campus Event',
        'event_date' => date('Y-m-d', strtotime('+7 days')),
        'event_time' => '10:00:00',
        'event_start_time' => '10:00:00',
        'event_end_time' => '18:00:00',
        'location' => 'Main Auditorium',
        'category' => 'Workshop',
        'club_name' => 'CampusHub',
        'seats' => 100,
        'max_participants' => 100
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
        $inserted = Database::iud("INSERT INTO registrations (events_event_id, users_user_id) VALUES (?, ?)", "ii", [$event_id, $user_id]);

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

    .info-tags {
      display: flex;
      gap: 10px;
      margin-top: 10px;
      font-size: 0.85rem;
      color: #4a5568;
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
      padding: 14px;
      background: var(--coral);
      color: #ffffff;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      font-size: 1rem;
      cursor: pointer;
      transition: background 0.2s;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .btn-submit:hover {
      background: #e55a3a;
    }

    .btn-secondary {
      display: block;
      width: 100%;
      text-align: center;
      padding: 12px;
      background: #f0f2f5;
      color: var(--ink);
      border: none;
      border-radius: 8px;
      font-weight: bold;
      font-size: 0.95rem;
      text-decoration: none;
      margin-top: 10px;
      box-sizing: border-box;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .btn-secondary:hover {
      background: #e4e7ed;
    }

    .alert-box {
      padding: 14px 18px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      font-size: 0.92rem;
    }

    .alert-success {
      background: #e6f9f0;
      color: #0d824d;
      border: 1px solid #a3e6cd;
    }

    .alert-error {
      background: #fee2e2;
      color: #991b1b;
      border: 1px solid #fca5a5;
    }
  </style>

  <div class="form-container">
    <div class="reg-card">
      <h1>Event Registration</h1>
      <p class="subtitle">Secure your participation ticket for this campus session</p>

      <?php if (!empty($success_msg)): ?>
        <div class="alert-box alert-success">
          <strong>Success!</strong> <?php echo $success_msg; ?>
          <div style="margin-top: 10px;">
            <a href="student/my_registrations.php" style="color: #0d824d; font-weight: bold; text-decoration: underline;">View My Registrations &rarr;</a>
          </div>
        </div>
      <?php endif; ?>

      <?php if (!empty($error_msg)): ?>
        <div class="alert-box alert-error">
          <strong>Notice:</strong> <?php echo $error_msg; ?>
        </div>
      <?php endif; ?>

      <!-- Target Event Summary -->
      <div class="event-summary-box">
        <h3><?php echo htmlspecialchars($event['title']); ?></h3>
        <p>Category: <strong><?php echo htmlspecialchars($event['category']); ?></strong></p>
        <div class="info-tags">
          <span>📅 <?php echo date('M d, Y', strtotime($event['event_date'])); ?></span>
          <span>⏰ <?php echo date('g:i A', strtotime($event['event_time'])); ?></span>
          <span>📍 <?php echo htmlspecialchars($event['location']); ?></span>
        </div>
      </div>

      <?php if (empty($success_msg)): ?>
        <form action="register_event.php?id=<?php echo htmlspecialchars($event_id); ?>" method="POST">
          <div class="form-group">
            <label>Registered Name</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Student'); ?>" disabled style="background: #f8fafc;">
          </div>

          <div class="form-group">
            <label>Notes / Dietary or Accessibility Requirements (Optional)</label>
            <textarea name="notes" rows="3" class="form-control" placeholder="Any specific requirements or team member names..."></textarea>
          </div>

          <button type="submit" class="btn-submit">Confirm Registration</button>
          <a href="event_details.php?id=<?php echo htmlspecialchars($event_id); ?>" class="btn-secondary">Cancel & Go Back</a>
        </form>
      <?php endif; ?>
    </div>
  </div>
</main>
<?php
require_once 'includes/footer.php';
?>
