<?php
session_start();
include("../config/db.php");

// Check Admin Login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Delete Organizer
if (isset($_GET['delete'])) {

    $id = (int)$_GET['delete'];

    mysqli_query($conn,"DELETE FROM users WHERE id='$id' AND role='organizer'");

    header("Location: organizers.php");
    exit();
}

// Change Role
if(isset($_POST['change_role'])){

    $id=(int)$_POST['user_id'];
    $role=mysqli_real_escape_string($conn,$_POST['role']);

    mysqli_query($conn,"UPDATE users SET role='$role' WHERE id='$id'");

    header("Location: organizers.php");
    exit();
}

// Search
$search="";

$sql="SELECT * FROM users WHERE role='organizer'";

if(isset($_GET['search']) && $_GET['search']!=""){

    $search=mysqli_real_escape_string($conn,$_GET['search']);

    $sql="SELECT * FROM users
          WHERE role='organizer'
          AND (
                fullname LIKE '%$search%'
                OR
                email LIKE '%$search%'
              )";
}

$result=mysqli_query($conn,$sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Manage Organizers</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<style>

body{

background:#f5f7fb;

}

.sidebar{

width:250px;
position:fixed;
top:0;
left:0;
bottom:0;
background:#1f2937;

}

.sidebar h3{

color:#fff;
text-align:center;
padding:20px;

}

.sidebar a{

display:block;
color:white;
padding:15px 25px;
text-decoration:none;

}

.sidebar a:hover{

background:#374151;

}

.main{

margin-left:250px;

}

.topbar{

background:white;
padding:20px;
box-shadow:0 2px 8px rgba(0,0,0,.1);

}

</style>

</head>

<body>

<div class="sidebar">

<h3>Pamoja Admin</h3>

<a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>

<a href="users.php"><i class="fas fa-users"></i> Users</a>

<a href="organizers.php"><i class="fas fa-user-tie"></i> Organizers</a>

<a href="events.php"><i class="fas fa-calendar"></i> Events</a>

<a href="tickets.php"><i class="fas fa-ticket"></i> Tickets</a>

<a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a>

<a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>

</div>

<div class="main">

<div class="topbar d-flex justify-content-between">

<h3>Manage Organizers</h3>

<form method="GET" class="d-flex">

<input
type="text"
name="search"
class="form-control me-2"
placeholder="Search Organizer"
value="<?php echo htmlspecialchars($search); ?>">

<button class="btn btn-primary">

<i class="fas fa-search"></i>

</button>

</form>

</div>

<div class="container-fluid mt-4">

<div class="card shadow">

<div class="card-body">

<table class="table table-hover table-bordered">

<thead class="table-dark">

<tr>

<th>ID</th>

<th>Full Name</th>

<th>Email</th>

<th>Role</th>

<th width="220">Action</th>

</tr>

</thead>

<tbody>

<?php while($row=mysqli_fetch_assoc($result)){ ?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo htmlspecialchars($row['fullname']); ?></td>

<td><?php echo htmlspecialchars($row['email']); ?></td>

<td>

<form method="POST">

<input
type="hidden"
name="user_id"
value="<?php echo $row['id']; ?>">

<select
name="role"
class="form-select">

<option value="organizer" selected>Organizer</option>

<option value="user">User</option>

<option value="admin">Admin</option>

</select>

</td>

<td>

<button
class="btn btn-success btn-sm"
name="change_role">

<i class="fas fa-save"></i>

Save

</button>

</form>

<a
href="?delete=<?php echo $row['id']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this organizer?')">

<i class="fas fa-trash"></i>

Delete

</a>

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