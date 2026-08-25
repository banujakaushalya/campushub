<?php
// Simple footer include
$path = "./";
if (file_exists("../includes/header.php")) {
    $path = "../";
}
?>
<footer class="site-footer">
  <style>
    .site-footer {
      margin-top: 56px;
      padding: 42px 24px 22px;
      color: #f8f5f0;
      background: #111111;
      border-top: 4px solid #ff6b4a;
    }

    .footer-container {
      display: grid;
      grid-template-columns: 1.4fr 1fr 1fr;
      gap: 32px;
      max-width: 1180px;
      margin: 0 auto;
    }

    .footer-brand h2 {
      margin: 0 0 10px;
      color: #ffffff;
      font-size: 1.6rem;
    }

    .footer-brand p {
      max-width: 300px;
      margin: 0;
      color: #aaa7a2;
      font-family: Arial, sans-serif;
      line-height: 1.6;
    }

    .site-footer h3 {
      margin: 0 0 14px;
      color: #ff6b4a;
      font-family: Arial, sans-serif;
      font-size: 0.78rem;
      letter-spacing: 0.08em;
      text-transform: uppercase;
    }

    .footer-links,
    .socials {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      gap: 9px;
    }

    .site-footer a {
      color: #f8f5f0;
      font-family: Arial, sans-serif;
      font-size: 0.95rem;
      text-decoration: none;
      transition: color 0.2s ease;
    }

    .site-footer a:hover,
    .site-footer a:focus-visible {
      color: #ff6b4a;
    }

    .footer-bottom {
      max-width: 1180px;
      margin: 34px auto 0;
      padding-top: 16px;
      color: #77736f;
      border-top: 1px solid #2c2c2c;
      font-family: Arial, sans-serif;
      font-size: 0.8rem;
    }

    @media (max-width: 680px) {
      .site-footer {
        padding: 34px 20px 20px;
      }

      .footer-container {
        grid-template-columns: 1fr 1fr;
        gap: 28px 20px;
      }

      .footer-brand {
        grid-column: 1 / -1;
      }
    }
  </style>
  <div class="footer-container">
    <div class="footer-brand">
      <h2>CampusHub</h2>
      <p>Your campus, connected. Discover events, share moments, and stay in the loop.</p>
    </div>

    <nav class="footer-links" aria-label="Footer navigation">
      <h3>Explore</h3>
      <a href="<?php echo $path; ?>index.php">Home</a>
      <a href="<?php echo $path; ?>about.php">About</a>
      <a href="<?php echo $path; ?>announcements.php">Announcements</a>
      <a href="<?php echo $path; ?>contact.php">Contact</a>
    </nav>

    <div class="socials">
      <h3>Portals</h3>
      <a href="<?php echo $path; ?>student/events.php">Student Events</a>
      <a href="<?php echo $path; ?>student/dashboard.php">Student Portal</a>
      <a href="<?php echo $path; ?>admin/dashboard.php">Admin Portal</a>
    </div>

  </div>
  <div class="footer-bottom">&copy; <?php echo date('Y'); ?> CampusHub. All rights reserved.</div>
</footer>
</body>
</html>
