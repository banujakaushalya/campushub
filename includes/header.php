<?php
// Simple header include with dynamic navigation
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Calculate base path for reliable links across root and subdirectories in XAMPP
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
$root_prefix = ($current_dir === 'admin' || $current_dir === 'student' || $current_dir === 'views') ? '../' : './';
$current_page = basename($_SERVER['PHP_SELF']);

$is_logged_in = !empty($_SESSION['user_id']);
$user_name = $is_logged_in ? ($_SESSION['user_name'] ?? 'User') : '';
$user_role = $is_logged_in ? ($_SESSION['role'] ?? 'student') : '';
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CampusHub - Student Services System</title>
  <link rel="stylesheet" href="<?php echo $root_prefix; ?>assets/css/style.css">
</head>

<body>
  <header>
    <style>
      .nav-bar {
        background-color: rgba(86, 131, 127, 0.32);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(12px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        color: #454654;
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 64px;
        width: 100%;
        max-width: 1240px;
        margin: 12px auto 0;
        padding: 0 16px;
        box-sizing: border-box;
        border-radius: 12px;
      }

      .nav-logo {
        display: flex;
        align-items: center;
        font-size: 1.8rem;
        color: #ffffff;
        font-weight: bold;
        text-shadow: #FF6B4A 1px 1px 2px;
        text-decoration: none;
        letter-spacing: -0.5px;
      }

      .nav-logo:hover {
        color: #ffffff;
      }

      .nav-links {
        display: flex;
        gap: 18px;
        align-items: center;
        margin: 0 16px;
      }

      .nav-links a {
        text-decoration: none;
        color: #2b354f;
        font-weight: 700;
        font-size: 0.95rem;
        padding: 6px 10px;
        border-radius: 6px;
        transition: all 0.2s ease;
      }

      .nav-links a:hover {
        color: #FF6B4A;
        background-color: rgba(255, 255, 255, 0.4);
      }

      .nav-links a.active {
        color: #FF6B4A;
        background-color: rgba(255, 255, 255, 0.6);
      }

      .nav-account {
        display: flex;
        align-items: center;
        gap: 12px;
      }

      /* Guest Login/Signup Buttons */
      .nav-buttons {
        display: flex;
        align-items: center;
        gap: 10px;
      }

      .btn-nav-login,
      .btn-nav-signup {
        display: inline-block;
        padding: 8px 18px;
        border-radius: 6px;
        font-weight: bold;
        font-size: 0.9rem;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
      }

      .btn-nav-login {
        background-color: transparent;
        color: #2b354f;
        border: 1.5px solid rgba(43, 53, 79, 0.4);
      }

      .btn-nav-login:hover {
        background-color: rgba(255, 255, 255, 0.6);
        color: #FF6B4A;
        border-color: #FF6B4A;
      }

      .btn-nav-signup {
        background-color: #FF6B4A;
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(255, 107, 74, 0.3);
      }

      .btn-nav-signup:hover {
        background-color: #e55a3a;
        transform: translateY(-1px);
      }

      /* Logged-in User Badge & Dropdown */
      .acc-container {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 10px;
        background-color: rgba(255, 255, 255, 0.5);
        padding: 5px 12px;
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.3);
      }

      .user-image img {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #FF6B4A;
        display: block;
      }

      .user-details {
        display: flex;
        flex-direction: column;
        line-height: 1.2;
      }

      .user-name {
        font-weight: bold;
        color: #2b354f;
        font-size: 0.9rem;
        max-width: 140px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }

      .user-role-badge {
        font-size: 0.72rem;
        text-transform: uppercase;
        color: #FF6B4A;
        font-weight: 800;
        letter-spacing: 0.5px;
      }

      .account-menu {
        position: relative;
        display: flex;
        align-items: center;
      }

      .account-btn {
        background-color: transparent;
        border: none;
        cursor: pointer;
        padding: 4px;
        display: flex;
        align-items: center;
        border-radius: 4px;
      }

      .account-btn:hover {
        background-color: rgba(255, 255, 255, 0.8);
      }

      .account-btn img {
        width: 20px;
        height: 20px;
      }

      .account-dropdown {
        display: none;
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        z-index: 1000;
        min-width: 180px;
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        border: 1px solid #eaeaf0;
        flex-direction: column;
        overflow: hidden;
      }

      .account-dropdown a {
        display: block;
        padding: 10px 16px;
        text-decoration: none;
        color: #2b354f;
        font-size: 0.88rem;
        font-weight: 600;
        border-bottom: 1px solid #f2f2f6;
        transition: background-color 0.15s ease;
      }

      .account-dropdown a:last-child {
        border-bottom: none;
      }

      .account-dropdown a:hover {
        background-color: #fff4f1;
        color: #FF6B4A;
      }

      .account-dropdown a.logout-link {
        color: #d93838;
      }

      .account-dropdown a.logout-link:hover {
        background-color: #ffeaea;
      }

      @media (max-width: 860px) {
        .nav-bar {
          flex-wrap: wrap;
          padding: 10px 14px;
          gap: 8px;
        }
        .nav-links {
          order: 3;
          width: 100%;
          justify-content: center;
          gap: 10px;
          flex-wrap: wrap;
          margin: 6px 0 0;
          padding-top: 8px;
          border-top: 1px solid rgba(255, 255, 255, 0.2);
        }
      }
    </style>

    <nav class="nav-bar">
      <a href="<?php echo $root_prefix; ?>index.php" class="nav-logo">CampusHub</a>

      <div class="nav-links">
        <a href="<?php echo $root_prefix; ?>index.php" class="<?php echo ($current_page === 'index.php') ? 'active' : ''; ?>">Home</a>
        <a href="<?php echo $root_prefix; ?>about.php" class="<?php echo ($current_page === 'about.php') ? 'active' : ''; ?>">About</a>

        <?php if ($is_logged_in && $user_role === 'admin'): ?>
          <!-- Admin Internal Navigations -->
          <a href="<?php echo $root_prefix; ?>admin/dashboard.php" class="<?php echo ($current_page === 'dashboard.php' && $current_dir === 'admin') ? 'active' : ''; ?>">Admin Panel</a>
          <a href="<?php echo $root_prefix; ?>admin/manage_events.php" class="<?php echo ($current_page === 'manage_events.php') ? 'active' : ''; ?>">Events</a>
          <a href="<?php echo $root_prefix; ?>admin/manage_students.php" class="<?php echo ($current_page === 'manage_students.php') ? 'active' : ''; ?>">Students</a>
          <a href="<?php echo $root_prefix; ?>admin/manage_registrations.php" class="<?php echo ($current_page === 'manage_registrations.php') ? 'active' : ''; ?>">Registrations</a>
          <a href="<?php echo $root_prefix; ?>announcements.php" class="<?php echo ($current_page === 'announcements.php') ? 'active' : ''; ?>">Notices</a>

        <?php elseif ($is_logged_in && $user_role === 'student'): ?>
          <!-- Student Internal Navigations -->
          <a href="<?php echo $root_prefix; ?>student/events.php" class="<?php echo ($current_page === 'events.php') ? 'active' : ''; ?>">Events</a>
          <a href="<?php echo $root_prefix; ?>student/my_registrations.php" class="<?php echo ($current_page === 'my_registrations.php') ? 'active' : ''; ?>">My Registrations</a>
          <a href="<?php echo $root_prefix; ?>announcements.php" class="<?php echo ($current_page === 'announcements.php') ? 'active' : ''; ?>">Announcements</a>
          <a href="<?php echo $root_prefix; ?>student/upload_photo.php" class="<?php echo ($current_page === 'upload_photo.php') ? 'active' : ''; ?>">Gallery Upload</a>
          <a href="<?php echo $root_prefix; ?>contact.php" class="<?php echo ($current_page === 'contact.php') ? 'active' : ''; ?>">Contact</a>

        <?php else: ?>
          <!-- Public Guest Navigation -->
          <a href="<?php echo $root_prefix; ?>student/events.php" class="<?php echo ($current_page === 'events.php') ? 'active' : ''; ?>">Events</a>
          <a href="<?php echo $root_prefix; ?>announcements.php" class="<?php echo ($current_page === 'announcements.php') ? 'active' : ''; ?>">Announcements</a>
          <a href="<?php echo $root_prefix; ?>contact.php" class="<?php echo ($current_page === 'contact.php') ? 'active' : ''; ?>">Contact</a>
        <?php endif; ?>
      </div>

      <div class="nav-account">
        <?php if ($is_logged_in): ?>
          <!-- Logged In User Account View -->
          <div class="acc-container">
            <div class="user-image">
              <img src="<?php echo $root_prefix; ?>assets/images/user.png" alt="User">
            </div>
            <div class="user-details">
              <span class="user-name"><?php echo htmlspecialchars($user_name); ?></span>
              <span class="user-role-badge"><?php echo htmlspecialchars($user_role); ?></span>
            </div>
            <div class="account-menu">
              <button class="account-btn" id="accountMenuBtn" aria-label="Toggle user menu">
                <img src="<?php echo $root_prefix; ?>assets/images/menu.png" alt="Menu">
              </button>

              <div class="account-dropdown" id="accountDropdown">
                <?php if ($user_role === 'admin'): ?>
                  <a href="<?php echo $root_prefix; ?>admin/dashboard.php">Dashboard</a>
                  <a href="<?php echo $root_prefix; ?>admin/add_event.php">Add New Event</a>
                  <a href="<?php echo $root_prefix; ?>admin/manage_announcements.php">Announcements</a>
                  <a href="<?php echo $root_prefix; ?>admin/upload_media.php">Upload Media</a>
                  <a href="<?php echo $root_prefix; ?>admin/export_events.php">Export XML Feed</a>
                <?php else: ?>
                  <a href="<?php echo $root_prefix; ?>student/dashboard.php">My Dashboard</a>
                  <a href="<?php echo $root_prefix; ?>student/profile.php">My Profile</a>
                  <a href="<?php echo $root_prefix; ?>student/my_registrations.php">My Registrations</a>
                  <a href="<?php echo $root_prefix; ?>student/upload_photo.php">Upload Activity Photos</a>
                <?php endif; ?>
                <a href="<?php echo $root_prefix; ?>logout.php" class="logout-link">Logout</a>
              </div>
            </div>
          </div>
        <?php else: ?>
          <!-- Guest Non-Logged In Action Buttons -->
          <div class="nav-buttons">
            <a href="<?php echo $root_prefix; ?>login.php" class="btn-nav-login">Login</a>
            <a href="<?php echo $root_prefix; ?>login.php?tab=signup" class="btn-nav-signup">Sign Up</a>
          </div>
        <?php endif; ?>
      </div>
    </nav>
  </header>

  <script>
    const accountBtn = document.getElementById('accountMenuBtn');
    const accountDropdown = document.getElementById('accountDropdown');

    if (accountBtn && accountDropdown) {
      accountBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        accountDropdown.style.display = accountDropdown.style.display === 'flex' ? 'none' : 'flex';
      });

      document.addEventListener('click', (event) => {
        if (!accountBtn.contains(event.target) && !accountDropdown.contains(event.target)) {
          accountDropdown.style.display = 'none';
        }
      });
    }
  </script>
