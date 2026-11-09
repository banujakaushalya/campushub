<?php
require_once '../includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$curr_role = $_SESSION['role'] ?? 'guest';
if ($curr_role !== 'super_admin' && $curr_role !== 'admin' && $curr_role !== 'moderator') {
    header("Location: ../student/dashboard.php");
    exit;
}

// Check if download parameter is set
if (isset($_GET['download']) && $_GET['download'] === 'xml') {
    header('Content-Type: application/xml; charset=utf-8');
    header('Content-Disposition: attachment; filename="events_export_' . date('Ymd_His') . '.xml"');

    $res = Database::search("SELECT * FROM events ORDER BY event_date ASC");

    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><events></events>');

    if ($res && $res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            $eventNode = $xml->addChild('event');
            $eventNode->addChild('event_id', (string)$row['event_id']);
            $eventNode->addChild('title', htmlspecialchars((string)$row['title']));
            $eventNode->addChild('description', htmlspecialchars((string)$row['description']));
            $eventNode->addChild('event_date', (string)$row['event_date']);
            $eventNode->addChild('event_start_time', (string)($row['event_start_time'] ?? ''));
            $eventNode->addChild('event_end_time', (string)($row['event_end_time'] ?? ''));
            $eventNode->addChild('img_path', htmlspecialchars((string)$row['img_path']));
            $eventNode->addChild('status', (string)$row['status']);
            $eventNode->addChild('seats', (string)($row['seats'] ?? '100'));

            // Parse metadata
            $metaNode = $eventNode->addChild('meta_data');
            $meta = !empty($row['meta_data']) ? json_decode($row['meta_data'], true) : [];
            if (is_array($meta)) {
                $metaNode->addChild('category', htmlspecialchars((string)($meta['category'] ?? '')));
                $metaNode->addChild('location', htmlspecialchars((string)($meta['location'] ?? '')));
                $metaNode->addChild('club_name', htmlspecialchars((string)($meta['club_name'] ?? '')));
                $tagsNode = $metaNode->addChild('tags');
                if (isset($meta['tags']) && is_array($meta['tags'])) {
                    foreach ($meta['tags'] as $tag) {
                        $tagsNode->addChild('tag', htmlspecialchars((string)$tag));
                    }
                }
            }
        }
    }

    echo $xml->asXML();
    exit;
}

require_once '../includes/header.php';

$res = Database::search("SELECT * FROM events ORDER BY event_date ASC");
$events = [];
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
    .export-card {
      max-width: 800px;
      margin: 40px auto 0;
      background: #ffffff;
      border: 1px solid var(--line);
      border-radius: 16px;
      padding: 36px;
      box-shadow: 0 10px 30px rgba(23, 27, 42, 0.08);
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }
    .btn-export {
      display: inline-block;
      background: var(--coral);
      color: #fff;
      padding: 12px 24px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: bold;
      font-size: 1rem;
      box-shadow: 0 4px 12px rgba(255, 107, 74, 0.3);
      margin-top: 16px;
    }
    .btn-export:hover {
      background: #e55a3a;
    }
  </style>

  <div class="export-card">
    <h1 style="margin: 0 0 10px; color: var(--ink);">Export Events (XML)</h1>
    <p style="color: var(--muted); margin: 0 0 20px;">
      Download campus events data with complete metadata (category, location, timing, seats, and tags) formatted in XML.
    </p>
    <p>Total Events Available for Export: <strong><?php echo count($events); ?></strong></p>
    <a href="export_events.php?download=xml" class="btn-export">Download XML Export</a>
    <div style="margin-top: 24px;">
      <a href="manage_events.php" style="color: var(--ink); text-decoration: none; font-weight: bold;">&larr; Return to Event Management</a>
    </div>
  </div>
</main>
<?php require_once '../includes/footer.php'; ?>
