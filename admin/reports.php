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

$admin_name = $_SESSION['fullname'];

/*
|--------------------------------------------------------------------------
| DASHBOARD STATISTICS
|--------------------------------------------------------------------------
*/

// Total Users (excluding admins)
$totalUsers = mysqli_fetch_assoc(
    mysqli_query($conn,
        "SELECT COUNT(*) AS total
         FROM users
         WHERE role='user'")
);

// Total Organizers
$totalOrganizers = mysqli_fetch_assoc(
    mysqli_query($conn,
        "SELECT COUNT(*) AS total
         FROM users
         WHERE role='organizer'")
);

// Total Events
$totalEvents = mysqli_fetch_assoc(
    mysqli_query($conn,
        "SELECT COUNT(*) AS total
         FROM events")
);

// Total Tickets Sold
$totalTickets = mysqli_fetch_assoc(
    mysqli_query($conn,
        "SELECT COUNT(*) AS total
         FROM tickets")
);

// Total Revenue
$totalRevenue = mysqli_fetch_assoc(
    mysqli_query($conn,
        "SELECT SUM(total_price) AS total
         FROM tickets")
);

// Ticket Types
$regularTickets = mysqli_fetch_assoc(
    mysqli_query($conn,
        "SELECT COUNT(*) AS total
         FROM tickets
         WHERE ticket_type='Regular'")
);

$vipTickets = mysqli_fetch_assoc(
    mysqli_query($conn,
        "SELECT COUNT(*) AS total
         FROM tickets
         WHERE ticket_type='VIP'")
);

$vvipTickets = mysqli_fetch_assoc(
    mysqli_query($conn,
        "SELECT COUNT(*) AS total
         FROM tickets
         WHERE ticket_type='VVIP'")
);

/*
|--------------------------------------------------------------------------
| MONTHLY SALES
|--------------------------------------------------------------------------
*/

$months = [];
$sales = [];

$query = mysqli_query($conn,"
SELECT
    DATE_FORMAT(purchase_date,'%b') AS month,
    SUM(total_price) AS revenue
FROM tickets
GROUP BY MONTH(purchase_date)
ORDER BY MONTH(purchase_date)
");

while($row = mysqli_fetch_assoc($query)){

    $months[] = $row['month'];
    $sales[] = $row['revenue'];

}

/*
|--------------------------------------------------------------------------
| RECENT SALES
|--------------------------------------------------------------------------
*/

$recentSales = mysqli_query($conn,"
SELECT
    tickets.*,
    users.fullname,
    events.title
FROM tickets
INNER JOIN users
ON tickets.user_id = users.id
INNER JOIN events
ON tickets.event_id = events.id
ORDER BY purchase_date DESC
LIMIT 10
");
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Pamoja Ticketing | Reports</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
    background:white;
    padding:20px;
    box-shadow:0 0 8px rgba(0,0,0,.08);
}

.card{
    border:none;
    border-radius:15px;
    box-shadow:0 5px 15px rgba(0,0,0,.08);
}

.stat-card{
    color:white;
}

.bg1{background:#2563eb;}
.bg2{background:#16a34a;}
.bg3{background:#ea580c;}
.bg4{background:#7c3aed;}

</style>

</head>

<body>

<div class="sidebar">

<h3>Pamoja Admin</h3>

<a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>

<a href="users.php"><i class="fas fa-users"></i> Users</a>

<a href="organizers.php"><i class="fas fa-user-tie"></i> Organizers</a>

<a href="events.php"><i class="fas fa-calendar"></i> Events</a>

<a href="tickets.php"><i class="fas fa-ticket-alt"></i> Tickets</a>

<a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a>

<a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>

</div>

<div class="main">

<div class="topbar d-flex justify-content-between align-items-center">

<div>

<h3 class="mb-0">Reports Dashboard</h3>

<small>

Welcome,

<strong><?php echo htmlspecialchars($admin_name); ?></strong>

</small>

</div>

<button class="btn btn-dark" onclick="window.print()">

<i class="fas fa-print"></i>

Print Report

</button>

</div>

<div class="container-fluid mt-4">

<div class="row">

<div class="col-md-3">
<div class="card stat-card bg1">
<div class="card-body">
<h2><?php echo $totalUsers['total']; ?></h2>
<p>Total Users</p>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card stat-card bg2">
<div class="card-body">
<h2><?php echo $totalOrganizers['total']; ?></h2>
<p>Organizers</p>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card stat-card bg3">
<div class="card-body">
<h2><?php echo $totalEvents['total']; ?></h2>
<p>Events</p>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card stat-card bg4">
<div class="card-body">
<h2><?php echo $totalTickets['total']; ?></h2>
<p>Tickets Sold</p>
</div>
</div>
</div>

</div>

<div class="row mt-4">

    <div class="col-md-4">

        <div class="card">

            <div class="card-body text-center">

                <h5>Total Revenue</h5>

                <h2 class="text-success">

                    KES <?php echo number_format($totalRevenue['total'] ?? 0,2); ?>

                </h2>

            </div>

        </div>

    </div>

    <div class="col-md-4">

        <div class="card">

            <div class="card-body text-center">

                <h5>Regular Tickets</h5>

                <h2 class="text-primary">

                    <?php echo $regularTickets['total']; ?>

                </h2>

            </div>

        </div>

    </div>

    <div class="col-md-4">

        <div class="card">

            <div class="card-body text-center">

                <h5>VIP + VVIP Tickets</h5>

                <h2 class="text-danger">

                    <?php echo $vipTickets['total'] + $vvipTickets['total']; ?>

                </h2>

            </div>

        </div>

    </div>

</div>

<div class="row mt-4">

<div class="col-lg-8">

<div class="card">

<div class="card-header">

<h5 class="mb-0">

Monthly Revenue

</h5>

</div>

<div class="card-body">

<canvas id="salesChart" height="100"></canvas>

</div>

</div>

</div>

<div class="col-lg-4">

<div class="card">

<div class="card-header">

<h5 class="mb-0">

Ticket Types

</h5>

</div>

<div class="card-body">

<canvas id="ticketChart"></canvas>

</div>

</div>

</div>

</div>

<div class="card mt-4">

<div class="card-header bg-dark text-white">

<h5 class="mb-0">

Recent Ticket Sales

</h5>

</div>

<div class="card-body table-responsive">

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>#</th>

<th>Customer</th>

<th>Event</th>

<th>Type</th>

<th>Quantity</th>

<th>Total</th>

<th>Date</th>

</tr>

</thead>

<tbody>

<?php

if(mysqli_num_rows($recentSales)>0){

while($sale=mysqli_fetch_assoc($recentSales)){

?>

<tr>

<td>

<?php echo $sale['id']; ?>

</td>

<td>

<?php echo htmlspecialchars($sale['fullname']); ?>

</td>

<td>

<?php echo htmlspecialchars($sale['title']); ?>

</td>

<td>

<?php echo htmlspecialchars($sale['ticket_type']); ?>

</td>

<td>

<?php echo $sale['quantity']; ?>

</td>

<td>

KES <?php echo number_format($sale['total_price'],2); ?>

</td>

<td>

<?php echo date("d M Y",strtotime($sale['purchase_date'])); ?>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="7" class="text-center">

No ticket sales available.

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

</div>

<script>

const salesChart = new Chart(document.getElementById('salesChart'),{

type:'bar',

data:{

labels:<?php echo json_encode($months); ?>,

datasets:[{

label:'Revenue (KES)',

data:<?php echo json_encode($sales); ?>,

backgroundColor:'#2563eb'

}]

},

options:{

responsive:true,

plugins:{

legend:{

display:false

}

}

}

});

const ticketChart = new Chart(document.getElementById('ticketChart'),{

type:'pie',

data:{

labels:['Regular','VIP','VVIP'],

datasets:[{

data:[

<?php echo $regularTickets['total']; ?>,

<?php echo $vipTickets['total']; ?>,

<?php echo $vvipTickets['total']; ?>

],

backgroundColor:[

'#2563eb',

'#16a34a',

'#dc2626'

]

}]

},

options:{

responsive:true

}

});

</script>

</body>

</html>