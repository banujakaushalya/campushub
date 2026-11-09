# 3. Website Planning

## 3.1 Website Purpose and Scope
The purpose of CampusHub is to centralize and automate student engagement workflows across partner campuses. 

### Scope Boundaries:
- **Public Portal:** Public visibility of campus life, news announcements, event directory, and interactive media gallery.
- **Student Member Portal:** Self-service capabilities for profile management, registration tracking, photo submissions, and role applications.
- **Administrative Portal:** Content authoring, event scheduling, student database oversight, registration auditing, and XML reporting.

---

## 3.2 Navigation Architecture & Sitemap
The website follows a clear hierarchy with consistent navigation paths.

```
CampusHub Web Application
│
├── Public Navigation
│   ├── Home (index.php)
│   ├── Events (events.php)
│   │   ├── Event Details (event_details.php)
│   │   └── Register for Event (register_event.php)
│   ├── Announcements (announcements.php)
│   │   └── Announcement Details (announcement_details.php)
│   ├── Gallery (gallery.php)
│   ├── About Us (about.php)
│   ├── Contact Us (contact.php)
│   ├── Login (login.php)
│   ├── Register (register.php)
│   └── Forgot Password (forgot_password.php)
│
├── Student Portal (student/)
│   ├── Dashboard (student/dashboard.php)
│   ├── My Profile & Avatar (student/profile.php)
│   ├── My Registrations (student/my_registrations.php)
│   ├── Upload Activity Photo (student/upload_photo.php)
│   └── Apply for Club Role (student/apply_role.php)
│
└── Administrator Portal (admin/)
    ├── Admin Dashboard (admin/dashboard.php)
    ├── Manage Events (admin/manage_events.php)
    │   ├── Add Event (admin/add_event.php)
    │   ├── Edit Event (admin/edit_event.php)
    │   └── Delete Event (admin/delete_event.php)
    ├── Manage Students (admin/manage_students.php)
    │   └── Edit Student (admin/edit_student.php)
    ├── Manage Registrations (admin/manage_registrations.php)
    ├── Manage Announcements (admin/manage_announcements.php)
    ├── Upload Gallery Media (admin/upload_media.php)
    ├── Manage User Roles (admin/manage_roles.php)
    ├── Export Events to XML (admin/export_events.php)
    └── Organization Settings (admin/organization_info.php)
```

---

## 3.3 Key Design Decisions

### 1. Modular Header & Footer Architecture
Instead of repeating HTML `<head>`, navigation bars, and footers across 20+ separate PHP pages, I created reusable modular includes:
- `includes/header.php`: Contains HTML head, CSS linkages, top branding bar, dynamic navigation links (which adjust according to whether a student or admin is logged in), and mobile menu drawer.
- `includes/footer.php`: Houses footer links, copyright notice, contact details, and common JavaScript references.
- `includes/db.php`: Encapsulates database connection and query helpers.

### 2. Design System & Color Palette
To make the site look modern and welcoming for students:
- **Primary Color:** Coral / Deep Orange (`#FF6B4A`) – used for main actions, active navigation pills, and important buttons.
- **Secondary Dark Tone:** Dark Ink Slate (`#171B2A` / `#0F172A`) – used for high-contrast text, headers, and clean dark footers.
- **Background Tones:** Soft Off-White / Light Gray (`#F8FAFC` and `#FFFFFF`) – used for cards and main body areas to prevent eye fatigue.
- **Accent & Status Colors:** Emerald Green (`#10B981`) for success badges and confirmed bookings, Amber/Orange (`#F59E0B`) for warnings, and Rose Red (`#EF4444`) for errors.

### 3. Typography
I selected clean modern system fonts (`-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif`) to ensure fast page loading without external font network delays, while delivering crisp readability across all operating systems.

### 4. Layout System
I utilized standard **CSS Flexbox** and **CSS Grid** rather than bulky third-party frontend frameworks. This gives full control over custom styling, demonstrates pure CSS proficiency as expected in Web Programming I, and ensures fast loading speeds.
