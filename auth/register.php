<?php
session_start();
include '../config/db.php';

$success = "";
$error = "";

if(isset($_POST['register'])) {

    $fullname = $_POST['fullname'];
    $email = $_POST['email'];

    $password = password_hash(
        $_POST['password'],
        PASSWORD_DEFAULT
    );

    $role = $_POST['role'];

    // CHECK IF EMAIL EXISTS
    $check = "SELECT * FROM users WHERE email='$email'";

    $result = mysqli_query($conn, $check);

    if(mysqli_num_rows($result) > 0) {

        $error = "Email already exists";

    } else {

        $sql = "INSERT INTO users(
                    fullname,
                    email,
                    password,
                    role
                )
                VALUES(
                    '$fullname',
                    '$email',
                    '$password',
                    '$role'
                )";

        if(mysqli_query($conn, $sql)) {

            $success = "Registration successful";

        } else {

            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register</title>

    <!-- GOOGLE FONT -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Poppins', sans-serif;
        }

        body{
            min-height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            background:linear-gradient(135deg,#4f46e5,#7c3aed);
            padding:20px;
        }

        .register-container{
            width:420px;
            background:white;
            padding:40px;
            border-radius:20px;
            box-shadow:0 10px 30px rgba(0,0,0,0.2);
        }

        .logo{
            text-align:center;
            margin-bottom:20px;
        }

        .logo i{
            font-size:55px;
            color:#4f46e5;
        }

        .register-container h2{
            text-align:center;
            margin-bottom:25px;
            color:#333;
        }

        .success{
            background:#d1fae5;
            color:#065f46;
            padding:12px;
            border-radius:8px;
            margin-bottom:15px;
            text-align:center;
            font-size:14px;
        }

        .error{
            background:#fee2e2;
            color:#b91c1c;
            padding:12px;
            border-radius:8px;
            margin-bottom:15px;
            text-align:center;
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

        .input-group input,
        .input-group select{
            width:100%;
            padding:14px 14px 14px 45px;
            border:1px solid #ccc;
            border-radius:10px;
            outline:none;
            font-size:15px;
            transition:0.3s;
            background:white;
        }

        .input-group input:focus,
        .input-group select:focus{
            border-color:#4f46e5;
            box-shadow:0 0 5px rgba(79,70,229,0.3);
        }

        .register-btn{
            width:100%;
            padding:14px;
            border:none;
            background:#4f46e5;
            color:white;
            font-size:16px;
            border-radius:10px;
            cursor:pointer;
            transition:0.3s;
            font-weight:600;
        }

        .register-btn:hover{
            background:#4338ca;
        }

        .extra-links{
            text-align:center;
            margin-top:20px;
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

        @media(max-width:480px){

            .register-container{
                width:100%;
                padding:30px 20px;
            }
        }

    </style>

</head>

<body>

    <div class="register-container">

        <div class="logo">
            <i class="fa-solid fa-user-plus"></i>
        </div>

        <h2>Create Account</h2>

        <?php if($success != "") { ?>

            <div class="success">
                <?php echo $success; ?>
            </div>

        <?php } ?>

        <?php if($error != "") { ?>

            <div class="error">
                <?php echo $error; ?>
            </div>

        <?php } ?>

        <form method="POST">

            <div class="input-group">

                <i class="fa-solid fa-user"></i>

                <input
                    type="text"
                    name="fullname"
                    placeholder="Full Name"
                    required
                >

            </div>

            <div class="input-group">

                <i class="fa-solid fa-envelope"></i>

                <input
                    type="email"
                    name="email"
                    placeholder="Email Address"
                    required
                >

            </div>

            <div class="input-group">

                <i class="fa-solid fa-lock"></i>

                <input
                    type="password"
                    name="password"
                    placeholder="Password"
                    required
                >

            </div>

            <div class="input-group">

                <i class="fa-solid fa-users"></i>

                <select name="role" required>

                    <option value="user">
                        User
                    </option>

                    <option value="organizer">
                        Organizer
                    </option>

                </select>

            </div>

            <button type="submit" name="register" class="register-btn">
                Register
            </button>

        </form>

        <div class="extra-links">

            <p>
                Already have an account?
                <a href="login.php">
                    Login Here
                </a>
            </p>

        </div>

    </div>

</body>
</html>