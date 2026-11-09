<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../includes/db.php';
require_once '../email/email.php';

$error = "";
$info = "";
$step = isset($_SESSION['master_step']) ? $_SESSION['master_step'] : 1;

// If already authenticated as super admin in master portal
if (isset($_SESSION['master_authenticated']) && $_SESSION['master_authenticated'] === true && ($_SESSION['role'] ?? '') === 'super_admin') {
    header("Location: index.php");
    exit;
}

// Handle Step 1: Email Submission & Code Generation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_code'])) {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        $error = "Please provide the master administrator email.";
    } else {
        // Query user with super_admin role
        $res = Database::search("SELECT u.* FROM users u JOIN admin a ON u.user_id = a.users_user_id WHERE u.email = ? AND a.role = 'super_admin' LIMIT 1", "s", [$email]);
        if ($res && $res->num_rows > 0) {
            $user = $res->fetch_assoc();
            
            // Generate a secure 6-digit numeric OTP code
            $code = (string)rand(100000, 999999);
            $_SESSION['master_email'] = $email;
            $_SESSION['master_user'] = $user;
            $_SESSION['master_otp'] = $code;
            $_SESSION['master_otp_time'] = time();
            $_SESSION['master_step'] = 2;
            $step = 2;
            
            // Send email using PHPMailer
            $sent = EmailHelper::sendAdminVerificationCode($email, $user['first_name'], $code);
            if ($sent) {
                $info = "A 6-digit security code has been sent to " . htmlspecialchars($email) . ". Please check your inbox.";
            } else {
                $info = "A one-time verification code has been generated. Code: " . $code;
            }
        } else {
            $error = "Unauthorized email. Only accounts with 'super_admin' role can access Master Control.";
        }
    }
}

// Handle Step 2: Verification Code Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_code'])) {
    $entered_code = trim($_POST['auth_code'] ?? '');
    $saved_code = $_SESSION['master_otp'] ?? '';
    $saved_time = $_SESSION['master_otp_time'] ?? 0;
    
    // 10 minutes expiry window
    if (time() - $saved_time > 600) {
        $error = "The verification code has expired. Please request a new code.";
        $_SESSION['master_step'] = 1;
        $step = 1;
    } elseif ($entered_code === $saved_code && !empty($saved_code)) {
        // Successfully verified code
        $user = $_SESSION['master_user'];
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['role'] = 'super_admin';
        $_SESSION['master_authenticated'] = true;
        
        // Clean up temporary auth session variables
        unset($_SESSION['master_otp']);
        unset($_SESSION['master_otp_time']);
        unset($_SESSION['master_step']);
        unset($_SESSION['master_user']);
        
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid verification code. Please check and try again.";
        $step = 2;
    }
}

// Reset / Change Email
if (isset($_GET['action']) && $_GET['action'] === 'reset') {
    unset($_SESSION['master_otp']);
    unset($_SESSION['master_otp_time']);
    unset($_SESSION['master_step']);
    unset($_SESSION['master_email']);
    $_SESSION['master_step'] = 1;
    $step = 1;
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Master Control Login | CampusHub</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    body {
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      background-color: #f3f4f6;
      font-family: Arial, sans-serif;
    }

    .log-container {
      display: flex;
      flex-direction: column;
      width: min(100% - 32px, 460px);
      margin: 32px 0;
      padding: 30px;
      box-sizing: border-box;
      background: #ffffff;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .form-group {
      display: flex;
      flex-direction: column;
      margin-bottom: 15px;
    }

    label,
    p {
      margin-bottom: 5px;
      color: #333333;
      font-family: Arial, sans-serif;
      font-size: 14px;
      font-weight: bold;
    }

    input {
      width: 100%;
      padding: 10px;
      box-sizing: border-box;
      border: 1px solid #cccccc;
      border-radius: 4px;
      font-size: 14px;
    }

    input:focus {
      outline: none;
      border-color: #ff6b4a;
    }

    .code-input {
      letter-spacing: 6px;
      font-size: 1.5rem;
      text-align: center;
      font-weight: bold;
      color: #ff6b4a;
    }

    button {
      width: 100%;
      padding: 11px;
      border: 0;
      border-radius: 4px;
      background: #ff6b4a;
      color: #ffffff;
      cursor: pointer;
      font-size: 14px;
      font-weight: bold;
      transition: background 0.2s ease;
    }

    button:hover {
      background: #e55a3a;
    }

    h2 {
      margin: 0 0 8px;
      text-align: center;
      color: #171b2a;
    }

    .subtitle {
      text-align: center;
      color: #687083;
      font-size: 13px;
      font-weight: normal;
      margin: 0 0 20px;
    }

    hr {
      width: 100%;
      height: 1px;
      margin: 20px 0;
      border: 0;
      background: #dddddd;
    }

    .alert {
      padding: 12px 14px;
      border-radius: 4px;
      margin-bottom: 18px;
      font-size: 14px;
      line-height: 1.4;
      font-weight: normal;
    }

    .alert-error {
      background-color: #ffeaea;
      color: #c92a2a;
      border: 1px solid #fcc2c2;
    }

    .alert-info {
      background-color: #e6f7ec;
      color: #1b7a3e;
      border: 1px solid #c2ebd0;
    }

    .back-links {
      display: flex;
      justify-content: space-between;
      margin-top: 15px;
      font-size: 14px;
    }

    .back-links a,
    .link-btn {
      color: #ff6b4a;
      text-decoration: underline;
      font-weight: bold;
    }

    .back-links a:hover,
    .link-btn:hover {
      color: #e55a3a;
      text-decoration: none;
    }

    .master-hint {
      margin-top: 15px;
      padding: 10px;
      background: #f8fafc;
      border: 1px dashed #cbd5e1;
      border-radius: 4px;
      font-size: 12px;
      color: #64748b;
    // 10 minutes expiry window
    if (time() - $saved_time > 600) {
        $error = "The verification code has expired. Please request a new code.";
        $_SESSION['master_step'] = 1;
        $step = 1;
    } elseif ($entered_code === $saved_code && !empty($saved_code)) {
        // Successfully verified code
        $user = $_SESSION['master_user'];
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['role'] = 'super_admin';
        $_SESSION['master_authenticated'] = true;
        
        // Clean up temporary auth session variables
        unset($_SESSION['master_otp']);
        unset($_SESSION['master_otp_time']);
        unset($_SESSION['master_step']);
        unset($_SESSION['master_user']);
        
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid verification code. Please check and try again.";
        $step = 2;
    }
}

// Reset / Change Email
if (isset($_GET['action']) && $_GET['action'] === 'reset') {
    unset($_SESSION['master_otp']);
    unset($_SESSION['master_otp_time']);
    unset($_SESSION['master_step']);
    unset($_SESSION['master_email']);
    $_SESSION['master_step'] = 1;
    $step = 1;
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Master Control Login | CampusHub</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    body {
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      background-color: #f3f4f6;
      font-family: Arial, sans-serif;
    }

    .log-container {
      display: flex;
      flex-direction: column;
      width: min(100% - 32px, 460px);
      margin: 32px 0;
      padding: 30px;
      box-sizing: border-box;
      background: #ffffff;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .form-group {
      display: flex;
      flex-direction: column;
      margin-bottom: 15px;
    }

    label,
    p {
      margin-bottom: 5px;
      color: #333333;
      font-family: Arial, sans-serif;
      font-size: 14px;
      font-weight: bold;
    }

    input {
      width: 100%;
      padding: 10px;
      box-sizing: border-box;
      border: 1px solid #cccccc;
      border-radius: 4px;
      font-size: 14px;
    }

    input:focus {
      outline: none;
      border-color: #ff6b4a;
    }

    .code-input {
      letter-spacing: 6px;
      font-size: 1.5rem;
      text-align: center;
      font-weight: bold;
      color: #ff6b4a;
    }

    button {
      width: 100%;
      padding: 11px;
      border: 0;
      border-radius: 4px;
      background: #ff6b4a;
      color: #ffffff;
      cursor: pointer;
      font-size: 14px;
      font-weight: bold;
      transition: background 0.2s ease;
    }

    button:hover {
      background: #e55a3a;
    }

    h2 {
      margin: 0 0 8px;
      text-align: center;
      color: #171b2a;
    }

    .subtitle {
      text-align: center;
      color: #687083;
      font-size: 13px;
      font-weight: normal;
      margin: 0 0 20px;
    }

    hr {
      width: 100%;
      height: 1px;
      margin: 20px 0;
      border: 0;
      background: #dddddd;
    }

    .alert {
      padding: 12px 14px;
      border-radius: 4px;
      margin-bottom: 18px;
      font-size: 14px;
      line-height: 1.4;
      font-weight: normal;
    }

    .alert-error {
      background-color: #ffeaea;
      color: #c92a2a;
      border: 1px solid #fcc2c2;
    }

    .alert-info {
      background-color: #e6f7ec;
      color: #1b7a3e;
      border: 1px solid #c2ebd0;
    }

    .back-links {
      display: flex;
      justify-content: space-between;
      margin-top: 15px;
      font-size: 14px;
    }

    .back-links a,
    .link-btn {
      color: #ff6b4a;
      text-decoration: underline;
      font-weight: bold;
    }

    .back-links a:hover,
    .link-btn:hover {
      color: #e55a3a;
      text-decoration: none;
    }

    .master-hint {
      margin-top: 15px;
      padding: 10px;
      background: #f8fafc;
      border: 1px dashed #cbd5e1;
      border-radius: 4px;
      font-size: 12px;
      color: #64748b;
      text-align: center;
      font-weight: normal;
    }
  </style>
</head>
<body>

<div class="log-container">
  <h2>Master Control Gateway</h2>
  <p class="subtitle">🔒 Restricted Super Administrator Portal</p>

  <?php if (!empty($error)): ?>
    <div class="alert alert-error">
      <?php echo htmlspecialchars($error); ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($info)): ?>
    <div class="alert alert-info">
      <?php echo $info; ?>
    </div>
  <?php endif; ?>

  <?php if ($step === 1): ?>
    <!-- Step 1: Request OTP Code via Email -->
    <form method="POST" action="login.php">
      <div class="form-group">
        <label for="email">Master Administrator Email</label>
        <input type="email" id="email" name="email" placeholder="Enter master administrator email" required autofocus>
      </div>

      <button type="submit" name="request_code">Request Security Code &rarr;</button>
    </form>

  <?php else: ?>
    <!-- Step 2: Submit 6-Digit Auth Code -->
    <form method="POST" action="login.php">
      <div class="form-group">
        <label for="auth_code">Enter 6-Digit Verification Code</label>
        <p style="font-size: 13px; color: #687083; font-weight: normal; margin-bottom: 8px;">
          Sent to <strong><?php echo htmlspecialchars($_SESSION['master_email'] ?? ''); ?></strong>
        </p>
        <input type="text" id="auth_code" name="auth_code" class="code-input" placeholder="••••••" maxlength="6" pattern="[0-9]{6}" required autofocus>
      </div>

      <button type="submit" name="verify_code">Verify & Access Master Control &rarr;</button>
    </form>

    <div style="text-align: center; margin-top: 15px;">
      <a href="login.php?action=reset" class="link-btn">&larr; Use a different email address</a>
    </div>
  <?php endif; ?>

  <hr>

  <div class="back-links">
    <a href="../admin/dashboard.php">&larr; Return to Admin Panel</a>
    <a href="../index.php">CampusHub Home</a>
  </div>
</div>

</body>
</html>
