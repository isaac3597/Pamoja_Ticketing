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
| SEARCH
|--------------------------------------------------------------------------
*/

$search = "";

$sql = "
SELECT
    tickets.*,
    users.fullname,
    users.email,
    events.title
FROM tickets
INNER JOIN users
    ON tickets.user_id = users.id
INNER JOIN events
    ON tickets.event_id = events.id
";

if(isset($_GET['search']) && $_GET['search']!=""){

    $search = mysqli_real_escape_string($conn,$_GET['search']);

    $sql .= "
    WHERE
        users.fullname LIKE '%$search%'
        OR users.email LIKE '%$search%'
        OR events.title LIKE '%$search%'
        OR tickets.ticket_type LIKE '%$search%'
    ";
}

$sql .= " ORDER BY tickets.purchase_date DESC";

$tickets = mysqli_query($conn,$sql);

/*
|--------------------------------------------------------------------------
| STATISTICS
|--------------------------------------------------------------------------
*/

$totalTickets = mysqli_fetch_assoc(
    mysqli_query($conn,"
        SELECT COUNT(*) AS total
        FROM tickets
    ")
);

$totalRevenue = mysqli_fetch_assoc(
    mysqli_query($conn,"
        SELECT SUM(total_price) AS total
        FROM tickets
    ")
);

$regularTickets = mysqli_fetch_assoc(
    mysqli_query($conn,"
        SELECT COUNT(*) AS total
        FROM tickets
        WHERE ticket_type='Regular'
    ")
);

$vipTickets = mysqli_fetch_assoc(
    mysqli_query($conn,"
        SELECT COUNT(*) AS total
        FROM tickets
        WHERE ticket_type='VIP'
    ")
);

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>

Manage Tickets

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

.card{
    border:none;
    border-radius:15px;
    box-shadow:0 5px 15px rgba(0,0,0,.08);
}

.stat-card{
    color:white;
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

</style>

</head>

<body>

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

<a href="reports.php">
<i class="fas fa-chart-bar"></i> Reports
</a>

<a href="logout.php">
<i class="fas fa-sign-out-alt"></i> Logout
</a>

</div>

<div class="main">

<div class="topbar d-flex justify-content-between align-items-center">

<div>

<h3 class="mb-0">

Ticket Management

</h3>

<small>

Welcome,

<strong>

<?php echo htmlspecialchars($admin_name); ?>

</strong>

</small>

</div>

<form method="GET" class="d-flex">

<input
type="text"
name="search"
class="form-control me-2"
placeholder="Search tickets..."
value="<?php echo htmlspecialchars($search); ?>">

<button class="btn btn-primary">

<i class="fas fa-search"></i>

</button>

</form>

</div>

<div class="container-fluid mt-4">

<div class="row">

<div class="col-md-3">

<div class="card stat-card bg1">

<div class="card-body">

<h2><?php echo $totalTickets['total']; ?></h2>

<p>Total Tickets Sold</p>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card stat-card bg2">

<div class="card-body">

<h2>KES <?php echo number_format($totalRevenue['total'] ?? 0); ?></h2>

<p>Total Revenue</p>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card stat-card bg3">

<div class="card-body">

<h2><?php echo $regularTickets['total']; ?></h2>

<p>Regular Tickets</p>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card stat-card bg4">

<div class="card-body">

<h2><?php echo $vipTickets['total']; ?></h2>

<p>VIP Tickets</p>

</div>

</div>

</div>

</div>

<!-- TICKETS TABLE STARTS HERE -->
 <div class="card mt-4 shadow">

    <div class="card-header bg-dark text-white">

        <h5 class="mb-0">
            <i class="fas fa-ticket-alt"></i>
            Ticket Sales
        </h5>

    </div>

    <div class="card-body table-responsive">

        <table class="table table-bordered table-hover align-middle">

            <thead class="table-dark">

                <tr>

                    <th>ID</th>
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Event</th>
                    <th>Ticket Type</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Seat No.</th>
                    <th>Purchase Date</th>
                    <th>QR Code</th>

                </tr>

            </thead>

            <tbody>

            <?php

            if(mysqli_num_rows($tickets) > 0){

                while($ticket = mysqli_fetch_assoc($tickets)){

            ?>

                <tr>

                    <td>

                        #<?php echo $ticket['id']; ?>

                    </td>

                    <td>

                        <?php echo htmlspecialchars($ticket['fullname']); ?>

                    </td>

                    <td>

                        <?php echo htmlspecialchars($ticket['email']); ?>

                    </td>

                    <td>

                        <?php echo htmlspecialchars($ticket['title']); ?>

                    </td>

                    <td>

                        <?php

                        $badge="secondary";

                        if($ticket['ticket_type']=="Regular"){

                            $badge="primary";

                        }elseif($ticket['ticket_type']=="VIP"){

                            $badge="success";

                        }elseif($ticket['ticket_type']=="VVIP"){

                            $badge="danger";

                        }

                        ?>

                        <span class="badge bg-<?php echo $badge; ?>">

                            <?php echo htmlspecialchars($ticket['ticket_type']); ?>

                        </span>

                    </td>

                    <td>

                        <?php echo $ticket['quantity']; ?>

                    </td>

                    <td>

                        <strong>

                            KES <?php echo number_format($ticket['total_price'],2); ?>

                        </strong>

                    </td>

                    <td>

                        <?php

                        if(!empty($ticket['seat_number'])){

                            echo htmlspecialchars($ticket['seat_number']);

                        }else{

                            echo "-";

                        }

                        ?>

                    </td>

                    <td>

                        <?php

                        echo date(
                            "d M Y H:i",
                            strtotime($ticket['purchase_date'])
                        );

                        ?>

                    </td>

                    <td>

                        <?php

                        if(!empty($ticket['qr_code'])){

                        ?>

                        <img
                        src="../uploads/<?php echo htmlspecialchars($ticket['qr_code']); ?>"
                        width="70"
                        height="70"
                        class="img-thumbnail">

                        <?php

                        }else{

                            echo "<span class='text-muted'>N/A</span>";

                        }

                        ?>

                    </td>

                </tr>

            <?php

                }

            }else{

            ?>

                <tr>

                    <td colspan="10" class="text-center">

                        <div class="py-5">

                            <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>

                            <h5>No Ticket Records Found</h5>

                            <p class="text-muted">

                                There are currently no ticket sales available.

                            </p>

                        </div>

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