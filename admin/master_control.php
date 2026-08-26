<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Seamless redirect to the dedicated Master Control portal
if (isset($_SESSION['master_authenticated']) && $_SESSION['master_authenticated'] === true && ($_SESSION['role'] ?? '') === 'super_admin') {
    header("Location: ../master_control/index.php");
} else {
    header("Location: ../master_control/login.php");
}
exit;
