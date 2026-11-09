<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$message = "";

// Handle registration cancellation
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['cancel_reg_id'])) {
    $reg_id_to_cancel = (int)$_POST['cancel_reg_id'];
    $cancel_res = Database::iud("DELETE FROM registrations WHERE registration_id = ? AND users_user_id = ?", "ii", [$reg_id_to_cancel, $user_id]);
    if ($cancel_res) {
        $message = "Registration successfully cancelled.";
    }
}

// Fetch user registrations with joined event details
$registrations = [];
$sql = "SELECT r.registration_id, r.reg_date, 
               e.event_id, e.title, e.description, e.event_date, e.event_start_time, e.event_end_time, e.img_path, e.meta_data, e.status as event_status, e.seats
        FROM registrations r
        JOIN events e ON r.events_event_id = e.event_id
        WHERE r.users_user_id = ?
        ORDER BY e.event_date DESC";

$res = Database::search($sql, "i", [$user_id]);

if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $meta = !empty($row['meta_data']) ? json_decode($row['meta_data'], true) : [];
        $row['category'] = $meta['category'] ?? ($row['category'] ?? 'Event');
        $row['club_name'] = $meta['club_name'] ?? ($row['club_name'] ?? 'CampusHub');
        $row['location'] = $meta['location'] ?? ($row['location'] ?? 'Campus Grounds');
        $row['tags'] = $meta['tags'] ?? [];
        $row['event_time'] = !empty($row['event_start_time']) ? $row['event_start_time'] : '09:00:00';
        $row['status'] = 'confirmed';
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

    .reg-hero,
    .content-section {
      width: 100%;
      max-width: 1200px;
      margin-right: auto;
      margin-left: auto;
    }

    .reg-hero {
      position: relative;
      display: flex;
      flex-direction: column;
      justify-content: center;
      min-height: 220px;
      margin-top: 36px;
      padding: 40px clamp(24px, 5vw, 64px);
      box-sizing: border-box;
      border-radius: 16px;
      background: linear-gradient(115deg, rgba(23, 27, 42, 0.92), rgba(43, 53, 79, 0.8)), url('../assets/images/hero2.jpg') center / cover;
      color: #ffffff;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .reg-hero h1 {
      margin: 0 0 8px;
      font-size: clamp(2rem, 4vw, 2.8rem);
      line-height: 1.1;
      text-shadow: #FF6B4A 1px 1px 2px;
    }

    .reg-hero p {
      max-width: 600px;
      margin: 0;
      color: rgba(255, 255, 255, 0.85);
      font-family: 'Cabin Sketch', cursive;
      font-size: 1.3rem;
    }

    .content-section {
      margin-top: 36px;
    }

    .alert {
      padding: 14px 18px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      font-size: 0.92rem;
      font-weight: 600;
      background-color: #e6f7ec;
      color: #1b7a3e;
      border: 1px solid #c2ebd0;
    }

    .reg-list {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .pass-card {
      background: #ffffff;
      border: 1px solid var(--line);
      border-radius: 14px;
      padding: 24px 28px;
      box-shadow: 0 8px 24px rgba(23, 27, 42, 0.05);
      display: grid;
      grid-template-columns: 3fr 1.2fr;
      gap: 24px;
      align-items: center;
      border-left: 6px solid var(--coral);
    }

    .pass-info h2 {
      margin: 0 0 6px;
      color: var(--ink);
      font-size: 1.25rem;
    }

    .pass-meta {
      display: flex;
      flex-wrap: wrap;
      gap: 16px;
      margin: 10px 0;
      color: #4a5568;
      font-size: 0.9rem;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .pass-meta span {
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .pass-notes {
      font-size: 0.88rem;
      color: var(--muted);
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      margin: 8px 0 0;
      font-style: italic;
    }

    .pass-actions {
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      gap: 12px;
    }

    .badge-status {
      padding: 6px 14px;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .badge-confirmed {
      background: #e6f7ec;
      color: #1b7a3e;
      border: 1px solid #c2ebd0;
    }

    .badge-cancelled {
      background: #ffeaea;
      color: #c92a2a;
      border: 1px solid #fcc2c2;
    }

    .btn-cancel {
      background: none;
      border: 1px solid #e2e8f0;
      color: #718096;
      padding: 8px 14px;
      border-radius: 6px;
      cursor: pointer;
      font-size: 0.82rem;
      font-weight: bold;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      transition: all 0.2s;
    }

    .btn-cancel:hover {
      background: #ffeaea;
      color: #c92a2a;
      border-color: #fcc2c2;
    }

    @media (max-width: 768px) {
      .pass-card {
        grid-template-columns: 1fr;
      }
      .pass-actions {
        align-items: flex-start;
      }
    }
  </style>

  <section class="reg-hero">
    <h1>My Registered Events</h1>
    <p>View your confirmed entry passes and manage campus event participation.</p>
  </section>

  <section class="content-section">
    <?php if (!empty($message)): ?>
      <div class="alert"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="reg-list">
      <?php if (empty($registrations)): ?>
        <div style="background: #ffffff; padding: 48px; text-align: center; border-radius: 12px; border: 1px solid var(--line);">
          <h2>No Active Event Registrations</h2>
          <p style="color: var(--muted); font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; margin-bottom: 20px;">
            You have not registered for any upcoming events yet.
          </p>
          <a href="../events.php" style="display: inline-block; background: var(--coral); color: #fff; padding: 12px 24px; border-radius: 8px; font-weight: bold; text-decoration: none;">Browse Events Calendar</a>
        </div>
      <?php else: ?>
        <?php foreach ($registrations as $reg): ?>
          <div class="pass-card">
            <div class="pass-info">
              <h2><?php echo htmlspecialchars($reg['title']); ?></h2>
              <div class="pass-meta">
                <span>📅 <strong><?php echo date('M d, Y', strtotime($reg['event_date'])); ?></strong> (<?php echo date('g:i A', strtotime($reg['event_time'])); ?>)</span>
                <span>📍 <?php echo htmlspecialchars($reg['location']); ?></span>
                <span>🏷️ <?php echo htmlspecialchars($reg['category'] ?? 'Event'); ?></span>
              </div>
              <?php if (!empty($reg['notes'])): ?>
                <p class="pass-notes">Notes: "<?php echo htmlspecialchars($reg['notes']); ?>"</p>
              <?php endif; ?>
            </div>

            <div class="pass-actions">
              <span class="badge-status <?php echo ($reg['status'] === 'cancelled') ? 'badge-cancelled' : 'badge-confirmed'; ?>">
                ● <?php echo ucfirst(htmlspecialchars($reg['status'])); ?>
              </span>

              <?php if ($reg['status'] !== 'cancelled'): ?>
                <form method="POST" action="my_registrations.php" onsubmit="return confirm('Are you sure you want to cancel this event registration?');">
                  <input type="hidden" name="cancel_reg_id" value="<?php echo htmlspecialchars($reg['registration_id']); ?>">
                  <button type="submit" class="btn-cancel">Cancel Registration</button>
                </form>
              <?php endif; ?>
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
