# Appendices

## Appendix A: Source Code Structure & Code Evidence

### Project Directory Structure
```
c:\xampp\htdocs\web\
│
├── index.php                     - Main Public Homepage
├── about.php                     - Organization Information
├── events.php                    - Events Listing & Search Filter
├── event_details.php             - Event Detailed View & Registration
├── register_event.php            - Event Registration Handler
├── announcements.php             - Notice Board
├── announcement_details.php      - Single Announcement View
├── gallery.php                   - Interactive Photo & Video Gallery
├── contact.php                   - Contact Form
├── login.php                     - User Authentication Form
├── loginProccess.php             - Server Login Handler
├── logout.php                    - Session Termination
├── register.php                  - New Student Account Creation
├── forgot_password.php           - Password Reset Workflow
│
├── includes/
│   ├── config.php                - Database Credentials
│   ├── db.php                    - MySQL Database Singleton & Prepared Helper
│   ├── functions.php             - Authentication & Sanitization Helpers
│   ├── header.php                - Reusable Semantic Header & Dynamic Nav
│   └── footer.php                - Reusable Semantic Footer & Dynamic Date
│
├── admin/
│   ├── dashboard.php             - Admin Analytics & Action Hub
│   ├── manage_events.php         - Event List & Management
│   ├── add_event.php             - New Event Form with Banner Upload
│   ├── edit_event.php            - Event Update Interface
│   ├── delete_event.php          - Event Deletion Handler
│   ├── manage_students.php       - Student Records Table
│   ├── edit_student.php          - Student Account Editor
│   ├── manage_registrations.php  - Event Attendance Roster
│   ├── manage_announcements.php  - Notice Board CRUD
│   ├── upload_media.php          - Gallery Media Uploader
│   ├── export_events.php         - XML Event Data Generator
│   ├── manage_roles.php          - User Role Manager
│   └── organization_info.php     - System Contact Config
│
├── student/
│   ├── dashboard.php             - Student Portal Dashboard
│   ├── profile.php               - Student Profile & Avatar Upload
│   ├── my_registrations.php      - Registered Events Roster
│   ├── upload_photo.php          - Student Photo Submission
│   └── apply_role.php            - Club Leadership Application
│
├── database/
│   └── campushub.sql             - Complete MySQL Database Schema Script
│
└── assets/
    ├── css/style.css             - Core Stylesheet
    ├── images/                   - Uploaded Media Assets & Banners
    └── js/                       - Client Interaction Scripts
```

### Source Code Screenshots Placeholders

#### Source Screenshot A.1: Database Prepared Helper (`includes/db.php`)
> `[Insert Screenshot of includes/db.php showing getConnection(), iud(), and search() methods]`  
> **Description:** Shows parameterized prepared statements preventing SQL injection vulnerabilities.

#### Source Screenshot A.2: XML Generation Script (`admin/export_events.php`)
> `[Insert Screenshot of admin/export_events.php showing SimpleXMLElement implementation]`  
> **Description:** Shows creation of XML document tree and HTTP attachment header dispatch.

#### Source Screenshot A.3: Media Upload Logic (`admin/upload_media.php`)
> `[Insert Screenshot of admin/upload_media.php showing file upload and validation code]`  
> **Description:** Shows file size checking, extension validation, unique timestamp renaming, and `move_uploaded_file()` execution.

---

## Appendix B: Database Structure & phpMyAdmin Screenshots

### Database Table Inventory (`campushub` schema)

| Table Name | Description | Primary Key | Foreign Keys |
| :--- | :--- | :--- | :--- |
| `users` | Stores student and staff accounts | `user_id` | None |
| `admin` | Assigns admin roles (`moderator`, `admin`, `super_admin`) | `admin_id` | `users_user_id` -> `users.user_id` |
| `events` | Stores event dates, times, seats, status, banners | `event_id` | None |
| `registrations` | Stores student event registration bookings | `registration_id` | `events_event_id`, `users_user_id` |
| `media` | Stores uploaded photos, videos, and JSON tags | `media_id` | `events_event_id`, `users_user_id` |
| `announcements` | Stores campus notices and priority tags | `id` | `admin_admin_id` -> `admin.admin_id` |
| `master_control` | Stores organizational contact email | `id` | None |

### Database Screenshots Placeholders

#### Database Screenshot B.1: phpMyAdmin Database Table Schema
> `[Insert Screenshot of phpMyAdmin showing list of all 7 tables in campushub database]`  
> **Description:** Overview of tables in the `campushub` database.

#### Database Screenshot B.2: `events` Table Structure & Rows
> `[Insert Screenshot of phpMyAdmin Structure tab for events table]`  
> **Description:** Displays column data types (`INT`, `VARCHAR`, `TEXT`, `DATE`, `TIME`, `ENUM`, `JSON`).

#### Database Screenshot B.3: `registrations` Table Relational View
> `[Insert Screenshot of registrations table records showing foreign key links]`  
> **Description:** Demonstrates relation between student IDs and event IDs.

---

## Appendix C: System Testing & Verification Matrix

| Test ID | Test Scenario | Input / Action | Expected Result | Actual Result | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-01** | Student Account Registration | Valid student details in `register.php` | Account created in `users` table; redirect to login | Successfully registered and redirected | **PASS** |
| **TC-02** | Login Authentication | Valid email/phone and password in `login.php` | Session initialized; redirect to appropriate dashboard | Redirected based on role (`student`/`admin`) | **PASS** |
| **TC-03** | Unauthorized Route Guarding | Direct URL access to `admin/dashboard.php` as guest | Access denied; immediate redirect to `login.php` | Redirected immediately | **PASS** |
| **TC-04** | Event Creation with Banner Upload | Fill event details and upload JPG image in `add_event.php` | Event record saved; image saved to `assets/images/` | Event created with banner visible on homepage | **PASS** |
| **TC-05** | Event Seat Booking | Click "Register" on event page while logged in | Record added to `registrations` table; seat count updated | Successfully registered for event | **PASS** |
| **TC-06** | Duplicate Registration Prevention | Re-attempt registering for same event | Alert message: "You are already registered" | Duplicate prevented gracefully | **PASS** |
| **TC-07** | Media Upload & Gallery Display | Upload photo in `admin/upload_media.php` | Image file moved; item visible in `gallery.php` | Image displayed with interactive lightbox | **PASS** |
| **TC-08** | XML Export Generation | Click "Download XML Export" in `export_events.php` | Browser downloads `.xml` file with complete event nodes | XML downloaded and verified well-formed | **PASS** |
| **TC-09** | XSS Sanitization Check | Input `<script>alert(1)</script>` into search/forms | Text converted to HTML entities via `htmlspecialchars()` | Script not executed; text displayed safely | **PASS** |
| **TC-10** | User Logout | Click "Logout" link | Session destroyed; redirected to `login.php` | Session wiped and redirected | **PASS** |
