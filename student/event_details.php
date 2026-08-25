<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;
$event = null;
$is_already_registered = false;

// Query event from database
$res = Database::search("SELECT * FROM events WHERE event_id = ? LIMIT 1", "i", [$event_id]);
if ($res && $res->num_rows > 0) {
    $event = $res->fetch_assoc();
} else {
    // Fallback sample event if not in DB
    $event = [
        'event_id' => $event_id,
        'title' => 'Full-Stack Web Dev Hackathon 2026',
        'description' => 'Join our 24-hour coding sprint building modern web applications with PHP, JavaScript, and APIs. Work in teams of up to 4 to design, build, and pitch functional software solutions before industry judges. Certificates, prize awards, and mentorship opportunities will be provided for top participating teams.',
        'category' => 'Competition',
        'club_name' => 'Computing Society',
        'event_date' => '2026-09-15',
        'event_time' => '09:00:00',
        'location' => 'Main Auditorium, Java Institute Tech Campus',
        'max_participants' => 80,
        'status' => 'upcoming'
    ];
}

// Check if currently logged in user is already registered
if ($is_logged_in && isset($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    $check_reg = Database::search("SELECT * FROM registrations WHERE events_event_id = ? AND users_user_id = ? LIMIT 1", "ii", [$event_id, $uid]);
    if ($check_reg && $check_reg->num_rows > 0) {
        $is_already_registered = true;
    }
}

// Get total registration count
$count_res = Database::search("SELECT COUNT(*) as total FROM registrations WHERE events_event_id = ? AND status = 'confirmed'", "i", [$event_id]);
$registered_count = 0;
if ($count_res && $row = $count_res->fetch_assoc()) {
    $registered_count = (int)$row['total'];
}
$seats_left = max(0, (int)$event['max_participants'] - $registered_count);
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

    .details-container {
      width: 100%;
      max-width: 1000px;
      margin: 36px auto 0;
    }

    .back-link {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      color: var(--ink);
      text-decoration: none;
      font-weight: bold;
      margin-bottom: 20px;
      font-size: 0.9rem;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .back-link:hover {
      color: var(--coral);
    }

    .details-card {
      background: #ffffff;
      border: 1px solid var(--line);
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(23, 27, 42, 0.08);
    }

    .details-banner {
      height: 260px;
      background: linear-gradient(115deg, rgba(23, 27, 42, 0.85), rgba(43, 53, 79, 0.6)), url('../assets/images/events/tech_coding_work_shop.jpg') center / cover;
      padding: 32px;
      box-sizing: border-box;
      display: flex;
      flex-direction: column;
      justify-content: flex-end;
      color: #ffffff;
    }

    .badge-row {
      display: flex;
      gap: 10px;
      margin-bottom: 12px;
    }

    .category-pill {
      background: var(--coral);
      padding: 5px 14px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .club-pill {
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      padding: 5px 14px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: bold;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .details-banner h1 {
      margin: 0;
      font-size: clamp(1.8rem, 3.5vw, 2.6rem);
      line-height: 1.15;
    }

    .details-body {
      padding: 36px;
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 36px;
    }

    .details-main h2 {
      margin: 0 0 14px;
      color: var(--ink);
      font-size: 1.4rem;
    }

    .details-main p {
      color: var(--muted);
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      font-size: 1rem;
      line-height: 1.65;
      margin: 0 0 24px;
    }

    .sidebar-card {
      background: #faf8ff;
      border: 1px solid var(--line);
      border-radius: 12px;
      padding: 24px;
      height: fit-content;
    }

    .sidebar-card h3 {
      margin: 0 0 16px;
      color: var(--ink);
      font-size: 1.15rem;
    }

    .meta-list {
      display: flex;
      flex-direction: column;
      gap: 16px;
      margin-bottom: 24px;
    }

    .meta-box {
      display: flex;
      align-items: flex-start;
      gap: 12px;
    }

    .meta-icon {
      font-size: 1.3rem;
      background: #ffffff;
      padding: 8px;
      border-radius: 8px;
      border: 1px solid var(--line);
    }

    .meta-text strong {
      display: block;
      color: var(--ink);
      font-size: 0.92rem;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .meta-text span {
      color: var(--muted);
      font-size: 0.85rem;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .btn-action {
      display: block;
      width: 100%;
      text-align: center;
      padding: 14px 20px;
      background: var(--coral);
      color: #ffffff;
      text-decoration: none;
      border-radius: 8px;
      font-weight: bold;
      font-size: 1rem;
      box-sizing: border-box;
      box-shadow: 0 4px 12px rgba(255, 107, 74, 0.35);
      transition: background 0.2s, transform 0.1s;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .btn-action:hover {
      background: #e55a3a;
      transform: translateY(-1px);
    }

    .btn-disabled {
      background: #e2e8f0;
      color: #718096;
      box-shadow: none;
      cursor: not-allowed;
    }

    .registered-banner {
      background: #e6f7ec;
      color: #1b7a3e;
      border: 1px solid #c2ebd0;
      padding: 12px 16px;
      border-radius: 8px;
      font-weight: bold;
      text-align: center;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      font-size: 0.9rem;
      margin-bottom: 12px;
    }

    @media (max-width: 860px) {
      .details-body {
        grid-template-columns: 1fr;
      }
    }
  </style>

  <div class="details-container">
    <a href="events.php" class="back-link">&larr; Back to Events List</a>

    <article class="details-card">
      <div class="details-banner">
        <div class="badge-row">
          <span class="category-pill"><?php echo htmlspecialchars($event['category'] ?? 'Event'); ?></span>
          <span class="club-pill"><?php echo htmlspecialchars($event['club_name'] ?? 'CampusHub'); ?></span>
        </div>
        <h1><?php echo htmlspecialchars($event['title']); ?></h1>
      </div>

      <div class="details-body">
        <div class="details-main">
          <h2>About This Event</h2>
          <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>

          <h2>Participation Guidelines</h2>
          <p>
            Please bring your Student ID card for venue check-in. Doors open 15 minutes before the scheduled session. All materials and certificates will be distributed to confirmed attendees.
          </p>
        </div>

        <div class="sidebar-card">
          <h3>Event Overview</h3>

          <div class="meta-list">
            <div class="meta-box">
              <div class="meta-icon">📅</div>
              <div class="meta-text">
                <strong>Date & Time</strong>
                <span><?php echo date('l, M d, Y', strtotime($event['event_date'])); ?> · <?php echo date('g:i A', strtotime($event['event_time'])); ?></span>
              </div>
            </div>

            <div class="meta-box">
              <div class="meta-icon">📍</div>
              <div class="meta-text">
                <strong>Location</strong>
                <span><?php echo htmlspecialchars($event['location']); ?></span>
              </div>
            </div>

            <div class="meta-box">
              <div class="meta-icon">👥</div>
              <div class="meta-text">
                <strong>Available Slots</strong>
                <span><?php echo $seats_left; ?> seats left (Capacity: <?php echo htmlspecialchars($event['max_participants']); ?>)</span>
              </div>
            </div>
          </div>

          <?php if ($is_already_registered): ?>
            <div class="registered-banner">
              ✓ You are registered for this event!
            </div>
            <a href="my_registrations.php" class="btn-action" style="background: var(--ink);">View My Passes</a>
          <?php elseif ($seats_left <= 0): ?>
            <button class="btn-action btn-disabled" disabled>Event Full</button>
          <?php else: ?>
            <a href="register_event.php?id=<?php echo htmlspecialchars($event['event_id']); ?>" class="btn-action">Register for Event</a>
          <?php endif; ?>
        </div>
      </div>
    </article>
  </div>
</main>
<?php
require_once '../includes/footer.php';
?>
