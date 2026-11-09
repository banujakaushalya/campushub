<?php
// Redirect to root register_event.php
$queryString = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("Location: ../register_event.php" . $queryString, true, 301);
exit;
