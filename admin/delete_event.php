<?php
require_once '../includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($event_id > 0) {
    Database::iud("DELETE FROM events WHERE event_id = ?", "i", [$event_id]);
}

header("Location: manage_events.php");
exit;
