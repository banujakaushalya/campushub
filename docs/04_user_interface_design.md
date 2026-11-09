# 4. User Interface Design

## 4.1 UI Design Philosophy
The user interface of CampusHub was designed around three main principles: **simplicity**, **clarity**, and **responsiveness**. Because students and staff access the system from various devices (mobile phones, laptops, and lab desktop PCs), the layout adapts fluidly to different screen dimensions.

---

## 4.2 Header and Navigation Bar (`includes/header.php`)
The header is the central navigation hub of the entire application. It includes:
1. **Brand Logo & Title:** A clean logo icon with the bold "CampusHub" text linking directly back to the homepage.
2. **Main Navigation Links:** Easy-to-find links for Home, Events, Announcements, Gallery, About, and Contact.
3. **Dynamic User Action Area:**
   - **For Guests:** Displays direct "Login" and "Register" buttons.
   - **For Logged-in Students:** Displays a "Dashboard" shortcut button, a personalized welcome avatar, and a "Logout" link.
   - **For Administrators:** Displays an "Admin Panel" shortcut button with an administrative shield badge.
4. **Mobile Hamburger Menu:** On smaller screens, navigation collapses into a mobile menu toggle, keeping the screen uncluttered.

---

## 4.3 Footer Component (`includes/footer.php`)
The footer provides a balanced closing section at the bottom of every page:
- **Column 1:** CampusHub summary and mission statement.
- **Column 2:** Quick links to major pages (Events, Gallery, Announcements).
- **Column 3:** Student help center, club coordination contacts, and emergency helpline.
- **Bottom Bar:** Dynamic copyright year generated with PHP `date('Y')`.

---

## 4.4 Major Page Layouts

### 1. Home Page (`index.php`)
- **Hero Section:** High-impact banner with an encouraging call-to-action ("Explore Events", "Join a Club").
- **Metrics Counter:** Highlights total student registrations, active clubs, and organized events.
- **Featured Events Section:** Displays upcoming events in responsive 3-column card layouts with event date badges, images, and "Register" buttons.
- **Announcements Preview:** Highlights the latest high-priority notices for immediate student attention.

### 2. Events Directory & Event Details (`events.php` & `event_details.php`)
- **Search & Filter Bar:** Allows students to search events by keyword or filter by category (e.g., Tech, Sports, Arts, Workshop).
- **Event Card Structure:** Includes thumbnail preview, date and time indicators, location badge, available seats counter, and a "View Details" button.
- **Event Detail Page:** Shows full description, schedule timeline, host details, and a one-click registration form.

### 3. Multimedia Gallery (`gallery.php`)
- **Filter Pills & Search:** Quick-filter media by type (All, Photos, Videos) or specific tags.
- **Responsive Media Grid:** Displays cards with media previews, type badges, event tags, and uploader attribution.
- **Interactive Lightbox Modal:** Clicking any item opens a full-screen lightbox modal displaying the high-resolution photo or video player with its complete caption.

### 4. Student Dashboard (`student/dashboard.php`)
- **Welcome Banner:** Greets the student by name and displays current date and active role.
- **Summary Cards:** Quick statistics showing "My Active Registrations", "Photos Uploaded", and "Upcoming Activities".
- **Action Shortcuts:** Quick buttons to update profile, view registered events, and upload photos.

### 5. Administrator Dashboard (`admin/dashboard.php`)
- **Management Metric Grid:** Live counts for total students, total events, total registrations, and announcements.
- **Action Hub:** Fast navigation buttons to manage events, manage students, manage announcements, and export XML reports.
- **Recent Activity Table:** Shows the latest event registrations and user signups in a clean tabular view.

---

## 4.5 UI Layout Wireframe Structure

```
+-------------------------------------------------------------------+
| [CampusHub Logo]    Home  Events  Gallery  About    [Login/Profile]|
+-------------------------------------------------------------------+
| HERO SECTION                                                      |
| "Connect, Participate, and Grow at CampusHub"                     |
| [ Explore Events Button ]         [ View Gallery Button ]         |
+-------------------------------------------------------------------+
| FEATURED UPCOMING EVENTS (3-Column Grid)                          |
| +-----------------+   +-----------------+   +-----------------+   |
| | Event Image     |   | Event Image     |   | Event Image     |   |
| | Hackathon 2026  |   | Sports Meet     |   | Drama Night     |   |
| | Date: Oct 12    |   | Date: Oct 18    |   | Date: Oct 25    |   |
| | [View Details]  |   | [View Details]  |   | [View Details]  |   |
| +-----------------+   +-----------------+   +-----------------+   |
+-------------------------------------------------------------------+
| FOOTER: About CampusHub | Quick Links | Contact Support | Copyright|
+-------------------------------------------------------------------+
```
