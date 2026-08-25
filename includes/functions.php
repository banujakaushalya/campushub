<?php
// Reusable functions (sanitisation, validation, auth checks)
function e($s) {
  return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function is_logged_in() {
  return !empty($_SESSION['user_id']);
}
