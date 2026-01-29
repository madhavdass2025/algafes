<?php
session_start();

function check_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

function check_super_admin() {
    if ($_SESSION['role'] !== 'super_admin') {
        die("Access denied. Super Admin privileges required.");
    }
}
?>
