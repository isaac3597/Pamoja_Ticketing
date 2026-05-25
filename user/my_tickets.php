<?php
session_start();
include '../config/db.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT tickets.*, events.title, events.event_date
        FROM tickets
        JOIN events ON tickets.event_id = events.id
        WHERE tickets.user_id='$user_id'
        ORDER BY tickets.purchase_date DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Event Management System</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="container">

    <div class="navbar">
        <a href="../index.php">Home</a>
        <a href="events.php">Events</a>
        <a href="my_tickets.php">My Tickets</a>
        <a href="../auth/logout.php">Logout</a>
    </div>

    <!-- PAGE CONTENT HERE -->

</div>

</body>
</html>