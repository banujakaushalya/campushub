<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

// Check role permission
$curr_role = $_SESSION['role'] ?? 'guest';
$curr_user_id = $_SESSION['user_id'] ?? 0;

if ($curr_role !== 'super_admin' && $curr_role !== 'admin') {
    echo "<script>window.location.href = '../student/dashboard.php';</script>";
    exit;
}

$message = "";
$message_type = "";

// 1. Handle Role Application Approval / Rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_application'])) {
    $app_id = (int)($_POST['application_id'] ?? 0);
    $action = $_POST['app_action'] ?? ''; // 'approve' or 'reject'
    $admin_notes = trim($_POST['admin_notes'] ?? '');

    // Get application details
    $app_res = Database::search("SELECT * FROM role_applications WHERE application_id = ? LIMIT 1", "i", [$app_id]);
    if ($app_res && $app = $app_res->fetch_assoc()) {
        $target_user_id = (int)$app['user_id'];
        $requested_role = $app['requested_role'];

        // Only super_admin can approve admin roles
        if ($requested_role === 'admin' && $curr_role !== 'super_admin') {
            $message = "Only Super Administrators have permission to approve Admin applications.";
            $message_type = "error";
        } else {
            if ($action === 'approve') {
                // Update user role
                Database::iud("UPDATE users SET role = ? WHERE user_id = ?", "si", [$requested_role, $target_user_id]);
                // If role is admin, ensure record exists in admin table
                if ($requested_role === 'admin') {
                    $chk = Database::search("SELECT admin_id FROM admin WHERE users_user_id = ?", "i", [$target_user_id]);
                    if (!$chk || $chk->num_rows === 0) {
                        Database::iud("INSERT INTO admin (users_user_id, department) VALUES (?, 'Student Affairs')", "i", [$target_user_id]);
                    }
                }
                // Update application status
                Database::iud(
                    "UPDATE role_applications SET status = 'approved', reviewed_by = ?, admin_notes = ?, reviewed_at = CURRENT_TIMESTAMP WHERE application_id = ?",
                    "isi",
                    [$curr_user_id, $admin_notes, $app_id]
                );
                $message = "Application approved successfully! User is now a " . ucfirst($requested_role) . ".";
                $message_type = "success";
            } elseif ($action === 'reject') {
                Database::iud(
                    "UPDATE role_applications SET status = 'rejected', reviewed_by = ?, admin_notes = ?, reviewed_at = CURRENT_TIMESTAMP WHERE application_id = ?",
                    "isi",
                    [$curr_user_id, $admin_notes, $app_id]
                );
                $message = "Application rejected.";
                $message_type = "info";
            }
        }
    }
}

// 2. Handle Manual Role Assignment / Revocation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_direct_role'])) {
    $target_uid = (int)($_POST['target_user_id'] ?? 0);
    $new_role = $_POST['new_role'] ?? '';

    // Check if valid role
    if ($new_role === 'admin' && $curr_role !== 'super_admin') {
        $message = "Only Super Administrators can declare or appoint Admins.";
        $message_type = "error";
    } elseif ($new_role === 'super_admin' && $curr_role !== 'super_admin') {
        $message = "Only Super Administrators can modify Super Admin accounts.";
        $message_type = "error";
    } elseif (in_array($new_role, ['student', 'moderator', 'admin', 'teacher', 'super_admin'])) {
        if (in_array($new_role, ['moderator', 'admin', 'super_admin'])) {
            $chk = Database::search("SELECT admin_id FROM admin WHERE users_user_id = ?", "i", [$target_uid]);
            if ($chk && $chk->num_rows > 0) {
                Database::iud("UPDATE admin SET role = ? WHERE users_user_id = ?", "si", [$new_role, $target_uid]);
            } else {
                Database::iud("INSERT INTO admin (users_user_id, role) VALUES (?, ?)", "is", [$target_uid, $new_role]);
            }
        } else {
            Database::iud("UPDATE users SET role = ? WHERE user_id = ?", "si", [$new_role, $target_uid]);
            Database::iud("DELETE FROM admin WHERE users_user_id = ?", "i", [$target_uid]);
        }
        $message = "User role successfully updated to " . ucfirst(str_replace('_', ' ', $new_role)) . ".";
        $message_type = "success";
    }
}

// Pending role applications
$pending_apps = [];

// Fetch all staff and users with effective roles
$staff_list = [];
$st_res = Database::search("SELECT u.user_id, u.first_name, u.last_name, u.email, u.phone, u.role as user_role, a.role as admin_role 
                           FROM users u 
                           LEFT JOIN admin a ON u.user_id = a.users_user_id 
                           ORDER BY u.user_id ASC");
if ($st_res && $st_res->num_rows > 0) {
    while ($row = $st_res->fetch_assoc()) {
        $row['role'] = !empty($row['admin_role']) ? $row['admin_role'] : $row['user_role'];
        $staff_list[] = $row;
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

    .roles-hero,
    .content-section {
      width: 100%;
      max-width: 1200px;
      margin-right: auto;
      margin-left: auto;
    }

    .roles-hero {
      position: relative;
      display: flex;
      flex-direction: column;
      justify-content: center;
      min-height: 180px;
      margin-top: 36px;
      padding: 32px clamp(24px, 5vw, 48px);
      box-sizing: border-box;
      border-radius: 16px;
      background: linear-gradient(115deg, rgba(23, 27, 42, 0.95), rgba(43, 53, 79, 0.85)), url('../assets/images/hero1.jpg') center / cover;
      color: #ffffff;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .roles-hero h1 {
      margin: 0 0 8px;
      font-size: clamp(1.8rem, 3.5vw, 2.4rem);
      text-shadow: #FF6B4A 1px 1px 2px;
    }

    .roles-hero p {
      max-width: 650px;
      margin: 0;
      color: rgba(255, 255, 255, 0.9);
      font-family: 'Cabin Sketch', cursive;
      font-size: 1.2rem;
    }

    .alert {
      padding: 14px 18px;
      border-radius: 8px;
      margin: 24px 0 0;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      font-size: 0.92rem;
    }

    .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .alert-info { background: #e0f2fe; color: #075985; border: 1px solid #bae6fd; }

    .role-section {
      background: #ffffff;
      border: 1px solid var(--line);
      border-radius: 12px;
      padding: 24px;
      margin-top: 28px;
      box-shadow: 0 4px 16px rgba(23, 27, 42, 0.04);
    }

    .section-head {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      border-bottom: 1px solid var(--line);
      padding-bottom: 12px;
    }

    .section-head h2 {
      margin: 0;
      font-size: 1.3rem;
      color: var(--ink);
    }

    .table-responsive {
      width: 100%;
      overflow-x: auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      font-size: 0.9rem;
    }

    th {
      background: #f8fafc;
      color: var(--muted);
      font-weight: 700;
      text-align: left;
      padding: 12px 14px;
      border-bottom: 1px solid var(--line);
      text-transform: uppercase;
      font-size: 0.78rem;
      letter-spacing: 0.5px;
    }

    td {
      padding: 14px;
      border-bottom: 1px solid #f0f2f5;
      color: var(--ink);
      vertical-align: middle;
    }

    tr:last-child td {
      border-bottom: none;
    }

    .role-badge {
      display: inline-block;
      padding: 4px 10px;
      border-radius: 12px;
      font-size: 0.75rem;
      font-weight: bold;
      text-transform: uppercase;
    }

    .badge-super_admin { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .badge-admin { background: #fee2e2; color: #991b1b; }
    .badge-moderator { background: #e0f2fe; color: #0369a1; }
    .badge-student { background: #f3f4f6; color: #4b5563; }
    .badge-teacher { background: #f3e8ff; color: #6b21a8; }

    .btn-action {
      display: inline-block;
      padding: 6px 12px;
      border-radius: 6px;
      font-size: 0.82rem;
      font-weight: 700;
      cursor: pointer;
      border: none;
      transition: background 0.2s ease;
      font-family: inherit;
    }

    .btn-approve { background: #16a34a; color: #ffffff; }
    .btn-approve:hover { background: #15803d; }
    .btn-reject { background: #dc2626; color: #ffffff; margin-left: 6px; }
    .btn-reject:hover { background: #b91c1c; }

    .action-form {
      display: flex;
      align-items: center;
      gap: 6px;
      flex-wrap: wrap;
    }

    .role-select {
      padding: 6px 10px;
      border: 1px solid #d1d5db;
      border-radius: 6px;
      font-size: 0.85rem;
    }

    .notes-input {
      padding: 6px 10px;
      border: 1px solid #d1d5db;
      border-radius: 6px;
      font-size: 0.82rem;
      width: 140px;
    }
  </style>

  <section class="roles-hero">
    <h1>Roles & Authority Management</h1>
    <p>Manage administrator privileges, appoint event moderators & review student role applications.</p>
  </section>

  <section class="content-section">
    <?php if (!empty($message)): ?>
      <div class="alert alert-<?php echo $message_type; ?>">
        <?php echo htmlspecialchars($message); ?>
      </div>
    <?php endif; ?>

    <!-- 1. Pending Role Applications from Students -->
    <div class="role-section">
      <div class="section-head">
        <h2>Pending Student Role Applications</h2>
        <span style="font-size: 0.88rem; color: var(--muted); font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
          <?php echo count($pending_apps); ?> application(s) awaiting review
        </span>
      </div>

      <?php if (empty($pending_apps)): ?>
        <div style="text-align: center; padding: 28px; color: var(--muted); font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
          <p style="font-size: 1rem; margin: 0; font-weight: bold; color: var(--ink);">No Pending Role Applications</p>
          <p style="margin: 4px 0 0;">All student applications have been processed.</p>
        </div>
      <?php else: ?>
        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>Applicant</th>
                <th>Requested Role</th>
                <th>Reason / Motivation</th>
                <th>Experience</th>
                <th>Date Applied</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($pending_apps as $pa): ?>
                <tr>
                  <td>
                    <strong><?php echo htmlspecialchars($pa['first_name'] . ' ' . $pa['last_name']); ?></strong><br>
                    <small style="color: var(--muted);"><?php echo htmlspecialchars($pa['email']); ?></small><br>
                    <small style="color: var(--muted);">ID: <?php echo htmlspecialchars($pa['student_id'] ?? 'N/A'); ?></small>
                  </td>
                  <td>
                    <span class="role-badge badge-<?php echo htmlspecialchars($pa['requested_role']); ?>">
                      <?php echo htmlspecialchars($pa['requested_role']); ?>
                    </span>
                  </td>
                  <td style="max-width: 250px;"><?php echo htmlspecialchars($pa['reason']); ?></td>
                  <td style="max-width: 200px;"><?php echo htmlspecialchars($pa['experience'] ?? 'None specified'); ?></td>
                  <td><?php echo date('M d, Y', strtotime($pa['applied_at'])); ?></td>
                  <td>
                    <form method="POST" action="manage_roles.php" class="action-form">
                      <input type="hidden" name="application_id" value="<?php echo $pa['application_id']; ?>">
                      <input type="text" name="admin_notes" class="notes-input" placeholder="Feedback notes...">
                      
                      <?php if ($pa['requested_role'] === 'admin' && $curr_role !== 'super_admin'): ?>
                        <span style="font-size: 0.78rem; color: #b91c1c; font-weight: bold;">SuperAdmin Required</span>
                      <?php else: ?>
                        <button type="submit" name="action_application" value="approve" onclick="document.getElementById('act_<?php echo $pa['application_id']; ?>').value='approve';" class="btn-action btn-approve">Approve</button>
                        <button type="submit" name="action_application" value="reject" onclick="document.getElementById('act_<?php echo $pa['application_id']; ?>').value='reject';" class="btn-action btn-reject">Reject</button>
                        <input type="hidden" name="app_action" id="act_<?php echo $pa['application_id']; ?>" value="approve">
                      <?php endif; ?>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

    <!-- 2. System Users & Direct Role Assignment -->
    <div class="role-section">
      <div class="section-head">
        <h2>User Authority Directory</h2>
        <span style="font-size: 0.88rem; color: var(--muted); font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
          Assign or update roles
        </span>
      </div>

      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>User Name</th>
              <th>Email / Phone</th>
              <th>Student/Staff ID</th>
              <th>Current Role</th>
              <th>Change Role</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($staff_list as $usr): ?>
              <tr>
                <td><strong><?php echo htmlspecialchars($usr['first_name'] . ' ' . $usr['last_name']); ?></strong></td>
                <td>
                  <?php echo htmlspecialchars($usr['email']); ?><br>
                  <small style="color: var(--muted);"><?php echo htmlspecialchars($usr['phone']); ?></small>
                </td>
                <td>STU-<?php echo str_pad($usr['user_id'], 4, '0', STR_PAD_LEFT); ?></td>
                <td>
                  <span class="role-badge badge-<?php echo htmlspecialchars($usr['role']); ?>">
                    <?php echo htmlspecialchars(str_replace('_', ' ', $usr['role'])); ?>
                  </span>
                </td>
                <td>
                  <?php if ($usr['role'] === 'super_admin' && $curr_role !== 'super_admin'): ?>
                    <span style="font-size: 0.82rem; color: var(--muted);">Protected Account</span>
                  <?php else: ?>
                    <form method="POST" action="manage_roles.php" class="action-form">
                      <input type="hidden" name="target_user_id" value="<?php echo $usr['user_id']; ?>">
                      <select name="new_role" class="role-select">
                        <option value="student" <?php echo ($usr['role'] === 'student') ? 'selected' : ''; ?>>Student</option>
                        <option value="moderator" <?php echo ($usr['role'] === 'moderator') ? 'selected' : ''; ?>>Moderator</option>
                        <?php if ($curr_role === 'super_admin'): ?>
                          <option value="admin" <?php echo ($usr['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                        <?php endif; ?>
                        <option value="teacher" <?php echo ($usr['role'] === 'teacher') ? 'selected' : ''; ?>>Teacher</option>
                      </select>
                      <button type="submit" name="assign_direct_role" class="btn-action btn-approve" style="padding: 6px 10px;">Save</button>
                    </form>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</main>
<?php
require_once '../includes/footer.php';
?>
