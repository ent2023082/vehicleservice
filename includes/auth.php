<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

function require_admin()
{
    if (($_SESSION['role'] ?? 'customer') !== 'admin') {
        header("Location: dashboard.php");
        exit;
    }
}
?>