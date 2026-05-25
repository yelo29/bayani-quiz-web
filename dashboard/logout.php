<?php
error_reporting(0);
ini_set('display_errors', 0);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

unset($_SESSION['is_admin']);
unset($_SESSION['admin_username']);
session_destroy();

header('Location: /dashboard/login.php');
exit;
