# Pure MS PowerPoint-Style Presentation Generator for CampusHub Viva Voce
import os
import subprocess
import pypdf

slides_data = [
    # SLIDE 1
    {
        "title": "PROJECT INTRODUCTION & SCOPE",
        "content": """
<h3 class="ppt-heading">WHAT THE PROJECT IS ABOUT</h3>
<ul class="ppt-list">
  <li>User Management: Students සහ Administrators සඳහා secure role-based authentication and access control</li>
  <li>Event Management: Dynamic event discovery, category filtering, සහ online seat capacity booking</li>
  <li>Multimedia Gallery: HTML5 audio සහ video streaming සමඟ student activity photo upload mechanism</li>
  <li>Notice Board: Urgent, High, සහ Normal priority tags සහිත campus announcements</li>
  <li>XML Data Exchange: External systems සහ partner campuses සමඟ data share කිරීමට structured XML export engine</li>
  <li>Database Integration: MySQL database එක සමඟ secure relational CRUD operations</li>
</ul>
<p class="ppt-paragraph" style="margin-top: 14pt;">
  CampusHub කියන්නේ partner educational institutions හරහා student clubs, workshops, sports tournaments, competitions, සහ cultural activities coordinate කරන centralised web-based information system එකකි. මම මේ project එකට ඉහත සඳහන් core components එකතු කරගෙන තියෙනවා:
</p>
"""
    },

    # SLIDE 2
    {
        "title": "CORE PURPOSE, PROPOSED SOLUTION & VERIFIED SCOPE",
        "content": """
<div class="ppt-section">
  <h3 class="ppt-heading">CORE PURPOSE</h3>
  <p class="ppt-paragraph">
    Manual registration issues, scattered event notices, disorganised media storage, සහ high administrative workload වගේ operational difficulties address කරලා, campus operations එකම central digital platform එකකට ගෙන ඒම තමයි core purpose එක.
  </p>
</div>

<div class="ppt-section">
  <h3 class="ppt-heading">PROPOSED SOLUTION</h3>
  <p class="ppt-paragraph">
    Modular 3-Tier Web Architecture එකක් මත HTML5, CSS3, PHP 8.x, සහ MySQL (MariaDB via XAMPP) යොදාගෙන, secure, dynamic, සහ responsive web application එකක් construct කරලා තියෙනවා.
  </p>
</div>

<div class="ppt-section">
  <h3 class="ppt-heading">VERIFIED SCOPE</h3>
  <p class="ppt-paragraph">
    User roles 3ක් (Guest, Registered Student, System Admin), dynamic Event CRUD with automated seat limit checks, MIME-type validated file upload, well-formed XML data export, සහ prepared statements සහිත secure backend එකක් මේකට ඇතුළත් කරලා තියෙනවා.
  </p>
</div>
"""
    },

    # SLIDE 3
    {
        "title": "SYSTEM STAKEHOLDERS & ACCESS ROLES",
        "content": """
<div class="ppt-section">
  <h3 class="ppt-heading">Guest / Public Visitor</h3>
  <ul class="ppt-list">
    <li>Public visitors ලට public Home, About, Events, Announcements, Gallery, සහ Contact pages browse කිරීමට අවසර ඇත.</li>
    <li>Event details සහ upcoming schedules search සහ filter කර නැරඹිය හැක.</li>
    <li>Event එකකට register වීමට හෝ photo upload කිරීමට අනිවාර්යයෙන්ම account එකක් create කර log in විය යුතුය.</li>
  </ul>
</div>

<div class="ppt-section">
  <h3 class="ppt-heading">Registered Student</h3>
  <ul class="ppt-list">
    <li>Personalized Student Dashboard (student/dashboard.php) වෙත access එක.</li>
    <li>Available events සඳහා 1-click online registration සහ dynamic seat reservation.</li>
    <li>student/my_registrations.php හරහා තමන් register වූ events track කිරීම.</li>
    <li>Profile details, contact number, avatar update කිරීම සහ activity photos upload කිරීම.</li>
  </ul>
</div>

<div class="ppt-section">
  <h3 class="ppt-heading">System Administrator</h3>
  <ul class="ppt-list">
    <li>Centralized Admin Dashboard (admin/dashboard.php) වෙත full administrative access එක.</li>
    <li>Events create, edit, delete, සහ visibility (display/hide) toggle කිරීම.</li>
    <li>Student accounts review කිරීම, registration rosters verify කිරීම, priority notices publish කිරීම, සහ XML data export කිරීම.</li>
  </ul>
</div>
"""
    },

    # SLIDE 4
    {
        "title": "Assumptions",
        "content": """
<ul class="ppt-list" style="margin-top: 10pt;">
  <li>Web application එක local development stack එකක් වන XAMPP (Apache HTTP Server 2.4+, PHP 8.x, MySQL/MariaDB) මත deploy කරලා test කරනවා.</li>
  <li>Modern web browsers (Chrome, Edge, Firefox, Safari) හරහා desktop, tablet, සහ mobile screen resolutions වලට responsive layouts automatically adjust වෙනවා.</li>
  <li>Event එකකට register වෙන්න කලින් student කෙනෙක් අනිවාර්යයෙන්ම system එකට log in වෙලා ඉන්න ඕනේ.</li>
  <li>එකම student කෙනෙක්ට එකම event එකට duplicate registrations කරන්න system එකෙන් allow කරන්නේ නෑ.</li>
  <li>Event එකක registered count එක max_seats limit එකට ළඟා වුණාම ඒ event එක "Full" විදිහට සලකනවා.</li>
  <li>Passwords database එකේ store කරන්නේ cryptographic Bcrypt hashing algorithm එකෙන් hash කරලා පමණයි.</li>
  <li>Client-side HTML5 validation යනු user convenience එකක් පමණක් වන අතර, ultimate security defense එක ලෙස server-side PHP validation ක්‍රියාත්මක වෙනවා.</li>
  <li>Relational tables අතර foreign key constraints මඟින් orphaned records prevent කර data integrity තහවුරු කරනවා.</li>
</ul>
"""
    },

    # SLIDE 5
    {
        "title": "Requirements",
        "content": """
<ul class="ppt-list" style="margin-top: 10pt;">
  <li>New student accounts register කරගත හැකි අතර, Bcrypt password hashing සහ role redirection සහිත secure authentication system එකක් තියෙන්න ඕනේ.</li>
  <li>Keyword search සහ category filtering (Workshops, Tech, Sports, Cultural) මඟින් events browse කරන්න සහ seat availability verify කරලා online book කරන්න student ට allow කරන්න ඕනේ.</li>
  <li>Administrators ලට events create, update, delete, සහ hide කරන්න සම්පූර්ණ Event CRUD operations තියෙන්න ඕනේ.</li>
  <li>Campus notices සහ urgent updates priority tags (Urgent, High, Normal) එක්ක notice board එකේ display කරන්න ඕනේ.</li>
  <li>Event activity photos සහ videos upload කර display කිරීමට secure multimedia gallery එකක් තියෙන්න ඕනේ.</li>
  <li>Active events වල සම්පූර්ණ තොරතුරු structured XML feed එකක් ලෙස generate කර download කර ගැනීමට export engine එකක් තියෙන්න ඕනේ.</li>
  <li>SQL Injection, XSS, සහ unauthorized page access වැළැක්වීමට robust backend security mechanisms ක්‍රියාත්මක වෙන්න ඕනේ.</li>
</ul>
"""
    },

    # SLIDE 6
    {
        "title": "Navigation Architecture & File Structure",
        "content": """
<p class="ppt-paragraph">
  Application එකේ reusability සහ maintainability උපරිම කරන්න modular structure එකක් යොදාගෙන තියෙනවා:
</p>
<ul class="ppt-list">
  <li><strong>Public Layer:</strong> index.php, events.php, event_details.php, announcements.php, gallery.php, about.php, contact.php, login.php, register.php, forgot_password.php</li>
  <li><strong>Student Portal (/student):</strong> student/dashboard.php, my_registrations.php, profile.php, upload_photo.php, apply_role.php, auth_check.php</li>
  <li><strong>Admin Portal (/admin):</strong> admin/dashboard.php, manage_events.php, add_event.php, edit_event.php, manage_students.php, manage_registrations.php, manage_announcements.php, export_events_xml.php</li>
  <li><strong>Shared Includes (/includes):</strong> db.php (central DB connection), header.php (session-aware navigation), footer.php, functions.php (sanitization helpers)</li>
</ul>
<p class="ppt-paragraph" style="margin-top: 12pt;">
  header.php එක dynamically change වෙනවා: Guest පරිශීලකයින්ට "Login / Register" buttons පෙන්වන අතර, logged-in student පරිශීලකයින්ට "Dashboard", avatar, සහ "Logout" options ස්වයංක්‍රීයව render වේ.
</p>
"""
    },

    # SLIDE 7
    {
        "title": "Front-End Architecture & UI Design",
        "content": """
<div class="ppt-section">
  <h3 class="ppt-heading">Semantic HTML5 Elements</h3>
  <p class="ppt-paragraph">
    Clean document structure එකක් සහ accessibility සහතික කිරීමට &lt;header&gt;, &lt;nav&gt;, &lt;main&gt;, &lt;section&gt;, &lt;article&gt;, &lt;aside&gt;, සහ &lt;footer&gt; semantic tags භාවිතා කරලා තියෙනවා. Forms වල &lt;label&gt;, &lt;input&gt;, &lt;select&gt;, &lt;button&gt; structured විදිහට implement කරලා තියෙනවා.
  </p>
</div>

<div class="ppt-section">
  <h3 class="ppt-heading">Responsive CSS3 Layout</h3>
  <p class="ppt-paragraph">
    External CSS3 stylesheet එකක් මඟින් mobile-first responsive grid එකක් ගොඩනගා ඇත: <code>grid-template-columns: repeat(auto-fill, minmax(300px, 1fr))</code>. මේ හරහා screen size එක අනුව columns 1, 2, හෝ 3ක් ලෙස cards fluidly adjust වේ.
  </p>
</div>

<div class="ppt-section">
  <h3 class="ppt-heading">Design Palette & Visual Consistency</h3>
  <ul class="ppt-list">
    <li>Primary Action Color: Coral (#FF6B4A) - CTA buttons සහ active state indicators</li>
    <li>Readable Typography: Slate Ink (#171B2A) - High contrast readability</li>
    <li>Clean Canvas: Soft White (#FFFFFF / #F8FAFC) - Minimalist presentation layout</li>
    <li>Interactive Feedback: Card hover transitions මඟින් modern user experience එකක් සලසා ඇත.</li>
  </ul>
</div>
"""
    },

    # SLIDE 8
    {
        "title": "Database Schema & Relational Design",
        "content": """
<p class="ppt-paragraph">
  Database architecture එක 3rd Normal Form (3NF) වලට අනුව normalize කරලා තියෙනවා. Tables 9ක් අතර primary සහ foreign key relationships enforce කරලා තියෙනවා:
</p>
<ul class="ppt-list">
  <li><strong>users Table:</strong> Student සහ staff accounts, Bcrypt password hashes, email, phone, avatar path, role, සහ account status store කරයි. Primary Key: id</li>
  <li><strong>events Table:</strong> Event title, category, date, venue, max seat capacity, banner image, සහ visibility status (display/hidden) manage කරයි. Primary Key: event_id</li>
  <li><strong>registrations Table:</strong> Student event bookings track කරන composite junction table එක. Foreign Keys: user_id, event_id. Duplicate bookings database level එකෙන් block කරයි.</li>
  <li><strong>media Table:</strong> User-uploaded activity photos සහ videos වල server file paths store කරයි. Foreign Keys: user_id, event_id.</li>
  <li><strong>announcements Table:</strong> Urgent, High, සහ Normal priority tags සහිත campus notices manage කරයි. Foreign Key: admin_id.</li>
  <li><strong>Foreign Key Constraints:</strong> ON DELETE CASCADE සහ RESTRICT මඟින් orphaned records ඇතිවීම 100% වළක්වා referential integrity තහවුරු කර ඇත.</li>
</ul>
"""
    },

    # SLIDE 9
    {
        "title": "Backend Architecture & Database Connectivity",
        "content": """
<div class="ppt-section">
  <h3 class="ppt-heading">Centralized DB Connection (includes/db.php)</h3>
  <p class="ppt-paragraph">
    PHP's mysqli extension එක centralized helper class එකක් ලෙස implement කර ඇත. Database connection error එකක් ආවොත් safe generic message එකක් display කරන අතර database credentials client ට expose නොවේ. UTF-8 (utf8mb4) character encoding explicitly set කර ඇත.
  </p>
</div>

<div class="ppt-section">
  <h3 class="ppt-heading">Prepared Statements & SQL Injection Defense</h3>
  <p class="ppt-paragraph">
    Dynamic queries සියල්ල prepared statements සහ parameter binding මඟින් execute කරනු ලැබේ:
  </p>
  <div class="ppt-code">
    $stmt = $conn-&gt;prepare("SELECT id, password_hash, role FROM users WHERE email = ?");<br>
    $stmt-&gt;bind_param("s", $email);<br>
    $stmt-&gt;execute();
  </div>
  <p class="ppt-paragraph" style="margin-top: 4pt;">
    SQL template එක සහ user data වෙන වෙනම database engine එකට යවන නිසා SQL Injection attacks 100% වළක්වා ඇත.
  </p>
</div>

<div class="ppt-section">
  <h3 class="ppt-heading">Cryptographic Password Security</h3>
  <p class="ppt-paragraph">
    Passwords කිසිවිටෙක plain-text ලෙස save නොකරන අතර, PHP password_hash($pwd, PASSWORD_BCRYPT) සහ password_verify() මඟින් secure salted hashing ක්‍රියාත්මක වේ.
  </p>
</div>
"""
    },

    # SLIDE 10
    {
        "title": "Session Management & Role-Based Access Control",
        "content": """
<div class="ppt-section">
  <h3 class="ppt-heading">Session Lifecycle & State Preservation</h3>
  <p class="ppt-paragraph">
    HTTP protocol එක stateless බැවින්, authenticated user state එක request අතර preserve කරගැනීමට PHP Sessions භාවිතා කරයි:
  </p>
  <ul class="ppt-list">
    <li>session_start(): Login වූ විගස server-side session initialization</li>
    <li>Session Variables: $_SESSION['user_id'], $_SESSION['user_role'], $_SESSION['user_name']</li>
    <li>Session Invalidation: session_unset() සහ session_destroy() මඟින් logout වීමේදී session data clear කර browser back-button hijacking වළක්වයි.</li>
  </ul>
</div>

<div class="ppt-section">
  <h3 class="ppt-heading">Page-Level RBAC Guards (auth_check.php)</h3>
  <p class="ppt-paragraph">
    Unauthorized visitors හෝ unauthorized roles sensitive pages වලට direct URL manipulation මඟින් ඇතුළු වීම වැළැක්වීමට gatekeeper scripts ක්‍රියාත්මක වේ:
  </p>
  <ul class="ppt-list">
    <li>Student Portal: student/auth_check.php මඟින් unauthorized visitors ලා login.php?error=unauthorized වෙත redirect කරයි.</li>
    <li>Admin Portal: $_SESSION['user_role'] === 'admin' verify කර, regular students පරිපාලන පිටු වෙත පිවිසීම 100% block කරනු ලැබේ.</li>
  </ul>
</div>
"""
    },

    # SLIDE 11
    {
        "title": "Core Module: Event Booking & Seat Management",
        "content": """
<div class="ppt-section">
  <h3 class="ppt-heading">Dynamic Event Discovery (events.php)</h3>
  <p class="ppt-paragraph">
    ක්‍රියාකාරී events පමණක් database එකෙන් dynamically retrieve කර (WHERE status = 'display'), categories (Workshops, Tech, Sports, Cultural) සහ keywords මඟින් filter කිරීමට ඉඩ සලසයි. සෑම event card එකකම available seats ගණන (max_seats - booked_seats) real-time dynamically calculate කර පෙන්වයි.
  </p>
</div>

<div class="ppt-section">
  <h3 class="ppt-heading">2-Step Booking Verification Algorithm</h3>
  <p class="ppt-paragraph">
    ශිෂ්‍යයෙකු "Register for Event" ක්ලික් කළ විට, system එක පියවර 2 කින් යුත් verification logic එකක් execute කරයි:
  </p>
  <ul class="ppt-list">
    <li><strong>1. Duplicate Booking Check:</strong> SELECT COUNT(*) FROM registrations WHERE user_id = ? AND event_id = ?. දැනටමත් ලියාපදිංචි වී ඇත්නම් warning message එකක් ලබා දේ.</li>
    <li><strong>2. Capacity Limit Check:</strong> Booked seats ගණන max_seats සීමාව ඉක්මවා ඇත්නම් "Event Full" ලෙස සලකා registration එක ප්‍රතික්ෂේප කරයි.</li>
    <li><strong>3. Safe Record Insertion:</strong> Checks pass වූ පසු record එක registrations table එකට insert වී success notification එක dashboard එකේ display වේ.</li>
  </ul>
</div>
"""
    },

    # SLIDE 12
    {
        "title": "XML Data Integration & Dynamic Export Engine",
        "content": """
<div class="ppt-section">
  <h3 class="ppt-heading">Strategic Role of XML in CampusHub</h3>
  <p class="ppt-paragraph">
    Partner educational institutions, mobile apps, සහ campus display screens සමඟ event data platform-independent ආකෘතියකින් share කර ගැනීමට XML export engine එකක් (admin/export_events_xml.php) සකස් කර ඇත.
  </p>
</div>

<div class="ppt-section">
  <h3 class="ppt-heading">Dynamic XML Generation & Well-Formedness</h3>
  <p class="ppt-paragraph">
    PHP DOMDocument / SimpleXMLElement හරහා database records iterate කර well-formed hierarchical XML structure එකක් generate කරයි:
  </p>
  <ul class="ppt-list">
    <li>Set response header: header("Content-Type: application/xml; charset=UTF-8");</li>
    <li>Text encoding via htmlspecialchars() to prevent XML parsing syntax errors</li>
  </ul>
  <div class="ppt-code">
    &lt;?xml version="1.0" encoding="UTF-8"?&gt;<br>
    &lt;campushub_events exported_at="2026-10-29"&gt;<br>
    &nbsp;&nbsp;&lt;event id="101"&gt;<br>
    &nbsp;&nbsp;&nbsp;&nbsp;&lt;title&gt;Annual Tech Hackathon 2026&lt;/title&gt;<br>
    &nbsp;&nbsp;&nbsp;&nbsp;&lt;category&gt;Technology&lt;/category&gt;<br>
    &nbsp;&nbsp;&nbsp;&nbsp;&lt;venue&gt;Main Computing Auditorium&lt;/venue&gt;<br>
    &nbsp;&nbsp;&nbsp;&nbsp;&lt;capacity&gt;150&lt;/capacity&gt;<br>
    &nbsp;&nbsp;&lt;/event&gt;<br>
    &lt;/campushub_events&gt;
  </div>
</div>
"""
    },

    # SLIDE 13
    {
        "title": "Multimedia Processing & Secure File Upload",
        "content": """
<div class="ppt-section">
  <h3 class="ppt-heading">HTML5 Audio & Video Integration (gallery.php)</h3>
  <p class="ppt-paragraph">
    Third-party plugins හෝ proprietary players මත යැපීමකින් තොරව, standard HTML5 &lt;video controls&gt; සහ &lt;audio controls&gt; elements මඟින් campus event highlight videos සහ recordings responsive විදිහට stream කරනු ලැබේ.
  </p>
</div>

<div class="ppt-section">
  <h3 class="ppt-heading">4-Stage File Upload Security Pipeline</h3>
  <p class="ppt-paragraph">
    Unrestricted File Upload මඟින් server එක compromise වීම වැළැක්වීමට strict verification pipeline එකක් ක්‍රියාත්මක කර ඇත:
  </p>
  <ul class="ppt-list">
    <li><strong>1. Extension Whitelisting:</strong> .jpg, .jpeg, .png, .webp, .mp4 පමණක් allow කර executable files (.php, .exe, .sh) immediately reject කරයි.</li>
    <li><strong>2. MIME Signature Inspection:</strong> finfo_file(FILEINFO_MIME_TYPE) මඟින් actual byte signature එක examine කර spoofed extensions හඳුනා ගනී.</li>
    <li><strong>3. File Size Enforcing:</strong> Max 5MB for images සහ 20MB for videos enforce කර server resource exhaustion වළක්වයි.</li>
    <li><strong>4. Cryptographic Renaming:</strong> uniqid("media_", true) මඟින් original file name එක replace කර directory traversal සහ file overwrite attacks 100% වළක්වයි.</li>
  </ul>
</div>
"""
    },

    # SLIDE 14
    {
        "title": "Validation Strategy & Error Management",
        "content": """
<div class="ppt-section">
  <h3 class="ppt-heading">Two-Tier Validation Architecture</h3>
  <ul class="ppt-list">
    <li><strong>1. Client-Side Validation (Browser Level):</strong> HTML5 native constraints (required, type="email", type="tel", minlength="6"). Form submission එකට කලින්ම immediate feedback ලබා දී server overhead අවම කරයි.</li>
    <li><strong>2. Server-Side Validation (PHP Backend - Primary Defense):</strong> Client-side checks bypass කළද data sanitize කර validate කරන ultimate barrier එකයි: clean_input(), filter_var($email, FILTER_VALIDATE_EMAIL), regex phone validation.</li>
  </ul>
</div>

<div class="ppt-section">
  <h3 class="ppt-heading">Cross-Site Scripting (XSS) Mitigation</h3>
  <p class="ppt-paragraph">
    Browser එකට render කරන සෑම dynamic variable එකක්ම output encoding wrapper function එකක් මඟින් sanitize කරනු ලැබේ:
  </p>
  <div class="ppt-code">
    function e($data) { return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8'); }
  </div>
  <p class="ppt-paragraph" style="margin-top: 4pt;">
    Special characters (&lt;, &gt;, &amp;, &quot;, &#39;) HTML entities බවට පත් කරන නිසා script injection වැළැක්වේ.
  </p>
</div>

<div class="ppt-section">
  <h3 class="ppt-heading">User-Friendly Error Feedback</h3>
  <p class="ppt-paragraph">
    Raw database errors හෝ stack traces කිසිවිටෙකත් end-user ට පෙන්වන්නේ නැත. Contextual alert messages (alert-success, alert-error) මඟින් පැහැදිලි actionable feedback ලබා දේ.
  </p>
</div>
"""
    },

    # SLIDE 15
    {
        "title": "Program Implementation & Viva Defence Readiness",
        "content": """
<div class="ppt-section">
  <h3 class="ppt-heading">Architectural Defence</h3>
  <p class="ppt-paragraph">
    3-Tier modular structure එක තෝරාගත්තේ code reusability, maintenance efficiency, සහ separation of concerns සහතික කර ගැනීමටයි. Reusable includes (header.php, footer.php, db.php) මඟින් DRY principle අනුගමනය කර ඇත.
  </p>
</div>

<div class="ppt-section">
  <h3 class="ppt-heading">Scenario-Based Coding Challenges Readiness</h3>
  <p class="ppt-paragraph">
    Viva Voce විභාගයේදී examiner විසින් ඉදිරිපත් කරන ඕනෑම live scenario-based coding challenge එකකට මුහුණ දීමට සූදානම් කර ඇත:
  </p>
  <ul class="ppt-list">
    <li><strong>Scenario 1: New Form Field එකක් එක් කිරීම:</strong> Form input capture, server-side validation, සහ prepared query එකට parameter binding.</li>
    <li><strong>Scenario 2: Cancellation Feature:</strong> DELETE FROM registrations WHERE reg_id = ? AND user_id = ? logic execute කිරීම.</li>
    <li><strong>Scenario 3: Relational JOIN Query:</strong> users, events, සහ registrations tables join කර dynamic attendance report ලබා ගැනීම.</li>
    <li><strong>Scenario 4: Custom XML Tag Export:</strong> Export script එකට new XML attributes එක් කිරීම.</li>
  </ul>
</div>
"""
    }
]

html_template = """<!DOCTYPE html>
<html lang="si">
<head>
<meta charset="utf-8">
<title>CampusHub Presentation</title>
<style>
  @page {
    size: 960pt 540pt;
    margin: 0;
  }
  * {
    box-sizing: border-box;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }
  html, body {
    margin: 0;
    padding: 0;
    background-color: #ffffff;
    font-family: 'Calibri', 'Segoe UI', 'Nirmala UI', 'Arial', sans-serif;
    color: #000000;
    -webkit-font-smoothing: antialiased;
  }
  .slide {
    width: 960pt;
    height: 540pt;
    max-width: 960pt;
    max-height: 540pt;
    position: relative;
    page-break-after: always;
    break-after: page;
    background: #ffffff;
    overflow: hidden;
    padding: 34pt 52pt 24pt 52pt;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
  }

  /* PowerPoint Main Slide Title */
  .ppt-title {
    font-size: 24pt;
    font-weight: 700;
    color: #000000;
    margin: 0 0 14pt 0;
    line-height: 1.2;
    text-transform: uppercase;
    letter-spacing: 0.3pt;
    font-family: 'Arial', 'Calibri', 'Segoe UI', sans-serif;
  }

  .ppt-heading {
    font-size: 13.5pt;
    font-weight: 700;
    color: #000000;
    margin: 0 0 3pt 0;
    text-transform: uppercase;
    font-family: 'Arial', 'Calibri', sans-serif;
  }

  .ppt-section {
    margin-bottom: 9pt;
  }

  /* PowerPoint Body Text */
  .ppt-paragraph {
    font-size: 12.5pt;
    line-height: 1.5;
    color: #111827;
    margin: 0 0 6pt 0;
  }

  /* PowerPoint Bullet List */
  .ppt-list {
    margin: 0 0 8pt 0;
    padding-left: 18pt;
    font-size: 12pt;
    line-height: 1.5;
    color: #111827;
    list-style-type: disc;
  }
  .ppt-list li {
    margin-bottom: 4.5pt;
  }
  .ppt-list li strong {
    color: #000000;
  }

  /* Code blocks inside PowerPoint */
  .ppt-code {
    background: #f8fafc;
    border-left: 3pt solid #64748b;
    padding: 5pt 9pt;
    font-family: 'Consolas', 'Courier New', monospace;
    font-size: 10.5pt;
    line-height: 1.35;
    color: #111827;
    margin: 4pt 0;
  }
</style>
</head>
<body>
"""

for idx, s in enumerate(slides_data):
    slide_num = idx + 1
    html_template += f"""
<section class="slide" id="slide-{slide_num}">
  <h1 class="ppt-title">{s['title']}</h1>
  {s['content']}
</section>
"""

html_template += """
</body>
</html>
"""

output_html = "CampusHub_Viva_Presentation.html"
with open(output_html, "w", encoding="utf-8") as f:
    f.write(html_template)

print(f"Generated {output_html} with {len(slides_data)} pure PowerPoint slides.")

chrome_path = r"C:\Program Files\Google\Chrome\Application\chrome.exe"
html_path = os.path.abspath(output_html)
pdf_path = os.path.abspath("CampusHub_Viva_Presentation.pdf")

cmd = [
    chrome_path,
    "--headless",
    "--no-sandbox",
    "--disable-gpu",
    "--no-pdf-header-footer",
    f"--print-to-pdf={pdf_path}",
    html_path
]

res = subprocess.run(cmd, capture_output=True, text=True)
print("Exit code:", res.returncode)

reader = pypdf.PdfReader(pdf_path)
print(f"Generated clean PDF: {len(reader.pages)} pages, Box: {reader.pages[0].mediabox.width} x {reader.pages[0].mediabox.height}")
