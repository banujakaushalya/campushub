# 1. Introduction

## 1.1 Background Scenario
CampusHub is an active and expanding student services organisation. It is responsible for organising, managing, and promoting student clubs, workshops, competitions, sports tournaments, cultural events, and student communities across various partner educational institutes.

Before introducing this digital platform, CampusHub handled daily operations manually using separate spreadsheets, email threads, social media groups, and paper sign-up sheets. As student participation increased, several operational challenges occurred:
- Student event registrations were hard to track accurately, causing double bookings and missed seats.
- Event announcements were scattered across different social channels, meaning students often missed important updates or deadlines.
- Activity photos and media were stored across multiple private drives and personal chat groups rather than in a central showcase gallery.
- Administrators spent excessive manual effort cross-checking student details, tracking event attendance, and updating club lists.

## 1.2 Purpose of the Web Information System
To solve these problems, management approved the development of **CampusHub**, a centralized web-based information portal. 

The primary goal of the system is to provide a single, organized platform where:
1. **Students** can easily browse upcoming events, view notices, register online with instant feedback, update their member profile, upload event activity photos, and review their registration history.
2. **Administrators** can publish and manage events, oversee student records, review event participants, upload promotional media, manage site announcements, and export event data for organizational reporting.

## 1.3 System Overview & Technology Stack
The web application was developed using standard web technologies taught in Web Programming I:
- **Client-Side (Front-End):** HTML5 for semantic page layout and accessibility, and custom CSS3 for responsive and visually appealing styling across desktop, tablet, and mobile screens.
- **Server-Side (Back-End):** PHP 8.x for backend logic, form processing, user authentication, session security, and data validation.
- **Database Layer:** MySQL / MariaDB database running on Apache via XAMPP, communicating via PHP's `mysqli` extension with prepared statements.
- **Data Exchange:** XML format export for exporting event information and structured data handling.
- **Multimedia & File Handling:** Server-side file upload scripts handling images and video assets stored on the web server with metadata saved in the database.

Through this project, I turned the manual, disconnected process of CampusHub into an efficient, interactive, and responsive web system.
