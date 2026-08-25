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
      padding: 0 20px 64px;
      box-sizing: border-box;
    }

    .hero,
    .content-section {
      width: 100%;
      max-width: 1200px;
      margin-right: auto;
      margin-left: auto;
    }

    .hero {
      position: relative;
      display: flex;
      align-items: center;
      min-height: 360px;
      margin-top: 42px;
      padding: 56px clamp(28px, 7vw, 92px);
      box-sizing: border-box;
      overflow: hidden;
      border-radius: 16px;
      background: linear-gradient(110deg, rgba(23, 27, 42, .92), rgba(23, 27, 42, .26)), url('assets/images/hero1.jpg') center / cover;
    }

    .hero-content {
      position: relative;
      z-index: 1;
      max-width: 620px;
      color: #ffffff;
    }

    .hero-content h1 {
      margin: 0 0 14px;
      font-size: clamp(2.2rem, 5vw, 4.5rem);
      line-height: .98;
    }

    .hero-content p {
      max-width: 500px;
      margin: 0;
      color: rgba(255, 255, 255, .78);
      font-family: Cabin Sketch, cursive;
      font-size: 1.25rem;
      
    }

    .content-section {
      margin-top: 54px;
    }

    .section-heading {
      display: flex;
      align-items: end;
      justify-content: space-between;
      gap: 20px;
      margin-bottom: 22px;
    }

    .section-heading h2 {
      margin: 0;
      color: var(--ink);
      font-size: 1.7rem;
    }

    .section-heading p {
      margin: 0;
      color: var(--muted);
      font-family: Cabin Sketch, cursive;
      font-size: 1.05rem;
    }

    .event-container {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 22px;
    }

    .event-card {
      display: flex;
      min-height: 356px;
      flex-direction: column;
      overflow: hidden;
      border: 1px solid var(--line);
      border-radius: 10px;
      background: #ffffff;
      box-shadow: 0 10px 24px rgba(23, 27, 42, .07);
      transition: transform .2s ease, box-shadow .2s ease;
    }

    .event-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 16px 30px rgba(23, 27, 42, .13);
    }

    .img-container {
      min-height: 174px;
      background: linear-gradient(135deg, rgba(255, 107, 74, .18), transparent), url('assets/images/events/tech_coding_work_shop.jpg') center / cover;
    }

    .event-info {
      display: flex;
      flex: 1;
      flex-direction: column;
      padding: 20px;
    }

    .event-info h3 {
      margin: 0 0 9px;
      color: var(--ink);
      font-size: 1.15rem;
    }

    .event-info p {
      margin: 0;
      color: var(--muted);
      font-family: sans-serif;
      font-size: .92rem;
      line-height: 1.55;
    }

    .event-info p.event-meta {
      margin: 0 0 9px;
      color: var(--coral);
      font-family: cursive, sans-serif;
      font-size: 1rem;
      font-weight: 700;
    }

    .announcement {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 24px;
      padding: 24px 28px;
      border-left: 4px solid var(--coral);
      background: #ffffff;
      box-shadow: 0 10px 24px rgba(23, 27, 42, .06);
    }

    .announcement h3,
    .announcement p {
      margin: 0;
    }

    .announcement h3 {
      color: var(--ink);
      font-size: 1.1rem;
    }

    .announcement p {
      margin-top: 6px;
      color: var(--muted);
      font-family: sans-serif;
      font-size: .9rem;
    }

    .announcement-date {
      flex: 0 0 auto;
      color: var(--coral);
      font-family: sans-serif;
      font-weight: 700;
    }

    .pagination-container {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      margin: 36px auto 0;
      width: 100%;
    }

    .page-link {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 40px;
      height: 40px;
      padding: 0 13px;
      box-sizing: border-box;
      border: 1px solid var(--line);
      border-radius: 6px;
      background: #ffffff;
      color: var(--ink);
      font-family: sans-serif;
      font-size: .88rem;
      font-weight: 700;
      text-decoration: none;
      transition: background .2s ease, border-color .2s ease, color .2s ease;
    }

    .page-link:hover {
      border-color: var(--ink);
      background: #f0f4f8;
    }

    .page-link:focus {
      outline: none;
    }

    .page-link.active {
      border-color: var(--ink);
      background: var(--ink);
      color: #ffffff;
      cursor: default;
    }

    .page-link.disabled {
      border-color: #eaeaea;
      background: #fafafa;
      color: #b5b5b5;
      pointer-events: none;
    }

    @media (max-width: 760px) {
      .page-container {
        padding-right: 14px;
        padding-left: 14px;
      }

      .hero {
        min-height: 300px;
        margin-top: 24px;
      }

      .event-container {
        grid-template-columns: 1fr;
      }

      .section-heading,
      .announcement {
        align-items: flex-start;
        flex-direction: column;
      }

      .pagination-container {
        gap: 5px;
      }

      .page-link {
        min-width: 36px;
        padding: 0 9px;
      }

      .page-link.prev-next {
        padding: 0 8px;
        font-size: .78rem;
      }
    }
  </style>

  <section class="hero">
    <div class="hero-content">
      <h1>Welcome to CampusHub</h1>
      <p>Discover and join exciting events happening around you.</p>
    </div>
  </section>

  <section class="content-section" aria-labelledby="featured-events-title">
    <div class="section-heading">
      <div>
        <h2 id="featured-events-title">Featured Events</h2>
        <p>Find your next campus experience.</p>
      </div>
      <p>Showing 1 to 6 of 18 events</p>
    </div>

    <div class="event-container">
      <article class="event-card">
        <div class="img-container"></div>
        <div class="event-info">
          <p class="event-meta">APR 18, 2026 · 10:00 AM</p>
          <h3>Tech Coding Workshop</h3>
          <p>Build practical skills with a hands-on session led by student developers.</p>
        </div>
      </article>
      <article class="event-card">
        <div class="img-container"></div>
        <div class="event-info">
          <p class="event-meta">APR 22, 2026 · 2:00 PM</p>
          <h3>Design Thinking Lab</h3>
          <p>Turn a bright idea into a thoughtful solution alongside your peers.</p>
        </div>
      </article>
      <article class="event-card">
        <div class="img-container"></div>
        <div class="event-info">
          <p class="event-meta">APR 25, 2026 · 5:30 PM</p>
          <h3>Campus Creative Night</h3>
          <p>Share music, art, and stories in an open evening for the whole campus.</p>
        </div>
      </article>
      <article class="event-card">
        <div class="img-container"></div>
        <div class="event-info">
          <p class="event-meta">APR 29, 2026 · 11:00 AM</p>
          <h3>Career Conversations</h3>
          <p>Meet local professionals and ask the questions that shape your next step.</p>
        </div>
      </article>
      <article class="event-card">
        <div class="img-container"></div>
        <div class="event-info">
          <p class="event-meta">MAY 03, 2026 · 9:00 AM</p>
          <h3>Community Volunteer Day</h3>
          <p>Spend a morning making a tangible difference with campus organizations.</p>
        </div>
      </article>
      <article class="event-card">
        <div class="img-container"></div>
        <div class="event-info">
          <p class="event-meta">MAY 07, 2026 · 6:00 PM</p>
          <h3>Student Founder Meetup</h3>
          <p>Connect with ambitious builders, founders, and future collaborators.</p>
        </div>
      </article>
    </div>

    <nav class="pagination-container" aria-label="Event pages">
      <a class="page-link prev-next disabled" href="#" aria-disabled="true">&laquo; Previous</a>
      <a class="page-link active" href="#" aria-current="page">1</a>
      <a class="page-link" href="#">2</a>
      <a class="page-link" href="#">3</a>
      <a class="page-link" href="#">4</a>
      <a class="page-link" href="#">5</a>
      <a class="page-link prev-next" href="#">Next &raquo;</a>
    </nav>
  </section>

  <section class="content-section" aria-labelledby="latest-announcement-title">
    <div class="section-heading">
      <div>
        <h2 id="latest-announcement-title">Latest Announcement</h2>
      </div>
    </div>
    <article class="announcement">
      <div>
        <h3>Spring event registration is now open</h3>
        <p>Browse the calendar and reserve your place at the events that interest you most.</p>
      </div>
      <span class="announcement-date">APR 10, 2026</span>
    </article>
    <article class="announcement">
      <div>
        <h3>Spring event registration is now open</h3>
        <p>Browse the calendar and reserve your place at the events that interest you most.</p>
      </div>
      <span class="announcement-date">APR 10, 2026</span>
    </article>
    <article class="announcement">
      <div>
        <h3>Spring event registration is now open</h3>
        <p>Browse the calendar and reserve your place at the events that interest you most.</p>
      </div>
      <span class="announcement-date">APR 10, 2026</span>
    </article>
    
  </section>
</main>
<?php
require_once 'includes/footer.php';
?>