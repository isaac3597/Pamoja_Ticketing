<?php
session_start();
include("../config/db.php");

// Check Admin Login
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch Logged in User
$user = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM users WHERE id='$user_id'"));

// Check Role
if($user['role'] != 'admin'){
    header("Location: ../index.php");
    exit();
}

// Dashboard Statistics
$totalUsers = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM users WHERE role='customer'"));

$totalOrganizers = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM users WHERE role='organizer'"));

$totalEvents = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM events"));

$totalTickets = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM tickets"));

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Administrator Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

<style>

body{
    background:#f5f7fb;
}

.sidebar{

    width:260px;
    position:fixed;
    top:0;
    left:0;
    bottom:0;
    background:#1f2937;
    color:#fff;
}

.sidebar h3{

    padding:25px;
    text-align:center;
    border-bottom:1px solid rgba(255,255,255,.1);
}

.sidebar a{

    display:block;
    color:#fff;
    text-decoration:none;
    padding:15px 25px;
    transition:.3s;
}

.sidebar a:hover{

    background:#374151;
}

.main{

    margin-left:260px;
}

.topbar{

    background:#fff;
    padding:20px;
    box-shadow:0 0 8px rgba(0,0,0,.08);
}

.card-box{

    border:none;
    border-radius:15px;
    color:#fff;
}

.bg1{
    background:#2563eb;
}

.bg2{
    background:#16a34a;
}

.bg3{
    background:#ea580c;
}

.bg4{
    background:#7c3aed;
}

.table{

    background:#fff;
}

</style>

</head>

<body>

<div class="sidebar">

<h3>Pamoja Admin</h3>

<a href="dashboard.php">
<i class="fas fa-home"></i>
 Dashboard
</a>

<a href="users.php">
<i class="fas fa-users"></i>
 Users
</a>

<a href="organizers.php">
<i class="fas fa-user-tie"></i>
 Organizers
</a>

<a href="events.php">
<i class="fas fa-calendar"></i>
 Events
</a>

<a href="tickets.php">
<i class="fas fa-ticket"></i>
 Tickets
</a>

<a href="reports.php">
<i class="fas fa-chart-bar"></i>
 Reports
</a>

<a href="../auth/logout.php">
<i class="fas fa-sign-out-alt"></i>
 Logout
</a>

</div>

<div class="main">

<div class="topbar d-flex justify-content-between">

<h4>

Administrator Dashboard

</h4>

<h5>

Welcome,
<?php echo $user['fullname']; ?>

</h5>

</div>

<div class="container-fluid mt-4">

<div class="row">

<div class="col-md-3">

<div class="card card-box bg1">

<div class="card-body">

<h2>

<?php echo $totalUsers; ?>

</h2>

<p>Total Customers</p>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card card-box bg2">

<div class="card-body">

<h2>

<?php echo $totalOrganizers; ?>

</h2>

<p>Total Organizers</p>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card card-box bg3">

<div class="card-body">

<h2>

<?php echo $totalEvents; ?>

</h2>

<p>Total Events</p>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card card-box bg4">

<div class="card-body">

<h2>

<?php echo $totalTickets; ?>

</h2>

<p>Total Tickets</p>

</div>

</div>

</div>

</div>

<div class="row mt-4">

<div class="col-md-6">

<div class="card">

<div class="card-header">

Recent Users

</div>

<div class="card-body">

<table class="table table-bordered">

<tr>

<th>Name</th>

<th>Email</th>

<th>Role</th>

</tr>

<?php

$result=mysqli_query($conn,"SELECT * FROM users ORDER BY id DESC LIMIT 5");

while($row=mysqli_fetch_assoc($result)){

?>

<tr>

<td><?php echo $row['fullname']; ?></td>

<td><?php echo $row['email']; ?></td>

<td><?php echo ucfirst($row['role']); ?></td>

</tr>

<?php } ?>

</table>

</div>

</div>

</div>

<div class="col-md-6">

<div class="card">

<div class="card-header">

Latest Events

</div>

<div class="card-body">

<table class="table table-bordered">

<tr>

<th>Event</th>

<th>Date</th>

<th>Venue</th>

</tr>

<?php

$events=mysqli_query($conn,"SELECT * FROM events ORDER BY id DESC LIMIT 5");

while($event=mysqli_fetch_assoc($events)){

?>

<tr>

<td><?php echo $event['title']; ?></td>

<td><?php echo $event['event_date']; ?></td>

<td><?php echo $event['location']; ?></td>

</tr>

<?php } ?>

</table>

</div>

</div>

</div>

</div>

</div>

</div>

</body>

</html>