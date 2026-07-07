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
    header("Location: reports.php");
    exit();
}

$event_id = (int)$_GET['id'];

/*
|--------------------------------------------------------------------------
| GET EVENT DETAILS
|--------------------------------------------------------------------------
*/

$eventQuery = mysqli_query($conn,"
SELECT *
FROM events
WHERE id='$event_id'
");

if(mysqli_num_rows($eventQuery)==0){

    header("Location: reports.php");
    exit();

}

$event = mysqli_fetch_assoc($eventQuery);

/*
|--------------------------------------------------------------------------
| EVENT STATUS
|--------------------------------------------------------------------------
*/

$today = date("Y-m-d");

if($event['event_date'] > $today){

    $status="Upcoming";
    $badge="success";

}elseif($event['event_date']==$today){

    $status="Today";
    $badge="warning";

}else{

    $status="Completed";
    $badge="secondary";

}

/*
|--------------------------------------------------------------------------
| TICKET SALES
|--------------------------------------------------------------------------
*/

$totalTickets = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT
COALESCE(SUM(quantity),0) AS total
FROM tickets
WHERE event_id='$event_id'
"));

$totalRevenue = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT
COALESCE(SUM(total_price),0) AS total
FROM tickets
WHERE event_id='$event_id'
"));

$regular = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT
COALESCE(SUM(quantity),0) AS total
FROM tickets
WHERE event_id='$event_id'
AND ticket_type='Regular'
"));

$vip = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT
COALESCE(SUM(quantity),0) AS total
FROM tickets
WHERE event_id='$event_id'
AND ticket_type='VIP'
"));

$vvip = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT
COALESCE(SUM(quantity),0) AS total
FROM tickets
WHERE event_id='$event_id'
AND ticket_type='VVIP'
"));

/*
|--------------------------------------------------------------------------
| REVENUE PER TICKET TYPE
|--------------------------------------------------------------------------
*/

$regularRevenue = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT
COALESCE(SUM(total_price),0) AS total
FROM tickets
WHERE event_id='$event_id'
AND ticket_type='Regular'
"));

$vipRevenue = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT
COALESCE(SUM(total_price),0) AS total
FROM tickets
WHERE event_id='$event_id'
AND ticket_type='VIP'
"));

$vvipRevenue = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT
COALESCE(SUM(total_price),0) AS total
FROM tickets
WHERE event_id='$event_id'
AND ticket_type='VVIP'
"));

/*
|--------------------------------------------------------------------------
| RECENT BUYERS
|--------------------------------------------------------------------------
*/

$buyers = mysqli_query($conn,"
SELECT
tickets.*,
users.fullname,
users.email
FROM tickets
INNER JOIN users
ON tickets.user_id=users.id
WHERE tickets.event_id='$event_id'
ORDER BY purchase_date DESC
LIMIT 20
");

$admin = $_SESSION['fullname'];

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>

Event Report.

</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

body{
background:#f4f6f9;
}

.sidebar{
width:260px;
position:fixed;
left:0;
top:0;
bottom:0;
background:#1f2937;
}

.sidebar h3{
color:#fff;
padding:25px;
text-align:center;
border-bottom:1px solid rgba(255,255,255,.1);
}

.sidebar a{
display:block;
padding:15px 25px;
color:#fff;
text-decoration:none;
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

.summary-card{
color:#fff;
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

<div class="topbar d-flex justify-content-between">

<div>

<h3 class="mb-0">

Event Performance Report

</h3>

<small>

Welcome,

<strong><?php echo htmlspecialchars($admin); ?></strong>

</small>

</div>

<div>

<a href="reports.php" class="btn btn-secondary">

<i class="fas fa-arrow-left"></i>

Back

</a>

<button onclick="window.print()" class="btn btn-dark">

<i class="fas fa-print"></i>

Print

</button>

</div>

</div>

<div class="container-fluid mt-4">

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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
    header("Location: reports.php");
    exit();
}

$event_id = (int)$_GET['id'];

/*
|--------------------------------------------------------------------------
| GET EVENT DETAILS
|--------------------------------------------------------------------------
*/

$eventQuery = mysqli_query($conn,"
SELECT *
FROM events
WHERE id='$event_id'
");

if(mysqli_num_rows($eventQuery)==0){

    header("Location: reports.php");
    exit();

}

$event = mysqli_fetch_assoc($eventQuery);

/*
|--------------------------------------------------------------------------
| EVENT STATUS
|--------------------------------------------------------------------------
*/

$today = date("Y-m-d");

if($event['event_date'] > $today){

    $status="Upcoming";
    $badge="success";

}elseif($event['event_date']==$today){

    $status="Today";
    $badge="warning";

}else{

    $status="Completed";
    $badge="secondary";

}

/*
|--------------------------------------------------------------------------
| TICKET SALES
|--------------------------------------------------------------------------
*/

$totalTickets = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT
COALESCE(SUM(quantity),0) AS total
FROM tickets
WHERE event_id='$event_id'
"));

$totalRevenue = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT
COALESCE(SUM(total_price),0) AS total
FROM tickets
WHERE event_id='$event_id'
"));

$regular = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT
COALESCE(SUM(quantity),0) AS total
FROM tickets
WHERE event_id='$event_id'
AND ticket_type='Regular'
"));

$vip = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT
COALESCE(SUM(quantity),0) AS total
FROM tickets
WHERE event_id='$event_id'
AND ticket_type='VIP'
"));

$vvip = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT
COALESCE(SUM(quantity),0) AS total
FROM tickets
WHERE event_id='$event_id'
AND ticket_type='VVIP'
"));

/*
|--------------------------------------------------------------------------
| REVENUE PER TICKET TYPE
|--------------------------------------------------------------------------
*/

$regularRevenue = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT
COALESCE(SUM(total_price),0) AS total
FROM tickets
WHERE event_id='$event_id'
AND ticket_type='Regular'
"));

$vipRevenue = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT
COALESCE(SUM(total_price),0) AS total
FROM tickets
WHERE event_id='$event_id'
AND ticket_type='VIP'
"));

$vvipRevenue = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT
COALESCE(SUM(total_price),0) AS total
FROM tickets
WHERE event_id='$event_id'
AND ticket_type='VVIP'
"));

/*
|--------------------------------------------------------------------------
| RECENT BUYERS
|--------------------------------------------------------------------------
*/

$buyers = mysqli_query($conn,"
SELECT
tickets.*,
users.fullname,
users.email
FROM tickets
INNER JOIN users
ON tickets.user_id=users.id
WHERE tickets.event_id='$event_id'
ORDER BY purchase_date DESC
LIMIT 20
");

$admin = $_SESSION['fullname'];

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>

Event Report.
</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

body{
background:#f4f6f9;
}

.sidebar{
width:260px;
position:fixed;
left:0;
top:0;
bottom:0;
background:#1f2937;
}

.sidebar h3{
color:#fff;
padding:25px;
text-align:center;
border-bottom:1px solid rgba(255,255,255,.1);
}

.sidebar a{
display:block;
padding:15px 25px;
color:#fff;
text-decoration:none;
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

.summary-card{
color:#fff;
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

<div class="topbar d-flex justify-content-between">

<div>

<h3 class="mb-0">

Event Performance Report

</h3>

<small>

Welcome,

<strong><?php echo htmlspecialchars($admin); ?></strong>

</small>

</div>

<div>

<a href="reports.php" class="btn btn-secondary">

<i class="fas fa-arrow-left"></i>

Back

</a>

<button onclick="window.print()" class="btn btn-dark">

<i class="fas fa-print"></i>

Print

</button>

</div>

</div>

<div class="container-fluid mt-4">

<div class="row">

    <div class="col-lg-8">

        <div class="card mb-4">

            <div class="card-body">

                <div class="row">

                    <div class="col-md-8">

                        <h2 class="mb-3">

                            <?php echo htmlspecialchars($event['title']); ?>

                        </h2>

                        <span class="badge bg-<?php echo $badge; ?> fs-6">

                            <?php echo $status; ?>

                        </span>

                    </div>

                    <div class="col-md-4 text-end">

                        <?php

                        if(!empty($event['image']) && file_exists("../uploads/".$event['image'])){

                        ?>

                        <img src="../uploads/<?php echo htmlspecialchars($event['image']); ?>"
                             class="img-fluid rounded shadow"
                             style="height:120px;width:160px;object-fit:cover;">

                        <?php } ?>

                    </div>

                </div>

                <hr>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <strong>
                            <i class="fas fa-user-tie text-primary"></i>
                            Organizer
                        </strong>

                        <br>

                        <?php echo htmlspecialchars($event['organizer']); ?>

                    </div>

                    <div class="col-md-6 mb-3">

                        <strong>
                            <i class="fas fa-calendar text-success"></i>
                            Event Date
                        </strong>

                        <br>

                        <?php echo date("l, d F Y",strtotime($event['event_date'])); ?>

                    </div>

                    <div class="col-md-6 mb-3">

                        <strong>
                            <i class="fas fa-location-dot text-danger"></i>
                            Venue
                        </strong>

                        <br>

                        <?php echo htmlspecialchars($event['location']); ?>

                    </div>

                    <div class="col-md-6 mb-3">

                        <strong>
                            <i class="fas fa-ticket text-warning"></i>
                            Event ID
                        </strong>

                        <br>

                        #<?php echo $event['id']; ?>

                    </div>

                </div>

                <hr>

                <h5>

                    <i class="fas fa-align-left"></i>

                    Description

                </h5>

                <p style="text-align:justify;line-height:1.8;">

                    <?php echo nl2br(htmlspecialchars($event['description'])); ?>

                </p>

            </div>

        </div>

    </div>

    <div class="col-lg-4">

        <div class="card mb-4">

            <div class="card-header bg-primary text-white">

                <h5 class="mb-0">

                    Event Summary

                </h5>

            </div>

            <div class="card-body">

                <table class="table table-borderless">

                    <tr>

                        <th>Total Tickets Sold</th>

                        <td class="text-end">

                            <?php echo $totalTickets['total']; ?>

                        </td>

                    </tr>

                    <tr>

                        <th>Total Revenue</th>

                        <td class="text-end text-success">

                            <strong>

                                KES <?php echo number_format($totalRevenue['total'],2); ?>

                            </strong>

                        </td>

                    </tr>

                    <tr>

                        <th>Regular Sold</th>

                        <td class="text-end">

                            <?php echo $regular['total']; ?>

                        </td>

                    </tr>

                    <tr>

                        <th>VIP Sold</th>

                        <td class="text-end">

                            <?php echo $vip['total']; ?>

                        </td>

                    </tr>

                    <tr>

                        <th>VVIP Sold</th>

                        <td class="text-end">

                            <?php echo $vvip['total']; ?>

                        </td>

                    </tr>

                </table>

            </div>

        </div>

    </div>

</div>

<div class="row">

    <div class="col-md-3">

        <div class="card summary-card bg1">

            <div class="card-body text-center">

                <h2>

                    <?php echo $regular['total']; ?>

                </h2>

                <p class="mb-0">

                    Regular Tickets

                </p>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card summary-card bg2">

            <div class="card-body text-center">

                <h2>

                    <?php echo $vip['total']; ?>

                </h2>

                <p class="mb-0">

                    VIP Tickets

                </p>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card summary-card bg3">

            <div class="card-body text-center">

                <h2>

                    <?php echo $vvip['total']; ?>

                </h2>

                <p class="mb-0">

                    VVIP Tickets

                </p>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card summary-card bg4">

            <div class="card-body text-center">

                <h2>

                    KES <?php echo number_format($totalRevenue['total']); ?>

                </h2>

                <p class="mb-0">

                    Total Revenue

                </p>

            </div>

        </div>

    </div>

</div>

<hr class="my-5">

<h3 class="mb-4">

    <i class="fas fa-chart-pie"></i>

    Ticket Sales Analysis

</h3>

<div class="row">

    <div class="col-lg-6">

        <div class="card mb-4">

            <div class="card-header bg-primary text-white">

                <h5 class="mb-0">
                    <i class="fas fa-chart-pie"></i>
                    Tickets Sold by Type
                </h5>

            </div>

            <div class="card-body">

                <canvas id="ticketChart" height="220"></canvas>

            </div>

        </div>

    </div>

    <div class="col-lg-6">

        <div class="card mb-4">

            <div class="card-header bg-success text-white">

                <h5 class="mb-0">
                    <i class="fas fa-chart-bar"></i>
                    Revenue by Ticket Type
                </h5>

            </div>

            <div class="card-body">

                <canvas id="revenueChart" height="220"></canvas>

            </div>

        </div>

    </div>

</div>

<div class="card mt-3">

    <div class="card-header bg-dark text-white d-flex justify-content-between">

        <h5 class="mb-0">

            <i class="fas fa-users"></i>

            Recent Ticket Purchases

        </h5>

        <span>

            <?php echo mysqli_num_rows($buyers); ?> Records

        </span>

    </div>

    <div class="card-body table-responsive">

        <table class="table table-bordered table-hover align-middle">

            <thead class="table-dark">

                <tr>

                    <th>#</th>

                    <th>Customer</th>

                    <th>Email</th>

                    <th>Ticket</th>

                    <th>Quantity</th>

                    <th>Seat</th>

                    <th>Amount Paid</th>

                    <th>Purchase Date</th>

                </tr>

            </thead>

            <tbody>

            <?php

            if(mysqli_num_rows($buyers)>0){

                while($buyer=mysqli_fetch_assoc($buyers)){

            ?>

            <tr>

                <td>

                    <?php echo $buyer['id']; ?>

                </td>

                <td>

                    <?php echo htmlspecialchars($buyer['fullname']); ?>

                </td>

                <td>

                    <?php echo htmlspecialchars($buyer['email']); ?>

                </td>

                <td>

                    <?php

                    $badge="secondary";

                    if($buyer['ticket_type']=="Regular"){

                        $badge="primary";

                    }elseif($buyer['ticket_type']=="VIP"){

                        $badge="success";

                    }elseif($buyer['ticket_type']=="VVIP"){

                        $badge="danger";

                    }

                    ?>

                    <span class="badge bg-<?php echo $badge; ?>">

                        <?php echo htmlspecialchars($buyer['ticket_type']); ?>

                    </span>

                </td>

                <td>

                    <?php echo $buyer['quantity']; ?>

                </td>

                <td>

                    <?php

                    echo !empty($buyer['seat_number'])
                    ? htmlspecialchars($buyer['seat_number'])
                    : "-";

                    ?>

                </td>

                <td>

                    <strong>

                        KES <?php echo number_format($buyer['total_price'],2); ?>

                    </strong>

                </td>

                <td>

                    <?php echo date("d M Y H:i",strtotime($buyer['purchase_date'])); ?>

                </td>

            </tr>

            <?php

                }

            }else{

            ?>

            <tr>

                <td colspan="8" class="text-center py-5">

                    <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>

                    <h5>No ticket purchases found.</h5>

                </td>

            </tr>

            <?php } ?>

            </tbody>

        </table>

    </div>

</div>

<div class="mt-4 mb-5 d-flex justify-content-between">

    <a href="reports.php" class="btn btn-secondary">

        <i class="fas fa-arrow-left"></i>

        Back to Reports

    </a>

    <button onclick="window.print()" class="btn btn-dark">

        <i class="fas fa-print"></i>

        Print Report

    </button>

</div>

</div>

</div>

<script>

new Chart(document.getElementById("ticketChart"),{

    type:"pie",

    data:{

        labels:["Regular","VIP","VVIP"],

        datasets:[{

            data:[

                <?php echo $regular['total']; ?>,

                <?php echo $vip['total']; ?>,

                <?php echo $vvip['total']; ?>

            ],

            backgroundColor:[

                "#2563eb",

                "#16a34a",

                "#dc2626"

            ]

        }]

    },

    options:{

        responsive:true,

        plugins:{

            legend:{

                position:"bottom"

            }

        }

    }

});

new Chart(document.getElementById("revenueChart"),{

    type:"bar",

    data:{

        labels:["Regular","VIP","VVIP"],

        datasets:[{

            label:"Revenue (KES)",

            data:[

                <?php echo $regularRevenue['total']; ?>,

                <?php echo $vipRevenue['total']; ?>,

                <?php echo $vvipRevenue['total']; ?>

            ]

        }]

    },

    options:{

        responsive:true,

        scales:{

            y:{

                beginAtZero:true

            }

        }

    }

});

</script>

</body>

</html>