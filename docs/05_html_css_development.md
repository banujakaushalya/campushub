# 5. HTML and CSS Development

## 5.1 HTML5 Implementation Approach
Semantic HTML5 elements were carefully selected throughout the project to ensure the code is well-structured, easy to maintain, and accessible to assistive technologies.

### Key Semantic Tags Used:
- `<header>`: Used at the top of every page for branding and navigation.
- `<nav>`: Wraps primary site navigation links and dropdown menus.
- `<main>`: Encloses the unique central content of each page.
- `<section>`: Divides page sections such as hero banners, feature grids, and statistical summary widgets.
- `<article>`: Encapsulates self-contained items like individual event cards, gallery photo cards, and announcement posts.
- `<footer>`: Contains site-wide metadata, contact details, and copyright information.
- `<form>`, `<label>`, `<input>`, `<select>`, `<textarea>`, `<button>`: Used for accessible and validated form inputs with explicit `for` and `id` bindings.

### Code Snippet: Semantic Event Card Example
```html
<article class="event-card">
  <div class="event-thumb">
    <img src="assets/images/hackathon.jpg" alt="Campus Hackathon 2026">
    <span class="category-badge">Technology</span>
  </div>
  <div class="event-content">
    <time datetime="2026-10-15" class="event-date">Oct 15, 2026</time>
    <h3 class="event-title">Campus Hackathon 2026</h3>
    <p class="event-summary">Join the 24-hour coding challenge at the main auditorium.</p>
    <div class="event-footer">
      <span class="seat-badge">45 Seats Left</span>
      <a href="event_details.php?id=1" class="btn btn-primary">View Details</a>
    </div>
  </div>
</article>
```

---

## 5.2 CSS3 Implementation & Styling Approach
All styles were written in clean, modern CSS using external stylesheets (`assets/css/style.css`) and modular page stylesheets.

### 1. CSS Custom Properties (Design Tokens)
Custom variables were declared to maintain consistency across all color schemes and spacings:
```css
:root {
  --primary-color: #FF6B4A;       /* Vibrant Coral Accent */
  --primary-hover: #E05333;
  --dark-ink: #171B2A;            /* Main Headings & Text */
  --muted-text: #64748B;          /* Subtitles & Descriptions */
  --border-line: #E2E8F0;         /* Card & Container Borders */
  --bg-light: #F8FAFC;            /* Clean Page Background */
  --card-bg: #FFFFFF;             /* White Container Surfaces */
  --success-green: #10B981;
  --danger-red: #EF4444;
  --radius-sm: 6px;
  --radius-md: 12px;
  --shadow-sm: 0 1px 3px rgba(0,0,0,0.08);
  --shadow-hover: 0 10px 25px rgba(0,0,0,0.12);
}
```

### 2. Flexbox and CSS Grid Systems
- **CSS Grid:** Used for 3-column event catalogues and media gallery grids that automatically reflow into 2 columns on tablets and 1 column on mobile phones:
```css
.events-grid, .gallery-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 24px;
}
```
- **Flexbox:** Used for alignment in navigation bars, card headers, form button groups, and dashboard statistic tiles:
```css
.navbar-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px 28px;
}
```

### 3. Micro-Interactions and Hover Animations
To give the website a lively and professional feel, subtle transitions were applied to buttons and cards:
```css
.event-card {
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.event-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-hover);
}
```

### 4. Responsive Media Queries
Media queries were implemented to ensure optimal layout on smaller screens:
```css
@media (max-width: 768px) {
  .nav-menu {
    display: none;
  }
  .mobile-toggle {
    display: block;
  }
  .events-grid {
    grid-template-columns: 1fr;
  }
}
```

---

## 5.3 User Interface Screenshots & Descriptions

### Screenshot 5.1: CampusHub Homepage (`index.php`)
> `[Insert Screenshot of Homepage Hero & Featured Events Here]`  
> **Description:** Shows the responsive homepage featuring the top navigation bar, welcome hero banner, dynamic statistics bar, and upcoming events grid.

### Screenshot 5.2: Public Event Directory (`events.php`)
> `[Insert Screenshot of Events Page with Search and Filter bar Here]`  
> **Description:** Shows the events catalog with the search input bar, category filter buttons, and responsive event cards.

### Screenshot 5.3: Responsive Media Gallery (`gallery.php`)
> `[Insert Screenshot of Media Gallery and Lightbox Modal Here]`  
> **Description:** Displays the media gallery with media tags, photo grid layout, and interactive lightbox preview.
