<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

// Beginner-friendly category and search filter
$category = isset($_GET['category']) ? trim($_GET['category']) : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$sql = "SELECT * FROM events WHERE status != 'cancelled'";
$params = [];
$types = "";

if ($category !== 'all' && !empty($category)) {
    $sql .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

if (!empty($search)) {
    $sql .= " AND (title LIKE ? OR description LIKE ? OR location LIKE ?)";
    $like = "%" . $search . "%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= "sss";
}

$sql .= " ORDER BY event_date ASC";

$events = [];
$res = Database::search($sql, $types, $params);

if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        // Count confirmed registrations for this event
        $count_res = Database::search("SELECT COUNT(*) as registered_count FROM registrations WHERE events_event_id = ? AND status = 'confirmed'", "i", [$row['event_id']]);
        $reg_count = 0;
        if ($count_res && $crow = $count_res->fetch_assoc()) {
            $reg_count = (int)$crow['registered_count'];
        }
        $row['registered_count'] = $reg_count;
        $row['seats_left'] = max(0, (int)$row['max_participants'] - $reg_count);
        $events[] = $row;
    }
} else {
    // Beginner fallback sample events if table is empty
    $events = [
        [
            'event_id' => 1,
            'title' => 'Full-Stack Web Dev Hackathon 2026',
            'description' => 'Join our 24-hour coding sprint building modern web applications with PHP, JavaScript, and APIs.',
            'category' => 'Competition',
            'club_name' => 'Computing Society',
            'event_date' => '2026-09-15',
            'event_time' => '09:00:00',
            'location' => 'Main Auditorium, Tech Campus',
            'max_participants' => 80,
            'registered_count' => 32,
            'seats_left' => 48
        ],
        [
            'event_id' => 2,
            'title' => 'AI & Robotics Innovation Workshop',
            'description' => 'Hands-on workshop exploring real-world AI applications and embedded hardware programming.',
            'category' => 'Workshop',
            'club_name' => 'Robotics Club',
            'event_date' => '2026-09-22',
            'event_time' => '14:00:00',
            'location' => 'Lab 04, Innovation Wing',
            'max_participants' => 40,
            'registered_count' => 25,
            'seats_left' => 15
        ],
        [
            'event_id' => 3,
            'title' => 'Annual Inter-University Sports Meet',
            'description' => 'Athletics, badminton, table tennis, and chess championships across participating campuses.',
            'category' => 'Sports',
            'club_name' => 'Sports Council',
            'event_date' => '2026-10-05',
            'event_time' => '08:00:00',
            'location' => 'University Sports Complex',
            'max_participants' => 200,
            'registered_count' => 110,
            'seats_left' => 90
        ],
        [
            'event_id' => 4,
            'title' => 'Music & Arts Acoustic Night',
            'description' => 'An evening of acoustic melodies, spoken word poetry, and creative student artwork exhibitions.',
            'category' => 'Cultural',
            'club_name' => 'Music & Arts Circle',
            'event_date' => '2026-10-18',
            'event_time' => '18:30:00',
            'location' => 'Open Amphitheatre',
            'max_participants' => 120,
            'registered_count' => 45,
            'seats_left' => 75
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

    .events-hero,
    .content-section {
      width: 100%;
      max-width: 1200px;
      margin-right: auto;
      margin-left: auto;
    }

    .events-hero {
      position: relative;
      display: flex;
      flex-direction: column;
      justify-content: center;
      min-height: 240px;
      margin-top: 36px;
      padding: 40px clamp(24px, 5vw, 64px);
      box-sizing: border-box;
      border-radius: 16px;
      background: linear-gradient(115deg, rgba(23, 27, 42, 0.92), rgba(43, 53, 79, 0.78)), url('../assets/images/hero1.jpg') center / cover;
      color: #ffffff;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .events-hero h1 {
      margin: 0 0 10px;
      font-size: clamp(2rem, 4vw, 3rem);
      line-height: 1.1;
      text-shadow: #FF6B4A 1px 1px 2px;
    }

    .events-hero p {
      max-width: 600px;
      margin: 0;
      color: rgba(255, 255, 255, 0.85);
      font-family: 'Cabin Sketch', cursive;
      font-size: 1.3rem;
    }

    .content-section {
      margin-top: 36px;
    }

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
    }

    .events-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
      gap: 24px;
    }

    .event-card {
      display: flex;
      flex-direction: column;
      background: #ffffff;
      border: 1px solid var(--line);
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 8px 24px rgba(23, 27, 42, 0.06);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .event-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 14px 30px rgba(23, 27, 42, 0.12);
    }

    .event-img {
      height: 160px;
      background: linear-gradient(135deg, rgba(255, 107, 74, 0.2), rgba(23, 27, 42, 0.4)), url('../assets/images/events/tech_coding_work_shop.jpg') center / cover;
      position: relative;
    }

    .event-category-badge {
      position: absolute;
      top: 14px;
      left: 14px;
      background: var(--coral);
      color: #ffffff;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .event-body {
      padding: 20px;
      display: flex;
      flex-direction: column;
      flex: 1;
    }

    .event-club {
      font-size: 0.82rem;
      color: var(--muted);
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      margin-bottom: 6px;
      font-weight: 600;
    }

    .event-title {
      margin: 0 0 10px;
      color: var(--ink);
      font-size: 1.25rem;
      line-height: 1.25;
    }

    .event-meta-info {
      display: flex;
      flex-direction: column;
      gap: 6px;
      margin-bottom: 14px;
      font-size: 0.88rem;
      color: #4a5568;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .meta-row {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .event-desc {
      color: var(--muted);
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      font-size: 0.92rem;
      line-height: 1.5;
      margin: 0 0 18px;
      flex: 1;
    }

    .event-actions {
      display: flex;
      gap: 10px;
      border-top: 1px solid #f0f2f5;
      padding-top: 14px;
      margin-top: auto;
    }

    .btn-details {
      flex: 1;
      text-align: center;
      padding: 10px 14px;
      background: #f0f2f5;
      color: var(--ink);
      text-decoration: none;
      border-radius: 8px;
      font-weight: bold;
      font-size: 0.88rem;
      transition: background 0.2s;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .btn-details:hover {
      background: #e4e7ed;
    }

    .btn-register {
      flex: 1.2;
      text-align: center;
      padding: 10px 14px;
      background: var(--coral);
      color: #ffffff;
      text-decoration: none;
      border-radius: 8px;
      font-weight: bold;
      font-size: 0.88rem;
      transition: background 0.2s;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .btn-register:hover {
      background: #e55a3a;
    }

    @media (max-width: 768px) {
      .events-grid {
        grid-template-columns: 1fr;
      }
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

  <section class="events-hero">
    <h1>Explore Campus Events</h1>
    <p>Discover coding hackathons, technical workshops, sports championships & cultural galas.</p>
  </section>

  <section class="content-section">
    <!-- Filter & Search -->
    <div class="filter-bar">
      <div class="category-pills">
        <a href="events.php?category=all" class="pill-btn <?php echo ($category === 'all') ? 'active' : ''; ?>">All</a>
        <a href="events.php?category=Workshop" class="pill-btn <?php echo ($category === 'Workshop') ? 'active' : ''; ?>">Workshops</a>
        <a href="events.php?category=Competition" class="pill-btn <?php echo ($category === 'Competition') ? 'active' : ''; ?>">Competitions</a>
        <a href="events.php?category=Sports" class="pill-btn <?php echo ($category === 'Sports') ? 'active' : ''; ?>">Sports</a>
        <a href="events.php?category=Cultural" class="pill-btn <?php echo ($category === 'Cultural') ? 'active' : ''; ?>">Cultural</a>
      </div>

      <form method="GET" action="events.php" class="search-box">
        <?php if ($category !== 'all'): ?>
          <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
        <?php endif; ?>
        <input type="text" name="search" class="search-input" placeholder="Search events..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn-search">Search</button>
      </form>
    </div>

    <!-- Events Grid -->
    <div class="events-grid">
      <?php if (empty($events)): ?>
        <div style="grid-column: 1 / -1; background: #ffffff; padding: 48px; text-align: center; border-radius: 12px; border: 1px solid var(--line);">
          <h2>No Events Found</h2>
          <p style="color: var(--muted); font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
            No campus events match your selected filters. Try changing your search keywords or category.
          </p>
        </div>
      <?php else: ?>
        <?php foreach ($events as $event): ?>
          <?php
            $date_formatted = date('l, M d, Y', strtotime($event['event_date']));
            $time_formatted = date('g:i A', strtotime($event['event_time']));
          ?>
          <div class="event-card">
            <div class="event-img">
              <span class="event-category-badge"><?php echo htmlspecialchars($event['category'] ?? 'Event'); ?></span>
            </div>
            <div class="event-body">
              <div class="event-club"><?php echo htmlspecialchars($event['club_name'] ?? 'CampusHub'); ?></div>
              <h2 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h2>

              <div class="event-meta-info">
                <div class="meta-row">
                  <span>📅</span>
                  <span><strong><?php echo $date_formatted; ?></strong> at <?php echo $time_formatted; ?></span>
                </div>
                <div class="meta-row">
                  <span>📍</span>
                  <span><?php echo htmlspecialchars($event['location']); ?></span>
                </div>
                <div class="meta-row">
                  <span>👥</span>
                  <span>Seats Left: <strong><?php echo htmlspecialchars($event['seats_left'] ?? $event['max_participants']); ?></strong> / <?php echo htmlspecialchars($event['max_participants']); ?></span>
                </div>
              </div>

              <p class="event-desc"><?php echo htmlspecialchars(mb_strimwidth($event['description'], 0, 110, '...')); ?></p>

              <div class="event-actions">
                <a href="event_details.php?id=<?php echo htmlspecialchars($event['event_id']); ?>" class="btn-details">Details</a>
                <a href="register_event.php?id=<?php echo htmlspecialchars($event['event_id']); ?>" class="btn-register">Register Now</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </section>
</main>
<?php
require_once '../includes/footer.php';
?>
