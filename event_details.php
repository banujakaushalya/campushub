<?php
require_once 'includes/header.php';
require_once 'includes/db.php';

$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$event = null;
$is_already_registered = false;
$seats_left = 0;

if ($event_id > 0) {
    // Query event from database
    $res = Database::search("SELECT * FROM events WHERE event_id = ? LIMIT 1", "i", [$event_id]);
    if ($res && $res->num_rows > 0) {
        $event = $res->fetch_assoc();
    }
}

if ($event) {
    // Parse meta_data JSON
    $meta = !empty($event['meta_data']) ? json_decode($event['meta_data'], true) : [];
    $event['title'] = $event['title'] ?? 'Event Details';
    $event['description'] = $event['description'] ?? '';
    $event['category'] = $meta['category'] ?? ($event['category'] ?? 'Event');
    $event['club_name'] = $meta['club_name'] ?? ($event['club_name'] ?? 'CampusHub');
    $event['location'] = $meta['location'] ?? ($event['location'] ?? 'Campus Grounds');
    $event['tags'] = $meta['tags'] ?? [];
    $event['event_date'] = !empty($event['event_date']) ? $event['event_date'] : date('Y-m-d');
    
    $start_time = !empty($event['event_start_time']) ? $event['event_start_time'] : (!empty($event['event_time']) ? $event['event_time'] : '09:00:00');
    $end_time = !empty($event['event_end_time']) ? $event['event_end_time'] : '17:00:00';
    $event['event_start_time'] = $start_time;
    $event['event_end_time'] = $end_time;
    $event['event_time'] = $start_time;

    $event_max = isset($event['seats']) && is_numeric($event['seats']) ? (int)$event['seats'] : (isset($event['max_participants']) ? (int)$event['max_participants'] : 100);
    $event['seats'] = $event_max;
    $event['max_participants'] = $event_max;
    $event['img_path'] = !empty($event['img_path']) ? $event['img_path'] : 'assets/images/events/tech_coding_work_shop.jpg';

    // Check if currently logged in user is already registered
    if (!empty($_SESSION['user_id'])) {
        $uid = (int)$_SESSION['user_id'];
        $check_reg = Database::search("SELECT * FROM registrations WHERE events_event_id = ? AND users_user_id = ? LIMIT 1", "ii", [$event_id, $uid]);
        if ($check_reg && $check_reg->num_rows > 0) {
            $is_already_registered = true;
        }
    }

    // Get total registration count
    $count_res = Database::search("SELECT COUNT(*) as total FROM registrations WHERE events_event_id = ?", "i", [$event_id]);
    $registered_count = 0;
    if ($count_res && $row = $count_res->fetch_assoc()) {
        $registered_count = (int)$row['total'];
    }
    $seats_left = max(0, $event_max - $registered_count);
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
      background: linear-gradient(115deg, rgba(23, 27, 42, 0.85), rgba(43, 53, 79, 0.6)), url('assets/images/events/tech_coding_work_shop.jpg') center / cover;
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
    }

    .club-pill {
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(8px);
      padding: 5px 14px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
    }

    .details-title {
      font-size: clamp(1.6rem, 3.5vw, 2.4rem);
      margin: 0;
      line-height: 1.2;
    }

    .details-content {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 32px;
      padding: 36px;
    }

    .section-heading {
      font-size: 1.25rem;
      color: var(--ink);
      margin: 0 0 16px;
      border-bottom: 2px solid #f0f2f5;
      padding-bottom: 8px;
    }

    .details-desc {
      color: #334155;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      font-size: 1rem;
      line-height: 1.7;
      white-space: pre-line;
      margin-bottom: 28px;
    }

    .sidebar-card {
      background: #f8fafc;
      border: 1px solid var(--line);
      border-radius: 12px;
      padding: 24px;
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .info-item {
      display: flex;
      gap: 14px;
      align-items: flex-start;
    }

    .info-icon {
      font-size: 1.4rem;
      line-height: 1;
    }

    .info-text h4 {
      margin: 0 0 4px;
      font-size: 0.82rem;
      text-transform: uppercase;
      color: var(--muted);
      letter-spacing: 0.5px;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .info-text p {
      margin: 0;
      font-weight: bold;
      color: var(--ink);
      font-size: 0.95rem;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .btn-action-register {
      display: block;
      width: 100%;
      text-align: center;
      padding: 14px;
      background: var(--coral);
      color: #ffffff;
      text-decoration: none;
      border-radius: 8px;
      font-weight: bold;
      font-size: 1rem;
      transition: background 0.2s;
      box-sizing: border-box;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .btn-action-register:hover {
      background: #e55a3a;
    }

    .btn-registered {
      background: #10b981;
      cursor: default;
    }

    .btn-registered:hover {
      background: #10b981;
    }

    .btn-full {
      background: #94a3b8;
      cursor: not-allowed;
    }

    .btn-full:hover {
      background: #94a3b8;
    }

    @media (max-width: 768px) {
      .details-content {
        grid-template-columns: 1fr;
      }
    }
  </style>

  <div class="details-container">
    <a href="events.php" class="back-link">&larr; Back to Events List</a>

    <?php if (!$event): ?>
      <div class="details-card" style="padding: 48px 24px; text-align: center;">
        <h2 style="color: var(--ink); margin: 0 0 12px; font-size: 1.6rem;">Event Not Found</h2>
        <p style="color: var(--muted); font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 1rem; margin: 0 0 24px;">
          The requested event does not exist or may have been removed.
        </p>
        <a href="events.php" class="btn-action-register" style="display: inline-block; width: auto; padding: 12px 28px;">Browse All Events</a>
      </div>
    <?php else: ?>
      <?php
        $banner_img = !empty($event['img_path']) ? $event['img_path'] : 'assets/images/events/tech_coding_work_shop.jpg';
      ?>
      <div class="details-card">
        <div class="details-banner" style="background: linear-gradient(115deg, rgba(23, 27, 42, 0.85), rgba(43, 53, 79, 0.6)), url('<?php echo htmlspecialchars($banner_img); ?>') center / cover;">
          <div class="badge-row">
            <span class="category-pill"><?php echo htmlspecialchars($event['category']); ?></span>
            <span class="club-pill"><?php echo htmlspecialchars($event['club_name']); ?></span>
          </div>
          <h1 class="details-title"><?php echo htmlspecialchars($event['title']); ?></h1>
        </div>

        <div class="details-content">
          <!-- Main Description -->
          <div>
            <h2 class="section-heading">About This Event</h2>
            <div class="details-desc">
              <?php echo nl2br(htmlspecialchars($event['description'])); ?>
            </div>

            <h2 class="section-heading">Guidelines & Requirements</h2>
            <ul style="color: #475569; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; padding-left: 20px;">
              <li>Please bring your student / staff ID card for on-site event verification.</li>
              <li>Arrive at least 15 minutes prior to the scheduled start time.</li>
              <li>Certificates of participation will be issued to confirmed attendees.</li>
            </ul>
          </div>

          <!-- Meta Sidebar -->
          <div>
            <div class="sidebar-card">
              <div class="info-item">
                <span class="info-icon">📅</span>
                <div class="info-text">
                  <h4>Date</h4>
                  <p><?php echo date('l, F d, Y', strtotime($event['event_date'])); ?></p>
                </div>
              </div>

              <div class="info-item">
                <span class="info-icon">⏰</span>
                <div class="info-text">
                  <h4>Time</h4>
                  <p><?php echo date('g:i A', strtotime($event['event_start_time'])); ?> - <?php echo date('g:i A', strtotime($event['event_end_time'])); ?></p>
                </div>
              </div>

              <div class="info-item">
                <span class="info-icon">📍</span>
                <div class="info-text">
                  <h4>Location</h4>
                  <p><?php echo htmlspecialchars($event['location']); ?></p>
                </div>
              </div>

              <div class="info-item">
                <span class="info-icon">👥</span>
                <div class="info-text">
                  <h4>Seats Remaining</h4>
                  <p><?php echo htmlspecialchars($seats_left); ?> of <?php echo htmlspecialchars($event['max_participants']); ?> slots</p>
                </div>
              </div>

              <hr style="border: 0; border-top: 1px solid var(--line); margin: 4px 0;">

              <?php if ($is_already_registered): ?>
                <a href="#" class="btn-action-register btn-registered">✓ Registered</a>
              <?php elseif ($seats_left <= 0): ?>
                <a href="#" class="btn-action-register btn-full">Event Full</a>
              <?php elseif (!empty($_SESSION['user_id'])): ?>
                <a href="register_event.php?id=<?php echo htmlspecialchars($event['event_id']); ?>" class="btn-action-register">Register for Event</a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</main>
<?php
require_once 'includes/footer.php';
?>
