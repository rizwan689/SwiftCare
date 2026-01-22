<?php
session_start();
require "../conn.php";
require "../middleware/auth.php";

// Check if user is doctor
if ($_SESSION['user']['role'] !== 'doctor') {
    header("Location: ../auth/login.php");
    exit;
}

$doctorId = $_SESSION['user']['id'];
$user = $_SESSION['user'];

$stmt = $pdo->prepare(
    "SELECT s.*, u.name AS patient_name
     FROM sessions s
     JOIN users u ON s.patient_id = u.id
     WHERE s.doctor_id = ? 
     ORDER BY s.session_date DESC"
);
$stmt->execute([$doctorId]);
$sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Sessions</title>
<style>
    .container1{
      border: 2px solid grey;
    width: 207px;
    height: 578px;
    float: left;
    }

    .container2{
    border-left: 2px solid grey;
    height: 578px;
    float: right;
    width: 1120px;
    }

    .sub-container1{
        text-align: center;
    }

    .list{
    margin-top: 35px;
    margin-right: 38px;
    margin-left: -2px;
    text-align: center;
    }

    .list ul li{
        list-style-type: none;
        padding-bottom: 30px;
    }

    .list ul li a{
        text-decoration: none;
        color: black;
    }

    .list ul li a:hover{
        border: 2px solid blue;
        color: blue;
    }

    .logout-butt{
    background-color: #E6F2FB;
    color: #3A8DDE;
    border-radius: 6px;
    border: none;
    padding: 8px 16px;
    width: 135px;
    cursor: pointer;
    }

    .logout-butt:hover{
        background-color: #3A8DDE;
        color: white;
    }

    .back{
        width: 80px;
        margin: 13px;
        background-color: #E6F2FB;
        color: #3A8DDE;
        border-radius: 6px;
        border: none;
        padding: 8px 16px;
        cursor: pointer;
    }

    .back:hover{
        background-color: #3A8DDE;
        color: white;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px;
    }

    th, td {
        border: 1px solid grey;
        padding: 12px;
        text-align: left;
    }

    th {
        background-color: rgb(21, 114, 211);
        color: white;
    }

    tr:hover {
        background-color: #f5f5f5;
    }

    .status {
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 12px;
        font-weight: bold;
    }

    .status.pending {
        background-color: #fff3cd;
        color: #856404;
    }

    .status.confirmed {
        background-color: #d4edda;
        color: #155724;
    }

    .status.completed {
        background-color: #d1ecf1;
        color: #0c5460;
    }

    .status.cancelled {
        background-color: #f8d7da;
        color: #721c24;
    }
</style>
</head>
<body>
    <div class="container1">
        <div class="sub-container1">
            <img style="width: 100px;" src="data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAkGBw0NDQ0NDQ4NDQ0NDg0NDg0ODQ8NDQ0NFREWFhURExMYHTQsJBolJxMTITEtJSkrMS8uGB8/ODg4PCg5LisBCgoKDg0OFxAQGy0lICYtLTUrLTcvLTUvLi41LS0rLy0wNTIvLS0tLy4uLS0tKzAvLSs3LTYtLi0rLS0tKystLf/AABEIAOEA4QMBEQACEQEDEQH/xAAbAAEAAwEBAQEAAAAAAAAAAAAAAQIDBAUGB//EADUQAQACAQEDCAkEAgMAAAAAAAABAgMRBBIxBQYTIUFRcYEWYWJjkZOhseEiMlLBFCNRctH/xAAaAQEBAAMBAQAAAAAAAAAAAAAAAQIEBQMG/8QAKhEBAAIBAgQFBAMBAAAAAAAAAAECAwQREjFhkQUUFSFREzJCgUFxsaH/2gAMAwEAAhEDEQA/AP3EAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAEAagagjUDUDUDUDUDUDUDUE6gagAkAAAAAAAAAAAAAEAjUFZkEbwG8CN4DeA3gN4DeA3gTvAbwJ3gWiQTqACQAAAAAAAAAQCJkFZkFJsCs2XYQbAAAAAAAABqbCYk2FosgvEgtEgkEgAAAAAAAArMgraQZzYFVBQAAAAAAAAAAAQTEoNKyC8AkEgAAAAAAiQUtIMrWBCgoAAAAAAAAAAAAAAmssRrWQXgEgAAAAAiQRIMrSDOFgSoAAAAAAxzbXix9V8mOk91r1rPwllXHa3KHnbLjp91ohGLbcF50plxWnujJWZ+GqzivHOJSufHb7bR3bsHqAAAAAAiUkaVlBpALQCQAAAARIKWBjeQRTgolQAAABntGemKlsl53a1jWZ/qPWypWbTtDDJkrjrNrcnyXKXLmXNM1pM4sfZWs6WtHtT/UOrh0lKe8+8uDqNdkyTtX2h5LaaKJEehyfyxm2eYiLTfH247zrGnsz2PDLpqZOktrBrcmKee8fD7DYNtx7RjjJjnq4TE/urbulyMmK2O3DZ9Bgz1zV4quhg9gAAAEW4IGOUG9QXgEgAAAAiQZ2BhkkE04QolQAAAB8lzn26cmXoYn9GLj68mnXPlw+Lq6PFw0455y4PiOfjycEco/14rcc5GoiFECPR5C2+cGeszP8AryTFMkdmk8LeX/rX1OL6lJ+YbWi1E4ssfE+0vuXFfTgAAAEgpjliOioNIBIAAAAKyClgc+QE4+EKLKAAAAPzrLkm1rXnja1rT4zOr6CsbREPkbW4rTLOZZMAECIBEqj9F2DJN8OG88bYsdp8ZrD5/JG15jq+uwW4sdbT8Q3YPUAAAkGeJiOigNIBYAAAAFZBnYHPkBbHwhRZQAAAB+dbTinHkvjnjS9q/CdH0FLcVYl8jkrwXms/xLJk80AaqiJkECP0jZMXR4sWP+GOlJ8YrEPnr24rTL7DFXgpWvxENWL0AAAJBliYjpoDSAWAAAABWQZ3Bz5AWx8IUWUAAAAfLc6+T5i3+TSP020rk9m3CLeE9UePi6Wizbx9Of04fienmLfVrynm+ddFyETII1VEA9jmzydObNGS0f6sMxaZ7LZI661+0z+WnrM3BThjnLoeHaacuTjnlH+vtnHfSAAAAEgyxMR0UBrALAAAAArIM7g58oLY+EKLKAAAAIvWLRNbRE1mJiYmNYmO6SJ2neEmImNpfLcqc2bxM32b9VePRWnS1f8ArM8Y8XTw66OWTu4mp8MtE8WLl8PAzbNlxzpkx3pPtUmrerkrblMOXfFen3RMK4sN7zpSl7z3VrNp+azesc5Y1pa32xMva5N5tZskxbP/AKafx6pyW8uzz+DTza2tfanvP/HR0/hmS875PaP+vrdnwUxUrjx1itKxpEQ5drTad55u9jx1x1itY2hoxZgAAAEgxxsR00BrALAAAAArIM7g58oLY+EKLKAAAAAAAAAAAAAAAEgxxsR00BrALAAAAArIM7g58gLY+EKLKAAAKZctaVm97VpWONrTERHmtazadoY2vWkb2naHg7bzqxV1jDScs/yt+inl2z9G7j0N597Ts5ebxalfbHG/+PIz85NrvwvXHHdSkfe2rbrosUc43c6/ieotynb+ocd+VtqnjtGbyyWr9ntGnxR+MPCdXnnnee6teVdqjhtGfzy3n7yTp8U/jDGNXnj857urBzj2ynHJF47r0rP1jSfq87aLDP8AGz3p4lqK/lv/AHD1tj521nSM+Kae3jner51nr+7VyeHzH2Tu38PjFZ9sldusPodl2rHmrv4r1vXvrPCe6Y7J8WhelqTtaNnWx5aZK8VJ3hqxegAABIMsTEdFAawCwAAAAKyDOwOfIC2PhCiygADk5T2+mzY5yX6+ytI43t3Q9MWK2S3DDw1GorgpxW/XV8Nyjyjl2m+9kt1R+2kfspHqj+3bxYa4o2q+X1GpyZ7b2n9OOZerwQIKIERqCFTdtse2ZMF4yYrTS3b3WjutHbDDJjrkja0M8Wa+K3FSdpfech8r02ukzpFctNOkx68Paj1OJqNPOG3T+H1Oi1ldRX4mOcPSa7dAAJBljYjpoDSAWAAAABWQUsDnyAnFPVCiygAD4znje/8Ak1idd2MVZp3dczrP0+kOvoIj6czHPd854tNvrRE8tvZ4LdcsUQIgEaqiBDVURqD0+bN7xtuDc1/VNq207abs66/DXyhq6yInDbdu+HTaNTTh/f8AT9DcJ9cAAi89U+CCmNB0UBpALAAAAAiQZ2BhkgFMM9cx5rA1UAAcu38n4dprFctN7TrrMTNbV8Jh6Y8t8c71l4Z9PjzRteHnei+ye9+Z+Hv57L07NT0rT9e56LbJ735n4Xz2Xp2PStP17notsnvfmfg89l6dj0nT9e56K7H735n4PPZenZPSdP17norsfvfmfg8/l6609I0/Xuj0U2P3vzPwefy9Ox6Rp+vd38m8kbPsus4qaWtGk3tM2vp3a93g8cuoyZfultafR4cHvSPf5dzxbQADPNPVp3/ZJE44QdFQXgFgAAAARIKWBjeAc9uqdY7Ab1trGsKJUAAAAAAAAAAAAAJkHPrvTr8PBiN8cA3qC8AkAAAAESCJBleAc+SoM63ms+rthR0VtExrAJUAAAAAAAAAAAJnTiDmyZN7qjh92I0x1BvSAawC0AkAAAAAESCloBleoML0BjGtZ1j8A1rtMf8o09cdcLuNYyVnhMfEFtQNQNQNQNQNQNQNQVm9Y4zHxBnbaI7Ov6QbjKbWtx+HYg0pQG9Kg2rALQCwAAAAAAIBEwCloBlaoMrUBlbGCk4wVnGCOjA6MDowDowOjA6MD0YJjGDSuMGtaA1rUGtagvEAkEgAAAAAAAgETAKzUFJqCk0BScYI6MEdGB0YI6MDowT0YHRgnowTGMFooC8VBeKgvEAkEgAAAAAAAAAgDQFZgEboI3QRugboI3ANwDcBO6BugboJ3QTFQTEAkEgkAAAAAAAAAAAAEAaAjQDQDQDQDQDQDQDQDQDQE6AaAkAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAH/2Q==" alt="">
            <p><b><?= htmlspecialchars($user['name']) ?></b><br> <?= htmlspecialchars($user['email']) ?></p>
            <a href="../auth/logout.php"><button class="logout-butt">Logout</button></a>
        </div>
        <hr>
        <div class="list">
            <ul>
                <li><a href="dasboard.php">Dashboard</a></li>
                <li><a href="sessions.php">My Sessions</a></li>
                <li><a href="manage_schedule.php">Schedule</a></li>
            </ul>
        </div>
    </div>

    <div class="container2">
        <div style="margin: 20px;">
            <a href="dasboard.php"><button class="back">Back</button></a>
            <h2 style="display: inline; margin-left: 20px;">My Sessions</h2>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($sessions) > 0): ?>
                    <?php foreach ($sessions as $session): ?>
                    <tr>
                        <td><?= htmlspecialchars($session['patient_name']) ?></td>
                        <td><?= date('M d, Y', strtotime($session['session_date'])) ?></td>
                        <td><?= date('h:i A', strtotime($session['session_time'])) ?></td>
                        <td>
                            <span class="status <?= strtolower($session['status']) ?>">
                                <?= htmlspecialchars($session['status']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($session['notes'] ?? 'N/A') ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">No sessions found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
