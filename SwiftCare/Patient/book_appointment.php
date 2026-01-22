<?php
session_start();
require "../conn.php";
require "../middleware/auth.php";

// Fetch available slots from the 'schedules' table
$stmt = $pdo->prepare("
    SELECT s.*, u.name as doctor_name, u.specialization 
    FROM schedules s
    JOIN users u ON s.doctor_id = u.id
    WHERE s.is_available = TRUE 
    AND s.available_date >= CURDATE()
    ORDER BY s.available_date ASC, s.start_time ASC
");
$stmt->execute();
$available_slots = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Appointment - SwiftCare</title>
    <style>
        /* Minimalist styling to match your dashboard */
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .slot-card { background: white; border-radius: 8px; padding: 20px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .doc-name { color: rgb(21, 114, 211); font-weight: bold; font-size: 1.1em; }
        .spec { color: #666; font-size: 0.9em; margin-bottom: 5px; }
        .book-btn { background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; transition: 0.3s; }
        .book-btn:hover { background: #218838; }
    </style>
</head>
<body>
    <div style="max-width: 900px; margin: auto;">
        <a href="dashboard.php" style="text-decoration: none; color: #666;">← Back to Dashboard</a>
        <h2 style="margin: 20px 0;">Available Appointments</h2>

        <?php if ($available_slots): ?>
            <?php foreach ($available_slots as $slot): ?>
                <div class="slot-card">
                    <div>
                        <div class="doc-name">Dr. <?= htmlspecialchars($slot['doctor_name']) ?></div>
                        <div class="spec"><?= htmlspecialchars($slot['specialization'] ?? 'General Physician') ?></div>
                        <div>📅 <?= date('M d, Y', strtotime($slot['available_date'])) ?> | 🕒 <?= date('h:i A', strtotime($slot['start_time'])) ?></div>
                    </div>
                    
                    <form action="process_booking.php" method="POST">
                        <input type="hidden" name="schedule_id" value="<?= $slot['id'] ?>">
                        <input type="hidden" name="doctor_id" value="<?= $slot['doctor_id'] ?>">
                        <input type="hidden" name="date" value="<?= $slot['available_date'] ?>">
                        <input type="hidden" name="time" value="<?= $slot['start_time'] ?>">
                        <button type="submit" class="book-btn">Book Appointment</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No available sessions found at the moment.</p>
        <?php endif; ?>
    </div>
</body>
</html>