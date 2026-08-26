<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

// Fetch user profile info
$user_info = null;
if ($user_id > 0) {
    $u_res = Database::search("SELECT * FROM users WHERE user_id = ? LIMIT 1", "i", [$user_id]);
    if ($u_res && $u_res->num_rows > 0) {
        $user_info = $u_res->fetch_assoc();
    }
}

// Count active registrations
$reg_count = 0;
if ($user_id > 0) {
    $c_res = Database::search("SELECT COUNT(*) as total FROM registrations WHERE users_user_id = ? AND status = 'confirmed'", "i", [$user_id]);
    if ($c_res && $crow = $c_res->fetch_assoc()) {
        $reg_count = (int)$crow['total'];
    }
}

// Count upcoming campus events
$upcoming_count = 0;
$e_res = Database::search("SELECT COUNT(*) as total FROM events WHERE status = 'upcoming'");
if ($e_res && $erow = $e_res->fetch_assoc()) {
    $upcoming_count = (int)$erow['total'];
}

// Count announcements
$ann_count = 0;
$a_res = Database::search("SELECT COUNT(*) as total FROM announcements");
if ($a_res && $arow = $a_res->fetch_assoc()) {
    $ann_count = (int)$arow['total'];
}

// Fetch 2 next registered events
$my_events = [];
if ($user_id > 0) {
    $m_res = Database::search("SELECT e.title, e.event_date, e.event_time, e.location, e.category 
                              FROM registrations r 
                              JOIN events e ON r.events_event_id = e.event_id 
                              WHERE r.users_user_id = ? AND r.status = 'confirmed' 
                              ORDER BY e.event_date ASC LIMIT 2", "i", [$user_id]);
    if ($m_res && $m_res->num_rows > 0) {
        while ($mrow = $m_res->fetch_assoc()) {
            $my_events[] = $mrow;
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

    .dash-hero,
    .content-section {
      width: 100%;
      max-width: 1200px;
      margin-right: auto;
      margin-left: auto;
    }

    .dash-hero {
      position: relative;
      display: flex;
      flex-direction: column;
      justify-content: center;
      min-height: 200px;
      margin-top: 36px;
      padding: 36px clamp(24px, 5vw, 64px);
      box-sizing: border-box;
      border-radius: 16px;
      background: linear-gradient(115deg, rgba(23, 27, 42, 0.94), rgba(43, 53, 79, 0.8)), url('../assets/images/hero1.jpg') center / cover;
      color: #ffffff;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .dash-hero h1 {
      margin: 0 0 8px;
      font-size: clamp(1.8rem, 3.5vw, 2.6rem);
      line-height: 1.1;
      text-shadow: #FF6B4A 1px 1px 2px;
    }

    .dash-hero p {
      max-width: 600px;
      margin: 0;
      color: rgba(255, 255, 255, 0.85);
      font-family: 'Cabin Sketch', cursive;
      font-size: 1.25rem;
    }

    .content-section {
      margin-top: 32px;
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
      margin-bottom: 32px;
    }

    .stat-card {
      background: #ffffff;
      border: 1px solid var(--line);
      border-radius: 12px;
      padding: 24px;
      display: flex;
      align-items: center;
      gap: 18px;
      box-shadow: 0 8px 20px rgba(23, 27, 42, 0.04);
    }

    .stat-icon {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 52px;
      height: 52px;
      border-radius: 12px;
      background: rgba(255, 107, 74, 0.12);
      color: var(--coral);
      font-size: 1.5rem;
    }

    .stat-details h3 {
      margin: 0 0 4px;
      color: var(--ink);
      font-size: 1.8rem;
    }

    .stat-details p {
      margin: 0;
      color: var(--muted);
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      font-size: 0.88rem;
      font-weight: 600;
    }

    .actions-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 18px;
      margin-bottom: 36px;
    }

    .action-btn-card {
      background: #ffffff;
      border: 1px solid var(--line);
      border-radius: 12px;
      padding: 20px;
      text-decoration: none;
      color: var(--ink);
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      box-shadow: 0 6px 18px rgba(23, 27, 42, 0.04);
      transition: transform 0.2s, border-color 0.2s, box-shadow 0.2s;
    }

    .action-btn-card:hover {
      transform: translateY(-3px);
      border-color: var(--coral);
      box-shadow: 0 10px 24px rgba(255, 107, 74, 0.15);
    }

    .action-emoji {
      font-size: 2rem;
      margin-bottom: 10px;
    }

    .action-label {
      font-size: 0.95rem;
      font-weight: bold;
      margin-bottom: 4px;
    }

    .action-desc {
      font-size: 0.8rem;
      color: var(--muted);
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .section-title {
      margin: 0 0 16px;
      color: var(--ink);
      font-size: 1.35rem;
    }

    .registered-preview {
      background: #ffffff;
      border: 1px solid var(--line);
      border-radius: 12px;
      padding: 24px;
      box-shadow: 0 8px 20px rgba(23, 27, 42, 0.04);
    }

    .preview-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 14px 0;
      border-bottom: 1px solid #f0f2f5;
    }

    .preview-item:last-child {
      border-bottom: none;
    }

    .preview-left h4 {
      margin: 0 0 4px;
      color: var(--ink);
      font-size: 1rem;
    }

    .preview-left p {
      margin: 0;
      color: var(--muted);
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      font-size: 0.85rem;
    }

    @media (max-width: 860px) {
      .stats-grid {
        grid-template-columns: 1fr;
      }
      .actions-grid {
        grid-template-columns: 1fr 1fr;
      }
    }
  </style>

  <section class="dash-hero">
    <h1>Welcome, <?php echo htmlspecialchars($user_info['first_name'] ?? 'Student'); ?>!</h1>
    <p>Student Portal · ID: <?php echo htmlspecialchars($user_info['student_id'] ?? 'JI-2024-042'); ?> · <?php echo htmlspecialchars($user_info['institution'] ?? 'Java Institute'); ?></p>
  </section>

  <section class="content-section">
    <!-- Quick Statistics -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">🎟️</div>
        <div class="stat-details">
          <h3><?php echo $reg_count; ?></h3>
          <p>My Active Passes</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">📅</div>
        <div class="stat-details">
          <h3><?php echo $upcoming_count; ?></h3>
          <p>Upcoming Campus Events</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">📢</div>
        <div class="stat-details">
          <h3><?php echo $ann_count; ?></h3>
          <p>Campus Notices</p>
        </div>
      </div>
    </div>

    <!-- Portal Shortcuts -->
    <h2 class="section-title">Quick Actions</h2>
    <div class="actions-grid">
      <a href="events.php" class="action-btn-card">
        <div class="action-emoji">🔍</div>
        <div class="action-label">Browse Events</div>
        <div class="action-desc">Explore workshops & hackathons</div>
      </a>

      <a href="my_registrations.php" class="action-btn-card">
        <div class="action-emoji">🎟️</div>
        <div class="action-label">My Registrations</div>
        <div class="action-desc">View your confirmed passes</div>
      </a>

      <a href="upload_photo.php" class="action-btn-card">
        <div class="action-emoji">📸</div>
        <div class="action-label">Upload Photos</div>
        <div class="action-desc">Share campus activity moments</div>
      </a>

      <a href="profile.php" class="action-btn-card">
        <div class="action-emoji">👤</div>
        <div class="action-label">My Profile</div>
        <div class="action-desc">Update contact details</div>
      </a>

      <a href="apply_role.php" class="action-btn-card">
        <div class="action-emoji">🌟</div>
        <div class="action-label">Apply for Role</div>
        <div class="action-desc">Request Moderator / Admin role</div>
      </a>
    </div>

    <!-- Next Upcoming Passes -->
    <h2 class="section-title">My Upcoming Event Schedule</h2>
    <div class="registered-preview">
      <?php if (empty($my_events)): ?>
        <p style="color: var(--muted); font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; margin: 0;">
          You do not have any registered events yet. <a href="events.php" style="color: var(--coral); font-weight: bold; text-decoration: none;">Browse Events &raquo;</a>
        </p>
      <?php else: ?>
        <?php foreach ($my_events as $me): ?>
          <div class="preview-item">
            <div class="preview-left">
              <h4><?php echo htmlspecialchars($me['title']); ?></h4>
              <p>📅 <?php echo date('M d, Y', strtotime($me['event_date'])); ?> (<?php echo date('g:i A', strtotime($me['event_time'])); ?>) · 📍 <?php echo htmlspecialchars($me['location']); ?></p>
            </div>
            <a href="my_registrations.php" style="color: var(--coral); font-weight: bold; font-size: 0.85rem; text-decoration: none; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">View Pass &rarr;</a>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </section>
</main>
<?php
require_once '../includes/footer.php';
?>
