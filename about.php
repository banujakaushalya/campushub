<?php
require_once 'includes/header.php';
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

    .about-hero,
    .content-section {
      width: 100%;
      max-width: 1200px;
      margin-right: auto;
      margin-left: auto;
    }

    .about-hero {
      position: relative;
      display: flex;
      flex-direction: column;
      justify-content: center;
      min-height: 280px;
      margin-top: 36px;
      padding: 48px clamp(24px, 5vw, 64px);
      box-sizing: border-box;
      border-radius: 16px;
      background: linear-gradient(115deg, rgba(23, 27, 42, 0.94), rgba(43, 53, 79, 0.8)), url('assets/images/hero2.jpg') center / cover;
      color: #ffffff;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .about-hero h1 {
      margin: 0 0 12px;
      font-size: clamp(2rem, 4vw, 3.2rem);
      line-height: 1.1;
      text-shadow: #FF6B4A 1px 1px 2px;
    }

    .about-hero p {
      max-width: 650px;
      margin: 0;
      color: rgba(255, 255, 255, 0.85);
      font-family: 'Cabin Sketch', cursive;
      font-size: 1.35rem;
      letter-spacing: 0.5px;
    }

    .content-section {
      margin-top: 48px;
    }

    .grid-2 {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 28px;
    }

    .grid-3 {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 24px;
    }

    .grid-4 {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 20px;
    }

    .info-card {
      background: #ffffff;
      border: 1px solid var(--line);
      border-radius: 12px;
      padding: 28px;
      box-shadow: 0 8px 24px rgba(23, 27, 42, 0.05);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .info-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 14px 28px rgba(23, 27, 42, 0.1);
    }

    .info-card h3 {
      margin: 0 0 12px;
      color: var(--ink);
      font-size: 1.3rem;
    }

    .info-card p {
      margin: 0;
      color: var(--muted);
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      font-size: 0.95rem;
      line-height: 1.6;
    }

    .stat-card {
      background: #ffffff;
      border: 1px solid var(--line);
      border-radius: 12px;
      padding: 24px;
      text-align: center;
      box-shadow: 0 8px 20px rgba(23, 27, 42, 0.04);
    }

    .stat-number {
      font-size: 2.5rem;
      color: var(--coral);
      font-weight: bold;
      margin-bottom: 6px;
    }

    .stat-label {
      color: var(--ink);
      font-size: 0.95rem;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      font-weight: 600;
    }

    .section-title {
      margin: 0 0 8px;
      color: var(--ink);
      font-size: 1.65rem;
    }

    .section-subtitle {
      margin: 0 0 24px;
      color: var(--muted);
      font-family: 'Cabin Sketch', cursive;
      font-size: 1.15rem;
    }

    .badge-icon {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 44px;
      height: 44px;
      border-radius: 10px;
      background: rgba(255, 107, 74, 0.12);
      color: var(--coral);
      font-size: 1.4rem;
      margin-bottom: 16px;
    }

    @media (max-width: 860px) {
      .grid-2, .grid-3, .grid-4 {
        grid-template-columns: 1fr;
      }
    }
  </style>

  <section class="about-hero">
    <h1>About CampusHub</h1>
    <p>Empowering student engagement, campus community, and event participation.</p>
  </section>

  <!-- Mission & Vision -->
  <section class="content-section">
    <div class="grid-2">
      <div class="info-card">
        <div class="badge-icon">🎯</div>
        <h3>Our Mission</h3>
        <p>
          CampusHub was engineered as a comprehensive digital portal for student affairs, designed to bridge the communication gap between university academic councils, student clubs, and attendees. We provide seamless event scheduling, transparent registration passes, and prompt administrative notices.
        </p>
      </div>

      <div class="info-card">
        <div class="badge-icon">🌟</div>
        <h3>Our Vision</h3>
        <p>
          To cultivate an energized, connected, and collaborative campus ecosystem where every student has immediate access to extracurricular enrichment, technical workshops, sports championships, and cultural celebrations throughout their academic journey.
        </p>
      </div>
    </div>
  </section>

  <!-- Key Stats -->
  <section class="content-section">
    <h2 class="section-title">CampusHub by the Numbers</h2>
    <p class="section-subtitle">Our growing community across academic faculties.</p>
    <div class="grid-4">
      <div class="stat-card">
        <div class="stat-number">500+</div>
        <div class="stat-label">Enrolled Students</div>
      </div>
      <div class="stat-card">
        <div class="stat-number">45+</div>
        <div class="stat-label">Events & Workshops</div>
      </div>
      <div class="stat-card">
        <div class="stat-number">12+</div>
        <div class="stat-label">Active Student Clubs</div>
      </div>
      <div class="stat-card">
        <div class="stat-number">98%</div>
        <div class="stat-label">Participation Rate</div>
      </div>
    </div>
  </section>

  <!-- Student Services Pillars -->
  <section class="content-section">
    <h2 class="section-title">Core Student Services</h2>
    <p class="section-subtitle">Key features available through the student portal.</p>
    <div class="grid-3">
      <div class="info-card">
        <div class="badge-icon">💻</div>
        <h3>Technical Workshops</h3>
        <p>Hands-on coding bootcamps, AI hackathons, and software engineering sessions run by computing societies and guest industry mentors.</p>
      </div>
      <div class="info-card">
        <div class="badge-icon">🏆</div>
        <h3>Sports & Tournaments</h3>
        <p>Annual inter-faculty championships, badminton leagues, cricket tournaments, and athletics meets managed by the Sports Council.</p>
      </div>
      <div class="info-card">
        <div class="badge-icon">📢</div>
        <h3>Official Announcements</h3>
        <p>Instant official notices from the Student Affairs Division regarding semester schedules, deadlines, logistics, and emergency alerts.</p>
      </div>
    </div>
  </section>

  <!-- Institutional Background -->
  <section class="content-section">
    <div class="info-card" style="border-left: 5px solid var(--coral);">
      <h3>Academic Institution Information</h3>
      <p>
        Developed for <strong>Java Institute for Advanced Technology</strong> student services, CampusHub demonstrates complete web development practices including dynamic relational database queries, authenticated session management, server-side data validation, and clean responsive user interfaces.
      </p>
    </div>
  </section>
</main>
<?php
require_once 'includes/footer.php';
?>
