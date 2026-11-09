<?php
require_once 'includes/header.php';
require_once 'includes/db.php';

$announcement_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$announcement = null;

if ($announcement_id > 0) {
    // Support querying by either 'id' or 'announcement_id' column
    $res = Database::search("SELECT * FROM announcements WHERE id = ? LIMIT 1", "i", [$announcement_id]);
    if (!$res || $res->num_rows === 0) {
        $res = Database::search("SELECT * FROM announcements WHERE announcement_id = ? LIMIT 1", "i", [$announcement_id]);
    }
    if ($res && $res->num_rows > 0) {
        $announcement = $res->fetch_assoc();
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

    .details-container {
      width: 100%;
      max-width: 900px;
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

    .announcement-card {
      background: #ffffff;
      border: 1px solid var(--line);
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(23, 27, 42, 0.08);
      padding: 36px;
    }

    .priority-badge {
      display: inline-block;
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      padding: 4px 12px;
      border-radius: 20px;
    }

    .priority-high {
      background: #ffebee;
      color: #c62828;
      border: 1px solid #ffcdd2;
    }

    .priority-medium {
      background: #fff3cd;
      color: #856404;
      border: 1px solid #ffeeba;
    }

    .priority-low {
      background: #e2f0d9;
      color: #2e7d32;
      border: 1px solid #c8e6c9;
    }

    .category-tag {
      font-size: 0.78rem;
      background: #f0f2f5;
      color: var(--muted);
      padding: 4px 12px;
      border-radius: 20px;
      font-weight: 700;
    }

    .announcement-meta {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-bottom: 20px;
      border-bottom: 1px solid #f0f2f5;
      margin-bottom: 24px;
    }

    .announcement-title {
      font-size: clamp(1.5rem, 3vw, 2.2rem);
      color: var(--ink);
      margin: 0 0 20px;
      line-height: 1.3;
    }

    .announcement-content {
      color: #334155;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      font-size: 1.05rem;
      line-height: 1.8;
      white-space: pre-line;
      margin-bottom: 32px;
    }

    .announcement-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 0.88rem;
      color: var(--muted);
      border-top: 1px solid #f0f2f5;
      padding-top: 18px;
    }

    .btn-browse {
      display: inline-block;
      padding: 12px 28px;
      background: var(--coral);
      color: #ffffff;
      text-decoration: none;
      border-radius: 8px;
      font-weight: bold;
      font-size: 0.95rem;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .btn-browse:hover {
      background: #e55a3a;
    }
  </style>

  <div class="details-container">
    <a href="announcements.php" class="back-link">&larr; Back to Announcements</a>

    <?php if (!$announcement): ?>
      <div class="announcement-card" style="text-align: center; padding: 48px 24px;">
        <h2 style="color: var(--ink); margin: 0 0 12px; font-size: 1.6rem;">Announcement Not Found</h2>
        <p style="color: var(--muted); font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 1rem; margin: 0 0 24px;">
          The requested announcement does not exist or may have been removed.
        </p>
        <a href="announcements.php" class="btn-browse">Browse All Announcements</a>
      </div>
    <?php else: ?>
      <?php
        $p_class = 'priority-' . strtolower($announcement['priority'] ?? 'medium');
        $date_posted_val = $announcement['date_posted'] ?? $announcement['created_at'] ?? 'now';
        $date_str = date('l, F d, Y \a\t g:i A', strtotime($date_posted_val));
        $category = $announcement['category'] ?? 'General';
        $priority = $announcement['priority'] ?? 'Notice';
      ?>
      <article class="announcement-card">
        <div class="announcement-meta">
          <div style="display: flex; gap: 8px; align-items: center;">
            <span class="category-tag"><?php echo htmlspecialchars($category); ?></span>
            <span class="priority-badge <?php echo $p_class; ?>"><?php echo htmlspecialchars($priority); ?></span>
          </div>
          <span style="font-size: 0.85rem; color: var(--muted); font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
            Posted: <strong><?php echo $date_str; ?></strong>
          </span>
        </div>

        <h1 class="announcement-title"><?php echo htmlspecialchars($announcement['title']); ?></h1>

        <div class="announcement-content">
          <?php echo nl2br(htmlspecialchars($announcement['content'])); ?>
        </div>

        <div class="announcement-footer">
          <span>CampusHub Official Notice</span>
          <?php if (!empty($announcement['date_expire'])): ?>
            <span>Expires: <?php echo date('M d, Y', strtotime($announcement['date_expire'])); ?></span>
          <?php endif; ?>
        </div>
      </article>
    <?php endif; ?>
  </div>
</main>
<?php
require_once 'includes/footer.php';
?>
