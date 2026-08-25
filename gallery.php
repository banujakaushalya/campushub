<?php
require_once 'includes/header.php';
require_once 'includes/db.php';

// Fetch media items
$media_list = [];
$res = Database::search("SELECT m.*, e.title as event_title FROM media m LEFT JOIN events e ON m.events_event_id = e.event_id ORDER BY m.created_at DESC");
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $media_list[] = $row;
    }
} else {
    // Beginner fallback sample media
    $media_list = [
        [
            'media_id' => 1,
            'title' => 'Web Dev Hackathon Sprint 2026',
            'file_url' => 'tech_coding_work_shop.jpg',
            'description' => 'Students collaborating on final stage software pitches at the main computing lab.',
            'event_title' => 'Full-Stack Web Dev Hackathon'
        ],
        [
            'media_id' => 2,
            'title' => 'Robotics Club Hardware Testing',
            'file_url' => 'hero1.jpg',
            'description' => 'Autonomous line-follower robot speed trials during the tech exhibition.',
            'event_title' => 'AI & Robotics Innovation Workshop'
        ],
        [
            'media_id' => 3,
            'title' => 'Campus Sports Meet Opening Ceremony',
            'file_url' => 'hero2.jpg',
            'description' => 'Track athletes line up for the 4x100m inter-faculty relay preliminaries.',
            'event_title' => 'Annual Sports Meet'
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

    .gallery-hero,
    .content-section {
      width: 100%;
      max-width: 1200px;
      margin-right: auto;
      margin-left: auto;
    }

    .gallery-hero {
      position: relative;
      display: flex;
      flex-direction: column;
      justify-content: center;
      min-height: 220px;
      margin-top: 36px;
      padding: 40px clamp(24px, 5vw, 64px);
      box-sizing: border-box;
      border-radius: 16px;
      background: linear-gradient(115deg, rgba(23, 27, 42, 0.92), rgba(43, 53, 79, 0.8)), url('assets/images/events/tech_coding_work_shop.jpg') center / cover;
      color: #ffffff;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .gallery-hero h1 {
      margin: 0 0 8px;
      font-size: clamp(2rem, 4vw, 2.8rem);
      line-height: 1.1;
      text-shadow: #FF6B4A 1px 1px 2px;
    }

    .gallery-hero p {
      max-width: 600px;
      margin: 0;
      color: rgba(255, 255, 255, 0.85);
      font-family: 'Cabin Sketch', cursive;
      font-size: 1.3rem;
    }

    .content-section {
      margin-top: 36px;
    }

    .gallery-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 28px;
    }

    .gallery-card {
      background: #ffffff;
      border: 1px solid var(--line);
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 6px 20px rgba(23, 27, 42, 0.05);
      transition: transform 0.2s, box-shadow 0.2s;
    }

    .gallery-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 28px rgba(23, 27, 42, 0.1);
    }

    .gallery-img {
      width: 100%;
      height: 220px;
      object-fit: cover;
      display: block;
      background-color: #eee;
    }

    .gallery-body {
      padding: 20px;
    }

    .gallery-tag {
      display: inline-block;
      background: rgba(255, 107, 74, 0.12);
      color: var(--coral);
      padding: 4px 10px;
      border-radius: 6px;
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      margin-bottom: 8px;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .gallery-title {
      margin: 0 0 8px;
      font-size: 1.15rem;
      color: var(--ink);
    }

    .gallery-desc {
      margin: 0;
      color: var(--muted);
      font-size: 0.88rem;
      line-height: 1.5;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }
  </style>

  <section class="gallery-hero">
    <h1>Campus Gallery & Memories</h1>
    <p>Capturing the vibrant spirit, celebrations, and achievements across our institute.</p>
  </section>

  <section class="content-section">
    <div class="gallery-grid">
      <?php foreach ($media_list as $media): ?>
        <?php 
          // Image path handling
          $img_src = 'assets/images/events/' . $media['file_url'];
          if (!file_exists($img_src)) {
              $img_src = 'assets/images/' . $media['file_url'];
              if (!file_exists($img_src)) {
                  $img_src = 'assets/images/hero1.jpg';
              }
          }
        ?>
        <article class="gallery-card">
          <img src="<?php echo htmlspecialchars($img_src); ?>" alt="<?php echo htmlspecialchars($media['title']); ?>" class="gallery-img">
          <div class="gallery-body">
            <?php if (!empty($media['event_title'])): ?>
              <span class="gallery-tag"><?php echo htmlspecialchars($media['event_title']); ?></span>
            <?php endif; ?>
            <h2 class="gallery-title"><?php echo htmlspecialchars($media['title']); ?></h2>
            <p class="gallery-desc"><?php echo htmlspecialchars($media['description'] ?? ''); ?></p>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </section>
</main>
<?php
require_once 'includes/footer.php';
?>
