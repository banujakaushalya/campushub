<?php
include_once __DIR__ . '/includes/db.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $jsonData = file_get_contents("php://input");
    $data = json_decode($jsonData, true);

    if (!is_array($data)) {
        echo json_encode(["success" => false, "message" => "Invalid request data."]);
        exit;
    }

    $email = trim($data["email"] ?? '');
    $password = $data['password'] ?? '';

    $errors = [];

    if ($email === '') {
        $errors[] = 'Email cannot be empty.';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    } else if ($password === '') {
        $errors[] = 'Password cannot be empty.';
    } else if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long.';
    }

    if (empty($errors)) {
        $result = Database::search(
            "SELECT users.user_id, users.first_name, users.last_name, users.email, users.password_hash, COALESCE(admin.`role`, users.`role`) AS `role` FROM users LEFT JOIN admin ON users.user_id = admin.users_user_id WHERE users.email = ? LIMIT 1;",
            "s",
            [$email]
        );

        $user = $result->fetch_assoc();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            echo json_encode(["success" => false, "message" => "Invalid email or password."]);
            exit;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['role'] = $user['role'];



        echo json_encode(["success" => true, "message" => "Welcome back, {$user['first_name']}."]);
        exit;
    }

    echo json_encode(["success" => false, "message" => $errors]);
    exit;
}

echo json_encode(["success" => false, "message" => "Invalid request method."]);