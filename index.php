<?php
require_once 'includes/header.php';
require_once 'includes/db.php';

// Fetch real events from database
$events = [];
$events_result = Database::search("SELECT * FROM events WHERE status = 'upcoming' ORDER BY event_date ASC LIMIT 6");
if ($events_result && $events_result->num_rows > 0) {
    while ($row = $events_result->fetch_assoc()) {
        $events[] = $row;
    }
} else {
    // Beginner fallback sample events if database is loading or empty
    $events = [
        [
            'event_id' => 1,
            'title' => 'Tech Coding Workshop',
            'description' => 'Build practical skills with a hands-on session led by student developers.',
            'category' => 'Workshop',
            'event_date' => '2026-04-18',
            'event_time' => '10:00:00',
            'location' => 'Lab 04, Tech Campus',
            'banner_image' => 'tech_coding_work_shop.jpg'
        ],
        [
            'event_id' => 2,
            'title' => 'Design Thinking Lab',
            'description' => 'Turn a bright idea into a thoughtful solution alongside your peers.',
            'category' => 'Workshop',
            'event_date' => '2026-04-22',
            'event_time' => '14:00:00',
            'location' => 'Design Studio 2',
            'banner_image' => 'tech_coding_work_shop.jpg'
        ],
        [
            'event_id' => 3,
            'title' => 'Campus Creative Night',
            'description' => 'Share music, art, and stories in an open evening for the whole campus.',
            'category' => 'Cultural',
            'event_date' => '2026-04-25',
            'event_time' => '17:30:00',
            'location' => 'Open Amphitheatre',
            'banner_image' => 'tech_coding_work_shop.jpg'
        ],
        [
            'event_id' => 4,
            'title' => 'Career Conversations',
            'description' => 'Meet local professionals and ask the questions that shape your next step.',
            'category' => 'Career',
            'event_date' => '2026-04-29',
            'event_time' => '11:00:00',
            'location' => 'Auditorium B',
            'banner_image' => 'tech_coding_work_shop.jpg'
        ],
        [
            'event_id' => 5,
            'title' => 'Community Volunteer Day',
            'description' => 'Spend a morning making a tangible difference with campus organizations.',
            'category' => 'Community',
            'event_date' => '2026-05-03',
            'event_time' => '09:00:00',
            'location' => 'Campus Green Grounds',
            'banner_image' => 'tech_coding_work_shop.jpg'
        ],
        [
            'event_id' => 6,
            'title' => 'Student Founder Meetup',
            'description' => 'Connect with ambitious builders, founders, and future collaborators.',
            'category' => 'Competition',
            'event_date' => '2026-05-07',
            'event_time' => '18:00:00',
            'location' => 'Innovation Hub',
            'banner_image' => 'tech_coding_work_shop.jpg'
        ]
    ];
}

// Fetch announcements
$announcements = [];
$ann_result = Database::search("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 3");
if ($ann_result && $ann_result->num_rows > 0) {
    while ($row = $ann_result->fetch_assoc()) {
        $announcements[] = $row;
    }
} else {
    $announcements = [
        [
            'announcement_id' => 1,
            'title' => 'Spring event registration is now open',
            'content' => 'Browse the calendar and reserve your place at the events that interest you most.',
            'created_at' => '2026-04-10'
        ],
        [
            'announcement_id' => 2,
            'title' => 'Club activities schedule for Semester 1',
            'content' => 'All society leadership meetings and initial orientation workshops are published.',
            'created_at' => '2026-04-08'
        ],
        [
            'announcement_id' => 3,
            'title' => 'Campus transport timings update',
            'content' => 'Shuttle service routes between Java Institute hubs have been revised.',
            'created_at' => '2026-04-05'
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
      padding: 0 20px 64px;
      box-sizing: border-box;
    }

    .hero,
    .content-section {
      width: 100%;
      max-width: 1200px;
      margin-right: auto;
      margin-left: auto;
    }

    .hero {
      position: relative;
      display: flex;
      align-items: center;
      min-height: 360px;
      margin-top: 42px;
      padding: 56px clamp(28px, 7vw, 92px);
      box-sizing: border-box;
      overflow: hidden;
      border-radius: 16px;
      background: linear-gradient(110deg, rgba(23, 27, 42, .92), rgba(23, 27, 42, .26)), url('assets/images/hero1.jpg') center / cover;
    }

    .hero-content {
      position: relative;
      z-index: 1;
      max-width: 620px;
      color: #ffffff;
    }

    .hero-content h1 {
      margin: 0 0 14px;
      font-size: clamp(2.2rem, 5vw, 4.5rem);
      line-height: .98;
    }

    .hero-content p {
      max-width: 500px;
      margin: 0;
      color: rgba(255, 255, 255, .78);
      font-family: Cabin Sketch, cursive;
      font-size: 1.25rem;
    }

    .content-section {
      margin-top: 54px;
    }

    .section-heading {
      display: flex;
      align-items: end;
      justify-content: space-between;
      gap: 20px;
      margin-bottom: 22px;
    }

    .section-heading h2 {
      margin: 0;
      color: var(--ink);
      font-size: 1.7rem;
    }

    .section-heading p {
      margin: 0;
      color: var(--muted);
      font-family: Cabin Sketch, cursive;
      font-size: 1.05rem;
    }

    .event-container {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 22px;
    }

    .event-card {
      display: flex;
      min-height: 356px;
      flex-direction: column;
      overflow: hidden;
      border: 1px solid var(--line);
      border-radius: 10px;
      background: #ffffff;
      box-shadow: 0 10px 24px rgba(23, 27, 42, .07);
      transition: transform .2s ease, box-shadow .2s ease;
      text-decoration: none;
    }

    .event-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 16px 30px rgba(23, 27, 42, .13);
    }

    .img-container {
      min-height: 174px;
      background: linear-gradient(135deg, rgba(255, 107, 74, .18), transparent), url('assets/images/events/tech_coding_work_shop.jpg') center / cover;
    }

    .event-info {
      display: flex;
      flex: 1;
      flex-direction: column;
      padding: 20px;
    }

    .event-info h3 {
      margin: 0 0 9px;
      color: var(--ink);
      font-size: 1.15rem;
    }

    .event-info p {
      margin: 0;
      color: var(--muted);
      font-family: sans-serif;
      font-size: .92rem;
      line-height: 1.55;
    }

    .event-info p.event-meta {
      margin: 0 0 9px;
      color: var(--coral);
      font-family: cursive, sans-serif;
      font-size: 1rem;
      font-weight: 700;
    }

    .announcement {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 24px;
      padding: 24px 28px;
      border-left: 4px solid var(--coral);
      background: #ffffff;
      box-shadow: 0 10px 24px rgba(23, 27, 42, .06);
      margin-bottom: 16px;
      border-radius: 6px;
    }

    .announcement h3,
    .announcement p {
      margin: 0;
    }

    .announcement h3 {
      color: var(--ink);
      font-size: 1.1rem;
    }

    .announcement p {
      margin-top: 6px;
      color: var(--muted);
      font-family: sans-serif;
      font-size: .9rem;
    }

    .announcement-date {
      flex: 0 0 auto;
      color: var(--coral);
      font-family: sans-serif;
      font-weight: 700;
    }

    .pagination-container {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      margin: 36px auto 0;
      width: 100%;
    }

    .page-link {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 40px;
      height: 40px;
      padding: 0 13px;
      box-sizing: border-box;
      border: 1px solid var(--line);
      border-radius: 6px;
      background: #ffffff;
      color: var(--ink);
      font-family: sans-serif;
      font-size: .88rem;
      font-weight: 700;
      text-decoration: none;
      transition: background .2s ease, border-color .2s ease, color .2s ease;
    }

    .page-link:hover {
      border-color: var(--ink);
      background: #f0f4f8;
    }

    .page-link.active {
      border-color: var(--ink);
      background: var(--ink);
      color: #ffffff;
      cursor: default;
    }

    @media (max-width: 760px) {
      .page-container {
        padding-right: 14px;
        padding-left: 14px;
      }

      .hero {
        min-height: 300px;
        margin-top: 24px;
      }

      .event-container {
        grid-template-columns: 1fr;
      }

      .section-heading,
      .announcement {
        align-items: flex-start;
        flex-direction: column;
      }
    }
  </style>

  <section class="hero">
    <div class="hero-content">
      <h1>Welcome to CampusHub</h1>
      <p>Discover and join exciting events happening around you.</p>
    </div>
  </section>

  <section class="content-section" aria-labelledby="featured-events-title">
    <div class="section-heading">
      <div>
        <h2 id="featured-events-title">Featured Events</h2>
        <p>Find your next campus experience.</p>
      </div>
      <p>Showing <?php echo count($events); ?> upcoming events</p>
    </div>

    <div class="event-container">
      <?php foreach ($events as $event): ?>
        <?php
          $formatted_date = date('M d, Y', strtotime($event['event_date']));
          $formatted_time = date('g:i A', strtotime($event['event_time']));
        ?>
        <a href="student/event_details.php?id=<?php echo htmlspecialchars($event['event_id']); ?>" class="event-card">
          <div class="img-container"></div>
          <div class="event-info">
            <p class="event-meta"><?php echo strtoupper($formatted_date); ?> · <?php echo $formatted_time; ?></p>
            <h3><?php echo htmlspecialchars($event['title']); ?></h3>
            <p><?php echo htmlspecialchars(mb_strimwidth($event['description'], 0, 95, '...')); ?></p>
          </div>
        </a>
      <?php endforeach; ?>
    </div>

    <nav class="pagination-container" aria-label="Event pages">
      <a class="page-link active" href="index.php">1</a>
      <a class="page-link" href="student/events.php">Browse All Events &raquo;</a>
    </nav>
  </section>

  <section class="content-section" aria-labelledby="latest-announcement-title">
    <div class="section-heading">
      <div>
        <h2 id="latest-announcement-title">Latest Announcements</h2>
        <p>Official circulars from Student Services.</p>
      </div>
      <p><a href="announcements.php" style="color: var(--coral); text-decoration: none; font-weight: bold;">View All &raquo;</a></p>
    </div>

    <?php foreach ($announcements as $ann): ?>
      <?php $ann_date = date('M d, Y', strtotime($ann['created_at'])); ?>
      <article class="announcement">
        <div>
          <h3><?php echo htmlspecialchars($ann['title']); ?></h3>
          <p><?php echo htmlspecialchars(mb_strimwidth($ann['content'], 0, 110, '...')); ?></p>
        </div>
        <span class="announcement-date"><?php echo strtoupper($ann_date); ?></span>
      </article>
    <?php endforeach; ?>
  </section>
</main>
<?php
require_once 'includes/footer.php';
?>
