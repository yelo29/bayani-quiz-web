<?php
error_reporting(0);
ini_set('display_errors', 0);

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/api_response.php';

// API Authentication Check
function api_auth_check() {
    if (!isset($_SESSION['user_id'])) {
        api_response(['error' => 'Unauthorized'], 401);
    }
}
