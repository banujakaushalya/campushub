<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

$success_msg = "";
$error_msg = "";

// Handle announcement deletion
if (isset($_GET['delete_id'])) {
    $del_id = (int)$_GET['delete_id'];
    Database::iud("DELETE FROM announcements WHERE announcement_id = ?", "i", [$del_id]);
    $success_msg = "Announcement deleted successfully.";
}

// Handle announcement creation
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    $category = isset($_POST['category']) ? trim($_POST['category']) : 'General';
    $priority = isset($_POST['priority']) ? trim($_POST['priority']) : 'medium';

    if (empty($title) || empty($content)) {
        $error_msg = "Title and Content cannot be empty.";
    } else {
        $inserted = Database::iud(
            "INSERT INTO announcements (title, content, category, priority, admin_admin_id) VALUES (?, ?, ?, ?, 1)",
            "ssss",
            [$title, $content, $category, $priority]
        );

        if ($inserted) {
            $success_msg = "New announcement published!";
        } else {
            $error_msg = "Failed to post announcement.";
        }
    }
}

// Fetch all announcements
$announcements = [];
$res = Database::search("SELECT * FROM announcements ORDER BY created_at DESC");
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $announcements[] = $row;
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

    .admin-layout {
      width: 100%;
      max-width: 1200px;
      margin: 36px auto 0;
      display: grid;
      grid-template-columns: 1fr 1.6fr;
      gap: 32px;
    }

    .form-card,
    .table-card {
      background: #ffffff;
      border: 1px solid var(--line);
      border-radius: 14px;
      padding: 28px;
      box-shadow: 0 8px 24px rgba(23, 27, 42, 0.05);
    }

    .form-card h2,
    .table-card h2 {
      margin: 0 0 6px;
      color: var(--ink);
      font-size: 1.35rem;
    }

    .subtitle {
      margin: 0 0 20px;
      color: var(--muted);
      font-family: 'Cabin Sketch', cursive;
      font-size: 1.1rem;
    }

    .form-group {
      margin-bottom: 16px;
    }

    .form-group label {
      display: block;
      margin-bottom: 6px;
      font-weight: 700;
      color: var(--ink);
      font-size: 0.88rem;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .form-control {
      width: 100%;
      padding: 10px 12px;
      border: 1.5px solid var(--line);
      border-radius: 8px;
      font-size: 0.92rem;
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
      background-color: var(--coral);
      color: #ffffff;
      padding: 12px;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      font-size: 0.95rem;
      cursor: pointer;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      font-size: 0.88rem;
      margin-top: 10px;
    }

    th {
      background: #faf8ff;
      text-align: left;
      padding: 10px 14px;
      font-weight: 700;
      color: var(--ink);
      border-bottom: 1px solid var(--line);
    }

    td {
      padding: 12px 14px;
      border-bottom: 1px solid #f2f4f7;
    }

    .btn-delete {
      color: #c92a2a;
      text-decoration: none;
      font-weight: bold;
      font-size: 0.8rem;
      padding: 4px 8px;
      border-radius: 4px;
      background: #ffeaea;
    }

    .alert {
      padding: 12px 16px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      font-size: 0.88rem;
      font-weight: 600;
      grid-column: 1 / -1;
    }

    .alert-success {
      background-color: #e6f7ec;
      color: #1b7a3e;
      border: 1px solid #c2ebd0;
    }

    .alert-error {
      background-color: #ffeaea;
      color: #c92a2a;
      border: 1px solid #fcc2c2;
    }

    @media (max-width: 860px) {
      .admin-layout {
        grid-template-columns: 1fr;
      }
    }
  </style>

  <div class="admin-layout">
    <?php if (!empty($success_msg)): ?>
      <div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
    <?php endif; ?>

    <?php if (!empty($error_msg)): ?>
      <div class="alert alert-error"><?php echo htmlspecialchars($error_msg); ?></div>
    <?php endif; ?>

    <!-- Add Announcement Form -->
    <div class="form-card">
      <h2>Post New Notice</h2>
      <p class="subtitle">Broadcast news across the student portal.</p>

      <form action="manage_announcements.php" method="POST">
        <div class="form-group">
          <label for="title">Title *</label>
          <input type="text" id="title" name="title" class="form-control" placeholder="e.g. Exam Timetable Released" required>
        </div>

        <div class="form-group">
          <label for="category">Category</label>
          <select id="category" name="category" class="form-control">
            <option value="General">General</option>
            <option value="Clubs">Clubs & Societies</option>
            <option value="Logistics">Transport & Logistics</option>
            <option value="Urgent">Emergency Alert</option>
          </select>
        </div>

        <div class="form-group">
          <label for="priority">Priority</label>
          <select id="priority" name="priority" class="form-control">
            <option value="low">Low</option>
            <option value="medium" selected>Medium</option>
            <option value="high">High</option>
            <option value="urgent">Urgent</option>
          </select>
        </div>

        <div class="form-group">
          <label for="content">Announcement Body *</label>
          <textarea id="content" name="content" class="form-control" rows="4" placeholder="Detailed message for students..." required></textarea>
        </div>

        <button type="submit" class="btn-submit">Publish Notice</button>
      </form>
    </div>

    <!-- Active Announcements Table -->
    <div class="table-card">
      <h2>Active Notices</h2>
      <p class="subtitle">Currently visible to all campus users.</p>

      <table>
        <thead>
          <tr>
            <th>Title</th>
            <th>Category</th>
            <th>Priority</th>
            <th>Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($announcements)): ?>
            <tr><td colspan="5" style="text-align: center; color: var(--muted);">No notices posted yet.</td></tr>
          <?php else: ?>
            <?php foreach ($announcements as $a): ?>
              <tr>
                <td><strong><?php echo htmlspecialchars($a['title']); ?></strong></td>
                <td><?php echo htmlspecialchars($a['category']); ?></td>
                <td><span style="text-transform: uppercase; font-size: 0.75rem; font-weight: bold;"><?php echo htmlspecialchars($a['priority']); ?></span></td>
                <td><?php echo date('M d', strtotime($a['created_at'])); ?></td>
                <td>
                  <a href="manage_announcements.php?delete_id=<?php echo htmlspecialchars($a['announcement_id']); ?>" class="btn-delete" onclick="return confirm('Delete this announcement?');">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>
<?php
require_once '../includes/footer.php';
?>
