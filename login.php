<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>CampusHub</title>
  <link rel="stylesheet" href="./assets/css/style.css">

  <style>
    body {
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      background-color: #f3f4f6;
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

    input,
    select {
      width: 100%;
      padding: 10px;
      box-sizing: border-box;
      border: 1px solid #cccccc;
      border-radius: 4px;
      font-size: 14px;
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
    }

    h2 {
      margin: 0 0 20px;
      text-align: center;
    }

    hr {
      width: 100%;
      height: 1px;
      margin: 10px 0 25px;
      border: 0;
      background: #dddddd;
    }

    .row-layout {
      display: flex;
      gap: 15px;
    }

    .row-layout .form-group {
      flex: 1;
      min-width: 0;
    }

    p button {
      width: auto;
      padding: 0;
      border: 0;
      border-radius: 0;
      background: none;
      color: #ff6b4a;
      display: inline;
      font-family: inherit;
      font-size: inherit;
      font-weight: inherit;
      text-decoration: underline;
      cursor: pointer;
    }

    p button:hover {
      color: #d44d2d;
      text-decoration: none;
    }

    [hidden] {
      display: none !important;
    }

    @media (max-width: 480px) {
      .log-container {
        padding: 24px 20px;
      }

      .row-layout {
        flex-direction: column;
        gap: 0;
      }
    }

    .notification-box {
      display: none;
      align-items: flex-start;
      position: fixed;
      right: 20px;
      bottom: 20px;
      width: min(450px, calc(100vw - 40px));
      margin: 0;
      padding: 16px;
      box-sizing: border-box;
      background-color: #ffffff;
      border-left: 4px solid #16a34a;
      border-radius: 6px;
      box-shadow: 0 4px 12px rgba(23, 27, 42, 0.1);
      z-index: 1000;
    }

    .notification-box.is-visible {
      display: flex;
    }

    .notification-icon {
      display: flex;
      flex: 0 0 24px;
      align-items: center;
      justify-content: center;
      margin-right: 14px;
      font-size: 20px;
      line-height: 24px;
    }

    .notification-content {
      flex: 1;
    }

    .notification-title {
      margin: 0 0 6px;
      color: #171b2a;
      font-size: 16px;
    }

    .notification-text {
      margin: 0;
      color: #687083;
      font-size: 14px;
      line-height: 1.4;
    }

    .notification-close {
      display: flex;
      flex: 0 0 24px;
      align-items: center;
      justify-content: center;
      width: 24px;
      height: 24px;
      margin-left: 12px;
      padding: 0;
      border: 0;
      background: none;
      color: #687083;
      cursor: pointer;
      font-size: 22px;
      line-height: 1;
    }

    .notification-close:hover {
      color: #16a34a;
    }

    .notification-box.is-success {
      border-left-color: #16a34a;
    }

    .notification-box.is-error {
      border-left-color: #dc2626;
    }

    .notification-box.is-error .notification-close:hover {
      color: #dc2626;
    }

    @media (max-width: 480px) {
      .notification-box {
        right: 16px;
        bottom: 16px;
        width: calc(100vw - 32px);
        padding: 14px;
      }

      .notification-icon {
        margin-right: 10px;
      }

      .notification-close {
        margin-left: 10px;
      }
    }
  </style>
</head>

<body>
  <div class="log-container">
    <div class="notification-box" id="notification" role="status" aria-live="polite" aria-atomic="true">
      <div class="notification-icon" aria-hidden="true">⚠️</div>
      <div class="notification-content">
        <h4 class="notification-title" id="notification-title">System Alert</h4>
        <p class="notification-text" id="notification-text"></p>
      </div>
      <button class="notification-close" id="notification-close" type="button"
        aria-label="Close notification">&times;</button>
    </div>
    <section class="login-container" aria-labelledby="login-heading">
      <h2 id="login-heading">Log In</h2>
      <form>
        <label for="user-name">User Name</label>
        <input type="text" name="user-name" id="userName">
        <div class="form-group">
          <label for="login-email-input">Enter Your Email</label>
          <input type="email" name="email" id="loginEmail" autocomplete="email" required>
        </div>
        <div class="form-group">
          <label for="login-pass">Enter Your Password</label>
          <input type="password" name="password" id="logInPass" autocomplete="current-password" required>
        </div>
        <button type="button" id="loginBtn" onclick="logIn();">Log In</button>
      </form>
      <p>Forgot password? <button type="button" id="show-forgot" onclick="toggleForgot();">Reset Password</button></p>
      <p>Don't have an account? <button type="button" id="show-register">Register</button></p>
    </section>

    <hr>

    <section class="forgot-container" aria-labelledby="forgot-heading" hidden>
      <h2 id="forgot-heading">Reset Password</h2>
      <div id="forgot-step-1">
        <div class="form-group">
          <label for="forgotEmail">Enter Your Email</label>
          <input type="email" id="forgotEmail" placeholder="your.email@campushub.com" required>
        </div>
        <button type="button" id="requestResetBtn" onclick="requestResetCode();">Send Verification Code</button>
      </div>

      <div id="forgot-step-2" hidden>
        <div class="form-group">
          <label for="resetCode">Enter 6-Digit Code</label>
          <input type="text" id="resetCode" placeholder="••••••" maxlength="6" style="letter-spacing:4px; font-weight:bold; text-align:center;" required>
        </div>
        <div class="form-group">
          <label for="newPass">New Password</label>
          <input type="password" id="newPass" required>
        </div>
        <div class="form-group">
          <label for="confirmNewPass">Confirm New Password</label>
          <input type="password" id="confirmNewPass" required>
        </div>
        <button type="button" id="verifyResetBtn" onclick="verifyAndResetPassword();">Update Password</button>
      </div>

      <p style="margin-top: 15px;">Remembered your password? <button type="button" id="show-login-forgot" onclick="toggleLogin();">Log In</button></p>
    </section>

    <section class="reg-container" aria-labelledby="register-heading" hidden>
      <h2 id="register-heading">Register</h2>
      <form>
        <div class="row-layout">
          <div class="form-group">
            <label for="fname">First Name</label>
            <input type="text" name="first_name" id="fname" autocomplete="given-name" required>
          </div>
          <div class="form-group">
            <label for="lname">Last Name</label>
            <input type="text" name="last_name" id="lname" autocomplete="family-name" required>
          </div>
        </div>

        <div class="row-layout">
          <div class="form-group">
            <label for="register-email">Email</label>
            <input type="email" name="email" id="registerEmail" autocomplete="email" required>
          </div>
          <div class="form-group">
            <label for="phone">Phone</label>
            <input type="tel" name="phone" id="phone" autocomplete="tel">
          </div>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" name="password" id="pass" autocomplete="family-name" required>
        </div>
        <div class="form-group">
          <label for="password-confirm">Confirm Password</label>
          <input type="password" name="confirm-password" id="confirmPass" autocomplete="family-name" required>
        </div>
        <div class="form-group">
          <label for="gender">Gender</label>
          <select name="gender" id="gender">
            <option value="">Select Gender</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
          </select>
        </div>

        <div class="form-group">
          <label for="Role">Role</label>
          <select name="gender" id="role">
            <option value="">Select Role</option>
            <option value="student">Student</option>
            <option value="teacher">Teacher/Lecturer</option>
          </select>
        </div>

        <button type="button" id="registerBtn" onclick="register();">Register</button>
      </form>
      <p>Already have an account? <button type="button" id="show-login">Log In</button></p>
    </section>
  </div>
  <script>
    const loginSection = document.querySelector('.login-container');
    const registerSection = document.querySelector('.reg-container');
    const forgotSection = document.querySelector('.forgot-container');
    const showRegisterButton = document.querySelector('#show-register');
    const showLoginButton = document.querySelector('#show-login');

    if (showRegisterButton) {
      showRegisterButton.addEventListener('click', () => {
        loginSection.hidden = true;
        forgotSection.hidden = true;
        registerSection.hidden = false;
      });
    }

    if (showLoginButton) {
      showLoginButton.addEventListener('click', () => {
        registerSection.hidden = true;
        forgotSection.hidden = true;
        loginSection.hidden = false;
      });
    }

    function toggleForgot() {
      loginSection.hidden = true;
      registerSection.hidden = true;
      forgotSection.hidden = false;
    }

    function toggleLogin() {
      forgotSection.hidden = true;
      registerSection.hidden = true;
      loginSection.hidden = false;
    }

    async function requestResetCode() {
      const email = document.getElementById('forgotEmail').value.trim();
      if (!email) {
        showNotification('Please enter your email address.', true);
        return;
      }

      try {
        const res = await fetch('forgot_password.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action: 'request_code', email: email })
        });
        const result = await res.json();

        if (result.success) {
          showNotification(result.message);
          document.getElementById('forgot-step-1').hidden = true;
          document.getElementById('forgot-step-2').hidden = false;
        } else {
          showNotification(result.message || 'Failed to send reset code.', true);
        }
      } catch (err) {
        showNotification('Network error. Please try again.', true);
      }
    }

    async function verifyAndResetPassword() {
      const code = document.getElementById('resetCode').value.trim();
      const newPass = document.getElementById('newPass').value;
      const confirmPass = document.getElementById('confirmNewPass').value;

      if (!code || !newPass || !confirmPass) {
        showNotification('Please fill in all fields.', true);
        return;
      }

      try {
        const res = await fetch('forgot_password.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            action: 'verify_and_reset',
            code: code,
            new_password: newPass,
            confirm_password: confirmPass
          })
        });
        const result = await res.json();

        if (result.success) {
          showNotification(result.message);
          setTimeout(() => {
            toggleLogin();
            document.getElementById('forgot-step-1').hidden = false;
            document.getElementById('forgot-step-2').hidden = true;
          }, 2000);
        } else {
          showNotification(result.message || 'Failed to reset password.', true);
        }
      } catch (err) {
        showNotification('Network error. Please try again.', true);
      }
    }

    async function register() {
      const fname = document.getElementById('fname').value.trim();
      const lname = document.getElementById('lname').value.trim();
      const email = document.getElementById('registerEmail').value.trim();
      const phone = document.getElementById('phone').value.trim();
      const password = document.getElementById('pass').value;
      const confirmPassword = document.getElementById('confirmPass').value;
      const gender = document.getElementById('gender').value;
      const role = document.getElementById('role').value;

      try {
        const res = await fetch('register.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            first_name: fname,
            last_name: lname,
            email: email,
            phone: phone,
            password: password,
            confirm_password: confirmPassword,
            gender: gender,
            role: role
          })
        });
        const result = await res.json();

        if (result.success) {
          showNotification(result.message);
          setTimeout(() => window.location.href = 'login.php', 2500);
        } else {
          showNotification(result.message || 'Registration failed.', true);
        }
      } catch (error) {
        console.error('Registration error:', error);
        showNotification('Unable to complete registration. Please try again.', true);
      }

    }

    async function logIn() {
      const logInPass = document.getElementById('logInPass').value.trim();
      const logInEmail = document.getElementById('loginEmail').value.trim();
      const userName = document.getElementById('userName').value.trim();
      try {
        alert(userName);
        const res = await fetch('loginProccess.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            email: logInEmail,
            password: logInPass
          })
        });
        const result = await res.json();
        if (result.success) {
          showNotification(result.message);
          setTimeout(() => {
            window.location.href = "index.php";
          }, 2500);
        } else {
          showNotification(result.message || 'Registration failed.', true);
        }
      } catch (error) {
        console.error('Login error:', error);
        showNotification('Unable to login. Please try again.', true);
      }

    }
  </script>
  <script src="./assets/js/main.js"></script>
</body>

</html>