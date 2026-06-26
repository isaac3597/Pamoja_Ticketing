<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user_id'])){
    header("Location:../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$admin = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT * FROM users WHERE id='$user_id'"));

if($admin['role'] != 'admin'){
    header("Location:../index.php");
    exit();
}

if(!isset($_GET['id'])){
    header("Location:users.php");
    exit();
}

$id = (int)$_GET['id'];

$user = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT * FROM users WHERE id='$id'"));

if(!$user){
    die("User not found.");
}

$message = "";

if(isset($_POST['update'])){

    $fullname = mysqli_real_escape_string($conn,$_POST['fullname']);
    $email = mysqli_real_escape_string($conn,$_POST['email']);
    $role = mysqli_real_escape_string($conn,$_POST['role']);

    mysqli_query($conn,"
        UPDATE users SET
        fullname='$fullname',
        email='$email',
        role='$role'
        WHERE id='$id'
    ");

    $message = "User updated successfully.";

    $user = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT * FROM users WHERE id='$id'"));
}
?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">

<title>Edit User</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

<style>

body{

    background:#f4f6f9;

}

.container{

    max-width:700px;

    margin-top:50px;

}

.card{

    border-radius:15px;

}

</style>

</head>

<body>

<div class="container">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h4>

<i class="fa fa-user-edit"></i>

Edit User

</h4>

</div>

<div class="card-body">

<?php

if($message!=""){

?>

<div class="alert alert-success">

<?php echo $message; ?>

</div>

<?php

}

?>

<form method="POST">

<div class="mb-3">

<label>

Full Name

</label>

<input
type="text"
name="fullname"
class="form-control"
value="<?php echo htmlspecialchars($user['fullname']); ?>"
required>

</div>

<div class="mb-3">

<label>

Email

</label>

<input
type="email"
name="email"
class="form-control"
value="<?php echo htmlspecialchars($user['email']); ?>"
required>

</div>

<div class="mb-3">

<label>

Role

</label>

<select
name="role"
class="form-select">

<option value="user"
<?php if($user['role']=="user") echo "selected"; ?>>

Customer

</option>

<option value="organizer"
<?php if($user['role']=="organizer") echo "selected"; ?>>

Organizer

</option>

<option value="admin"
<?php if($user['role']=="admin") echo "selected"; ?>>

Administrator

</option>

</select>

</div>

<button
name="update"
class="btn btn-success">

<i class="fa fa-save"></i>

Update User

</button>

<a
href="users.php"
class="btn btn-secondary">

Back

</a>

</form>

</div>

</div>

</div>

</body>

</html>