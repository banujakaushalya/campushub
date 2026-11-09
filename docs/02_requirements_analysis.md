# 2. Requirements Analysis

## 2.1 User Identification and Stakeholders
The CampusHub system caters to two main user groups with clearly separated privileges:

1. **Guest / Public Visitor:**
   - Any campus student or visitor who visits the website without logging in.
   - Can explore the home page, read the About page, view general announcements, browse the public media gallery, view the list of events, and access the contact page.
   - Must sign up or log in before registering for events or uploading photos.

2. **Registered Student:**
   - Authenticated student with a unique profile.
   - Can access the Student Dashboard (`student/dashboard.php`).
   - Can register for upcoming events and track registered events in "My Registrations" (`student/my_registrations.php`).
   - Can update their personal details, phone number, and profile avatar (`student/profile.php`).
   - Can upload activity photos into the campus media gallery (`student/upload_photo.php`).
   - Can apply for club and student leadership positions (`student/apply_role.php`).

3. **Administrator (Admin / Super Admin / Moderator):**
   - Authorized staff managing operations via the Admin Dashboard (`admin/dashboard.php`).
   - Can create, edit, delete, and view all campus events (`admin/manage_events.php`, `admin/add_event.php`, `admin/edit_event.php`).
   - Can view student lists, change student details, and manage accounts (`admin/manage_students.php`, `admin/edit_student.php`).
   - Can monitor event registration rosters (`admin/manage_registrations.php`).
   - Can publish, update, and hide announcements with priority levels (`admin/manage_announcements.php`).
   - Can upload official event photos and videos to the gallery (`admin/upload_media.php`).
   - Can export event data as structured XML files (`admin/export_events.php`).
   - Can maintain master contact information (`admin/organization_info.php`).

---

## 2.2 Functional Requirements

### User & Authentication Management
- **FR-01 (Registration):** New users can create an account by providing first name, last name, phone number, email, gender, and password.
- **FR-02 (Login & Session):** Users log in using email/phone and password. Sessions are securely started and maintained to remember logged-in status.
- **FR-03 (Role-Based Access Control):** Pages check session roles (`student`, `admin`, `super_admin`, `moderator`). Unauthorized attempts are redirected immediately.
- **FR-04 (Password Recovery):** Users who forget their credentials can reset their passwords using the password reset workflow.

### Event Management & Booking
- **FR-05 (Event Discovery):** Students can browse active events, filter by category or date, and search by title keywords.
- **FR-06 (Event Registration):** Logged-in students can register for available seats with a single click. The system checks whether seats remain and avoids duplicate registrations.
- **FR-07 (Admin Event CRUD):** Admins can insert new events with banner images, edit existing events, toggle visibility (`display`/`hide`), or delete expired events.

### Multimedia & Gallery
- **FR-08 (Media Upload):** Students and Admins can upload event photos/videos with custom titles, descriptions, and tags.
- **FR-09 (Multimedia Showcase):** The gallery dynamically displays uploaded media with interactive lightbox popups and filtering options.

### Announcements & Information
- **FR-10 (Notice Board):** Admins can publish notices with priority flags (`low`, `medium`, `high`, `urgent`). Expired notices are automatically hidden or marked.
- **FR-11 (Contact Form):** Visitors can send general inquiries through a validated contact form.

### Data Export (XML)
- **FR-12 (XML Export):** Admins can generate and download an XML file containing comprehensive event records and nested metadata.

---

## 2.3 Non-Functional Requirements

- **Usability:** The user interface uses clear layouts, recognizable icons, distinct call-to-action buttons, and clear feedback messages on every form submission.
- **Responsiveness:** CSS flexbox and grid layouts ensure pages render smoothly on mobile phones, tablets, laptops, and desktop screens.
- **Security:**
  - Passwords are securely hashed using `password_hash()` (Bcrypt).
  - All SQL queries interacting with dynamic user input use prepared statements (`bind_param`) to prevent SQL Injection attacks.
  - User inputs are sanitized using `htmlspecialchars()` to prevent Cross-Site Scripting (XSS).
- **Data Integrity:** Foreign key relationships in MySQL ensure orphaned records are prevented when events or users are referenced in registrations and media tables.
- **Performance:** Lightweight CSS and clean procedural PHP queries ensure pages load quickly on local XAMPP Apache servers without unnecessary dependencies.

---

## 2.4 Documented Assumptions
As permitted by the assignment brief, the following assumptions were made during development:
1. **Local Server Setup:** The application runs in a local XAMPP environment (Apache Web Server, PHP 8.x, MySQL/MariaDB on port 3306).
2. **User Identity:** Students register with a unique phone number and valid email address.
3. **File Uploads:** Uploaded media files (images such as JPEG/PNG/WebP, videos such as MP4) are stored in the local server folder (`assets/images/`), while their relative file paths and metadata are saved in the database.
4. **Email Dispatch:** Email verification and password resets utilize local PHPMailer scripts with fallback simulation when an external SMTP internet connection is unavailable.
5. **Initial Seeding:** The system is pre-seeded with sample categories, events, announcements, and an administrator account (`admin`) to allow immediate testing and demonstration.
