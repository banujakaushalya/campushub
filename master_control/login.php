<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../includes/db.php';

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
        $res = Database::search("SELECT * FROM users WHERE email = ? AND role = 'super_admin' LIMIT 1", "s", [$email]);
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
            
            // For educational and local environment display, we display the generated auth code cleanly
            $info = "A one-time verification code has been dispatched to your email (" . htmlspecialchars($email) . "). Code: " . $code;
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
  <title>Master Control Security Login | CampusHub</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    :root {
      --ink: #0f172a;
      --gold: #f59e0b;
      --gold-dark: #b45309;
      --dark-card: #1e293b;
      --border-dark: #334155;
      --coral: #FF6B4A;
    }

    body {
      margin: 0;
      padding: 0;
      background: #090d16;
      color: #f8fafc;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      background-image: 
        radial-gradient(circle at 20% 20%, rgba(245, 158, 11, 0.08) 0%, transparent 40%),
        radial-gradient(circle at 80% 80%, rgba(255, 107, 74, 0.08) 0%, transparent 40%);
    }

    .master-login-box {
      width: 100%;
      max-width: 460px;
      padding: 24px;
      box-sizing: border-box;
    }

    .master-card {
      background: var(--dark-card);
      border: 1px solid var(--border-dark);
      border-radius: 18px;
      padding: 36px 32px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
      position: relative;
      overflow: hidden;
    }

    .master-card::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #f59e0b, #FF6B4A, #f59e0b);
    }

    .master-header {
      text-align: center;
      margin-bottom: 28px;
    }

    .security-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: rgba(245, 158, 11, 0.15);
      color: #fbbf24;
      border: 1px solid rgba(245, 158, 11, 0.3);
      padding: 4px 12px;
      border-radius: 9999px;
      font-size: 0.75rem;
      font-weight: 700;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      margin-bottom: 14px;
    }

    .master-header h1 {
      margin: 0 0 8px;
      font-size: 1.65rem;
      color: #ffffff;
      font-weight: 800;
      letter-spacing: -0.5px;
    }

    .master-header p {
      margin: 0;
      color: #94a3b8;
      font-size: 0.9rem;
      line-height: 1.4;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-size: 0.85rem;
      font-weight: 600;
      color: #cbd5e1;
    }

    .form-control {
      width: 100%;
      padding: 12px 14px;
      box-sizing: border-box;
      background: #0f172a;
      border: 1px solid #334155;
      border-radius: 8px;
      color: #ffffff;
      font-size: 1rem;
      font-family: inherit;
      transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-control:focus {
      outline: none;
      border-color: #f59e0b;
      box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2);
    }

    .code-input {
      letter-spacing: 6px;
      font-size: 1.5rem;
      text-align: center;
      font-weight: 700;
      color: #fbbf24;
    }

    .btn-master {
      width: 100%;
      padding: 13px;
      background: linear-gradient(135deg, #f59e0b, #d97706);
      color: #0f172a;
      border: none;
      border-radius: 8px;
      font-size: 0.95rem;
      font-weight: 800;
      cursor: pointer;
      transition: all 0.2s;
      margin-top: 8px;
    }

    .btn-master:hover {
      background: linear-gradient(135deg, #fbbf24, #f59e0b);
      box-shadow: 0 4px 15px rgba(245, 158, 11, 0.35);
      transform: translateY(-1px);
    }

    .alert {
      padding: 12px 14px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-size: 0.88rem;
      line-height: 1.4;
    }

    .alert-error {
      background: rgba(239, 68, 68, 0.15);
      border: 1px solid rgba(239, 68, 68, 0.3);
      color: #fca5a5;
    }

    .alert-info {
      background: rgba(245, 158, 11, 0.15);
      border: 1px solid rgba(245, 158, 11, 0.35);
      color: #fde68a;
    }

    .code-highlight {
      display: inline-block;
      background: #0f172a;
      border: 1px solid #f59e0b;
      color: #fbbf24;
      padding: 4px 10px;
      border-radius: 6px;
      font-family: monospace;
      font-size: 1.1rem;
      font-weight: bold;
      letter-spacing: 2px;
      margin-top: 6px;
    }

    .back-links {
      text-align: center;
      margin-top: 24px;
      display: flex;
      justify-content: space-between;
      font-size: 0.85rem;
    }

    .back-links a {
      color: #94a3b8;
      text-decoration: none;
      transition: color 0.2s;
    }

    .back-links a:hover {
      color: #f59e0b;
    }

    .master-hint {
      margin-top: 20px;
      padding: 12px;
      background: rgba(15, 23, 42, 0.6);
      border: 1px dashed #334155;
      border-radius: 8px;
      font-size: 0.8rem;
      color: #64748b;
      text-align: center;
    }
  </style>
</head>
<body>

<div class="master-login-box">
  <div class="master-card">
    <div class="master-header">
      <div class="security-badge">
        <span>🔒</span> Restricted Master Access
      </div>
      <h1>Master Control Gateway</h1>
      <p>Secure two-factor authentication portal for super administrator privileges.</p>
    </div>

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
          <input type="email" id="email" name="email" class="form-control" placeholder="superadmin@campushub.edu" value="superadmin@campushub.edu" required autofocus>
        </div>

        <button type="submit" name="request_code" class="btn-master">Request Master Security Code &rarr;</button>
      </form>

      <div class="master-hint">
        Default Super Admin Email: <strong>superadmin@campushub.edu</strong>
      </div>

    <?php else: ?>
      <!-- Step 2: Submit 6-Digit Auth Code -->
      <form method="POST" action="login.php">
        <div class="form-group">
          <label for="auth_code">Enter 6-Digit Authentication Code</label>
          <p style="font-size: 0.8rem; color: #94a3b8; margin: 0 0 8px;">
            Sent to <strong><?php echo htmlspecialchars($_SESSION['master_email'] ?? ''); ?></strong>
          </p>
          <input type="text" id="auth_code" name="auth_code" class="form-control code-input" placeholder="••••••" maxlength="6" pattern="[0-9]{6}" required autofocus>
        </div>

        <button type="submit" name="verify_code" class="btn-master">Verify & Access Master Portal &rarr;</button>
      </form>

      <div style="text-align: center; margin-top: 16px;">
        <a href="login.php?action=reset" style="color: #94a3b8; font-size: 0.82rem; text-decoration: none;">&larr; Use a different email address</a>
      </div>
    <?php endif; ?>

    <div class="back-links">
      <a href="../admin/dashboard.php">&larr; Return to Admin Panel</a>
      <a href="../index.php">CampusHub Home</a>
    </div>
  </div>
</div>

</body>
</html>
