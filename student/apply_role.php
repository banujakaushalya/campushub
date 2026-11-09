<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

// Check if user is logged in
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$message = "";
$message_type = "";

if ($user_id === 0) {
    echo "<script>window.location.href = '../login.php';</script>";
    exit;
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_application'])) {
    $requested_role = trim($_POST['requested_role'] ?? 'moderator');
    $reason = trim($_POST['reason'] ?? '');
    $experience = trim($_POST['experience'] ?? '');

    if (empty($reason)) {
        $message = "Please provide a reason for requesting this role.";
        $message_type = "error";
    } elseif ($requested_role !== 'moderator' && $requested_role !== 'admin') {
        $message = "Invalid role selected.";
        $message_type = "error";
    } else {
        $message = "Your application for " . ucfirst($requested_role) . " position has been submitted for administrator review!";
        $message_type = "success";
    }
}

// Previous role applications list
$my_applications = [];
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

    .apply-hero,
    .content-section {
      width: 100%;
      max-width: 1000px;
      margin-right: auto;
      margin-left: auto;
    }

    .apply-hero {
      position: relative;
      display: flex;
      flex-direction: column;
      justify-content: center;
      min-height: 180px;
      margin-top: 36px;
      padding: 32px clamp(24px, 5vw, 48px);
      box-sizing: border-box;
      border-radius: 16px;
      background: linear-gradient(115deg, rgba(23, 27, 42, 0.95), rgba(43, 53, 79, 0.85)), url('../assets/images/hero2.jpg') center / cover;
      color: #ffffff;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .apply-hero h1 {
      margin: 0 0 8px;
      font-size: clamp(1.8rem, 3.5vw, 2.4rem);
      text-shadow: #FF6B4A 1px 1px 2px;
    }

    .apply-hero p {
      max-width: 650px;
      margin: 0;
      color: rgba(255, 255, 255, 0.9);
      font-family: 'Cabin Sketch', cursive;
      font-size: 1.2rem;
    }

    .content-section {
      margin-top: 32px;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 32px;
    }

    .form-card, .history-card {
      background: #ffffff;
      border: 1px solid var(--line);
      border-radius: 12px;
      padding: 28px;
      box-shadow: 0 4px 16px rgba(23, 27, 42, 0.04);
    }

    .card-title {
      margin: 0 0 16px;
      color: var(--ink);
      font-size: 1.3rem;
      border-bottom: 1px solid var(--line);
      padding-bottom: 12px;
    }

    .form-group {
      margin-bottom: 18px;
    }

    .form-group label {
      display: block;
      margin-bottom: 6px;
      font-size: 0.9rem;
      font-weight: 700;
      color: var(--ink);
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .form-control {
      width: 100%;
      padding: 10px 14px;
      box-sizing: border-box;
      border: 1px solid #d1d5db;
      border-radius: 8px;
      font-size: 0.95rem;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    textarea.form-control {
      min-height: 100px;
      resize: vertical;
    }

    .btn-submit {
      background: var(--coral);
      color: #ffffff;
      border: none;
      padding: 12px 24px;
      font-size: 0.95rem;
      font-weight: 700;
      border-radius: 8px;
      cursor: pointer;
      width: 100%;
      transition: background 0.2s ease;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .btn-submit:hover {
      background: #e55a3a;
    }

    .alert {
      padding: 14px 18px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      font-size: 0.92rem;
    }

    .alert-success {
      background: #dcfce7;
      color: #166534;
      border: 1px solid #bbf7d0;
    }

    .alert-error {
      background: #fee2e2;
      color: #991b1b;
      border: 1px solid #fecaca;
    }

    .app-item {
      padding: 14px;
      border: 1px solid var(--line);
      border-radius: 8px;
      margin-bottom: 14px;
      background: #fafafa;
    }

    .app-item:last-child {
      margin-bottom: 0;
    }

    .app-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 8px;
    }

    .app-role {
      font-weight: 700;
      color: var(--ink);
      text-transform: capitalize;
    }

    .status-badge {
      display: inline-block;
      padding: 3px 8px;
      border-radius: 4px;
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
    }

    .status-pending { background: #fef3c7; color: #92400e; }
    .status-approved { background: #dcfce7; color: #166534; }
    .status-rejected { background: #fee2e2; color: #991b1b; }

    .app-meta {
      font-size: 0.82rem;
      color: var(--muted);
      margin: 4px 0;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .role-info-box {
      background: #f0f9ff;
      border: 1px solid #bae6fd;
      border-radius: 8px;
      padding: 12px;
      margin-bottom: 18px;
      font-size: 0.85rem;
      color: #0369a1;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    @media (max-width: 768px) {
      .content-section {
        grid-template-columns: 1fr;
      }
    }
  </style>

  <section class="apply-hero">
    <h1>Apply for Leadership Role</h1>
    <p>Step up to become an Event Moderator or Student Administrator in CampusHub.</p>
  </section>

  <section class="content-section">
    <!-- Application Form -->
    <div class="form-card">
      <h2 class="card-title">Role Application Form</h2>

      <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $message_type; ?>">
          <?php echo htmlspecialchars($message); ?>
        </div>
      <?php endif; ?>

      <div class="role-info-box">
        <strong>Role Responsibilities:</strong><br>
        • <strong>Moderator:</strong> Upload media content & manage event schedules.<br>
        • <strong>Admin:</strong> Manage student records, registrations, announcements & system content.
      </div>

      <form method="POST" action="apply_role.php">
        <div class="form-group">
          <label for="requested_role">Requested Position *</label>
          <select name="requested_role" id="requested_role" class="form-control" required>
            <option value="moderator">Moderator (Media & Event Coordination)</option>
            <option value="admin">Administrator (Full Campus Operations)</option>
          </select>
        </div>

        <div class="form-group">
          <label for="reason">Why do you want this role? *</label>
          <textarea name="reason" id="reason" class="form-control" placeholder="Describe your motivation and how you plan to contribute to campus activities..." required></textarea>
        </div>

        <div class="form-group">
          <label for="experience">Relevant Experience / Society Involvements</label>
          <textarea name="experience" id="experience" class="form-control" placeholder="List any clubs, computing societies, or event coordination experience you have..."></textarea>
        </div>

        <button type="submit" name="submit_application" class="btn-submit">Submit Application</button>
      </form>
    </div>

    <!-- Application Status / History -->
    <div class="history-card">
      <h2 class="card-title">My Application History</h2>

      <?php if (empty($my_applications)): ?>
        <div style="text-align: center; padding: 36px 0; color: var(--muted);">
          <p style="font-size: 1rem; margin-bottom: 6px; font-weight: bold; color: var(--ink);">No Applications Submitted</p>
          <p style="font-size: 0.88rem; margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
            You have not applied for any leadership positions yet. Fill out the form on the left to submit your request.
          </p>
        </div>
      <?php else: ?>
        <?php foreach ($my_applications as $app): ?>
          <div class="app-item">
            <div class="app-header">
              <span class="app-role"><?php echo htmlspecialchars($app['requested_role']); ?> Role</span>
              <span class="status-badge status-<?php echo htmlspecialchars($app['status']); ?>">
                <?php echo htmlspecialchars($app['status']); ?>
              </span>
            </div>
            <p class="app-meta"><strong>Applied on:</strong> <?php echo date('M d, Y', strtotime($app['applied_at'])); ?></p>
            <p class="app-meta"><strong>Reason:</strong> <?php echo htmlspecialchars($app['reason']); ?></p>
            <?php if (!empty($app['admin_notes'])): ?>
              <p class="app-meta" style="color: #0369a1; background: #e0f2fe; padding: 6px 8px; border-radius: 4px; margin-top: 6px;">
                <strong>Admin Feedback:</strong> <?php echo htmlspecialchars($app['admin_notes']); ?>
              </p>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </section>
</main>
<?php
require_once '../includes/footer.php';
?>
