<?php
session_start();
require "../conn.php";

if ($_SESSION['user']['role'] !== 'admin') {
    exit("Unauthorized");
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Safety check: Don't delete if someone already booked it
    $check = $pdo->prepare("SELECT is_available FROM schedules WHERE id = ?");
    $check->execute([$id]);
    $slot = $check->fetch();

    if ($slot && $slot['is_available'] == 0) {
        header("Location: schedule_manager.php?error=Cannot delete a booked slot.");
    } else {
        $stmt = $pdo->prepare("DELETE FROM schedules WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: schedule_manager.php?success=Schedule deleted.");
    }
}