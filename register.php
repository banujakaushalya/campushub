<?php
include_once __DIR__ . '/includes/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Get the JSON data from the request body
  $jsonData = file_get_contents("php://input");
  $data = json_decode($jsonData, true);

  // Extract the form data
  $firstName = $data['first_name'] ?? '';
  $lastName = $data['last_name'] ?? '';
  $email = $data['email'] ?? '';
  $phone = $data['phone'] ?? '';
  $password = $data['password'] ?? '';
  $confirmPassword = $data['confirm_password'] ?? '';
  $gender = $data['gender'] ?? '';
  $role = $data['role'] ?? '';

  $errors = [];

  if (empty($firstName)) {
    $errors[] = "First name is required.";
  } else if (empty($lastName)) {
    $errors[] = "Last name is required.";
  } else if (empty($email)) {
    $errors[] = "Email is required.";
  } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format.";
  } else if (empty($phone)) {
    $errors[] = "Phone number is required.";
  } else if (empty($password)) {
    $errors[] = "Password is required.";
  } elseif (strlen($password) < 8) {
    $errors[] = "Password must be at least 8 characters long.";
  } else if ($password !== $confirmPassword) {
    $errors[] = "Passwords do not match.";
    echo "pass = $password", "conpass = $confirmPassword";
  } else if (empty($gender)) {
    $errors[] = "Gender is required.";
  } else if (empty($role)) {
    $errors[] = "Role is required.";
  }
  if (empty($errors)) {

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $isExist = Database::search("SELECT email, phone FROM users WHERE email = ? OR phone = ?;", "ss", [$email, $phone]);
    if ($isExist->num_rows > 0) {
      echo json_encode(["success" => false, "message" => "An account with this email or phone number already exists."]);
      exit;
    }

    $isRegister = Database::iud("INSERT INTO users(first_name, last_name, password_hash, gender, email, phone, `role`) VALUES (?, ?, ?, ?, ?, ?, ?);", "sssssss", [$firstName, $lastName, $hashed, $gender, $email, $phone, $role]);

    if ($isRegister) {
      echo json_encode(["success" => true, "message" => "User $firstName registerd successfully."]);
    } else {
      echo json_encode(["success" => false, "message" => "Registration Failed"]);
    }

  } else {
    echo json_encode(["success" => false, "message" => $errors]);
  }
} else {
  echo json_encode(["success" => false, "message" => "Invalid Request Methode."]);
}
