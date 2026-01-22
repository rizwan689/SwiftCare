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

$todaySessions = $pdo->prepare(
    "SELECT COUNT(*) FROM sessions WHERE doctor_id=? AND session_date=CURDATE()"
);
$todaySessions->execute([$doctorId]);
$todaySessionsCount = $todaySessions->fetchColumn();

$totalSessions = $pdo->prepare(
    "SELECT COUNT(*) FROM sessions WHERE doctor_id=?"
);
$totalSessions->execute([$doctorId]);
$totalSessionsCount = $totalSessions->fetchColumn();

$totalPatients = $pdo->prepare(
    "SELECT COUNT(DISTINCT patient_id) FROM sessions WHERE doctor_id=?"
);
$totalPatients->execute([$doctorId]);
$totalPatientsCount = $totalPatients->fetchColumn();

$upcomingSessions = $pdo->prepare(
    "SELECT s.*, u.name AS patient_name
     FROM sessions s
     JOIN users u ON s.patient_id = u.id
     WHERE s.doctor_id = ? AND s.session_date >= CURDATE()
     ORDER BY s.session_date ASC
     LIMIT 5"
);
$upcomingSessions->execute([$doctorId]);
$sessions = $upcomingSessions->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
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
        border-left: blue;
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

    .con1{
       width: 100%;
        height: 48px;
        margin-bottom: 10px;
    }

    .head-dashboard{
            font-size: x-large;
            margin-top: 6px;
            margin-left: 9px;
    }

     .con2{
           background-image: url('https://cdn.create.vista.com/api/media/small/499107952/stock-photo-doctor-desk-office-concept-blank-sheet-medical-clipboard-prescription-form');
           background-repeat: no-repeat;
           object-fit: contain;
           background-size: cover;
           width: 100%;
           height: 215px;
           margin-bottom: 10px;
           margin-left: 8px;
    }

     .con3{
           width: 50%;
    height: 283px;
    }

     .con4{
         width: 49%;
    height: 283px;
    margin-left: 572px;
    margin-top: -287px;
    }

    .date{
      float: right;
    margin-top: -54px;
    margin-right: 14px;
    }

    .welcome{
        margin-left: 15px;
        color: white;
        font-weight: bold;
    }

    .header{
      margin-left: 15px;
      color: white;
      font-size: 2em;
    }

    .text{
        margin-left: 15px;
        width: 80%;
        color: white;
        background-color: rgba(0,0,0,0.5);
        padding: 10px;
        border-radius: 5px;
    }

    .butt{
        margin-left: 15px;
    width: 231px;
    height: 30px;
    background-color: blue;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    }

    .butt:hover{
        background-color: darkblue;
    }

    .cont1{
        border: 2px solid grey;
        width: 46%;
        height: 80px;
        margin-bottom: 10px;
        margin-left: 14px;
        text-align: center;
    } 

    .cont2{
    border: 2px solid grey;
    width: 46%;
    height: 81px;
    margin-left: 288px;
    margin-top: -94px;
    margin-bottom: 10px;
    text-align: center;
    }

    .cont3{
         border: 2px solid grey;
         width: 46%;
          height: 80px;
          margin-left: 14px;
          text-align: center;
    }

    .cont4{
        border: 2px solid grey;
    width: 46%;
    height: 80px;
    margin-left: 289px;
    margin-top: -84px;
    text-align: center;
    }

    .doc1{
      margin-top: -18px;
    }

    .patient1{
            margin-top: -18px;
    }

    .booking{
            margin-top: -18px;
    }

    .head3{
        margin-left: 15px;
    }

    .main-table{
        width: 100%;
        text-align: center;
    }

    .main-table th{
        border-bottom: 2px solid rgb(66, 66, 185);
        padding: 8px;
    }

    .main-table td{
        padding: 8px;
    }

    @media screen and (max-width: 1200px) {
        .container2 { width: calc(100% - 215px); }
        .con4 { margin-left: 52%; }
    }

    @media screen and (max-width: 768px) {
        .container1, .container2 {
            float: none;
            width: 100%;
            height: auto;
            border: none;
        }
        .container1 { border-bottom: 2px solid grey; padding-bottom: 20px; }
        .list ul { padding: 0; display: flex; flex-wrap: wrap; justify-content: center; gap: 15px; }
        .list ul li { padding-bottom: 0; }
        
        .con3, .con4 {
            width: 100%;
            height: auto;
            margin-left: 0;
            margin-top: 20px;
            float: none;
        }
        
        .cont2, .cont4 {
            margin-left: 14px;
            margin-top: 10px;
        }
        
        .con1 { height: auto; text-align: center; }
        .date { float: none; margin-top: 10px; margin-right: 0; }
        .text { width: 90%; }
        
        .cont1, .cont2, .cont3, .cont4 {
            width: 90%;
            margin: 10px auto;
        }
    }
</style>
</head>
<body>
    <div class="container1">
            <div class="sub-container1">
                <img style="width: 100px;" class="image" src="data:image/jpeg;base64,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" alt="">
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
        <div class="con1">
            <p class="head-dashboard">Dashboard</p>
            <p class="date" id="dateTime"></p>
        </div>

        <div class="con2">
            <p class="welcome">Welcome!</p>
            <h2 class="header"><?= htmlspecialchars($user['name']) ?></h2>
            <p class="text">Welcome to your doctor dashboard. Here you can manage your appointments, view your schedule, and track your patient sessions. Stay organized and provide the best care for your patients.</p>
            <a href="sessions.php"><button class="butt">View My Sessions</button></a>
        </div>

        <div class="con3">
            <h3 class="head3">Status</h3>
            <div class="cont1">
                <p style="font-size: 2em; font-weight: bold; color: rgb(21, 114, 211);"><?= $todaySessionsCount ?></p><br>
                <p class="doc1">Today's Sessions</p>
            </div>

            <div class="cont2">
                <p style="font-size: 2em; font-weight: bold; color: rgb(21, 114, 211);"><?= $totalSessionsCount ?></p><br>
                <p class="patient1">Total Sessions</p>
            </div>

            <div class="cont3">
                <p style="font-size: 2em; font-weight: bold; color: rgb(21, 114, 211);"><?= $totalPatientsCount ?></p><br>
                <p class="booking">Total Patients</p>
            </div>

            <div class="cont4">
                <p style="font-size: 2em; font-weight: bold; color: rgb(21, 114, 211);"><?= $user['specialization'] ?? 'N/A' ?></p>
                <p>Specialization</p>
            </div>
        </div>


        <div class="con4">
            <h3 style="text-align: center;" class="head4">Your Upcoming Sessions</h3>
            <div class="table1">
            <table class="main-table">
                <tr>
                    <th>Patient</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                </tr>

                <?php if (count($sessions) > 0): ?>
                    <?php foreach ($sessions as $session): ?>
                    <tr>
                        <td><?= htmlspecialchars($session['patient_name']) ?></td>
                        <td><?= $session['session_date'] ?></td>
                        <td><?= $session['session_time'] ?></td>
                        <td><?= htmlspecialchars($session['status']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center;">No upcoming sessions</td>
                    </tr>
                <?php endif; ?>
            </table>
            </div>
        </div>

    </div>
</body>
<script>
function showDateTime() {
    const now = new Date();
    const date = now.toLocaleDateString();
    const time = now.toLocaleTimeString();
    document.getElementById('dateTime').innerHTML = "Today Date and Time <br>" + date + ' ' + time;
}

setInterval(showDateTime, 1000);
showDateTime();
</script>
</html>