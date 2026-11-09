# 6. PHP Development

## 6.1 Server-Side Architecture Overview
PHP is the core backend scripting engine for CampusHub. It processes form data submitted by users, interacts with the MySQL database, controls access according to user roles, renders dynamic HTML content, and manages user session lifecycles.

---

## 6.2 PHP Variables and Data Types
Various PHP data types were utilized to model data and application state:
- **Strings:** Storing user names, emails, queries, and file paths (e.g., `$first_name = "Alex";`).
- **Integers:** Handling database keys, event seat counts, and IDs (e.g., `$event_id = (int)$_GET['id'];`).
- **Indexed Arrays:** Storing lists of database records (e.g., `$events = [];` holding query results).
- **Associative Arrays:** Storing structured key-value pairs (e.g., `$meta = ['category' => 'Sports', 'location' => 'Main Ground'];`).
- **Booleans:** Managing validation flags and operation statuses (e.g., `$is_valid = true;`).

---

## 6.3 Modular File Inclusions (`require_once` & `include_once`)
To follow the DRY (Don't Repeat Yourself) principle, common sections of code were separated into modular files inside the `includes/` directory:

```php
<?php
// Loading configuration and reusable layout components
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';
?>
```

Using `require_once` guarantees that essential files like the database handler and auth functions are included exactly once per script execution, preventing duplicate function declaration errors.

---

## 6.4 Custom Functions & Helper Library (`includes/functions.php`)
I developed dedicated utility functions to handle repeated tasks cleanly across pages:

```php
<?php
// 1. Output encoding to prevent XSS attacks
function e($data) {
    return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
}

// 2. Input sanitization for user submissions
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// 3. User authentication status check
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// 4. Role checking for Student
function is_student() {
    return is_logged_in() && isset($_SESSION['role']) && $_SESSION['role'] === 'student';
}

// 5. Role checking for Admin
function is_admin() {
    return is_logged_in() && isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'super_admin', 'moderator']);
}

// 6. Access Guard to protect restricted pages
function require_login($redirect_to = '../login.php') {
    if (!is_logged_in()) {
        header("Location: " . $redirect_to);
        exit;
    }
}
?>
```

---

## 6.5 Form Handling & Data Processing (`$_POST` and `$_GET`)

### 1. `POST` Request Handling (Event Registration Example)
Form submissions that modify data or handle sensitive inputs use `$_SERVER['REQUEST_METHOD'] === 'POST'`:

```php
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_event'])) {
    $event_id = isset($_POST['event_id']) ? (int)$_POST['event_id'] : 0;
    $user_id  = $_SESSION['user_id'] ?? 0;

    if ($event_id > 0 && $user_id > 0) {
        // Check for existing registration to prevent duplicates
        $check = Database::search("SELECT registration_id FROM registrations WHERE events_event_id = ? AND users_user_id = ?", "ii", [$event_id, $user_id]);
        
        if ($check && $check->num_rows > 0) {
            $msg = "You are already registered for this event.";
        } else {
            Database::iud("INSERT INTO registrations (events_event_id, users_user_id) VALUES (?, ?)", "ii", [$event_id, $user_id]);
            $msg = "Registration successful!";
        }
    }
}
?>
```

### 2. `GET` Parameter Passing (Event Details & Search Filtering)
URL query parameters are captured safely using `$_GET` with typecasting and default fallback values:
- `event_details.php?id=3`: Loads details for event ID 3.
- `events.php?category=Sports&q=tournament`: Searches for matching sports events.
- `export_events.php?download=xml`: Triggers the XML file download response.

---

## 6.6 Session Management & State Persistence
PHP sessions (`session_start()`) are initialized at the beginning of each request. When a student or administrator logs in successfully:
- `$_SESSION['user_id']`: Identifies the user in database lookups.
- `$_SESSION['user_name']`: Displays their personalized greeting in the header.
- `$_SESSION['role']`: Controls role-based access rights across pages.
- `$_SESSION['email']`: Identifies the logged-in email.

When the user clicks "Logout" (`logout.php`), `session_unset()` and `session_destroy()` are called, securely wiping session variables and redirecting back to `login.php`.
