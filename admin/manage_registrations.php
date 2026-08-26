<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

// Check role permission: super_admin or admin
$curr_role = $_SESSION['role'] ?? 'guest';
if ($curr_role !== 'super_admin' && $curr_role !== 'admin') {
    echo "<script>window.location.href = '../student/dashboard.php';</script>";
    exit;
}

$msg = "";

// Handle status update
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['reg_id']) && isset($_POST['new_status'])) {
    $reg_id = (int)$_POST['reg_id'];
    $new_status = trim($_POST['new_status']);
    
    $up = Database::iud("UPDATE registrations SET status = ? WHERE registration_id = ?", "si", [$new_status, $reg_id]);
    if ($up) {
        $msg = "Registration status updated successfully.";
    }
}

// Fetch all registrations
$registrations = [];
$sql = "SELECT r.registration_id, r.status, r.notes, r.registered_at,
               u.first_name, u.last_name, u.email, u.student_id,
               e.title as event_title, e.event_date
        FROM registrations r
        JOIN users u ON r.users_user_id = u.user_id
        JOIN events e ON r.events_event_id = e.event_id
        ORDER BY r.registered_at DESC";

$res = Database::search($sql);
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $registrations[] = $row;
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

    .header-bar,
    .table-container {
      width: 100%;
      max-width: 1200px;
      margin-right: auto;
      margin-left: auto;
    }

    .header-bar {
      margin-top: 36px;
      margin-bottom: 24px;
    }

    .header-bar h1 {
      margin: 0;
      color: var(--ink);
      font-size: 1.8rem;
    }

    .table-container {
      background: #ffffff;
      border: 1px solid var(--line);
      border-radius: 12px;
      overflow-x: auto;
      box-shadow: 0 8px 24px rgba(23, 27, 42, 0.05);
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
      padding: 14px 20px;
      font-weight: 700;
      color: var(--ink);
      border-bottom: 1px solid var(--line);
    }

    td {
      padding: 14px 20px;
      border-bottom: 1px solid #f2f4f7;
      color: #333d52;
    }

    .status-select {
      padding: 6px 10px;
      border-radius: 6px;
      border: 1px solid var(--line);
      font-size: 0.82rem;
      font-weight: 600;
    }

    .btn-update {
      background: var(--ink);
      color: #fff;
      border: none;
      padding: 6px 12px;
      border-radius: 6px;
      font-size: 0.8rem;
      font-weight: bold;
      cursor: pointer;
      margin-left: 4px;
    }

    .alert {
      padding: 14px 18px;
      border-radius: 8px;
      margin-bottom: 20px;
      background-color: #e6f7ec;
      color: #1b7a3e;
      border: 1px solid #c2ebd0;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      font-weight: 600;
    }
  </style>

  <div class="header-bar">
    <h1>All Event Registrations</h1>
    <p style="margin: 4px 0 0; color: var(--muted); font-family: 'Cabin Sketch', cursive; font-size: 1.15rem;">
      Monitor student participant rosters and adjust confirmation statuses.
    </p>
  </div>

  <?php if (!empty($msg)): ?>
    <div class="alert" style="max-width: 1200px; margin: 0 auto 20px;"><?php echo htmlspecialchars($msg); ?></div>
  <?php endif; ?>

  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th>Student</th>
          <th>Student ID</th>
          <th>Registered Event</th>
          <th>Event Date</th>
          <th>Notes</th>
          <th>Status & Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($registrations as $r): ?>
          <tr>
            <td><strong><?php echo htmlspecialchars($r['first_name'] . ' ' . $r['last_name']); ?></strong><br><small style="color: var(--muted);"><?php echo htmlspecialchars($r['email']); ?></small></td>
            <td><?php echo htmlspecialchars($r['student_id'] ?? 'N/A'); ?></td>
            <td><?php echo htmlspecialchars($r['event_title']); ?></td>
            <td><?php echo date('M d, Y', strtotime($r['event_date'])); ?></td>
            <td><small style="color: var(--muted);"><?php echo htmlspecialchars($r['notes'] ?? 'None'); ?></small></td>
            <td>
              <form method="POST" action="manage_registrations.php" style="display: flex; align-items: center;">
                <input type="hidden" name="reg_id" value="<?php echo htmlspecialchars($r['registration_id']); ?>">
                <select name="new_status" class="status-select">
                  <option value="confirmed" <?php echo ($r['status'] === 'confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                  <option value="pending" <?php echo ($r['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                  <option value="cancelled" <?php echo ($r['status'] === 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                </select>
                <button type="submit" class="btn-update">Save</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</main>
<?php
require_once '../includes/footer.php';
?>
