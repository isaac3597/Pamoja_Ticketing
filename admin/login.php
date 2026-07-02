<?php
session_start();
include("../config/db.php");

// If already logged in as admin
if (isset($_SESSION['user_id'])) {

    $id = $_SESSION['user_id'];

    $check = mysqli_query($conn, "SELECT * FROM users WHERE id='$id'");

    if ($check && mysqli_num_rows($check) > 0) {

        $user = mysqli_fetch_assoc($check);

        if ($user['role'] == 'admin') {
            header("Location: dashboard.php");
            exit();
        }
    }
}

$error = "";

if (isset($_POST['login'])) {

    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = trim($_POST['password']);

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' LIMIT 1");

    if ($query && mysqli_num_rows($query) == 1) {

        $user = mysqli_fetch_assoc($query);

        // Check if user is an administrator
        if ($user['role'] != 'admin') {

            $error = "Access denied. Administrator account required.";

        } else {

            if (password_verify($password, $user['password'])) {

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['role'] = $user['role'];

                header("Location: dashboard.php");
                exit();

            } else {

                $error = "Invalid email or password.";
            }
        }

    } else {

        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Pamoja Ticketing | Administrator Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<style>

body{
    background:linear-gradient(135deg,#0f172a,#2563eb);
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    font-family:Segoe UI, sans-serif;
}

.login-card{
    width:420px;
    border:none;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 20px 50px rgba(0,0,0,.25);
}

.login-header{
    background:#1e293b;
    color:#fff;
    padding:30px;
    text-align:center;
}

.login-header i{
    font-size:55px;
    margin-bottom:10px;
}

.login-body{
    background:#fff;
    padding:35px;
}

.form-control{
    height:50px;
}

.btn-login{
    height:50px;
    font-weight:bold;
}

.footer{
    text-align:center;
    margin-top:20px;
    color:#666;
    font-size:14px;
}

</style>

</head>

<body>

<div class="card login-card">

<div class="login-header">

<i class="fas fa-user-shield"></i>

<h3>Pamoja Ticketing</h3>

<p class="mb-0">Administrator Login</p>

</div>

<div class="login-body">

<?php if($error!=""){ ?>

<div class="alert alert-danger">
<i class="fas fa-circle-exclamation"></i>
<?php echo $error; ?>
</div>

<?php } ?>

<form method="POST">

<div class="mb-3">

<label class="form-label">Email Address</label>

<input
type="email"
name="email"
class="form-control"
placeholder="Enter email"
required>

</div>

<div class="mb-3">

<label class="form-label">Password</label>

<div class="input-group">

<input
type="password"
name="password"
id="password"
class="form-control"
placeholder="Enter password"
required>

<button
class="btn btn-outline-secondary"
type="button"
onclick="togglePassword()">

<i class="fas fa-eye"></i>

</button>

</div>

</div>

<div class="d-grid">

<button
type="submit"
name="login"
class="btn btn-primary btn-login">

<i class="fas fa-sign-in-alt"></i>
Login

</button>

</div>

</form>

<!-- HOME BUTTON ADDED HERE -->
<div class="d-grid mt-3">

<a href="../index.php" class="btn btn-outline-secondary">
    <i class="fas fa-home"></i> Back to Home
</a>

</div>

<div class="footer">
© <?php echo date("Y"); ?> Pamoja Ticketing System
</div>

</div>

</div>

<script>

function togglePassword(){

    var x=document.getElementById("password");

    if(x.type==="password"){
        x.type="text";
    }else{
        x.type="password";
    }

}

</script>

</body>
</html>