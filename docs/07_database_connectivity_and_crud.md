# 7. Database Connectivity and Data Manipulation (CRUD)

## 7.1 Database Architecture and Schema Design
The application uses a relational MySQL database named `campushub`. The database schema is designed in Third Normal Form (3NF) to ensure data consistency, eliminate duplicate data storage, and maintain referential integrity with foreign key constraints.

```
+------------------+         +-----------------------+
|      users       |1       *|     registrations     |
|------------------|---------|-----------------------|
| PK user_id       |         | PK registration_id    |
|    first_name    |         | FK users_user_id      |
|    last_name     |         | FK events_event_id    |
|    email         |         |    reg_date           |
|    phone         |         +-----------------------+
|    password_hash |                     *
|    role          |                     |
|    profile_img   |                     |
+------------------+                     |
        |1                               |
        |                                |
        |*                               |1
+------------------+         +-----------------------+
|      media       |*       1|        events         |
|------------------|---------|-----------------------|
| PK media_id      |         | PK event_id           |
|    title         |         |    title              |
|    media_type    |         |    description        |
|    file_path     |         |    event_date         |
|    meta_data     |         |    event_start_time   |
| FK events_event_id         |    event_end_time     |
| FK users_user_id |         |    img_path           |
+------------------+         |    seats              |
                             |    status             |
                             +-----------------------+
```

### Table Definitions:
1. **`users` Table:** Stores student and staff accounts (name, email, phone, hashed password, role, profile image).
2. **`admin` Table:** Links to `users` table to assign elevated privileges (`moderator`, `admin`, `super_admin`).
3. **`events` Table:** Stores campus events with schedule times, total seat capacities, banner image paths, status, and JSON metadata.
4. **`registrations` Table:** Represents the many-to-many relationship linking a `user_id` to an `event_id` along with the registration timestamp.
5. **`media` Table:** Stores uploaded photos and videos linked to a specific event and the uploading user.
6. **`announcements` Table:** Stores urgent notices and news with expiration dates and priority levels.
7. **`master_control` Table:** Stores global administrative settings and notification email addresses.

---

## 7.2 Database Connection Class (`includes/db.php`)
A custom helper class `Database` was created using PHP’s `mysqli` extension. It wraps connection management and prepared statements for both read and write queries.

```php
<?php
include_once __DIR__ . '/config.php';

class Database {
    private static $connection = null;

    // Singleton Database Connection Handler
    public static function getConnection() {
        if (self::$connection === null) {
            mysqli_report(MYSQLI_REPORT_OFF);
            $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, 3306);
            if ($conn->connect_error) {
                return null;
            }
            $conn->set_charset("utf8mb4");
            self::$connection = $conn;
        }
        return self::$connection;
    }

    // Insert / Update / Delete (IUD) Operations with Prepared Statements
    public static function iud($query, $types = "", $params = []) {
        $conn = self::getConnection();
        if (!$conn) return false;
        
        $statement = $conn->prepare($query);
        if (!$statement) return false;

        if (!empty($types) && !empty($params)) {
            $statement->bind_param($types, ...$params);
        }
        $result = $statement->execute();
        $statement->close();
        return $result;
    }

    // Search / Select Query Operation with Prepared Statements
    public static function search($query, $types = null, $params = []) {
        $conn = self::getConnection();
        if (!$conn) return false;

        $statement = $conn->prepare($query);
        if (!$statement) return false;

        if ($types !== null && !empty($params)) {
            $statement->bind_param($types, ...$params);
        }
        $statement->execute();
        $result = $statement->get_result();
        $statement->close();
        return $result;
    }
}
?>
```

---

## 7.3 Complete CRUD Implementation Examples

### 1. CREATE (Insert Operation)
Adding a new student registration during sign-up:
```php
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);
$query = "INSERT INTO users (first_name, last_name, phone, email, password_hash, role) VALUES (?, ?, ?, ?, ?, 'student')";
$status = Database::iud($query, "sssss", [$first_name, $last_name, $phone, $email, $hashedPassword]);
```

### 2. READ (Select Operation)
Displaying all active upcoming events for students:
```php
$query = "SELECT * FROM events WHERE status = 'display' AND event_date >= CURDATE() ORDER BY event_date ASC";
$result = Database::search($query);
while ($row = $result->fetch_assoc()) {
    echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
}
```

### 3. UPDATE (Update Operation)
Modifying student profile details:
```php
$query = "UPDATE users SET first_name = ?, last_name = ?, phone = ? WHERE user_id = ?";
$status = Database::iud($query, "sssi", [$first_name, $last_name, $phone, $user_id]);
```

### 4. DELETE (Delete Operation)
Deleting an event in the administrator panel (`admin/delete_event.php`):
```php
$event_id = (int)$_GET['id'];
$query = "DELETE FROM events WHERE event_id = ?";
$status = Database::iud($query, "i", [$event_id]);
```
