<?php
session_start();

include '../config/db.php';

$error = "";

if(isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Find user by email
    $sql = "SELECT * FROM users WHERE email='$email'";

    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) > 0) {

        $user = mysqli_fetch_assoc($result);

        // Verify password
        if(password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['fullname'] = $user['fullname'];

            // Redirect by role
            if($user['role'] == 'admin') {

                header("Location: ../admin/dashboard.php");

            } elseif($user['role'] == 'organizer') {

                header("Location: ../organizer/dashboard.php");

            } else {

                header("Location: ../user/events.php");
            }

            exit();

        } else {

            $error = "Invalid email or password";
        }

    } else {

        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pamoja Ticketing System - Login</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Poppins',sans-serif;
        }

        body{
            display:flex;
            justify-content:center;
            align-items:center;
            min-height:100vh;
            background:linear-gradient(135deg,#4f46e5,#7c3aed);
        }

        .login-container{
            width:400px;
            background:#fff;
            padding:40px;
            border-radius:20px;
            box-shadow:0 15px 35px rgba(0,0,0,.2);
        }

        .logo{
            text-align:center;
            margin-bottom:15px;
        }

        .logo i{
            font-size:55px;
            color:#4f46e5;
        }

        h2{
            text-align:center;
            margin-bottom:25px;
            color:#333;
        }

        .error{
            background:#ffe5e5;
            color:#d8000c;
            padding:12px;
            border-radius:8px;
            text-align:center;
            margin-bottom:20px;
            font-size:14px;
        }

        .input-group{
            position:relative;
            margin-bottom:20px;
        }

        .input-group i{
            position:absolute;
            top:50%;
            left:15px;
            transform:translateY(-50%);
            color:#777;
        }

        .input-group input{
            width:100%;
            padding:14px 14px 14px 45px;
            border:1px solid #ccc;
            border-radius:10px;
            outline:none;
            font-size:15px;
            transition:.3s;
        }

        .input-group input:focus{
            border-color:#4f46e5;
            box-shadow:0 0 6px rgba(79,70,229,.3);
        }

        .login-btn{
            width:100%;
            padding:14px;
            border:none;
            border-radius:10px;
            background:#4f46e5;
            color:#fff;
            font-size:16px;
            font-weight:600;
            cursor:pointer;
            transition:.3s;
        }

        .login-btn:hover{
            background:#4338ca;
        }

        .admin-btn{
            display:block;
            width:100%;
            padding:14px;
            margin-top:15px;
            background:#111827;
            color:#fff;
            text-align:center;
            text-decoration:none;
            border-radius:10px;
            font-weight:600;
            transition:.3s;
        }

        .admin-btn:hover{
            background:#000;
        }

        .extra-links{
            margin-top:20px;
            text-align:center;
            font-size:14px;
        }

        .extra-links a{
            color:#4f46e5;
            text-decoration:none;
            font-weight:600;
        }

        .extra-links a:hover{
            text-decoration:underline;
        }

        @media(max-width:450px){

            .login-container{
                width:90%;
                padding:30px 20px;
            }

        }

    </style>

</head>
<body>

<div class="login-container">

    <div class="logo">
        <i class="fa-solid fa-ticket"></i>
    </div>

    <h2>Welcome Back</h2>

    <?php if($error != ""){ ?>
        <div class="error">
            <?php echo $error; ?>
        </div>
    <?php } ?>

    <form method="POST">

        <div class="input-group">
            <i class="fa-solid fa-envelope"></i>
            <input
                type="email"
                name="email"
                placeholder="Enter your email"
                required>
        </div>

        <div class="input-group">
            <i class="fa-solid fa-lock"></i>
            <input
                type="password"
                name="password"
                placeholder="Enter your password"
                required>
        </div>

        <button type="submit" name="login" class="login-btn">
            <i class="fa-solid fa-right-to-bracket"></i>
            Login
        </button>

    </form>

    <!-- Administrator Login Button -->
    <a href="../admin/login.php" class="admin-btn">
        <i class="fa-solid fa-user-shield"></i>
        Administrator Login
    </a>

    <div class="extra-links">

        <p>
            Don't have an account?
            <a href="register.php">Register Here</a>
        </p>

    </div>

</div>

</body>
</html>