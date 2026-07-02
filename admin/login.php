<?php
session_start();

include("../config/db.php");

$error = "";

if(isset($_POST['login']))
{
    $email = mysqli_real_escape_string($conn,$_POST['email']);
    $password = $_POST['password'];

    $query = mysqli_query($conn,"SELECT * FROM admins WHERE email='$email'");

    if(mysqli_num_rows($query)>0)
    {
        $admin = mysqli_fetch_assoc($query);

        if(password_verify($password,$admin['password']))
        {
            $_SESSION['admin_id']=$admin['admin_id'];
            $_SESSION['admin_name']=$admin['fullname'];

            header("Location: dashboard.php");
            exit();
        }
        else
        {
            $error="Invalid Email or Password.";
        }
    }
    else
    {
        $error="Invalid Email or Password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Admin Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-5">

<div class="card shadow">

<div class="card-header bg-dark text-white text-center">

<h3>Administrator Login</h3>

</div>

<div class="card-body">

<?php
if($error!="")
{
echo "<div class='alert alert-danger'>$error</div>";
}
?>

<form method="POST">

<div class="mb-3">
<label>Email</label>
<input
type="email"
name="email"
class="form-control"
required>
</div>

<div class="mb-3">
<label>Password</label>
<input
type="password"
name="password"
class="form-control"
required>
</div>

<div class="d-grid">

<button
type="submit"
name="login"
class="btn btn-dark">

Login

</button>

</div>

</form>

</div>

</div>

</div>

</div>

</div>

</body>

</html>