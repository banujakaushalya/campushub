<?php
header('Content-Type: application/json');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/db.php';
require_once 'email/email.php';

$raw_input = file_get_contents('php://input');
$data = json_decode($raw_input, true) ?: $_POST;

$action = $data['action'] ?? '';

if ($action === 'request_code') {
    $email = trim($data['email'] ?? '');
    if (empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Please enter your registered email address.']);
        exit;
    }

    $res = Database::search("SELECT user_id, first_name, email FROM users WHERE email = ? LIMIT 1", "s", [$email]);
    if ($res && $res->num_rows > 0) {
        $user = $res->fetch_assoc();
        $code = (string)rand(100000, 999999);

        $_SESSION['reset_email'] = $user['email'];
        $_SESSION['reset_user_id'] = $user['user_id'];
        $_SESSION['reset_otp'] = $code;
        $_SESSION['reset_otp_time'] = time();

        $sent = EmailHelper::sendResetCode($user['email'], $user['first_name'], $code);

        if ($sent) {
            echo json_encode(['success' => true, 'message' => 'Reset verification code sent to ' . $user['email']]);
        } else {
            echo json_encode(['success' => true, 'message' => 'Verification code generated (' . $code . '). Email dispatch attempted.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No account found with that email address.']);
    }
    exit;
}

if ($action === 'verify_and_reset') {
    $code = trim($data['code'] ?? '');
    $new_password = $data['new_password'] ?? '';
    $confirm_password = $data['confirm_password'] ?? '';

    $saved_email = $_SESSION['reset_email'] ?? '';
    $saved_user_id = $_SESSION['reset_user_id'] ?? 0;
    $saved_code = $_SESSION['reset_otp'] ?? '';
    $saved_time = $_SESSION['reset_otp_time'] ?? 0;

    if (empty($saved_code) || empty($saved_user_id)) {
        echo json_encode(['success' => false, 'message' => 'Session expired. Please request a new reset code.']);
        exit;
    }

    if (time() - $saved_time > 900) {
        echo json_encode(['success' => false, 'message' => 'Verification code has expired. Please request a new code.']);
        exit;
    }

    if ($code !== $saved_code) {
        echo json_encode(['success' => false, 'message' => 'Invalid verification code. Please check your email.']);
        exit;
    }

    if (strlen($new_password) < 8) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters long.']);
        exit;
    }

    if ($new_password !== $confirm_password) {
        echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
        exit;
    }

    $hash = password_hash($new_password, PASSWORD_BCRYPT);
    $updated = Database::iud("UPDATE users SET password_hash = ? WHERE user_id = ?", "si", [$hash, $saved_user_id]);

    if ($updated) {
        unset($_SESSION['reset_email']);
        unset($_SESSION['reset_user_id']);
        unset($_SESSION['reset_otp']);
        unset($_SESSION['reset_otp_time']);

        echo json_encode(['success' => true, 'message' => 'Password updated successfully! You can now log in.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update password. Please try again.']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request.']);
