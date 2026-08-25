<?php
// Basic error handler
set_error_handler(function($severity, $message, $file, $line) {
  error_log("[$severity] $message in $file on line $line");
});
