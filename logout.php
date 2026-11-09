<?php
// Destroy session and redirect to home
session_start();
session_unset();
$_SESSION = [];
session_destroy();
header('Location: index.php');
exit;
