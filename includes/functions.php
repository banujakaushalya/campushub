<?php
// CampusHub Helper Functions
// Web Programming I (H7DT)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Sanitize user output to prevent XSS
function e($data) {
    return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
}

// 2. Sanitize input strings
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    return htmlspecialchars($data);
}

// 3. Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// 4. Check if current user is an Admin
function is_admin() {
    return is_logged_in() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// 5. Check if current user is a Student
function is_student() {
    return is_logged_in() && isset($_SESSION['role']) && $_SESSION['role'] === 'student';
}

// 6. Auth Guard: Ensure user is logged in
function require_login($redirect_to = '../login.php') {
    if (!is_logged_in()) {
        header("Location: " . $redirect_to);
        exit;
    }
}

// 7. Auth Guard: Ensure user is Admin
function require_admin($redirect_to = '../login.php') {
    if (!is_admin()) {
        header("Location: " . $redirect_to);
        exit;
    }
}

// 8. Get current logged in user details
function get_current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

function get_current_user_name() {
    return $_SESSION['user_name'] ?? 'Guest';
}

function get_current_user_role() {
    return $_SESSION['role'] ?? 'guest';
}
