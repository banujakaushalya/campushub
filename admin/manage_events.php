<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

// Check role permission: super_admin, admin, or moderator
$curr_role = $_SESSION['role'] ?? 'guest';
if ($curr_role !== 'super_admin' && $curr_role !== 'admin' && $curr_role !== 'moderator') {
    echo "<script>window.location.href = '../student/dashboard.php';</script>";
    exit;
}

$events = [];
$res = Database::search("SELECT * FROM events ORDER BY event_date DESC");
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $events[] = $row;
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
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 36px;
      margin-bottom: 24px;
      flex-wrap: wrap;
      gap: 16px;
    }

    .header-bar h1 {
      margin: 0;
      color: var(--ink);
      font-size: 1.8rem;
    }

    .btn-add {
      background: var(--coral);
      color: #ffffff;
      padding: 12px 22px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: bold;
      font-size: 0.95rem;
      box-shadow: 0 4px 12px rgba(255, 107, 74, 0.3);
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .btn-add:hover {
      background: #e55a3a;
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

    .btn-sm {
      padding: 6px 12px;
      border-radius: 6px;
      font-size: 0.8rem;
      font-weight: bold;
      text-decoration: none;
      display: inline-block;
    }

    .btn-edit {
      background: #f0f2f5;
      color: var(--ink);
      margin-right: 6px;
    }

    .btn-delete {
      background: #ffeaea;
      color: #c92a2a;
    }
  </style>

  <div class="header-bar">
    <div>
      <h1>Event Management</h1>
      <p style="margin: 4px 0 0; color: var(--muted); font-family: 'Cabin Sketch', cursive; font-size: 1.15rem;">
        Add, edit, and maintain active campus events.
      </p>
    </div>
    <a href="add_event.php" class="btn-add">+ Add New Event</a>
  </div>

  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th>Event Title</th>
          <th>Category</th>
          <th>Club / Society</th>
          <th>Date & Time</th>
          <th>Location</th>
          <th>Capacity</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($events)): ?>
          <tr><td colspan="8" style="text-align: center; padding: 30px;">No events created yet.</td></tr>
        <?php else: ?>
          <?php foreach ($events as $ev): ?>
            <tr>
              <td><strong><?php echo htmlspecialchars($ev['title']); ?></strong></td>
              <td><span style="background: #f0f2f5; padding: 4px 8px; border-radius: 6px; font-weight: 600;"><?php echo htmlspecialchars($ev['category']); ?></span></td>
              <td><?php echo htmlspecialchars($ev['club_name']); ?></td>
              <td><?php echo date('M d, Y', strtotime($ev['event_date'])); ?><br><small style="color: var(--muted);"><?php echo date('g:i A', strtotime($ev['event_time'])); ?></small></td>
              <td><?php echo htmlspecialchars($ev['location']); ?></td>
              <td><?php echo htmlspecialchars($ev['max_participants']); ?></td>
              <td><strong><?php echo ucfirst(htmlspecialchars($ev['status'])); ?></strong></td>
              <td>
                <a href="edit_event.php?id=<?php echo htmlspecialchars($ev['event_id']); ?>" class="btn-sm btn-edit">Edit</a>
                <a href="delete_event.php?id=<?php echo htmlspecialchars($ev['event_id']); ?>" class="btn-sm btn-delete" onclick="return confirm('Delete this event?');">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>
<?php
require_once '../includes/footer.php';
?>
