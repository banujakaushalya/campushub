<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../includes/db.php';

// Strict Master Control Authentication Verification
if (!isset($_SESSION['master_authenticated']) || $_SESSION['master_authenticated'] !== true || ($_SESSION['role'] ?? '') !== 'super_admin') {
    header("Location: login.php");
    exit;
}

$message = "";
$message_type = "";

// Handle Master Settings Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_master_control'])) {
    $allow_registration = isset($_POST['allow_registration']) ? 'true' : 'false';
    $allow_media_upload = isset($_POST['allow_media_upload']) ? 'true' : 'false';
    $maintenance_mode = isset($_POST['maintenance_mode']) ? 'true' : 'false';
    $system_name = trim($_POST['system_name'] ?? 'CampusHub Student Services System');
    $emergency_broadcast = trim($_POST['emergency_broadcast'] ?? '');
    $institution_name = trim($_POST['institution_name'] ?? 'Java Institute for Advanced Technology');

    $controls = [
        'allow_registration' => $allow_registration,
        'allow_media_upload' => $allow_media_upload,
        'maintenance_mode' => $maintenance_mode,
        'system_name' => $system_name,
        'emergency_broadcast' => $emergency_broadcast,
        'institution_name' => $institution_name
    ];

    foreach ($controls as $key => $val) {
        $chk = Database::search("SELECT id FROM master_control WHERE setting_key = ?", "s", [$key]);
        if ($chk && $chk->num_rows > 0) {
            Database::iud("UPDATE master_control SET setting_value = ? WHERE setting_key = ?", "ss", [$val, $key]);
        } else {
            Database::iud("INSERT INTO master_control (setting_key, setting_value) VALUES (?, ?)", "ss", [$key, $val]);
        }
    }

    $message = "Master control configurations updated and saved successfully!";
    $message_type = "success";
}

// Fetch current master control values
$controls_data = [];
$res = Database::search("SELECT setting_key, setting_value FROM master_control");
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $controls_data[$row['setting_key']] = $row['setting_value'];
    }
}

// Fetch system counts
$total_users = 0;
$u_res = Database::search("SELECT COUNT(*) as c FROM users");
if ($u_res && $row = $u_res->fetch_assoc()) {
    $total_users = (int)$row['c'];
}

$total_events = 0;
$e_res = Database::search("SELECT COUNT(*) as c FROM events");
if ($e_res && $row = $e_res->fetch_assoc()) {
    $total_events = (int)$row['c'];
}

$total_media = 0;
$m_res = Database::search("SELECT COUNT(*) as c FROM media");
if ($m_res && $row = $m_res->fetch_assoc()) {
    $total_media = (int)$row['c'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Super Admin Master Control Console | CampusHub</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    :root {
      --ink: #0f172a;
      --gold: #f59e0b;
      --gold-dark: #d97706;
      --dark-bg: #090d16;
      --card-bg: #131b2e;
      --border-color: #232f48;
      --coral: #FF6B4A;
      --muted: #94a3b8;
    }

    body {
      margin: 0;
      padding: 0;
      background: var(--dark-bg);
      color: #f8fafc;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      min-height: 100vh;
    }

    .master-nav {
      background: rgba(19, 27, 46, 0.85);
      backdrop-filter: blur(12px);
      border-bottom: 1px solid var(--border-color);
      padding: 14px 28px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: sticky;
      top: 0;
      z-index: 100;
    }

    .master-brand {
      display: flex;
      align-items: center;
      gap: 12px;
      text-decoration: none;
      color: #ffffff;
    }

    .master-brand-tag {
      background: rgba(245, 158, 11, 0.2);
      border: 1px solid rgba(245, 158, 11, 0.4);
      color: #fbbf24;
      padding: 3px 8px;
      border-radius: 6px;
      font-size: 0.72rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .master-nav-links {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    .master-nav-links a {
      color: var(--muted);
      text-decoration: none;
      font-size: 0.88rem;
      font-weight: 600;
      padding: 6px 12px;
      border-radius: 6px;
      transition: all 0.2s;
    }

    .master-nav-links a:hover {
      color: #ffffff;
      background: rgba(255, 255, 255, 0.05);
    }

    .btn-logout {
      background: rgba(239, 68, 68, 0.2);
      border: 1px solid rgba(239, 68, 68, 0.4);
      color: #fca5a5 !important;
    }

    .btn-logout:hover {
      background: rgba(239, 68, 68, 0.35) !important;
      color: #ffffff !important;
    }

    .container {
      max-width: 1000px;
      margin: 0 auto;
      padding: 32px 24px 64px;
    }

    .hero-banner {
      background: linear-gradient(135deg, #1e293b, #0f172a);
      border: 1px solid rgba(245, 158, 11, 0.3);
      border-radius: 16px;
      padding: 32px 36px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
      margin-bottom: 28px;
      position: relative;
    }

    .hero-banner h1 {
      margin: 0 0 8px;
      font-size: 1.8rem;
      color: #fbbf24;
    }

    .hero-banner p {
      margin: 0;
      color: #94a3b8;
      font-size: 0.95rem;
      line-height: 1.5;
    }

    .status-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 16px;
      margin-bottom: 28px;
    }

    .status-card {
      background: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: 12px;
      padding: 18px 20px;
      display: flex;
      flex-direction: column;
    }

    .status-card .label {
      font-size: 0.8rem;
      color: var(--muted);
      text-transform: uppercase;
      font-weight: 700;
      letter-spacing: 0.5px;
      margin-bottom: 6px;
    }

    .status-card .val {
      font-size: 1.4rem;
      font-weight: 800;
      color: #ffffff;
    }

    .control-card {
      background: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: 14px;
      padding: 32px;
      margin-bottom: 28px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }

    .control-card h2 {
      margin: 0 0 20px;
      font-size: 1.25rem;
      color: #ffffff;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .toggle-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 18px 0;
      border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }

    .toggle-row:last-of-type {
      border-bottom: none;
    }

    .toggle-info h4 {
      margin: 0 0 4px;
      font-size: 1rem;
      color: #ffffff;
    }

    .toggle-info p {
      margin: 0;
      color: var(--muted);
      font-size: 0.85rem;
      line-height: 1.4;
    }

    .switch {
      position: relative;
      display: inline-block;
      width: 52px;
      height: 28px;
      flex-shrink: 0;
    }

    .switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #334155;
      transition: .3s;
      border-radius: 28px;
    }

    .slider:before {
      position: absolute;
      content: "";
      height: 20px;
      width: 20px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      transition: .3s;
      border-radius: 50%;
    }

    input:checked + .slider {
      background-color: var(--gold);
    }

    input:checked + .slider:before {
      transform: translateX(24px);
    }

    .form-group {
      margin-top: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-size: 0.88rem;
      font-weight: 700;
      color: #cbd5e1;
    }

    .form-control {
      width: 100%;
      padding: 12px 16px;
      box-sizing: border-box;
      background: #090d16;
      border: 1px solid var(--border-color);
      border-radius: 8px;
      color: #ffffff;
      font-size: 0.95rem;
      font-family: inherit;
    }

    .form-control:focus {
      outline: none;
      border-color: var(--gold);
    }

    .btn-save {
      background: linear-gradient(135deg, #f59e0b, #d97706);
      color: #0f172a;
      border: none;
      padding: 13px 32px;
      font-size: 0.95rem;
      font-weight: 800;
      border-radius: 8px;
      cursor: pointer;
      margin-top: 24px;
      transition: all 0.2s;
    }

    .btn-save:hover {
      background: linear-gradient(135deg, #fbbf24, #f59e0b);
      box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4);
      transform: translateY(-1px);
    }

    .alert {
      padding: 14px 18px;
      border-radius: 8px;
      margin-bottom: 24px;
      font-size: 0.92rem;
    }

    .alert-success {
      background: rgba(34, 197, 94, 0.15);
      border: 1px solid rgba(34, 197, 94, 0.35);
      color: #86efac;
    }

    .shortcut-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 16px;
    }

    .shortcut-item {
      background: #090d16;
      border: 1px solid var(--border-color);
      border-radius: 10px;
      padding: 16px;
      text-decoration: none;
      color: #ffffff;
      display: flex;
      align-items: center;
      gap: 12px;
      transition: all 0.2s;
    }

    .shortcut-item:hover {
      border-color: var(--gold);
      transform: translateY(-2px);
      background: #0f172a;
    }

    .shortcut-icon {
      font-size: 1.5rem;
    }
  </style>
</head>
<body>

<nav class="master-nav">
  <a href="index.php" class="master-brand">
    <strong style="font-size: 1.2rem; letter-spacing: -0.5px;">CampusHub</strong>
    <span class="master-brand-tag">Master Control</span>
  </a>

  <div class="master-nav-links">
    <a href="../admin/dashboard.php">&larr; Admin Center</a>
    <a href="../admin/manage_roles.php">Role Approvals</a>
    <a href="../index.php" target="_blank">View Live Site &nearr;</a>
    <a href="logout.php" class="btn-logout">Exit Master Mode</a>
  </div>
</nav>

<main class="container">
  <div class="hero-banner">
    <h1>Super Admin Master Control Unit</h1>
    <p>Global architectural switches, emergency broadcasts, and high-privilege system parameters.</p>
  </div>

  <?php if (!empty($message)): ?>
    <div class="alert alert-<?php echo $message_type; ?>">
      <?php echo htmlspecialchars($message); ?>
    </div>
  <?php endif; ?>

  <div class="status-grid">
    <div class="status-card">
      <span class="label">Total Accounts</span>
      <span class="val"><?php echo $total_users; ?></span>
    </div>
    <div class="status-card">
      <span class="label">Live Events</span>
      <span class="val"><?php echo $total_events; ?></span>
    </div>
    <div class="status-card">
      <span class="label">Gallery Media</span>
      <span class="val"><?php echo $total_media; ?></span>
    </div>
    <div class="status-card">
      <span class="label">System State</span>
      <span class="val" style="color: <?php echo (($controls_data['maintenance_mode'] ?? 'false') === 'true') ? '#ef4444' : '#22c55e'; ?>; font-size: 1.1rem;">
        <?php echo (($controls_data['maintenance_mode'] ?? 'false') === 'true') ? '⚠️ Maintenance Active' : '● Operational'; ?>
      </span>
    </div>
  </div>

  <div class="control-card">
    <h2><span>⚙️</span> Global System Configuration</h2>
    <form method="POST" action="index.php">
      
      <div class="toggle-row">
        <div class="toggle-info">
          <h4>Student Self-Registration Portal</h4>
          <p>Allow new students to independently register and generate accounts.</p>
        </div>
        <label class="switch">
          <input type="checkbox" name="allow_registration" <?php echo (($controls_data['allow_registration'] ?? 'true') === 'true') ? 'checked' : ''; ?>>
          <span class="slider"></span>
        </label>
      </div>

      <div class="toggle-row">
        <div class="toggle-info">
          <h4>Student Photo & Media Uploads</h4>
          <p>Enable student club members and regular users to upload event gallery photos.</p>
        </div>
        <label class="switch">
          <input type="checkbox" name="allow_media_upload" <?php echo (($controls_data['allow_media_upload'] ?? 'true') === 'true') ? 'checked' : ''; ?>>
          <span class="slider"></span>
        </label>
      </div>

      <div class="toggle-row">
        <div class="toggle-info">
          <h4>Campus Maintenance Mode</h4>
          <p>Restrict standard student logins during exam or maintenance windows.</p>
        </div>
        <label class="switch">
          <input type="checkbox" name="maintenance_mode" <?php echo (($controls_data['maintenance_mode'] ?? 'false') === 'true') ? 'checked' : ''; ?>>
          <span class="slider"></span>
        </label>
      </div>

      <div class="form-group">
        <label for="system_name">System Name / Campus Portal Title</label>
        <input type="text" name="system_name" id="system_name" class="form-control" value="<?php echo htmlspecialchars($controls_data['system_name'] ?? 'CampusHub Student Services System'); ?>" required>
      </div>

      <div class="form-group">
        <label for="institution_name">Institution / University Branch</label>
        <input type="text" name="institution_name" id="institution_name" class="form-control" value="<?php echo htmlspecialchars($controls_data['institution_name'] ?? 'Java Institute for Advanced Technology'); ?>" required>
      </div>

      <div class="form-group">
        <label for="emergency_broadcast">Master Emergency Banner Broadcast (Leave blank for normal operations)</label>
        <input type="text" name="emergency_broadcast" id="emergency_broadcast" class="form-control" placeholder="e.g. Weather Advisory: All evening events rescheduled." value="<?php echo htmlspecialchars($controls_data['emergency_broadcast'] ?? ''); ?>">
      </div>

      <button type="submit" name="save_master_control" class="btn-save">Apply Master Configurations</button>
    </form>
  </div>

  <div class="control-card">
    <h2><span>🚀</span> High-Privilege Administration Hubs</h2>
    <div class="shortcut-grid">
      <a href="../admin/manage_roles.php" class="shortcut-item">
        <span class="shortcut-icon">👑</span>
        <div>
          <strong>Role Governance</strong>
          <div style="font-size: 0.8rem; color: var(--muted);">Approve & Declare Admins</div>
        </div>
      </a>
      <a href="../admin/manage_students.php" class="shortcut-item">
        <span class="shortcut-icon">👥</span>
        <div>
          <strong>Student Directory</strong>
          <div style="font-size: 0.8rem; color: var(--muted);">Direct database oversight</div>
        </div>
      </a>
      <a href="../admin/organization_info.php" class="shortcut-item">
        <span class="shortcut-icon">🏛️</span>
        <div>
          <strong>Organization Info</strong>
          <div style="font-size: 0.8rem; color: var(--muted);">Edit public branch records</div>
        </div>
      </a>
      <a href="../admin/dashboard.php" class="shortcut-item">
        <span class="shortcut-icon">📊</span>
        <div>
          <strong>Admin Command Center</strong>
          <div style="font-size: 0.8rem; color: var(--muted);">Main operational portal</div>
        </div>
      </a>
    </div>
  </div>
</main>

</body>
</html>
