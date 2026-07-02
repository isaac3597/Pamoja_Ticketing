<?php
session_start();

include("../../config/db.php");

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Get logged-in user
$user_id = $_SESSION['user_id'];

$result = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");

if (!$result || mysqli_num_rows($result) == 0) {

    session_destroy();

    header("Location: ../login.php");

    exit();
}

$admin = mysqli_fetch_assoc($result);

// Check role
if ($admin['role'] != 'admin') {

    header("Location: ../../index.php");

    exit();
}
?>