<?php
// Redirect to root events.php
$queryString = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("Location: ../events.php" . $queryString, true, 301);
exit;
