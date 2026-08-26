<?php
// Start session if not started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Simple path helper for subfolders (easy for viva defense)
$path = "./";
if (file_exists("../includes/header.php")) {
    $path = "../";
}

// Check user login session variables
$is_logged_in = false;
$user_name = "";
$user_role = "";

if (isset($_SESSION['user_id'])) {
    $is_logged_in = true;
    if (isset($_SESSION['user_name'])) {
        $user_name = $_SESSION['user_name'];
    } else {
        $user_name = "User";
    }
    if (isset($_SESSION['role'])) {
        $user_role = $_SESSION['role'];
    } else {
        $user_role = "student";
    }
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CampusHub - Student Services System</title>
  <link rel="stylesheet" href="<?php echo $path; ?>assets/css/style.css">
</head>

<body>
  <header class="main-header">
    <style>
      .main-header {
        position: relative;
        z-index: 9999;
        width: 100%;
        padding: 12px 24px 0;
        box-sizing: border-box;
      }

      .nav-bar {
        position: relative;
        z-index: 9999;
        background-color: rgba(86, 131, 127, 0.35);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        color: #2b354f;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        min-height: 64px;
        width: 100%;
        margin: 0 auto;
        padding: 6px clamp(14px, 2vw, 32px);
        box-sizing: border-box;
        border-radius: 14px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      }

      .nav-logo {
        display: flex;
        align-items: center;
        font-size: 1.85rem;
        color: #ffffff;
        font-family: 'Archivo Black', sans-serif;
        font-weight: bold;
        text-shadow: #FF6B4A 1px 1px 3px;
        text-decoration: none;
        letter-spacing: -0.5px;
        flex-shrink: 0;
      }

      .nav-logo:hover {
        color: #ffffff;
      }

      .nav-links {
        display: flex;
        flex: 1;
        justify-content: center;
        align-items: center;
        gap: 6px 14px;
        flex-wrap: wrap;
        margin: 0 10px;
      }

      .nav-links a {
        text-decoration: none;
        color: #1e2638;
        font-weight: 700;
        font-size: 0.92rem;
        padding: 7px 12px;
        border-radius: 8px;
        white-space: nowrap;
        transition: all 0.2s ease;
      }

      .nav-links a:hover {
        color: #FF6B4A;
        background-color: rgba(255, 255, 255, 0.5);
      }

      .nav-links a.active {
        color: #FF6B4A;
        background-color: rgba(255, 255, 255, 0.75);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
      }

      .nav-account {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-shrink: 0;
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
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.9rem;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        white-space: nowrap;
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
        box-shadow: 0 2px 8px rgba(255, 107, 74, 0.35);
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
        background-color: rgba(255, 255, 255, 0.65);
        padding: 4px 12px 4px 6px;
        border-radius: 9999px;
        border: 1px solid rgba(255, 255, 255, 0.4);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
      }

      .user-image img {
        width: 36px;
        height: 36px;
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
        font-weight: 700;
        color: #1e2638;
        font-size: 0.88rem;
        max-width: 140px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }

      .user-role-badge {
        font-size: 0.7rem;
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
        padding: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: background-color 0.15s ease;
      }

      .account-btn:hover {
        background-color: rgba(255, 255, 255, 0.8);
      }

      .account-btn img {
        width: 18px;
        height: 18px;
        display: block;
      }

      .account-dropdown {
        display: none;
        position: absolute;
        top: calc(100% + 12px);
        right: 0;
        z-index: 10000;
        min-width: 200px;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.22);
        border: 1px solid #eaeaf0;
        flex-direction: column;
        overflow: hidden;
      }

      .account-dropdown a {
        display: block;
        padding: 11px 18px;
        text-decoration: none;
        color: #2b354f;
        font-size: 0.9rem;
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

      @media (max-width: 992px) {
        .main-header {
          padding: 8px 12px 0;
        }

        .nav-bar {
          flex-wrap: wrap;
          padding: 10px 14px;
          gap: 10px;
        }

        .nav-links {
          order: 3;
          width: 100%;
          justify-content: center;
          gap: 8px 12px;
          margin: 4px 0 0;
          padding-top: 8px;
          border-top: 1px solid rgba(255, 255, 255, 0.2);
        }
      }
    </style>

    <nav class="nav-bar">
      <a href="<?php echo $path; ?>index.php" class="nav-logo">CampusHub</a>

      <div class="nav-links">
        <a href="<?php echo $path; ?>index.php">Home</a>
        <a href="<?php echo $path; ?>about.php">About</a>

        <?php if ($is_logged_in == true && ($user_role == "super_admin" || $user_role == "admin")): ?>
          <!-- Admin / Super Admin Internal Navigation -->
          <a href="<?php echo $path; ?>admin/dashboard.php">Dashboard</a>
          <a href="<?php echo $path; ?>admin/manage_events.php">Events</a>
          <a href="<?php echo $path; ?>admin/manage_students.php">Students</a>
          <a href="<?php echo $path; ?>admin/manage_registrations.php">Registrations</a>
          <a href="<?php echo $path; ?>admin/manage_roles.php">Roles & Approvals</a>
          <a href="<?php echo $path; ?>announcements.php">Notices</a>
          <a href="<?php echo $path; ?>gallery.php">Gallery</a>

        <?php elseif ($is_logged_in == true && $user_role == "moderator"): ?>
          <!-- Moderator Internal Navigation -->
          <a href="<?php echo $path; ?>admin/dashboard.php">Mod Panel</a>
          <a href="<?php echo $path; ?>admin/manage_events.php">Manage Events</a>
          <a href="<?php echo $path; ?>admin/upload_media.php">Upload Media</a>
          <a href="<?php echo $path; ?>gallery.php">Gallery</a>
          <a href="<?php echo $path; ?>announcements.php">Notices</a>

        <?php elseif ($is_logged_in == true && ($user_role == "student" || $user_role == "teacher")): ?>
          <!-- Student / Teacher Internal Navigation -->
          <a href="<?php echo $path; ?>student/events.php">Events</a>
          <a href="<?php echo $path; ?>student/my_registrations.php">My Registrations</a>
          <a href="<?php echo $path; ?>gallery.php">Gallery</a>
          <a href="<?php echo $path; ?>announcements.php">Announcements</a>
          <a href="<?php echo $path; ?>student/upload_photo.php">Upload Photos</a>
          <a href="<?php echo $path; ?>student/apply_role.php">Apply for Role</a>
          <a href="<?php echo $path; ?>contact.php">Contact</a>

        <?php else: ?>
          <!-- Public Guest Navigation -->
          <a href="<?php echo $path; ?>student/events.php">Events</a>
          <a href="<?php echo $path; ?>gallery.php">Gallery</a>
          <a href="<?php echo $path; ?>announcements.php">Announcements</a>
          <a href="<?php echo $path; ?>contact.php">Contact</a>
        <?php endif; ?>
      </div>

      <div class="nav-account">
        <?php if ($is_logged_in == true): ?>
          <!-- Logged In User Account View -->
          <div class="acc-container">
            <div class="user-image">
              <img src="<?php echo $path; ?>assets/images/user.png" alt="User">
            </div>
            <div class="user-details">
              <span class="user-name"><?php echo htmlspecialchars($user_name); ?></span>
              <span class="user-role-badge"><?php echo htmlspecialchars(str_replace('_', ' ', $user_role)); ?></span>
            </div>
            <div class="account-menu">
              <button class="account-btn" id="accountMenuBtn" type="button" aria-label="Toggle user menu">
                <img src="<?php echo $path; ?>assets/images/menu.png" alt="Menu">
              </button>

              <div class="account-dropdown" id="accountDropdown">
                <?php if ($user_role == "super_admin"): ?>
                  <a href="<?php echo $path; ?>admin/dashboard.php">Super Admin Dashboard</a>
                  <a href="<?php echo $path; ?>master_control/index.php">Master Control Unit</a>
                  <a href="<?php echo $path; ?>admin/manage_roles.php">Manage Admins & Mods</a>
                  <a href="<?php echo $path; ?>admin/manage_events.php">Manage Events</a>
                  <a href="<?php echo $path; ?>admin/organization_info.php">Organization Info</a>
                <?php elseif ($user_role == "admin"): ?>
                  <a href="<?php echo $path; ?>admin/dashboard.php">Admin Dashboard</a>
                  <a href="<?php echo $path; ?>admin/add_event.php">Add New Event</a>
                  <a href="<?php echo $path; ?>admin/manage_roles.php">Declare / Add Moderators</a>
                  <a href="<?php echo $path; ?>admin/manage_announcements.php">Manage Announcements</a>
                  <a href="<?php echo $path; ?>admin/organization_info.php">Organization Info</a>
                  <a href="<?php echo $path; ?>admin/upload_media.php">Upload Media</a>
                <?php elseif ($user_role == "moderator"): ?>
                  <a href="<?php echo $path; ?>admin/dashboard.php">Moderator Dashboard</a>
                  <a href="<?php echo $path; ?>admin/manage_events.php">Manage Events</a>
                  <a href="<?php echo $path; ?>admin/add_event.php">Add New Event</a>
                  <a href="<?php echo $path; ?>admin/upload_media.php">Upload Media</a>
                <?php else: ?>
                  <a href="<?php echo $path; ?>student/dashboard.php">My Dashboard</a>
                  <a href="<?php echo $path; ?>student/profile.php">My Profile</a>
                  <a href="<?php echo $path; ?>student/my_registrations.php">My Registrations</a>
                  <a href="<?php echo $path; ?>student/upload_photo.php">Upload Photos</a>
                  <a href="<?php echo $path; ?>student/apply_role.php">Apply for Mod/Admin</a>
                <?php endif; ?>
                <a href="<?php echo $path; ?>logout.php" class="logout-link">Logout</a>
              </div>
            </div>
          </div>
        <?php else: ?>
          <!-- Guest Non-Logged In Action Buttons -->
          <div class="nav-buttons">
            <a href="<?php echo $path; ?>login.php" class="btn-nav-login">Login</a>
            <a href="<?php echo $path; ?>login.php?tab=signup" class="btn-nav-signup">Sign Up</a>
          </div>
        <?php endif; ?>
      </div>
    </nav>
  </header>

  <script>
    var accountBtn = document.getElementById('accountMenuBtn');
    var accountDropdown = document.getElementById('accountDropdown');

    if (accountBtn && accountDropdown) {
      accountBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        if (accountDropdown.style.display === 'flex') {
          accountDropdown.style.display = 'none';
        } else {
          accountDropdown.style.display = 'flex';
        }
      });

      document.addEventListener('click', function(event) {
        if (!accountBtn.contains(event.target) && !accountDropdown.contains(event.target)) {
          accountDropdown.style.display = 'none';
        }
      });
    }
  </script>
