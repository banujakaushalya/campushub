<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Unset master control session specific tokens
unset($_SESSION['master_authenticated']);
unset($_SESSION['master_otp']);
unset($_SESSION['master_otp_time']);
unset($_SESSION['master_step']);
unset($_SESSION['master_email']);

header("Location: login.php");
exit;
