# 10. Error Handling and Validation

## 10.1 Overview of Validation Strategy
A reliable web application must validate user input at two layers:
1. **Client-Side Validation (Browser Level):** Gives immediate feedback to the student before form submission, reducing unnecessary server requests.
2. **Server-Side Validation (PHP Backend Level):** Acts as the primary security defense, ensuring data integrity even if client-side checks are bypassed or disabled.

---

## 10.2 Client-Side Validation Implementation
Client-side validation is implemented using native HTML5 attributes:

- **Mandatory Fields:** `required` attribute prevents users from submitting empty forms.
- **Email Format:** `type="email"` ensures valid email syntax (`user@domain.com`).
- **Phone Numbers:** `type="tel"` with input masks prevents invalid characters.
- **Numeric Limits:** `min="1"` and `max="500"` on event seat capacity fields prevent negative or unrealistic numbers.
- **Accepted File Formats:** `accept="image/*,video/*"` restricts file dialog selections to valid media formats.

```html
<!-- Example of Validated Client Form -->
<input type="email" id="email" name="email" placeholder="student@campus.edu" required>
<input type="password" id="password" name="password" minlength="6" required>
<input type="number" id="seats" name="seats" min="1" max="500" required>
```

---

## 10.3 Server-Side Validation & Sanitization (`includes/functions.php`)
Every incoming parameter in `$_POST` and `$_GET` is sanitized and verified by backend PHP scripts before interacting with the database.

```php
<?php
// Example: Validating User Registration Input
$errors = [];

$first_name = clean_input($_POST['first_name'] ?? '');
$last_name  = clean_input($_POST['last_name'] ?? '');
$email      = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$phone      = trim($_POST['phone'] ?? '');
$password   = $_POST['password'] ?? '';

// Check required fields
if (empty($first_name)) {
    $errors[] = "First name is required.";
}
if (empty($last_name)) {
    $errors[] = "Last name is required.";
}
if (!$email) {
    $errors[] = "Please provide a valid email address.";
}
if (strlen($password) < 6) {
    $errors[] = "Password must be at least 6 characters long.";
}

// Check for existing phone number in database
if (empty($errors)) {
    $check = Database::search("SELECT user_id FROM users WHERE phone = ?", "s", [$phone]);
    if ($check && $check->num_rows > 0) {
        $errors[] = "A user with this phone number already exists.";
    }
}
?>
```

---

## 10.4 Error Handling & User Feedback

### 1. Visual Alert Messages
Feedback messages are presented in styled alert boxes:

```php
<?php if (!empty($error_msg)): ?>
  <div class="alert alert-danger" style="background: #FEE2E2; color: #991B1B; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; border: 1px solid #F87171;">
    <strong>Error:</strong> <?php echo htmlspecialchars($error_msg); ?>
  </div>
<?php elseif (!empty($success_msg)): ?>
  <div class="alert alert-success" style="background: #D1FAE5; color: #065F46; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; border: 1px solid #34D399;">
    <strong>Success:</strong> <?php echo htmlspecialchars($success_msg); ?>
  </div>
<?php endif; ?>
```

### 2. Database Error Handling
In `includes/db.php`, database connection issues are caught gracefully using `mysqli_report(MYSQLI_REPORT_OFF)`. This prevents sensitive raw database error messages or stack traces from exposing database credentials to end users:

```php
mysqli_report(MYSQLI_REPORT_OFF);
$conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, 3306);
if ($conn->connect_error) {
    // Log privately and return safe null response
    error_log("Database Connection Failed: " . $conn->connect_error);
    return null;
}
```

### 3. Session & Route Guard Protection
Restricted administrative scripts verify the user's role at the very beginning of the script. Unauthorized visitors are redirected to the login page immediately:

```php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
```
