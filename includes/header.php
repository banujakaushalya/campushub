<?php
// Simple header include
if (session_status() === PHP_SESSION_NONE)
  session_start();
?>
<!doctype html>
<html>

<head>
  <meta charset="utf-8">
  <title>CampusHub</title>
  <link rel="stylesheet" href="./assets/css/style.css">
</head>

<body>
  <header>
    <style>
      .nav-bar {

        background-color: rgba(86, 131, 127, 0.32);


        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(12px);


        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);


        color: #454654;
        display: flex;
        height: 60px;
        width: 100%;
        border-radius: 10px 10px 10px 10px;
      }

      .nav-logo {
        flex: 1;
        font-size: 2rem;
        color: #ffffff;
        font-weight: bold;
        align-self: center;
        margin-left: 20px;
        text-shadow: #FF6B4A 1px 1px 2px;
      }

      .nav-links {
        flex: 1;
        justify-content: space-evenly;
        align-items: center;
        display: flex;
      }

      .nav-links a {
        text-decoration: none;
        color: #454654;
        font-weight: bold;
      }

      .nav-account {
        flex: 1;
        align-items: center;
        display: flex;
        justify-content: flex-end;
        margin-right: 20px;

      }

      .nav-buttons button {
        background-color: #FF6B4A;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        display: none;
      }

      .acc-container {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: auto;
        margin-left: auto;
      }

      .user-image img {
        width: 40px;
        height: 40px;
      }

      .user-name {
        font-weight: bold;
        color: #454654;
        margin: 0;
      }

      .account-menu {
        position: relative;
        display: flex;
        align-items: center;
        background-color: transparent;
      }

      .account-btn {
        background-color: transparent;
        border: none;
        cursor: pointer;
      }

      .account-btn img {
        width: 30px;
        height: 30px;
      }

      .account-dropdown {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        z-index: 1000;
        min-width: 150px;
        margin-top: 8px;
        padding: 8px 0;
        background-color: #faf8ff;
        border-radius: 4px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        /* display: flex; */
        flex-direction: column;
        height: auto;
      }

      .account-dropdown .dropdown-item {
        padding: 8px 16px;
        cursor: pointer;
        color: #454654;
        background-color: transparent;
        border: none;
      }
    </style>
    <nav class="nav-bar">
      <div class="nav-logo">CampusHub</div>
      <div class="nav-links">
        <a href="/index.php">Home</a>
        <a href="/about.php">About</a>
        <a href="/contact.php">Contact</a>
      </div>
      <div class="nav-account">
        <div class="acc-container">
          <div class="user-image"><img src="assets\images\user.png" alt=""></div>
          <label class="user-name">Name Here</label>
          <div class="account-menu">
            <button class="account-btn">
              <img src="assets/images/menu.png" alt="Menu">
            </button>

            <div class="account-dropdown">
              <button class="dropdown-item">Settings</button>
              <button class="dropdown-item">Profile</button>
              <button class="dropdown-item">Logout</button>
            </div>
          </div>
        </div>
        <div class="nav-buttons">
          <button class="btn-login">Login</button>
          <button class="btn-signup">Sign Up</button>
        </div>
      </div>
    </nav>
  </header>

  <script>
    const accountBtn = document.querySelector('.account-btn');
    const accountDropdown = document.querySelector('.account-dropdown');

    accountBtn.addEventListener('click', () => {
      accountDropdown.style.display = accountDropdown.style.display === 'none' ? 'flex' : 'none';
    });
    document.addEventListener('click', (event) => {
      if (!accountBtn.contains(event.target) && !accountDropdown.contains(event.target)) {
        accountDropdown.style.display = 'none';
      }
    });
  </script>