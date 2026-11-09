<?php
// Redirect to root event_details.php
$queryString = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("Location: ../event_details.php" . $queryString, true, 301);
exit;
