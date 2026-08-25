<?php
require_once 'includes/header.php';

$message_sent = false;
$error_message = "";

// Beginner-friendly form submission handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $department = isset($_POST['department']) ? trim($_POST['department']) : 'Student Services';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    if (empty($name) || empty($email) || empty($message)) {
        $error_message = "Please fill in all required fields (Name, Email, and Message).";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } else {
        // Form submitted successfully
        $message_sent = true;
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

    .contact-hero,
    .content-section {
      width: 100%;
      max-width: 1200px;
      margin-right: auto;
      margin-left: auto;
    }

    .contact-hero {
      position: relative;
      display: flex;
      flex-direction: column;
      justify-content: center;
      min-height: 240px;
      margin-top: 36px;
      padding: 40px clamp(24px, 5vw, 64px);
      box-sizing: border-box;
      border-radius: 16px;
      background: linear-gradient(115deg, rgba(23, 27, 42, 0.92), rgba(43, 53, 79, 0.75)), url('assets/images/hero1.jpg') center / cover;
      color: #ffffff;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .contact-hero h1 {
      margin: 0 0 10px;
      font-size: clamp(2rem, 4vw, 3rem);
      line-height: 1.1;
      text-shadow: #FF6B4A 1px 1px 2px;
    }

    .contact-hero p {
      max-width: 600px;
      margin: 0;
      color: rgba(255, 255, 255, 0.85);
      font-family: 'Cabin Sketch', cursive;
      font-size: 1.3rem;
    }

    .content-section {
      margin-top: 40px;
    }

    .contact-layout {
      display: grid;
      grid-template-columns: 1.2fr 1fr;
      gap: 32px;
    }

    .contact-card {
      background: #ffffff;
      border: 1px solid var(--line);
      border-radius: 12px;
      padding: 32px;
      box-shadow: 0 8px 24px rgba(23, 27, 42, 0.05);
    }

    .contact-card h2 {
      margin: 0 0 8px;
      color: var(--ink);
      font-size: 1.5rem;
    }

    .contact-card p.subtitle {
      margin: 0 0 24px;
      color: var(--muted);
      font-family: 'Cabin Sketch', cursive;
      font-size: 1.1rem;
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
      transition: border-color 0.2s, box-shadow 0.2s;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .form-control:focus {
      outline: none;
      border-color: var(--coral);
      box-shadow: 0 0 0 3px rgba(255, 107, 74, 0.15);
    }

    textarea.form-control {
      resize: vertical;
      min-height: 120px;
    }

    .btn-submit {
      display: inline-block;
      width: 100%;
      background-color: var(--coral);
      color: #ffffff;
      padding: 14px 24px;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      font-size: 1rem;
      cursor: pointer;
      box-shadow: 0 4px 12px rgba(255, 107, 74, 0.35);
      transition: background-color 0.2s, transform 0.1s;
    }

    .btn-submit:hover {
      background-color: #e55a3a;
      transform: translateY(-1px);
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

    .info-item {
      display: flex;
      align-items: flex-start;
      gap: 16px;
      margin-bottom: 22px;
      padding-bottom: 18px;
      border-bottom: 1px solid #f0f2f5;
    }

    .info-item:last-child {
      border-bottom: none;
      margin-bottom: 0;
      padding-bottom: 0;
    }

    .info-icon {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 44px;
      height: 44px;
      border-radius: 10px;
      background: rgba(255, 107, 74, 0.12);
      color: var(--coral);
      font-size: 1.3rem;
      flex-shrink: 0;
    }

    .info-text h4 {
      margin: 0 0 4px;
      color: var(--ink);
      font-size: 1rem;
    }

    .info-text p {
      margin: 0;
      color: var(--muted);
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      font-size: 0.9rem;
      line-height: 1.5;
    }

    @media (max-width: 860px) {
      .contact-layout {
        grid-template-columns: 1fr;
      }
    }
  </style>

  <section class="contact-hero">
    <h1>Contact CampusHub</h1>
    <p>Reach out to Student Services, Club Coordinators, or Technical Support.</p>
  </section>

  <section class="content-section">
    <div class="contact-layout">
      <!-- Contact Form -->
      <div class="contact-card">
        <h2>Send Us a Message</h2>
        <p class="subtitle">We typically reply within 24 hours.</p>

        <?php if ($message_sent): ?>
          <div class="alert alert-success">
            Thank you! Your message has been received. Our Student Services team will get back to you shortly.
          </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
          <div class="alert alert-error">
            <?php echo htmlspecialchars($error_message); ?>
          </div>
        <?php endif; ?>

        <form action="contact.php" method="POST">
          <div class="form-group">
            <label for="name">Your Name *</label>
            <input type="text" id="name" name="name" class="form-control" placeholder="e.g. Kamal Perera" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
          </div>

          <div class="form-group">
            <label for="email">Email Address *</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="e.g. kamal@student.edu" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
          </div>

          <div class="form-group">
            <label for="department">Department / Topic</label>
            <select id="department" name="department" class="form-control">
              <option value="Student Services">Student Services & Academic Help</option>
              <option value="Event Coordination">Event & Club Inquiries</option>
              <option value="Technical Support">Technical Portal Support</option>
              <option value="General Feedback">General Feedback</option>
            </select>
          </div>

          <div class="form-group">
            <label for="message">Message *</label>
            <textarea id="message" name="message" class="form-control" placeholder="Write your inquiry or question here..." required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
          </div>

          <button type="submit" class="btn-submit">Submit Inquiry</button>
        </form>
      </div>

      <!-- Campus Details & Help Desk -->
      <div class="contact-card">
        <h2>Campus Information</h2>
        <p class="subtitle">Direct campus contact points and office locations.</p>

        <div class="info-item">
          <div class="info-icon">📍</div>
          <div class="info-text">
            <h4>Campus Address</h4>
            <p>Java Institute for Advanced Technology<br>Main Student Center, 2nd Floor<br>Colombo, Sri Lanka</p>
          </div>
        </div>

        <div class="info-item">
          <div class="info-icon">📞</div>
          <div class="info-text">
            <h4>Helpline Phone</h4>
            <p>Hotline: +94 77 123 4567<br>Office: +94 11 234 5678 (Mon - Fri: 8:30 AM - 5:00 PM)</p>
          </div>
        </div>

        <div class="info-item">
          <div class="info-icon">✉️</div>
          <div class="info-text">
            <h4>Official Email</h4>
            <p>General: info@campushub.edu<br>Student Affairs: students@campushub.edu</p>
          </div>
        </div>

        <div class="info-item">
          <div class="info-icon">🕒</div>
          <div class="info-text">
            <h4>Help Desk Hours</h4>
            <p>Monday - Friday: 8:30 AM to 5:00 PM<br>Saturday: 9:00 AM to 1:00 PM<br>Sunday & Public Holidays: Closed</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>
<?php
require_once 'includes/footer.php';
?>
