<?php
session_start();
require "../conn.php";

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if email already exists
    $checkStmt = $pdo->prepare("SELECT * FROM users WHERE email=?");
    $checkStmt->execute([$_POST['email']]);
    
    if ($checkStmt->fetch()) {
        $error = "Email already registered. Please login instead.";
    } else {
        // Determine role - default to 'patient' unless specified
        $role = $_POST['role'] ?? 'patient';
        
        $stmt = $pdo->prepare(
            "INSERT INTO users (name,email,password,role) VALUES (?,?,?,?)"
        );
        
        if ($stmt->execute([
            $_POST['name'],
            $_POST['email'],
            password_hash($_POST['password'], PASSWORD_DEFAULT),
            $role
        ])) {
            $success = "Registration successful! Redirecting to login...";
            header("Refresh: 2; url=login.php");
        } else {
            $error = "Registration failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>

<style>

body{
    min-height: 100vh;
}

/* ===== ORIGINAL STYLES (UNCHANGED) ===== */

.form-2{
    background-color: burlywood;
    border: 2px solid black;
    border-radius: 24px;
    text-align: center;
    width: 500px;
    height: auto;
    margin: 50px 0px 0px 456px;
    padding: 20px;
}

h1{
    text-align: center;
    font-style: italic;
}

.Register-page{
    margin-top: 25px;
}

.Register-page input{
    margin: 9px;
    padding: 5px;
    border-radius: 5px;
    text-align: center;
}

.Register-page select{
    margin: 9px;
    padding: 5px;
    border-radius: 5px;
    text-align: center;
    width: 200px;
}

.Register-page p a{
    margin-left: 21px;
}

.button-submit{
    width: 180px;
}

.error-message {
    color: red;
    font-weight: bold;
    margin: 10px 0;
}

.success-message {
    color: green;
    font-weight: bold;
    margin: 10px 0;
}

/* ===== RESPONSIVE FIX (ADDED ONLY) ===== */

/* Tablets */
@media (max-width: 1024px){
    .form-2{
        margin: 80px auto;
    }
}

/* Mobile */
@media (max-width: 768px){
    .form-2{
        width: 90%;
        height: auto;
        margin: 60px auto;
        padding: 20px;
    }

    .Register-page input{
        width: 90%;
    }

    .Register-page select{
        width: 90%;
    }

    .button-submit{
        width: 70%;
    }

    .Register-page p a{
        margin-left: 0;
        display: inline-block;
        margin-top: 10px;
    }
}

</style>
</head>

<body>

    <div class="form-2">
        <h1>Registration Form</h1>

        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-message"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form action="" class="Register-page" method="POST">

            <label style="font-weight: bold;">Full Name</label><br>
            <input type="text" name="name" placeholder="Enter Your Name" required><br><br>

            <label style="font-weight: bold;">Email</label><br>
            <input type="email" name="email" placeholder="Enter Your Email" required><br><br>

            <label style="font-weight: bold;">Password</label><br>
            <input type="password" name="password" placeholder="Enter Your Password" required><br><br>

            <label style="font-weight: bold;">Register As</label><br>
            <select name="role" required>
                <option value="patient">Patient</option>
                <option value="doctor">Doctor</option>
            </select><br><br>

            <input type="submit" class="button-submit" value="Submit" style="font-weight: bold;">

            <p>
                <a href="login.php">
                    If User Click Here TO Login?
                </a>
            </p>

        </form>
    </div>

</body>
</html>
