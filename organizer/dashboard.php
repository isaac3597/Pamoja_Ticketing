<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'organizer') {
    header('Location: ../auth/login.php');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Organizer Dashboard</title>
</head>
<body>

<h1>Welcome <?php echo $_SESSION['fullname']; ?></h1>



</body>
</html>


<!DOCTYPE html>
<html>
<head>
    <title>Event Management System</title>
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="navbar">

    <a href="create_event.php">
        Create Event
    </a>

    <a href="generate_report.php">
        Download Ticket Holders Report
    </a>

    <a href="../index.php">
        Home
    </a>

    <a href="../auth/logout.php">
        Logout
    </a>
<a href="manage_events.php">
    Manage Events
</a>
<button id="darkModeBtn">
    🌙 Dark Mode
</button>
</div>
<script>

const darkBtn =
    document.getElementById('darkModeBtn');

// Load saved mode
if(localStorage.getItem('darkMode') === 'enabled') {

    document.body.classList.add('dark-mode');
}

darkBtn.addEventListener('click', () => {

    document.body.classList.toggle('dark-mode');

    // Save mode
    if(document.body.classList.contains('dark-mode')) {

        localStorage.setItem(
            'darkMode',
            'enabled'
        );

    } else {

        localStorage.setItem(
            'darkMode',
            'disabled'
        );
    }
});

</script>
</body>
</html>