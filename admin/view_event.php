<?php
session_start();
include("../config/db.php");

/*
|--------------------------------------------------------------------------
| CHECK ADMIN LOGIN
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| CHECK EVENT ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id'])) {
    header("Location: events.php");
    exit();
}

$event_id = (int)$_GET['id'];

/*
|--------------------------------------------------------------------------
| FETCH EVENT
|--------------------------------------------------------------------------
*/

$query = mysqli_query($conn,"
    SELECT *
    FROM events
    WHERE id='$event_id'
");

if(mysqli_num_rows($query)==0){

    header("Location: events.php");
    exit();

}

$event = mysqli_fetch_assoc($query);

$admin_name = $_SESSION['fullname'];

/*
|--------------------------------------------------------------------------
| EVENT STATUS
|--------------------------------------------------------------------------
*/

$today = date("Y-m-d");

if($event['event_date'] > $today){

    $status = "Upcoming";
    $badge = "success";

}elseif($event['event_date'] == $today){

    $status = "Today";
    $badge = "warning";

}else{

    $status = "Completed";
    $badge = "secondary";

}
?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>

View Event

</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
rel="stylesheet">

<style>

body{

    background:#f5f7fb;

}

.sidebar{

    width:260px;

    position:fixed;

    top:0;

    bottom:0;

    left:0;

    background:#1f2937;

}

.sidebar h3{

    color:#fff;

    text-align:center;

    padding:25px;

    border-bottom:1px solid rgba(255,255,255,.1);

}

.sidebar a{

    display:block;

    color:white;

    padding:15px 25px;

    text-decoration:none;

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

.card{

    border:none;

    border-radius:15px;

    box-shadow:0 5px 15px rgba(0,0,0,.08);

}

.event-image{

    width:100%;

    height:350px;

    object-fit:cover;

    border-radius:12px;

}

.info-title{

    font-weight:bold;

    color:#0f172a;

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

<a href="logout.php">
<i class="fas fa-sign-out-alt"></i>
Logout
</a>

</div>

<div class="main">

<div class="topbar d-flex justify-content-between">

<div>

<h3 class="mb-0">

View Event

</h3>

<small>

Welcome,

<strong>

<?php echo htmlspecialchars($admin_name); ?>

</strong>

</small>

</div>

<a href="events.php"
class="btn btn-dark">

<i class="fas fa-arrow-left"></i>

Back to Events

</a>

</div>

<div class="container-fluid mt-4">

<div class="row">

<div class="col-lg-5">

<div class="card">

<div class="card-body">

<?php

if(!empty($event['image']) && file_exists("../uploads/".$event['image'])){

?>

<img
src="../uploads/<?php echo htmlspecialchars($event['image']); ?>"
class="event-image">

<?php

}else{

?>

<img
src="https://via.placeholder.com/600x350?text=No+Image"
class="event-image">

<?php } ?>

</div>

</div>

</div>

<div class="col-lg-7">

<div class="card">

<div class="card-body">

<h2>

<?php echo htmlspecialchars($event['title']); ?>

</h2>

<span class="badge bg-<?php echo $badge; ?>">

<?php echo $status; ?>

</span>

<hr>
<div class="row">

    <div class="col-md-6 mb-3">

        <p class="info-title">
            <i class="fas fa-user"></i>
            Organizer
        </p>

        <p>
            <?php echo htmlspecialchars($event['organizer']); ?>
        </p>

    </div>

    <div class="col-md-6 mb-3">

        <p class="info-title">
            <i class="fas fa-location-dot"></i>
            Venue
        </p>

        <p>
            <?php echo htmlspecialchars($event['location']); ?>
        </p>

    </div>

    <div class="col-md-6 mb-3">

        <p class="info-title">
            <i class="fas fa-calendar"></i>
            Event Date
        </p>

        <p>
            <?php echo date("l, d F Y", strtotime($event['event_date'])); ?>
        </p>

    </div>

    <div class="col-md-6 mb-3">

        <p class="info-title">
            <i class="fas fa-hashtag"></i>
            Event ID
        </p>

        <p>
            #<?php echo $event['id']; ?>
        </p>

    </div>

</div>

<hr>

<h5 class="mb-3">

    <i class="fas fa-align-left"></i>

    Event Description

</h5>

<p style="text-align:justify;line-height:1.8;">

<?php echo nl2br(htmlspecialchars($event['description'])); ?>

</p>

</div>

</div>

</div>

</div>

<!-- Ticket Information -->

<div class="card mt-4">

<div class="card-header bg-dark text-white">

<h5 class="mb-0">

<i class="fas fa-ticket-alt"></i>

Ticket Information

</h5>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-4">

<div class="card border-primary">

<div class="card-body text-center">

<h6>Regular Ticket</h6>

<h3 class="text-primary">

KES <?php echo number_format($event['regular_price']); ?>

</h3>

<hr>

Available

<br>

<strong>

<?php echo $event['regular_tickets']; ?>

</strong>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card border-success">

<div class="card-body text-center">

<h6>VIP Ticket</h6>

<h3 class="text-success">

KES <?php echo number_format($event['vip_price']); ?>

</h3>

<hr>

Available

<br>

<strong>

<?php echo $event['vip_tickets']; ?>

</strong>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card border-danger">

<div class="card-body text-center">

<h6>VVIP Ticket</h6>

<h3 class="text-danger">

KES <?php echo number_format($event['vvip_price']); ?>

</h3>

<hr>

Available

<br>

<strong>

<?php echo $event['vvip_tickets']; ?>

</strong>

</div>

</div>

</div>

</div>

</div>

</div>

<div class="text-end mt-4 mb-4">

<a href="events.php" class="btn btn-secondary">

<i class="fas fa-arrow-left"></i>

Back to Events

</a>

</div>

</div>

</div>

</body>

</html>