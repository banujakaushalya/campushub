<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

// Check role permission
$curr_role = $_SESSION['role'] ?? 'guest';
if ($curr_role !== 'super_admin' && $curr_role !== 'admin' && $curr_role !== 'moderator') {
    echo "<script>window.location.href = '../student/dashboard.php';</script>";
    exit;
}

// Fetch key metrics
$student_count = 0;
$s_res = Database::search("SELECT COUNT(*) as total FROM users WHERE role = 'student'");
if ($s_res && $srow = $s_res->fetch_assoc()) {
    $student_count = (int)$srow['total'];
}

$event_count = 0;
$e_res = Database::search("SELECT COUNT(*) as total FROM events");
if ($e_res && $erow = $e_res->fetch_assoc()) {
    $event_count = (int)$erow['total'];
}

$reg_count = 0;
$r_res = Database::search("SELECT COUNT(*) as total FROM registrations");
if ($r_res && $rrow = $r_res->fetch_assoc()) {
    $reg_count = (int)$rrow['total'];
}

$ann_count = 0;
$a_res = Database::search("SELECT COUNT(*) as total FROM announcements");
if ($a_res && $arow = $a_res->fetch_assoc()) {
    $ann_count = (int)$arow['total'];
}

$role_app_count = 0;
$ra_res = Database::search("SELECT COUNT(*) as total FROM role_applications WHERE status = 'pending'");
if ($ra_res && $rarow = $ra_res->fetch_assoc()) {
    $role_app_count = (int)$rarow['total'];
}

// Fetch recent registrations
$recent_regs = [];
$reg_sql = "SELECT r.registration_id, r.status, r.registered_at, 
                   u.first_name, u.last_name, u.email, u.student_id,
                   e.title as event_title, e.event_date
            FROM registrations r
            JOIN users u ON r.users_user_id = u.user_id
            JOIN events e ON r.events_event_id = e.event_id
            ORDER BY r.registered_at DESC LIMIT 5";

$rr_res = Database::search($reg_sql);
if ($rr_res && $rr_res->num_rows > 0) {
    while ($row = $rr_res->fetch_assoc()) {
        $recent_regs[] = $row;
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

    .admin-hero,
    .content-section {
      width: 100%;
      max-width: 1200px;
      margin-right: auto;
      margin-left: auto;
    }

    .admin-hero {
      position: relative;
      display: flex;
      flex-direction: column;
      justify-content: center;
      min-height: 200px;
      margin-top: 36px;
      padding: 36px clamp(24px, 5vw, 64px);
      box-sizing: border-box;
      border-radius: 16px;
      background: linear-gradient(115deg, rgba(23, 27, 42, 0.94), rgba(43, 53, 79, 0.8)), url('../assets/images/hero2.jpg') center / cover;
      color: #ffffff;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .admin-hero h1 {
      margin: 0 0 8px;
      font-size: clamp(1.8rem, 3.5vw, 2.6rem);
      line-height: 1.1;
      text-shadow: #FF6B4A 1px 1px 2px;
    }

    .admin-hero p {
      max-width: 600px;
      margin: 0;
      color: rgba(255, 255, 255, 0.85);
      font-family: 'Cabin Sketch', cursive;
      font-size: 1.25rem;
    }

    .content-section {
      margin-top: 32px;
    }

    .metrics-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 20px;
      margin-bottom: 32px;
    }

    .metric-card {
      background: #ffffff;
      border: 1px solid var(--line);
      border-radius: 12px;
      padding: 22px;
      box-shadow: 0 6px 18px rgba(23, 27, 42, 0.04);
      display: flex;
      align-items: center;
      gap: 16px;
    }

    .metric-icon {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 48px;
      height: 48px;
      border-radius: 10px;
      background: rgba(255, 107, 74, 0.12);
      color: var(--coral);
      font-size: 1.4rem;
    }

    .metric-data h3 {
      margin: 0 0 2px;
      color: var(--ink);
      font-size: 1.6rem;
    }

    .metric-data p {
      margin: 0;
      color: var(--muted);
      font-size: 0.85rem;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      font-weight: 600;
    }

    .admin-actions {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 16px;
      margin-bottom: 36px;
    }

    .admin-btn {
      background: #ffffff;
      border: 1px solid var(--line);
      border-radius: 10px;
      padding: 16px;
      text-decoration: none;
      color: var(--ink);
      font-weight: bold;
      font-size: 0.92rem;
      display: flex;
      align-items: center;
      gap: 12px;
      box-shadow: 0 4px 12px rgba(23, 27, 42, 0.03);
      transition: all 0.2s;
    }

    .admin-btn:hover {
      border-color: var(--coral);
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(255, 107, 74, 0.15);
    }

    .table-container {
      background: #ffffff;
      border: 1px solid var(--line);
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 8px 24px rgba(23, 27, 42, 0.05);
    }

    .table-header-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px 24px;
      border-bottom: 1px solid var(--line);
    }

    .table-header-bar h2 {
      margin: 0;
      font-size: 1.25rem;
      color: var(--ink);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      font-size: 0.9rem;
    }

    th {
      background: #faf8ff;
      text-align: left;
      padding: 12px 20px;
      font-weight: 700;
      color: var(--ink);
      border-bottom: 1px solid var(--line);
    }

    td {
      padding: 14px 20px;
      border-bottom: 1px solid #f2f4f7;
      color: #333d52;
    }

    tr:last-child td {
      border-bottom: none;
    }

    .status-tag {
      display: inline-block;
      padding: 4px 10px;
      border-radius: 12px;
      font-size: 0.75rem;
      font-weight: bold;
      text-transform: uppercase;
    }

    .tag-confirmed {
      background: #e6f7ec;
      color: #1b7a3e;
    }

    .tag-cancelled {
      background: #ffeaea;
      color: #c92a2a;
    }

    @media (max-width: 860px) {
      .metrics-grid, .admin-actions {
        grid-template-columns: 1fr 1fr;
      }
    }
  </style>

  <section class="admin-hero">
    <h1>Admin Command Center</h1>
    <p>Manage campus events, student registrations, official circulars & system settings.</p>
  </section>

  <section class="content-section">
    <!-- Metric Cards -->
    <div class="metrics-grid">
      <div class="metric-card">
        <div class="metric-icon">👥</div>
        <div class="metric-data">
          <h3><?php echo $student_count; ?></h3>
          <p>Total Students</p>
        </div>
      </div>

      <div class="metric-card">
        <div class="metric-icon">📅</div>
        <div class="metric-data">
          <h3><?php echo $event_count; ?></h3>
          <p>Total Events</p>
        </div>
      </div>

      <div class="metric-card">
        <div class="metric-icon">🎟️</div>
        <div class="metric-data">
          <h3><?php echo $reg_count; ?></h3>
          <p>Registrations</p>
        </div>
      </div>

      <div class="metric-card">
        <div class="metric-icon">📢</div>
        <div class="metric-data">
          <h3><?php echo $ann_count; ?></h3>
          <p>Announcements</p>
        </div>
      </div>
    </div>

    <!-- Admin Shortcuts based on Role -->
    <div class="admin-actions">
      <?php if ($curr_role === 'super_admin' || $curr_role === 'admin' || $curr_role === 'moderator'): ?>
        <a href="manage_events.php" class="admin-btn">
          <span>📅</span> Manage Events
        </a>
        <a href="add_event.php" class="admin-btn">
          <span>➕</span> Add New Event
        </a>
        <a href="upload_media.php" class="admin-btn">
          <span>📸</span> Upload Media
        </a>
      <?php endif; ?>

      <?php if ($curr_role === 'super_admin' || $curr_role === 'admin'): ?>
        <a href="manage_students.php" class="admin-btn">
          <span>🎓</span> Student Directory
        </a>
        <a href="manage_registrations.php" class="admin-btn">
          <span>🎟️</span> Registrations
        </a>
        <a href="manage_announcements.php" class="admin-btn">
          <span>📢</span> Announcements
        </a>
        <a href="manage_roles.php" class="admin-btn">
          <span>👑</span> Roles & Approvals <?php if ($role_app_count > 0): ?><strong style="background:var(--coral);color:#fff;border-radius:10px;padding:2px 6px;font-size:0.75rem;margin-left:4px;"><?php echo $role_app_count; ?></strong><?php endif; ?>
        </a>
        <a href="organization_info.php" class="admin-btn">
          <span>🏛️</span> Org Info
        </a>
      <?php endif; ?>

      <?php if ($curr_role === 'super_admin'): ?>
        <a href="../master_control/index.php" class="admin-btn" style="border-color: #f59e0b; background: #fffbeb;">
          <span>⚙️</span> Master Control
        </a>
      <?php endif; ?>
    </div>

    <!-- Recent Registrations Table (Admins only) -->
    <?php if ($curr_role === 'super_admin' || $curr_role === 'admin'): ?>
      <div class="table-container">
        <div class="table-header-bar">
          <h2>Latest Student Event Registrations</h2>
          <a href="manage_registrations.php" style="color: var(--coral); font-weight: bold; text-decoration: none; font-size: 0.9rem; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">View All &raquo;</a>
        </div>

        <table>
          <thead>
            <tr>
              <th>Student</th>
              <th>ID</th>
              <th>Event</th>
              <th>Registration Date</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($recent_regs)): ?>
              <tr>
                <td colspan="5" style="text-align: center; padding: 28px; color: var(--muted);">No event registrations found in the database.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($recent_regs as $reg): ?>
                <tr>
                  <td><strong><?php echo htmlspecialchars($reg['first_name'] . ' ' . $reg['last_name']); ?></strong><br><small style="color: var(--muted);"><?php echo htmlspecialchars($reg['email']); ?></small></td>
                  <td><?php echo htmlspecialchars($reg['student_id'] ?? 'N/A'); ?></td>
                  <td><?php echo htmlspecialchars($reg['event_title']); ?></td>
                  <td><?php echo date('M d, Y · g:i A', strtotime($reg['registered_at'])); ?></td>
                  <td>
                    <span class="status-tag <?php echo ($reg['status'] === 'cancelled') ? 'tag-cancelled' : 'tag-confirmed'; ?>">
                      <?php echo ucfirst(htmlspecialchars($reg['status'])); ?>
                    </span>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </section>
</main>
<?php
require_once '../includes/footer.php';
?>
