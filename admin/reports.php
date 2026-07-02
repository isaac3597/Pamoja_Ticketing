<?php
session_start();
include("../config/db.php");

// Check admin login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$admin_name = $_SESSION['fullname'];

// Get all events with ticket statistics
$events = mysqli_query($conn, "
SELECT
    e.id,
    e.title,
    e.organizer,
    e.location,
    e.event_date,

    COUNT(t.id) AS tickets_sold,

    IFNULL(SUM(t.total_price),0) AS revenue

FROM events e

LEFT JOIN tickets t
ON e.id = t.event_id

GROUP BY
    e.id,
    e.title,
    e.organizer,
    e.location,
    e.event_date

ORDER BY e.event_date DESC
");
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Pamoja Ticketing | Reports</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<style>

body{
    background:#f5f7fb;
    font-family:Segoe UI,Tahoma,sans-serif;
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

.sidebar a:hover,
.sidebar a.active{
    background:#374151;
}

.main{
    margin-left:260px;
}

.topbar{
    background:#fff;
    padding:20px;
    box-shadow:0 0 10px rgba(0,0,0,.08);
}

.card{
    border:none;
    border-radius:15px;
    box-shadow:0 5px 15px rgba(0,0,0,.08);
}

.table th{
    white-space:nowrap;
}

</style>

</head>

<body>

<!-- Sidebar -->

<div class="sidebar">

<h3>Pamoja Admin</h3>

<a href="dashboard.php">
<i class="fas fa-home"></i> Dashboard
</a>

<a href="users.php">
<i class="fas fa-users"></i> Users
</a>

<a href="organizers.php">
<i class="fas fa-user-tie"></i> Organizers
</a>

<a href="events.php">
<i class="fas fa-calendar"></i> Events
</a>

<a href="tickets.php">
<i class="fas fa-ticket-alt"></i> Tickets
</a>

<a href="reports.php" class="active">
<i class="fas fa-chart-bar"></i> Reports
</a>

<a href="logout.php">
<i class="fas fa-sign-out-alt"></i> Logout
</a>

</div>

<!-- Main -->

<div class="main">

<div class="topbar d-flex justify-content-between align-items-center">

<div>

<h3 class="mb-0">
Event Performance Reports
</h3>

<small>

Welcome,

<strong><?php echo htmlspecialchars($admin_name); ?></strong>

</small>

</div>

<div>

<button onclick="window.print()" class="btn btn-dark">

<i class="fas fa-print"></i>

Print Reports

</button>

</div>

</div>

<div class="container-fluid mt-4">

<div class="card">

<div class="card-header bg-dark text-white">

<h4 class="mb-0">

<i class="fas fa-chart-line"></i>

Event Reports

</h4>

</div>

<div class="card-body table-responsive">

<table class="table table-bordered table-hover align-middle">

<thead class="table-dark">

<tr>

<th>ID</th>

<th>Event</th>

<th>Organizer</th>

<th>Venue</th>

<th>Date</th>

<th>Tickets Sold</th>

<th>Revenue</th>

<th>Status</th>

<th>Action</th>

</tr>

</thead>

<tbody>

<?php
if(mysqli_num_rows($events) > 0){

    while($event = mysqli_fetch_assoc($events)){

        // Determine event status
        if(strtotime($event['event_date']) > strtotime(date("Y-m-d"))){

            $status = "Upcoming";
            $badge = "success";

        }elseif(date("Y-m-d") == date("Y-m-d", strtotime($event['event_date']))){

            $status = "Today";
            $badge = "warning";

        }else{

            $status = "Completed";
            $badge = "secondary";

        }
?>

<tr>

    <td>
        <?php echo $event['id']; ?>
    </td>

    <td>

        <strong>

            <?php echo htmlspecialchars($event['title']); ?>

        </strong>

    </td>

    <td>

        <?php echo htmlspecialchars($event['organizer']); ?>

    </td>

    <td>

        <?php echo htmlspecialchars($event['location']); ?>

    </td>

    <td>

        <?php echo date("d M Y", strtotime($event['event_date'])); ?>

    </td>

    <td class="text-center">

        <span class="badge bg-primary fs-6">

            <?php echo $event['tickets_sold']; ?>

        </span>

    </td>

    <td>

        <strong class="text-success">

            KES <?php echo number_format($event['revenue'],2); ?>

        </strong>

    </td>

    <td>

        <span class="badge bg-<?php echo $badge; ?>">

            <?php echo $status; ?>

        </span>

    </td>

    <td>

        <a href="event_report.php?id=<?php echo $event['id']; ?>"

           class="btn btn-primary btn-sm">

            <i class="fas fa-chart-line"></i>

            View Report

        </a>

    </td>

</tr>

<?php

    }

}else{

?>

<tr>

<td colspan="9" class="text-center py-5">

<i class="fas fa-folder-open fa-3x text-muted mb-3"></i>

<h5>No Events Found</h5>

<p class="text-muted">

No event reports are available.

</p>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

</div>

</body>

</html>