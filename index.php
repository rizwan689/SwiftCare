<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header("Location: auth/login.php");
    exit();
}

$user = $_SESSION['user'];
switch ($user['role']) {
    case 'admin':
        header("Location: admin/dashboard.php");
        break;
    case 'doctor':
        header("Location: doctor/dasboard.php");
        break;
    case 'patient':
        header("Location: Patient/dashboard.php");
        break;
    default:
        header("Location: auth/login.php");
}
exit();
