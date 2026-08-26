<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

// Check role permission: super_admin or admin
$curr_role = $_SESSION['role'] ?? 'guest';
if ($curr_role !== 'super_admin' && $curr_role !== 'admin') {
    echo "<script>window.location.href = '../student/dashboard.php';</script>";
    exit;
}

$student_user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$success_msg = "";
$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
    $last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $gender = isset($_POST['gender']) ? trim($_POST['gender']) : 'male';
    $student_id = isset($_POST['student_id']) ? trim($_POST['student_id']) : '';
    $institution = isset($_POST['institution']) ? trim($_POST['institution']) : '';
    $role = isset($_POST['role']) ? trim($_POST['role']) : 'student';

    if (empty($first_name) || empty($last_name) || empty($email)) {
        $error_msg = "First Name, Last Name, and Email are required.";
    } else {
        $updated = Database::iud(
            "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, gender = ?, student_id = ?, institution = ?, role = ? WHERE user_id = ?",
            "ssssssssi",
            [$first_name, $last_name, $email, $phone, $gender, $student_id, $institution, $role, $student_user_id]
        );

        if ($updated) {
            $success_msg = "Student record updated successfully!";
        } else {
            $error_msg = "Failed to update student record.";
        }
    }
}

// Fetch student record
$student = null;
if ($student_user_id > 0) {
    $res = Database::search("SELECT * FROM users WHERE user_id = ? LIMIT 1", "i", [$student_user_id]);
    if ($res && $res->num_rows > 0) {
        $student = $res->fetch_assoc();
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

    .form-container {
      width: 100%;
      max-width: 760px;
      margin: 36px auto 0;
    }

    .form-card {
      background: #ffffff;
      border: 1px solid var(--line);
      border-radius: 16px;
      padding: 36px;
      box-shadow: 0 10px 30px rgba(23, 27, 42, 0.08);
    }

    .form-card h1 {
      margin: 0 0 6px;
      color: var(--ink);
      font-size: 1.8rem;
    }

    .subtitle {
      margin: 0 0 24px;
      color: var(--muted);
      font-family: 'Cabin Sketch', cursive;
      font-size: 1.15rem;
    }

    .form-row-2 {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 18px;
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

    .btn-submit {
      background: var(--coral);
      color: #ffffff;
      border: none;
      padding: 12px 24px;
      font-size: 1rem;
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
      padding: 12px 16px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      font-size: 0.9rem;
      font-weight: 600;
    }

    .alert-success { background-color: #e6f7ec; color: #1b7a3e; border: 1px solid #c2ebd0; }
    .alert-error { background-color: #ffeaea; color: #c92a2a; border: 1px solid #fcc2c2; }
  </style>

  <div class="form-container">
    <div class="form-card">
      <h1>Edit Student Record</h1>
      <p class="subtitle">Modify student profile details, institute affiliation, and system role.</p>

      <?php if (!empty($success_msg)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
        <a href="manage_students.php" style="display: inline-block; margin-bottom: 20px; color: var(--coral); font-weight: bold; text-decoration: none;">&larr; Return to Student Directory</a>
      <?php endif; ?>

      <?php if (!empty($error_msg)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error_msg); ?></div>
      <?php endif; ?>

      <?php if (!$student): ?>
        <p style="color: var(--muted); text-align: center; padding: 24px;">Student record not found.</p>
        <a href="manage_students.php" style="color: var(--coral); font-weight: bold; text-decoration: none;">&larr; Return to Student Directory</a>
      <?php else: ?>
        <form action="edit_student.php?id=<?php echo $student_user_id; ?>" method="POST">
          <div class="form-row-2">
            <div class="form-group">
              <label for="first_name">First Name *</label>
              <input type="text" id="first_name" name="first_name" class="form-control" value="<?php echo htmlspecialchars($student['first_name']); ?>" required>
            </div>

            <div class="form-group">
              <label for="last_name">Last Name *</label>
              <input type="text" id="last_name" name="last_name" class="form-control" value="<?php echo htmlspecialchars($student['last_name']); ?>" required>
            </div>
          </div>

          <div class="form-row-2">
            <div class="form-group">
              <label for="email">Email Address *</label>
              <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($student['email']); ?>" required>
            </div>

            <div class="form-group">
              <label for="phone">Phone Number</label>
              <input type="text" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($student['phone'] ?? ''); ?>">
            </div>
          </div>

          <div class="form-row-2">
            <div class="form-group">
              <label for="student_id">Student / Registration ID</label>
              <input type="text" id="student_id" name="student_id" class="form-control" value="<?php echo htmlspecialchars($student['student_id'] ?? ''); ?>">
            </div>

            <div class="form-group">
              <label for="gender">Gender</label>
              <select id="gender" name="gender" class="form-control">
                <option value="male" <?php echo ($student['gender'] === 'male') ? 'selected' : ''; ?>>Male</option>
                <option value="female" <?php echo ($student['gender'] === 'female') ? 'selected' : ''; ?>>Female</option>
                <option value="other" <?php echo ($student['gender'] === 'other') ? 'selected' : ''; ?>>Other</option>
              </select>
            </div>
          </div>

          <div class="form-row-2">
            <div class="form-group">
              <label for="institution">Institution / University Branch</label>
              <input type="text" id="institution" name="institution" class="form-control" value="<?php echo htmlspecialchars($student['institution'] ?? ''); ?>">
            </div>

            <div class="form-group">
              <label for="role">User Role</label>
              <select id="role" name="role" class="form-control">
                <option value="student" <?php echo ($student['role'] === 'student') ? 'selected' : ''; ?>>Student</option>
                <option value="moderator" <?php echo ($student['role'] === 'moderator') ? 'selected' : ''; ?>>Moderator</option>
                <?php if ($curr_role === 'super_admin'): ?>
                  <option value="admin" <?php echo ($student['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                <?php endif; ?>
                <option value="teacher" <?php echo ($student['role'] === 'teacher') ? 'selected' : ''; ?>>Teacher</option>
              </select>
            </div>
          </div>

          <button type="submit" class="btn-submit">Save Student Record</button>
          <a href="manage_students.php" style="display: block; text-align: center; margin-top: 14px; color: var(--ink); text-decoration: none; font-weight: bold; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Cancel & Return</a>
        </form>
      <?php endif; ?>
    </div>
  </div>
</main>
<?php
require_once '../includes/footer.php';
?>
