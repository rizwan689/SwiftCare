<?php
session_start();
require "../conn.php";
require "../middleware/auth.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['schedule_id'])) {
    $patient_id = $_SESSION['user']['id'];
    $doctor_id = $_POST['doctor_id'];
    $schedule_id = $_POST['schedule_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    try {
        $pdo->beginTransaction();

        // 1. Insert the new session
        $stmt = $pdo->prepare("
            INSERT INTO sessions (doctor_id, patient_id, schedule_id, session_date, session_time, status) 
            VALUES (?, ?, ?, ?, ?, 'confirmed')
        ");
        $stmt->execute([$doctor_id, $patient_id, $schedule_id, $date, $time]);

        // 2. Update the schedule table so this slot is no longer visible
        $updateStmt = $pdo->prepare("UPDATE schedules SET is_available = FALSE WHERE id = ?");
        $updateStmt->execute([$schedule_id]);

        $pdo->commit();
        header("Location: dashboard.php?booking=success");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Booking failed: " . $e->getMessage());
    }
}