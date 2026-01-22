<?php
session_start();
require "../conn.php";
require "../middleware/auth.php";

if ($_SESSION['user']['role'] !== 'doctor') {
    header("Location: ../auth/login.php");
    exit;
}

$doctorId = $_SESSION['user']['id'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_schedule'])) {
    $date = $_POST['available_date'];
    $start = $_POST['start_time'];
    $end = $_POST['end_time'];

    $startTime = strtotime($start);
    $endTime = strtotime($end);

    if ($startTime >= $endTime) {
        $message = "<p style='color:red;'>End time must be after start time.</p>";
    } else {
        try {
            $pdo->beginTransaction();
            
            // Loop to create 1-hour increments
            while ($startTime < $endTime) {
                $slotStart = date("H:i:s", $startTime);
                $nextHour = $startTime + 3600; // 3600 seconds = 1 hour
                
                // Don't create a slot if it exceeds the doctor's end time
                if ($nextHour > $endTime) break;
                
                $slotEnd = date("H:i:s", $nextHour);

                $stmt = $pdo->prepare("INSERT INTO schedules (doctor_id, available_date, start_time, end_time, is_available) VALUES (?, ?, ?, ?, 1)");
                $stmt->execute([$doctorId, $date, $slotStart, $slotEnd]);

                $startTime = $nextHour;
            }
            
            $pdo->commit();
            $message = "<p style='color:green;'>Schedule slots created successfully!</p>";
        } catch (Exception $e) {
            $pdo->rollBack();
            $message = "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
        }
    }
}

// Fetch existing slots to display
$fetchSlots = $pdo->prepare("SELECT * FROM schedules WHERE doctor_id = ? AND available_date >= CURDATE() ORDER BY available_date, start_time");
$fetchSlots->execute([$doctorId]);
$mySlots = $fetchSlots->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage My Schedule</title>
    <link rel="stylesheet" href="style.css"> <style>
        .form-box { background: white; padding: 20px; border: 1px solid #ddd; border-radius: 8px; margin: 20px; }
        .slot-table { width: 95%; margin: 20px; border-collapse: collapse; }
        .slot-table th, .slot-table td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        .btn-save { background: #3A8DDE; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container2" style="width: 100%; float: none;">
        <div style="margin: 20px;">
            <a href="dasboard.php" style="text-decoration: none;">← Back to Dashboard</a>
            <h2>Manage Your Availability</h2>
            <?= $message ?>
        </div>

        <div class="form-box">
            <h3>Add New Time Block (Splits into 1-hour slots)</h3>
            <form method="POST">
                <label>Date:</label>
                <input type="date" name="available_date" required min="<?= date('Y-m-d') ?>">
                
                <label>Start Time:</label>
                <input type="time" name="start_time" required>
                
                <label>End Time:</label>
                <input type="time" name="end_time" required>
                
                <button type="submit" name="add_schedule" class="btn-save">Generate Slots</button>
            </form>
        </div>

        <table class="slot-table">
            <thead>
                <tr style="background: #E6F2FB;">
                    <th>Date</th>
                    <th>Time Slot</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mySlots as $slot): ?>
                <tr>
                    <td><?= $slot['available_date'] ?></td>
                    <td><?= date('h:i A', strtotime($slot['start_time'])) ?> - <?= date('h:i A', strtotime($slot['end_time'])) ?></td>
                    <td><?= $slot['is_available'] ? "<span style='color:green;'>Available</span>" : "<span style='color:red;'>Booked</span>" ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>