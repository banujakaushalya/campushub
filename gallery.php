<?php
require_once 'includes/header.php';
require_once 'includes/db.php';

// Filter & Sort parameters
$search = trim($_GET['q'] ?? '');
$event_filter = isset($_GET['event']) && $_GET['event'] !== '' ? (int)$_GET['event'] : 0;
$type_filter = trim($_GET['type'] ?? 'all');
$tag_filter = trim($_GET['tag'] ?? '');
$sort = trim($_GET['sort'] ?? 'newest');

// Fetch events list for the side panel filter
$events_list = [];
$e_res = Database::search("SELECT event_id, title FROM events ORDER BY title ASC");
if ($e_res && $e_res->num_rows > 0) {
    while ($row = $e_res->fetch_assoc()) {
        $events_list[] = $row;
    }
}

// Fetch all existing tags for filter pills
$available_tags = [];
$tag_res = Database::search("SELECT meta_data FROM media WHERE meta_data IS NOT NULL AND meta_data != ''");
if ($tag_res && $tag_res->num_rows > 0) {
    while ($row = $tag_res->fetch_assoc()) {
        $meta = json_decode($row['meta_data'], true);
        if (isset($meta['tags']) && is_array($meta['tags'])) {
            foreach ($meta['tags'] as $t) {
                $clean_t = ltrim(trim($t), '#');
                if (!empty($clean_t) && !in_array($clean_t, $available_tags)) {
                    $available_tags[] = $clean_t;
                }
            }
        }
    }
}

// Build filtered query
$sql = "SELECT m.*, e.title as event_title, e.event_date, u.first_name, u.last_name 
        FROM media m 
        LEFT JOIN events e ON m.events_event_id = e.event_id 
        LEFT JOIN users u ON m.users_user_id = u.user_id 
        WHERE 1=1";
$types = "";
$params = [];

if (!empty($search)) {
    $sql .= " AND (m.title LIKE ? OR m.description LIKE ? OR e.title LIKE ? OR m.meta_data LIKE ?)";
    $types .= "ssss";
    $s_term = "%$search%";
    $params[] = $s_term;
    $params[] = $s_term;
    $params[] = $s_term;
    $params[] = $s_term;
}

if ($event_filter > 0) {
    $sql .= " AND m.events_event_id = ?";
    $types .= "i";
    $params[] = $event_filter;
}

if ($type_filter !== 'all' && ($type_filter === 'photo' || $type_filter === 'video')) {
    $sql .= " AND m.media_type = ?";
    $types .= "s";
    $params[] = $type_filter;
}

if (!empty($tag_filter)) {
    $sql .= " AND m.meta_data LIKE ?";
    $types .= "s";
    $params[] = "%" . $tag_filter . "%";
}

// Sorting logic
switch ($sort) {
    case 'oldest':
        $sql .= " ORDER BY m.created_at ASC";
        break;
    case 'title_asc':
        $sql .= " ORDER BY m.title ASC";
        break;
    case 'title_desc':
        $sql .= " ORDER BY m.title DESC";
        break;
    case 'event_title':
        $sql .= " ORDER BY e.title ASC, m.created_at DESC";
        break;
    case 'newest':
    default:
        $sql .= " ORDER BY m.created_at DESC";
        break;
}

$media_list = [];
if (!empty($types)) {
    $res = Database::search($sql, $types, $params);
} else {
    $res = Database::search($sql);
}

if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $media_list[] = $row;
    }
}

// Total count check
$total_count_res = Database::search("SELECT COUNT(*) as c FROM media");
$total_in_db = 0;
if ($total_count_res && $row = $total_count_res->fetch_assoc()) {
    $total_in_db = (int)$row['c'];
}

$has_active_filters = (!empty($search) || $event_filter > 0 || $type_filter !== 'all' || !empty($tag_filter) || $sort !== 'newest');
?>
<main class="page-container">
  <style>
    .page-container {
      --ink: #171b2a;
      --muted: #687083;
      --line: #e4e7ed;
      --coral: #FF6B4A;
      --coral-light: rgba(255, 107, 74, 0.12);
      width: 100%;
      padding: 0 24px 64px;
      box-sizing: border-box;
    }

    .gallery-hero,
    .content-layout {
      width: 100%;
      max-width: 1240px;
      margin-right: auto;
      margin-left: auto;
    }

    .gallery-hero {
      position: relative;
      display: flex;
      flex-direction: column;
      justify-content: center;
      min-height: 200px;
      margin-top: 32px;
      padding: 36px clamp(24px, 5vw, 64px);
      box-sizing: border-box;
      border-radius: 16px;
      background: linear-gradient(115deg, rgba(23, 27, 42, 0.94), rgba(43, 53, 79, 0.82)), url('assets/images/events/tech_coding_work_shop.jpg') center / cover;
      color: #ffffff;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .gallery-hero h1 {
      margin: 0 0 8px;
      font-size: clamp(2rem, 4vw, 2.6rem);
      line-height: 1.1;
      text-shadow: #FF6B4A 1px 1px 2px;
    }

    .gallery-hero p {
      max-width: 650px;
      margin: 0;
      color: rgba(255, 255, 255, 0.88);
      font-family: 'Cabin Sketch', cursive;
      font-size: 1.25rem;
    }

    .hero-actions {
      margin-top: 18px;
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
    }

    .btn-hero-upload {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: var(--coral);
      color: #ffffff;
      padding: 9px 20px;
      border-radius: 8px;
      font-weight: 700;
      text-decoration: none;
      font-size: 0.9rem;
      transition: all 0.2s;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .btn-hero-upload:hover {
      background: #e55a3a;
      transform: translateY(-1px);
    }

    /* 2-Column Responsive Layout */
    .content-layout {
      margin-top: 32px;
      display: grid;
      grid-template-columns: 290px 1fr;
      gap: 28px;
      align-items: start;
    }

    /* Side Panel Styling */
    .side-panel {
      background: #ffffff;
      border: 1px solid var(--line);
      border-radius: 14px;
      padding: 24px;
      box-shadow: 0 4px 18px rgba(23, 27, 42, 0.04);
      position: sticky;
      top: 24px;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .side-panel-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      padding-bottom: 12px;
      border-bottom: 1px solid #f0f2f6;
    }

    .side-panel-header h3 {
      margin: 0;
      font-size: 1.1rem;
      color: var(--ink);
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .filter-group {
      margin-bottom: 22px;
    }

    .filter-group label {
      display: block;
      margin-bottom: 8px;
      font-size: 0.82rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: var(--muted);
    }

    .form-control-filter {
      width: 100%;
      padding: 10px 12px;
      box-sizing: border-box;
      border: 1px solid #d1d5db;
      border-radius: 8px;
      font-size: 0.92rem;
      color: var(--ink);
      background-color: #fdfdfd;
      transition: border-color 0.2s;
      font-family: inherit;
    }

    .form-control-filter:focus {
      outline: none;
      border-color: var(--coral);
    }

    .media-type-pills {
      display: flex;
      gap: 6px;
    }

    .type-pill {
      flex: 1;
      text-align: center;
      padding: 8px 6px;
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 6px;
      font-size: 0.82rem;
      font-weight: 600;
      color: var(--muted);
      cursor: pointer;
      text-decoration: none;
      transition: all 0.2s;
    }

    .type-pill:hover {
      border-color: var(--coral);
      color: var(--coral);
    }

    .type-pill.active {
      background: var(--coral-light);
      border-color: var(--coral);
      color: var(--coral);
      font-weight: 700;
    }

    .tag-cloud {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
    }

    .tag-pill {
      display: inline-block;
      padding: 4px 10px;
      background: #f1f5f9;
      border: 1px solid #e2e8f0;
      border-radius: 9999px;
      font-size: 0.78rem;
      font-weight: 600;
      color: #475569;
      text-decoration: none;
      transition: all 0.15s;
    }

    .tag-pill:hover {
      background: var(--coral-light);
      color: var(--coral);
      border-color: var(--coral);
    }

    .tag-pill.active {
      background: var(--coral);
      color: #ffffff;
      border-color: var(--coral);
    }

    .btn-apply-filters {
      width: 100%;
      background: var(--ink);
      color: #ffffff;
      border: none;
      padding: 11px;
      border-radius: 8px;
      font-size: 0.92rem;
      font-weight: 700;
      cursor: pointer;
      transition: background 0.2s;
      margin-top: 6px;
    }

    .btn-apply-filters:hover {
      background: #2b354f;
    }

    .btn-reset-filters {
      display: block;
      width: 100%;
      text-align: center;
      margin-top: 10px;
      padding: 8px;
      color: var(--muted);
      font-size: 0.84rem;
      font-weight: 600;
      text-decoration: none;
      box-sizing: border-box;
      border-radius: 6px;
      transition: color 0.2s;
    }

    .btn-reset-filters:hover {
      color: #d93838;
      background: #fff1f1;
    }

    /* Gallery Main Area */
    .gallery-main {
      width: 100%;
    }

    .gallery-top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      background: #ffffff;
      border: 1px solid var(--line);
      border-radius: 10px;
      padding: 12px 18px;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .results-count {
      font-size: 0.9rem;
      color: var(--muted);
      font-weight: 600;
    }

    .results-count strong {
      color: var(--ink);
    }

    .active-badge-list {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
      align-items: center;
    }

    .active-badge {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      background: var(--coral-light);
      color: var(--coral);
      padding: 3px 8px;
      border-radius: 6px;
      font-size: 0.78rem;
      font-weight: 600;
    }

    .gallery-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 24px;
    }

    .gallery-card {
      background: #ffffff;
      border: 1px solid var(--line);
      border-radius: 14px;
      overflow: hidden;
      box-shadow: 0 4px 16px rgba(23, 27, 42, 0.04);
      transition: transform 0.2s, box-shadow 0.2s;
      display: flex;
      flex-direction: column;
      cursor: pointer;
    }

    .gallery-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 28px rgba(23, 27, 42, 0.1);
    }

    .gallery-img-wrap {
      position: relative;
      width: 100%;
      height: 220px;
      background-color: #f1f5f9;
      overflow: hidden;
    }

    .gallery-img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
      transition: transform 0.3s ease;
    }

    .gallery-card:hover .gallery-img {
      transform: scale(1.04);
    }

    .media-type-badge {
      position: absolute;
      top: 12px;
      right: 12px;
      background: rgba(23, 27, 42, 0.8);
      backdrop-filter: blur(4px);
      color: #ffffff;
      padding: 3px 8px;
      border-radius: 6px;
      font-size: 0.72rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .gallery-body {
      padding: 18px 20px;
      display: flex;
      flex-direction: column;
      flex: 1;
    }

    .gallery-tag {
      display: inline-block;
      align-self: flex-start;
      background: var(--coral-light);
      color: var(--coral);
      padding: 4px 10px;
      border-radius: 6px;
      font-size: 0.75rem;
      font-weight: 700;
      margin-bottom: 8px;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .gallery-title {
      margin: 0 0 6px;
      font-size: 1.12rem;
      color: var(--ink);
      font-weight: 700;
    }

    .gallery-desc {
      margin: 0 0 12px;
      color: var(--muted);
      font-size: 0.88rem;
      line-height: 1.45;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      flex: 1;
    }

    .gallery-meta-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-top: 1px solid #f0f2f6;
      padding-top: 10px;
      margin-top: auto;
      font-size: 0.78rem;
      color: #94a3b8;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .gallery-card-tags {
      display: flex;
      flex-wrap: wrap;
      gap: 4px;
      margin-top: 8px;
    }

    .gallery-card-tag {
      font-size: 0.72rem;
      color: #64748b;
      background: #f8fafc;
      padding: 2px 6px;
      border-radius: 4px;
    }

    /* Lightbox Modal */
    .lightbox-modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(15, 23, 42, 0.92);
      backdrop-filter: blur(8px);
      z-index: 99999;
      justify-content: center;
      align-items: center;
      padding: 24px;
      box-sizing: border-box;
    }

    .lightbox-content {
      max-width: 900px;
      width: 100%;
      background: #ffffff;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
      position: relative;
      animation: zoomIn 0.2s ease-out;
    }

    @keyframes zoomIn {
      from { transform: scale(0.95); opacity: 0; }
      to { transform: scale(1); opacity: 1; }
    }

    .lightbox-img-box {
      width: 100%;
      max-height: 520px;
      background: #0f172a;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .lightbox-img-box img {
      max-width: 100%;
      max-height: 520px;
      object-fit: contain;
      display: block;
    }

    .lightbox-details {
      padding: 24px;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .lightbox-close-btn {
      position: absolute;
      top: 14px;
      right: 14px;
      background: rgba(23, 27, 42, 0.7);
      color: #ffffff;
      border: none;
      width: 36px;
      height: 36px;
      border-radius: 50%;
      font-size: 1.2rem;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background 0.2s;
    }

    .lightbox-close-btn:hover {
      background: var(--coral);
    }

    /* Responsive */
    @media (max-width: 880px) {
      .content-layout {
        grid-template-columns: 1fr;
      }

      .side-panel {
        position: static;
      }
    }
  </style>

  <section class="gallery-hero">
    <h1>Campus Gallery & Memories</h1>
    <p>Explore event highlights, student showcases, workshops, and sports championships across campus.</p>
    
    <div class="hero-actions">
      <?php if (isset($_SESSION['role']) && ($_SESSION['role'] === 'super_admin' || $_SESSION['role'] === 'admin' || $_SESSION['role'] === 'moderator')): ?>
        <a href="admin/upload_media.php" class="btn-hero-upload">
          <span>📸</span> Upload Media (Admin/Mod)
        </a>
      <?php elseif (isset($_SESSION['user_id'])): ?>
        <a href="student/upload_photo.php" class="btn-hero-upload">
          <span>📷</span> Share Event Photo
        </a>
      <?php endif; ?>
    </div>
  </section>

  <div class="content-layout">
    <!-- Left Side Panel: Filtering & Sorting -->
    <aside class="side-panel">
      <div class="side-panel-header">
        <h3><span>🔍</span> Filter & Sort</h3>
        <?php if ($has_active_filters): ?>
          <a href="gallery.php" style="font-size: 0.78rem; color: var(--coral); font-weight: 700; text-decoration: none;">Reset All</a>
        <?php endif; ?>
      </div>

      <form method="GET" action="gallery.php" id="galleryFilterForm">
        <!-- 1. Search Query -->
        <div class="filter-group">
          <label for="q">Keyword Search</label>
          <input type="text" id="q" name="q" class="form-control-filter" placeholder="Search title, details, tags..." value="<?php echo htmlspecialchars($search); ?>">
        </div>

        <!-- 2. Sort Selection -->
        <div class="filter-group">
          <label for="sort">Sort Gallery</label>
          <select id="sort" name="sort" class="form-control-filter" onchange="this.form.submit()">
            <option value="newest" <?php echo ($sort === 'newest') ? 'selected' : ''; ?>>Newest Uploads First</option>
            <option value="oldest" <?php echo ($sort === 'oldest') ? 'selected' : ''; ?>>Oldest Uploads First</option>
            <option value="title_asc" <?php echo ($sort === 'title_asc') ? 'selected' : ''; ?>>Title (A to Z)</option>
            <option value="title_desc" <?php echo ($sort === 'title_desc') ? 'selected' : ''; ?>>Title (Z to A)</option>
            <option value="event_title" <?php echo ($sort === 'event_title') ? 'selected' : ''; ?>>By Event Title</option>
          </select>
        </div>

        <!-- 3. Event Filter -->
        <div class="filter-group">
          <label for="event">Associated Event</label>
          <select id="event" name="event" class="form-control-filter" onchange="this.form.submit()">
            <option value="">All Events & General</option>
            <?php foreach ($events_list as $ev): ?>
              <option value="<?php echo $ev['event_id']; ?>" <?php echo ($event_filter === (int)$ev['event_id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($ev['title']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- 4. Media Type Filter -->
        <div class="filter-group">
          <label>Media Format</label>
          <input type="hidden" name="type" id="typeInput" value="<?php echo htmlspecialchars($type_filter); ?>">
          <div class="media-type-pills">
            <button type="button" class="type-pill <?php echo ($type_filter === 'all') ? 'active' : ''; ?>" onclick="setMediaType('all')">All</button>
            <button type="button" class="type-pill <?php echo ($type_filter === 'photo') ? 'active' : ''; ?>" onclick="setMediaType('photo')">Photos</button>
            <button type="button" class="type-pill <?php echo ($type_filter === 'video') ? 'active' : ''; ?>" onclick="setMediaType('video')">Videos</button>
          </div>
        </div>

        <!-- 5. Hashtag Filter -->
        <?php if (!empty($available_tags)): ?>
          <div class="filter-group">
            <label>Popular Topics & Tags</label>
            <input type="hidden" name="tag" id="tagInput" value="<?php echo htmlspecialchars($tag_filter); ?>">
            <div class="tag-cloud">
              <?php foreach ($available_tags as $tg): ?>
                <button type="button" class="tag-pill <?php echo ($tag_filter === $tg) ? 'active' : ''; ?>" onclick="setTagFilter('<?php echo htmlspecialchars($tg); ?>')">
                  #<?php echo htmlspecialchars($tg); ?>
                </button>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>

        <button type="submit" class="btn-apply-filters">Apply Filters</button>
        <?php if ($has_active_filters): ?>
          <a href="gallery.php" class="btn-reset-filters">Clear All Active Filters</a>
        <?php endif; ?>
      </form>
    </aside>

    <!-- Right Side: Gallery Results & Cards -->
    <section class="gallery-main">
      <div class="gallery-top-bar">
        <div class="results-count">
          Showing <strong><?php echo count($media_list); ?></strong> of <?php echo $total_in_db; ?> media items
        </div>

        <?php if ($has_active_filters): ?>
          <div class="active-badge-list">
            <?php if (!empty($search)): ?>
              <span class="active-badge">Keyword: "<?php echo htmlspecialchars($search); ?>"</span>
            <?php endif; ?>
            <?php if ($event_filter > 0): ?>
              <span class="active-badge">Event Filtered</span>
            <?php endif; ?>
            <?php if ($type_filter !== 'all'): ?>
              <span class="active-badge"><?php echo ucfirst(htmlspecialchars($type_filter)); ?>s</span>
            <?php endif; ?>
            <?php if (!empty($tag_filter)): ?>
              <span class="active-badge">#<?php echo htmlspecialchars($tag_filter); ?></span>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>

      <?php if (!empty($media_list)): ?>
        <div class="gallery-grid">
          <?php foreach ($media_list as $media): ?>
            <?php 
              // Resolve image source
              $img_src = 'assets/images/events/' . $media['file_url'];
              if (!file_exists($img_src)) {
                  $img_src = 'assets/images/' . $media['file_url'];
                  if (!file_exists($img_src)) {
                      $img_src = 'assets/images/hero1.jpg';
                  }
              }

              // Extract tags
              $card_tags = [];
              if (!empty($media['meta_data'])) {
                  $meta = json_decode($media['meta_data'], true);
                  if (isset($meta['tags']) && is_array($meta['tags'])) {
                      $card_tags = $meta['tags'];
                  }
              }
            ?>
            <article class="gallery-card" onclick="openLightbox('<?php echo htmlspecialchars($img_src); ?>', '<?php echo htmlspecialchars(addslashes($media['title'])); ?>', '<?php echo htmlspecialchars(addslashes($media['description'] ?? '')); ?>', '<?php echo htmlspecialchars(addslashes($media['event_title'] ?? 'Campus Event')); ?>')">
              <div class="gallery-img-wrap">
                <img src="<?php echo htmlspecialchars($img_src); ?>" alt="<?php echo htmlspecialchars($media['title']); ?>" class="gallery-img" loading="lazy">
                <span class="media-type-badge"><?php echo htmlspecialchars($media['media_type']); ?></span>
              </div>
              <div class="gallery-body">
                <?php if (!empty($media['event_title'])): ?>
                  <span class="gallery-tag"><?php echo htmlspecialchars($media['event_title']); ?></span>
                <?php endif; ?>
                <h2 class="gallery-title"><?php echo htmlspecialchars($media['title']); ?></h2>
                <p class="gallery-desc"><?php echo htmlspecialchars($media['description'] ?? ''); ?></p>
                
                <?php if (!empty($card_tags)): ?>
                  <div class="gallery-card-tags">
                    <?php foreach ($card_tags as $ct): ?>
                      <span class="gallery-card-tag"><?php echo htmlspecialchars($ct); ?></span>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>

                <div class="gallery-meta-footer">
                  <span><?php echo date('M d, Y', strtotime($media['created_at'])); ?></span>
                  <span><?php echo htmlspecialchars($media['first_name'] ? ($media['first_name'] . ' ' . $media['last_name']) : 'CampusHub'); ?></span>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div style="background: #ffffff; border: 1px solid var(--line); border-radius: 12px; padding: 48px; text-align: center; color: var(--muted); font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
          <div style="font-size: 2.5rem; margin-bottom: 12px;">🖼️</div>
          <h2 style="color: var(--ink); margin-bottom: 8px;">No Media Items Found</h2>
          <p style="margin: 0 0 18px;">No photos or videos match your selected filter criteria.</p>
          <a href="gallery.php" style="display: inline-block; background: var(--coral); color: #ffffff; padding: 8px 18px; border-radius: 6px; font-weight: 700; text-decoration: none; font-size: 0.9rem;">Reset All Filters</a>
        </div>
      <?php endif; ?>
    </section>
  </div>
</main>

<!-- Lightbox Modal Component -->
<div id="galleryLightbox" class="lightbox-modal" onclick="closeLightbox(event)">
  <div class="lightbox-content" onclick="event.stopPropagation()">
    <button class="lightbox-close-btn" onclick="closeLightbox()">&times;</button>
    <div class="lightbox-img-box">
      <img id="lightboxImg" src="" alt="Media Preview">
    </div>
    <div class="lightbox-details">
      <span id="lightboxEvent" class="gallery-tag" style="margin-bottom: 6px;"></span>
      <h2 id="lightboxTitle" style="margin: 0 0 8px; font-size: 1.3rem; color: var(--ink);"></h2>
      <p id="lightboxDesc" style="margin: 0; color: var(--muted); font-size: 0.92rem; line-height: 1.5;"></p>
    </div>
  </div>
</div>

<script>
  function setMediaType(type) {
    document.getElementById('typeInput').value = type;
    document.getElementById('galleryFilterForm').submit();
  }

  function setTagFilter(tag) {
    var tagInput = document.getElementById('tagInput');
    if (tagInput.value === tag) {
      tagInput.value = '';
    } else {
      tagInput.value = tag;
    }
    document.getElementById('galleryFilterForm').submit();
  }

  function openLightbox(src, title, desc, eventTitle) {
    document.getElementById('lightboxImg').src = src;
    document.getElementById('lightboxTitle').textContent = title;
    document.getElementById('lightboxDesc').textContent = desc;
    var eventElem = document.getElementById('lightboxEvent');
    if (eventTitle) {
      eventElem.textContent = eventTitle;
      eventElem.style.display = 'inline-block';
    } else {
      eventElem.style.display = 'none';
    }
    document.getElementById('galleryLightbox').style.display = 'flex';
  }

  function closeLightbox(event) {
    document.getElementById('galleryLightbox').style.display = 'none';
  }

  // Close modal on ESC key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeLightbox();
    }
  });
</script>

<?php
require_once 'includes/footer.php';
?>
