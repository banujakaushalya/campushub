<?php
require_once 'includes/header.php';
require_once 'includes/db.php';

// Beginner-friendly search & category filter handling
$filter_category = isset($_GET['category']) ? trim($_GET['category']) : 'all';
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

$announcements = [];
$db_connected = false;

$sql = "SELECT * FROM announcements WHERE 1=1";
$params = [];
$types = "";

if ($filter_category !== 'all' && !empty($filter_category)) {
    $sql .= " AND category = ?";
    $params[] = $filter_category;
    $types .= "s";
}

if (!empty($search_query)) {
    $sql .= " AND (title LIKE ? OR content LIKE ?)";
    $like = "%" . $search_query . "%";
    $params[] = $like;
    $params[] = $like;
    $types .= "ss";
}

$sql .= " ORDER BY created_at DESC";

$result = Database::search($sql, $types, $params);

if ($result && $result->num_rows > 0) {
    $db_connected = true;
    while ($row = $result->fetch_assoc()) {
        $announcements[] = $row;
    }
} else {
    // Beginner fallback sample announcements if DB table is empty or loading
    $announcements = [
        [
            'announcement_id' => 1,
            'title' => 'Welcome to CampusHub 2026 Semester Portal',
            'content' => 'The unified student portal is now live! Register for upcoming student club events, workshops, sports championships, and access multimedia resources.',
            'category' => 'General',
            'priority' => 'high',
            'created_at' => date('Y-m-d H:i:s')
        ],
        [
            'announcement_id' => 2,
            'title' => 'Club Registration Deadline for Term 3',
            'content' => 'All students wishing to join competitive societies (Computing, Robotics, Sports) must finalize registrations before the designated semester cutoff date.',
            'category' => 'Clubs',
            'priority' => 'medium',
            'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
        ],
        [
            'announcement_id' => 3,
            'title' => 'Inter-Campus Shuttle Transport Schedule Update',
            'content' => 'Special shuttle buses will run between campuses for the upcoming Inter-University Sports Meet. Pickup locations are at the Main Gate.',
            'category' => 'Logistics',
            'priority' => 'low',
            'created_at' => date('Y-m-d H:i:s', strtotime('-5 days'))
        ]
    ];
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

    .announcement-hero,
    .content-section {
      width: 100%;
      max-width: 1200px;
      margin-right: auto;
      margin-left: auto;
    }

    .announcement-hero {
      position: relative;
      display: flex;
      flex-direction: column;
      justify-content: center;
      min-height: 240px;
      margin-top: 36px;
      padding: 40px clamp(24px, 5vw, 64px);
      box-sizing: border-box;
      border-radius: 16px;
      background: linear-gradient(115deg, rgba(23, 27, 42, 0.94), rgba(43, 53, 79, 0.8)), url('assets/images/hero2.jpg') center / cover;
      color: #ffffff;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .announcement-hero h1 {
      margin: 0 0 10px;
      font-size: clamp(2rem, 4vw, 3rem);
      line-height: 1.1;
      text-shadow: #FF6B4A 1px 1px 2px;
    }

    .announcement-hero p {
      max-width: 600px;
      margin: 0;
      color: rgba(255, 255, 255, 0.85);
      font-family: 'Cabin Sketch', cursive;
      font-size: 1.3rem;
    }

    .content-section {
      margin-top: 36px;
    }

    /* Filters Bar */
    .filter-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 16px;
      background: #ffffff;
      padding: 16px 20px;
      border: 1px solid var(--line);
      border-radius: 12px;
      margin-bottom: 28px;
      box-shadow: 0 4px 16px rgba(23, 27, 42, 0.04);
    }

    .category-pills {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
    }

    .pill-btn {
      padding: 7px 16px;
      border-radius: 20px;
      font-size: 0.88rem;
      font-weight: 700;
      text-decoration: none;
      color: var(--ink);
      background: #f0f2f5;
      transition: all 0.2s ease;
      border: 1px solid transparent;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .pill-btn:hover {
      background: #e4e7ed;
      color: var(--coral);
    }

    .pill-btn.active {
      background: var(--coral);
      color: #ffffff;
    }

    .search-box {
      display: flex;
      gap: 8px;
    }

    .search-input {
      padding: 8px 14px;
      border: 1.5px solid var(--line);
      border-radius: 8px;
      font-size: 0.9rem;
      outline: none;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .search-input:focus {
      border-color: var(--coral);
    }

    .btn-search {
      padding: 8px 16px;
      background: var(--ink);
      color: #ffffff;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      font-size: 0.88rem;
      transition: background 0.2s;
    }

    .btn-search:hover {
      background: #2b354f;
    }

    /* Announcement Cards */
    .announcement-list {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .announcement-card {
      background: #ffffff;
      border-radius: 12px;
      border: 1px solid var(--line);
      padding: 24px 28px;
      box-shadow: 0 8px 24px rgba(23, 27, 42, 0.05);
      border-left: 5px solid var(--coral);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .announcement-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 12px 28px rgba(23, 27, 42, 0.09);
    }

    .announcement-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 16px;
      margin-bottom: 12px;
      flex-wrap: wrap;
    }

    .announcement-header h2 {
      margin: 0;
      color: var(--ink);
      font-size: 1.25rem;
    }

    .badges {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .priority-badge {
      font-size: 0.72rem;
      text-transform: uppercase;
      font-weight: 800;
      padding: 4px 10px;
      border-radius: 12px;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      letter-spacing: 0.5px;
    }

    .priority-urgent, .priority-high {
      background: #ffe5e5;
      color: #d63031;
      border: 1px solid #fab1a0;
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
      padding: 4px 10px;
      border-radius: 12px;
      font-weight: 700;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .announcement-body {
      color: var(--muted);
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      font-size: 0.95rem;
      line-height: 1.6;
      margin: 0 0 14px;
    }

    .announcement-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 0.82rem;
      color: #9aa0a6;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      border-top: 1px solid #f2f4f7;
      padding-top: 12px;
    }

    .announcement-date {
      color: var(--coral);
      font-weight: 700;
    }

    @media (max-width: 768px) {
      .filter-bar {
        flex-direction: column;
        align-items: stretch;
      }
      .search-box {
        width: 100%;
      }
      .search-input {
        flex: 1;
      }
    }
  </style>

  <section class="announcement-hero">
    <h1>Campus Announcements</h1>
    <p>Official news, club circulars, schedule updates, and academic notices.</p>
  </section>

  <section class="content-section">
    <!-- Filter & Search Bar -->
    <div class="filter-bar">
      <div class="category-pills">
        <a href="announcements.php?category=all" class="pill-btn <?php echo ($filter_category === 'all') ? 'active' : ''; ?>">All</a>
        <a href="announcements.php?category=General" class="pill-btn <?php echo ($filter_category === 'General') ? 'active' : ''; ?>">General</a>
        <a href="announcements.php?category=Clubs" class="pill-btn <?php echo ($filter_category === 'Clubs') ? 'active' : ''; ?>">Clubs</a>
        <a href="announcements.php?category=Logistics" class="pill-btn <?php echo ($filter_category === 'Logistics') ? 'active' : ''; ?>">Logistics</a>
      </div>

      <form method="GET" action="announcements.php" class="search-box">
        <?php if ($filter_category !== 'all'): ?>
          <input type="hidden" name="category" value="<?php echo htmlspecialchars($filter_category); ?>">
        <?php endif; ?>
        <input type="text" name="search" class="search-input" placeholder="Search notices..." value="<?php echo htmlspecialchars($search_query); ?>">
        <button type="submit" class="btn-search">Search</button>
      </form>
    </div>

    <!-- Announcement Cards List -->
    <div class="announcement-list">
      <?php if (count($announcements) === 0): ?>
        <div class="announcement-card" style="text-align: center; padding: 40px;">
          <h2>No announcements found</h2>
          <p class="announcement-body">No circulars match your selected search or filter criteria.</p>
        </div>
      <?php else: ?>
        <?php foreach ($announcements as $item): ?>
          <?php 
            $p_class = 'priority-' . strtolower($item['priority'] ?? 'medium');
            $date_str = date('M d, Y', strtotime($item['created_at'] ?? 'now'));
          ?>
          <article class="announcement-card">
            <div class="announcement-header">
              <h2><?php echo htmlspecialchars($item['title']); ?></h2>
              <div class="badges">
                <span class="category-tag"><?php echo htmlspecialchars($item['category'] ?? 'General'); ?></span>
                <span class="priority-badge <?php echo $p_class; ?>"><?php echo htmlspecialchars($item['priority'] ?? 'Notice'); ?></span>
              </div>
            </div>

            <p class="announcement-body"><?php echo nl2br(htmlspecialchars($item['content'])); ?></p>

            <div class="announcement-footer">
              <span>CampusHub Student Affairs Notice</span>
              <span class="announcement-date">📅 <?php echo $date_str; ?></span>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </section>
</main>
<?php
require_once 'includes/footer.php';
?>
