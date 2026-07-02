<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user_id'])){
    header("Location:../auth/login.php");
    exit();
}

$user_id=$_SESSION['user_id'];

$admin=mysqli_fetch_assoc(mysqli_query($conn,
"SELECT * FROM users WHERE id='$user_id'"));

if($admin['role']!='admin'){
    header("Location:../index.php");
    exit();
}

// Delete User
if(isset($_GET['delete'])){

    $id=(int)$_GET['delete'];

    mysqli_query($conn,"DELETE FROM users WHERE id='$id' AND role!='admin'");

    header("Location:users.php");
    exit();
}

$search="";

if(isset($_GET['search'])){

    $search=mysqli_real_escape_string($conn,$_GET['search']);

    $sql="SELECT * FROM users
          WHERE fullname LIKE '%$search%'
          OR email LIKE '%$search%'
          ORDER BY id DESC";

}else{

    $sql="SELECT * FROM users ORDER BY id DESC";

}

$result=mysqli_query($conn,$sql);

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>User Management</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

<style>

body{

background:#f5f7fb;

}

.container{

margin-top:40px;

}

.table{

background:white;

}

</style>

</head>

<body>

<div class="container">

<div class="d-flex justify-content-between mb-4">

<h2>

<i class="fa fa-users"></i>

Manage Users

</h2>

<a href="dashboard.php" class="btn btn-primary">

Dashboard

</a>

</div>

<form method="GET">

<div class="row mb-3">

<div class="col-md-10">

<input
type="text"
name="search"
class="form-control"
placeholder="Search user..."
value="<?php echo $search;?>">

</div>

<div class="col-md-2">

<button class="btn btn-success w-100">

Search

</button>

</div>

</div>

</form>

<table class="table table-bordered table-hover">

<tr class="table-dark">

<th>ID</th>

<th>Full Name</th>

<th>Email</th>

<th>Role</th>

<th>Created</th>

<th width="170">

Action

</th>

</tr>

<?php

while($row=mysqli_fetch_assoc($result)){

?>

<tr>

<td>

<?php echo $row['id'];?>

</td>

<td>

<?php echo $row['fullname'];?>

</td>

<td>

<?php echo $row['email'];?>

</td>

<td>

<?php echo ucfirst($row['role']);?>

</td>

<td>

<?php echo $row['created_at'];?>

</td>

<td>

<a
href="edit_user.php?id=<?php echo $row['id'];?>"
class="btn btn-warning btn-sm">

<i class="fa fa-edit"></i>

</a>

<a
href="users.php?delete=<?php echo $row['id'];?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this user?')">

<i class="fa fa-trash"></i>

</a>

</td>

</tr>

<?php

}

?>

</table>

</div>

</body>

</html>