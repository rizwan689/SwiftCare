<?php
session_start();
require "../conn.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=?");
    $stmt->execute([$_POST['email']]);
    $user = $stmt->fetch();

    if ($user && password_verify($_POST['password'], $user['password'])) {
        $_SESSION['user'] = $user;

        // Fix the redirect path
        if ($user['role'] === 'admin') {
            header("Location: ../admin/dashboard.php");
        } elseif ($user['role'] === 'doctor') {
            header("Location: ../doctor/dasboard.php");
        } elseif ($user['role'] === 'patient') {
            header("Location: ../Patient/dashboard.php");
        } else {
            header("Location: ../index.php");
        }
        exit;
    }
    $error = "Invalid credentials";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

<style>

body{
    min-height: 100vh;
}

/* ===== ORIGINAL STYLES (UNCHANGED) ===== */

.form{
    background-color: burlywood;
    border: 2px solid black;
    border-radius: 24px;
    text-align: center;
    width: 500px;
    height: 400px;
    margin: 104px 0px 0px 456px;
    display: flexbox;
}



h1{
    text-align: center;
    font-style: italic;
}

.login-page{
    margin-top: 25px;
}

.login-page input{
    margin: 9px;
    padding: 5px;
    border-radius: 5px;
    text-align: center;
}

.login-page p a{
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

/* ===== RESPONSIVE FIX (ADDED ONLY) ===== */

/* Tablets */
@media (max-width: 1024px){
    .form{
        margin: 100px auto;
    }
}

/* Mobile */
@media (max-width: 768px){
    .form{
        width: 90%;
        height: auto;
        margin: 80px auto;
        padding: 20px;
    }

    .login-page input{
        width: 90%;
    }

    .button-submit{
        width: 70%;
    }

    .login-page p a{
        margin-left: 0;
        display: inline-block;
        margin-top: 10px;
    }
}

</style>
</head>

<body>

    <div class="form">
        <h1>Welcome Back!</h1>
        <p>Login with your details to continue</p>

        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="" class="login-page" method="POST">

            <label style="font-weight: bold;">Email</label><br>
            <input type="email" name="email" placeholder="Enter Your Email" required><br><br>

            <label style="font-weight: bold;">Password</label><br>
            <input type="password" name="password" placeholder="Enter Your Password" required><br><br>

            <input type="submit" class="button-submit" value="Submit" style="font-weight: bold;">

            <p>
                <a href="register.php">
                    If Not User Click Here TO Sign Up Yourself?
                </a>
            </p>

        </form>
    </div>

</body>
</html>
