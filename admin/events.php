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
| DELETE EVENT
|--------------------------------------------------------------------------
*/

if (isset($_GET['delete'])) {

    $event_id = (int) $_GET['delete'];

    // Delete image first
    $img = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT image FROM events WHERE id='$event_id'")
    );

    if (!empty($img['image']) && file_exists("../uploads/" . $img['image'])) {
        unlink("../uploads/" . $img['image']);
    }

    mysqli_query($conn, "DELETE FROM events WHERE id='$event_id'");

    header("Location: events.php?success=deleted");
    exit();
}

/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/

$search = "";

$sql = "SELECT * FROM events";

if (isset($_GET['search']) && $_GET['search'] != "") {

    $search = mysqli_real_escape_string($conn, $_GET['search']);

    $sql .= " WHERE
            title LIKE '%$search%'
            OR organizer LIKE '%$search%'
            OR location LIKE '%$search%'";
}

$sql .= " ORDER BY event_date DESC";

$events = mysqli_query($conn, $sql);

/*
|--------------------------------------------------------------------------
| DASHBOARD STATISTICS
|--------------------------------------------------------------------------
*/

$totalEvents = mysqli_num_rows(
    mysqli_query($conn, "SELECT * FROM events")
);

$totalRegular = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT SUM(regular_tickets) AS total FROM events"
    )
);

$totalVIP = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT SUM(vip_tickets) AS total FROM events"
    )
);

$totalVVIP = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT SUM(vvip_tickets) AS total FROM events"
    )
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Manage Events</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
rel="stylesheet">

<style>

body{

    background:#f4f7fb;

}

.sidebar{

    position:fixed;

    width:260px;

    top:0;

    left:0;

    bottom:0;

    background:#1f2937;

}

.sidebar h3{

    color:white;

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

    box-shadow:0 0 10px rgba(0,0,0,.08);

}

.card{

    border:none;

    border-radius:15px;

    box-shadow:0 5px 15px rgba(0,0,0,.08);

}

.stat-card{

    color:white;

    border-radius:15px;

}

.bg-blue{

    background:#2563eb;

}

.bg-green{

    background:#16a34a;

}

.bg-orange{

    background:#ea580c;

}

.bg-purple{

    background:#7c3aed;

}

table img{

    width:80px;

    border-radius:8px;

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

<div class="topbar d-flex justify-content-between align-items-center">

<div>

<h3 class="mb-0">

Manage Events

</h3>

<small>

Welcome,
<strong><?php echo htmlspecialchars($admin_name); ?></strong>

</small>

</div>

<form method="GET" class="d-flex">

<input

type="text"

name="search"

class="form-control me-2"

placeholder="Search events..."

value="<?php echo htmlspecialchars($search); ?>">

<button class="btn btn-primary">

<i class="fas fa-search"></i>

</button>

</form>

</div>

<div class="container-fluid mt-4">

<?php if(isset($_GET['success'])){ ?>

<div class="alert alert-success">

Event deleted successfully.

</div>

<?php } ?>

<div class="row">

<div class="col-md-3">

<div class="card stat-card bg-blue">

<div class="card-body">

<h2><?php echo $totalEvents; ?></h2>

<p>Total Events</p>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card stat-card bg-green">

<div class="card-body">

<h2><?php echo $totalRegular['total'] ?? 0; ?></h2>

<p>Regular Tickets</p>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card stat-card bg-orange">

<div class="card-body">

<h2><?php echo $totalVIP['total'] ?? 0; ?></h2>

<p>VIP Tickets</p>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card stat-card bg-purple">

<div class="card-body">

<h2><?php echo $totalVVIP['total'] ?? 0; ?></h2>

<p>VVIP Tickets</p>

</div>

</div>

</div>

</div>



<!-- EVENTS TABLE STARTS HERE -->
 <div class="card mt-3 shadow">

    <div class="card-header bg-dark text-white">

        <h5 class="mb-0">
            <i class="fas fa-calendar-alt"></i>
            All Events
        </h5>

    </div>

    <div class="card-body table-responsive">

        <table class="table table-bordered table-hover align-middle">

            <thead class="table-dark">

                <tr>

                    <th width="60">#</th>

                    <th width="90">Image</th>

                    <th>Title</th>

                    <th>Organizer</th>

                    <th>Location</th>

                    <th>Date</th>

                    <th>Regular</th>

                    <th>VIP</th>

                    <th>VVIP</th>

                    <th width="180">Actions</th>

                </tr>

            </thead>

            <tbody>

            <?php

            if(mysqli_num_rows($events) > 0){

                while($event = mysqli_fetch_assoc($events)){

            ?>

                <tr>

                    <td>
                        <?php echo $event['id']; ?>
                    </td>

                    <td>

                        <?php

                        if(!empty($event['image']) && file_exists("../uploads/".$event['image'])){

                        ?>

                        <img src="../uploads/<?php echo htmlspecialchars($event['image']); ?>"
                             class="img-fluid rounded"
                             style="width:70px;height:70px;object-fit:cover;">

                        <?php

                        }else{

                            echo "<span class='text-muted'>No Image</span>";

                        }

                        ?>

                    </td>

                    <td>

                        <strong>

                            <?php echo htmlspecialchars($event['title']); ?>

                        </strong>

                        <br>

                        <small class="text-muted">

                            <?php

                            echo substr(
                                htmlspecialchars($event['description']),
                                0,
                                60
                            );

                            ?>...

                        </small>

                    </td>

                    <td>

                        <?php echo htmlspecialchars($event['organizer']); ?>

                    </td>

                    <td>

                        <?php echo htmlspecialchars($event['location']); ?>

                    </td>

                    <td>

                        <?php echo date("d M Y",strtotime($event['event_date'])); ?>

                    </td>

                    <td>

                        <strong>KES <?php echo number_format($event['regular_price']); ?></strong>

                        <br>

                        <small>

                            Tickets:
                            <?php echo $event['regular_tickets']; ?>

                        </small>

                    </td>

                    <td>

                        <strong>KES <?php echo number_format($event['vip_price']); ?></strong>

                        <br>

                        <small>

                            Tickets:
                            <?php echo $event['vip_tickets']; ?>

                        </small>

                    </td>

                    <td>

                        <strong>KES <?php echo number_format($event['vvip_price']); ?></strong>

                        <br>

                        <small>

                            Tickets:
                            <?php echo $event['vvip_tickets']; ?>

                        </small>

                    </td>

                    <td>

                        <a href="view_event.php?id=<?php echo $event['id']; ?>"
                           class="btn btn-info btn-sm">

                            <i class="fas fa-eye"></i>

                        </a>

                        <a href="edit_event.php?id=<?php echo $event['id']; ?>"
                           class="btn btn-warning btn-sm">

                            <i class="fas fa-edit"></i>

                        </a>

                        <a href="events.php?delete=<?php echo $event['id']; ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Delete this event permanently?');">

                            <i class="fas fa-trash"></i>

                        </a>

                    </td>

                </tr>

            <?php

                }

            }else{

            ?>

            <tr>

                <td colspan="10" class="text-center">

                    No events found.

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