<?php
require_once '../includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$curr_role = $_SESSION['role'] ?? 'guest';
if ($curr_role !== 'super_admin' && $curr_role !== 'admin' && $curr_role !== 'moderator') {
    header("Location: ../student/dashboard.php");
    exit;
}

$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($event_id > 0) {
    Database::iud("DELETE FROM registrations WHERE events_event_id = ?", "i", [$event_id]);
    Database::iud("DELETE FROM media WHERE events_event_id = ?", "i", [$event_id]);
    Database::iud("DELETE FROM events WHERE event_id = ?", "i", [$event_id]);
}

header("Location: manage_events.php");
exit;
