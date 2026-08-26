<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 2;
$success_msg = "";
$error_msg = "";

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
    $last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $gender = isset($_POST['gender']) ? trim($_POST['gender']) : 'male';
    $student_id = isset($_POST['student_id']) ? trim($_POST['student_id']) : '';
    $institution = isset($_POST['institution']) ? trim($_POST['institution']) : '';

    if (empty($first_name) || empty($last_name) || empty($phone)) {
        $error_msg = "First Name, Last Name, and Phone are required fields.";
    } else {
        $updated = Database::iud(
            "UPDATE users SET first_name = ?, last_name = ?, phone = ?, gender = ?, student_id = ?, institution = ? WHERE user_id = ?",
            "ssssssi",
            [$first_name, $last_name, $phone, $gender, $student_id, $institution, $user_id]
        );

        if ($updated) {
            $_SESSION['user_name'] = $first_name . ' ' . $last_name;
            $success_msg = "Profile updated successfully!";
        } else {
            $error_msg = "Could not update profile. Please verify your data.";
        }
    }
}

// Fetch current user details
$user = null;
$res = Database::search("SELECT * FROM users WHERE user_id = ? LIMIT 1", "i", [$user_id]);
if ($res && $res->num_rows > 0) {
    $user = $res->fetch_assoc();
} else {
    $user = [
        'first_name' => '',
        'last_name' => '',
        'email' => '',
        'phone' => '',
        'gender' => 'male',
        'student_id' => '',
        'institution' => ''
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

    .profile-container {
      width: 100%;
      max-width: 720px;
      margin: 40px auto 0;
    }

    .profile-card {
      background: #ffffff;
      border: 1px solid var(--line);
      border-radius: 16px;
      padding: 36px;
      box-shadow: 0 10px 30px rgba(23, 27, 42, 0.08);
    }

    .profile-card h1 {
      margin: 0 0 8px;
      color: var(--ink);
      font-size: 1.6rem;
    }

    .profile-card p.subtitle {
      margin: 0 0 24px;
      color: var(--muted);
      font-family: 'Cabin Sketch', cursive;
      font-size: 1.15rem;
    }

    .form-row-2 {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 700;
      color: var(--ink);
      font-size: 0.9rem;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .form-control {
      width: 100%;
      padding: 12px 14px;
      border: 1.5px solid var(--line);
      border-radius: 8px;
      font-size: 0.95rem;
      color: var(--ink);
      box-sizing: border-box;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .form-control:focus {
      outline: none;
      border-color: var(--coral);
      box-shadow: 0 0 0 3px rgba(255, 107, 74, 0.15);
    }

    .form-control:disabled {
      background-color: #f7f7fa;
      color: #8c93a4;
      cursor: not-allowed;
    }

    .btn-save {
      width: 100%;
      background-color: var(--coral);
      color: #ffffff;
      padding: 14px;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      font-size: 1rem;
      cursor: pointer;
      box-shadow: 0 4px 12px rgba(255, 107, 74, 0.35);
      transition: background-color 0.2s;
    }

    .btn-save:hover {
      background-color: #e55a3a;
    }

    .alert {
      padding: 14px 18px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      font-size: 0.92rem;
      font-weight: 600;
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

    @media (max-width: 600px) {
      .form-row-2 {
        grid-template-columns: 1fr;
      }
    }
  </style>

  <div class="profile-container">
    <div class="profile-card">
      <h1>My Student Profile</h1>
      <p class="subtitle">Manage your personal information and student credentials.</p>

      <?php if (!empty($success_msg)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
      <?php endif; ?>

      <?php if (!empty($error_msg)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error_msg); ?></div>
      <?php endif; ?>

      <form action="profile.php" method="POST">
        <div class="form-row-2">
          <div class="form-group">
            <label for="first_name">First Name *</label>
            <input type="text" id="first_name" name="first_name" class="form-control" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
          </div>
          <div class="form-group">
            <label for="last_name">Last Name *</label>
            <input type="text" id="last_name" name="last_name" class="form-control" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
          </div>
        </div>

        <div class="form-group">
          <label>Email Address (Account Identifier)</label>
          <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
        </div>

        <div class="form-row-2">
          <div class="form-group">
            <label for="phone">Phone Number *</label>
            <input type="text" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
          </div>
          <div class="form-group">
            <label for="gender">Gender</label>
            <select id="gender" name="gender" class="form-control">
              <option value="male" <?php echo ($user['gender'] === 'male') ? 'selected' : ''; ?>>Male</option>
              <option value="female" <?php echo ($user['gender'] === 'female') ? 'selected' : ''; ?>>Female</option>
              <option value="other" <?php echo ($user['gender'] === 'other') ? 'selected' : ''; ?>>Other</option>
            </select>
          </div>
        </div>

        <div class="form-row-2">
          <div class="form-group">
            <label for="student_id">Student ID</label>
            <input type="text" id="student_id" name="student_id" class="form-control" value="<?php echo htmlspecialchars($user['student_id'] ?? 'JI-2024-042'); ?>">
          </div>
          <div class="form-group">
            <label for="institution">Academic Institution</label>
            <input type="text" id="institution" name="institution" class="form-control" value="<?php echo htmlspecialchars($user['institution'] ?? 'Java Institute for Advanced Technology'); ?>">
          </div>
        </div>

        <button type="submit" class="btn-save">Save Profile Changes</button>
      </form>
    </div>
  </div>
</main>
<?php
require_once '../includes/footer.php';
?>
