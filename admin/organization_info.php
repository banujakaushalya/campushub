<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

// Check role permission: only super_admin and admin
$curr_role = $_SESSION['role'] ?? 'guest';
if ($curr_role !== 'super_admin' && $curr_role !== 'admin') {
    echo "<script>window.location.href = '../student/dashboard.php';</script>";
    exit;
}

$message = "";
$message_type = "";

// Handle updating organization info
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_org_info'])) {
    $org_name = trim($_POST['org_name'] ?? '');
    $org_tagline = trim($_POST['org_tagline'] ?? '');
    $org_email = trim($_POST['org_email'] ?? '');
    $org_phone = trim($_POST['org_phone'] ?? '');
    $org_address = trim($_POST['org_address'] ?? '');
    $org_about = trim($_POST['org_about'] ?? '');

    // Save key-value settings in master_control or settings
    $settings = [
        'org_name' => $org_name,
        'org_tagline' => $org_tagline,
        'org_email' => $org_email,
        'org_phone' => $org_phone,
        'org_address' => $org_address,
        'org_about' => $org_about
    ];

    foreach ($settings as $key => $val) {
        // Check if key exists
        $chk = Database::search("SELECT id FROM master_control WHERE setting_key = ?", "s", [$key]);
        if ($chk && $chk->num_rows > 0) {
            Database::iud("UPDATE master_control SET setting_value = ? WHERE setting_key = ?", "ss", [$val, $key]);
        } else {
            Database::iud("INSERT INTO master_control (setting_key, setting_value) VALUES (?, ?)", "ss", [$key, $val]);
        }
    }

    $message = "Organizational details updated successfully!";
    $message_type = "success";
}

// Fetch current organizational settings
$org_data = [];
$res = Database::search("SELECT setting_key, setting_value FROM master_control");
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $org_data[$row['setting_key']] = $row['setting_value'];
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

    .org-hero,
    .content-section {
      width: 100%;
      max-width: 900px;
      margin-right: auto;
      margin-left: auto;
    }

    .org-hero {
      position: relative;
      display: flex;
      flex-direction: column;
      justify-content: center;
      min-height: 180px;
      margin-top: 36px;
      padding: 32px clamp(24px, 5vw, 48px);
      box-sizing: border-box;
      border-radius: 16px;
      background: linear-gradient(115deg, rgba(23, 27, 42, 0.95), rgba(43, 53, 79, 0.85)), url('../assets/images/hero2.jpg') center / cover;
      color: #ffffff;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .org-hero h1 {
      margin: 0 0 8px;
      font-size: clamp(1.8rem, 3.5vw, 2.4rem);
      text-shadow: #FF6B4A 1px 1px 2px;
    }

    .org-hero p {
      max-width: 650px;
      margin: 0;
      color: rgba(255, 255, 255, 0.9);
      font-family: 'Cabin Sketch', cursive;
      font-size: 1.2rem;
    }

    .form-card {
      background: #ffffff;
      border: 1px solid var(--line);
      border-radius: 12px;
      padding: 28px;
      margin-top: 28px;
      box-shadow: 0 4px 16px rgba(23, 27, 42, 0.04);
    }

    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 18px;
    }

    .form-group {
      margin-bottom: 18px;
    }

    .form-group.full-width {
      grid-column: 1 / -1;
    }

    .form-group label {
      display: block;
      margin-bottom: 6px;
      font-size: 0.9rem;
      font-weight: 700;
      color: var(--ink);
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .form-control {
      width: 100%;
      padding: 10px 14px;
      box-sizing: border-box;
      border: 1px solid #d1d5db;
      border-radius: 8px;
      font-size: 0.95rem;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    textarea.form-control {
      min-height: 110px;
      resize: vertical;
    }

    .btn-save {
      background: var(--coral);
      color: #ffffff;
      border: none;
      padding: 12px 28px;
      font-size: 0.95rem;
      font-weight: 700;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.2s ease;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .btn-save:hover {
      background: #e55a3a;
    }

    .alert {
      padding: 14px 18px;
      border-radius: 8px;
      margin-top: 24px;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      font-size: 0.92rem;
    }

    .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

    @media (max-width: 768px) {
      .form-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>

  <section class="org-hero">
    <h1>Maintain Organisational Information</h1>
    <p>Update institute profile, contact details, campus address & official about statement.</p>
  </section>

  <section class="content-section">
    <?php if (!empty($message)): ?>
      <div class="alert alert-<?php echo $message_type; ?>">
        <?php echo htmlspecialchars($message); ?>
      </div>
    <?php endif; ?>

    <div class="form-card">
      <form method="POST" action="organization_info.php">
        <div class="form-grid">
          <div class="form-group">
            <label for="org_name">Institute / Organization Name *</label>
            <input type="text" name="org_name" id="org_name" class="form-control" value="<?php echo htmlspecialchars($org_data['org_name'] ?? 'CampusHub Student Affairs'); ?>" required>
          </div>

          <div class="form-group">
            <label for="org_tagline">Tagline / Motto</label>
            <input type="text" name="org_tagline" id="org_tagline" class="form-control" value="<?php echo htmlspecialchars($org_data['org_tagline'] ?? 'Connecting Students, Society Leaders & Campus Activities'); ?>">
          </div>

          <div class="form-group">
            <label for="org_email">Official Contact Email *</label>
            <input type="email" name="org_email" id="org_email" class="form-control" value="<?php echo htmlspecialchars($org_data['org_email'] ?? 'affairs@campushub.edu'); ?>" required>
          </div>

          <div class="form-group">
            <label for="org_phone">Student Help Desk Phone *</label>
            <input type="text" name="org_phone" id="org_phone" class="form-control" value="<?php echo htmlspecialchars($org_data['org_phone'] ?? '+94 11 234 5678'); ?>" required>
          </div>

          <div class="form-group full-width">
            <label for="org_address">Campus Headquarters Address</label>
            <input type="text" name="org_address" id="org_address" class="form-control" value="<?php echo htmlspecialchars($org_data['org_address'] ?? 'No. 20, Innovation Way, Colombo, Sri Lanka'); ?>">
          </div>

          <div class="form-group full-width">
            <label for="org_about">About Organization Statement</label>
            <textarea name="org_about" id="org_about" class="form-control" placeholder="Describe the mission and vision of the student council..."><?php echo htmlspecialchars($org_data['org_about'] ?? 'CampusHub is the premier centralized platform for university student life, club activities, event registration, and multimedia publication.'); ?></textarea>
          </div>
        </div>

        <button type="submit" name="save_org_info" class="btn-save">Save Organisational Information</button>
      </form>
    </div>
  </section>
</main>
<?php
require_once '../includes/footer.php';
?>
