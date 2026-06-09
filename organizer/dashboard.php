<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'organizer') {
    header('Location: ../auth/login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Organizer Dashboard</title>

    <!-- GOOGLE FONT -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Poppins', sans-serif;
        }

        body{
            background:#f4f7ff;
            min-height:100vh;
            transition:0.3s;
        }

        /* NAVBAR */

        .navbar{
            width:100%;
            background:#4f46e5;
            padding:15px 40px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            flex-wrap:wrap;
            box-shadow:0 4px 10px rgba(0,0,0,0.1);
        }

        .logo{
            color:white;
            font-size:22px;
            font-weight:700;
        }

        .nav-links{
            display:flex;
            gap:15px;
            align-items:center;
            flex-wrap:wrap;
        }

        .nav-links a,
        .nav-links button{
            text-decoration:none;
            background:white;
            color:#4f46e5;
            padding:10px 16px;
            border:none;
            border-radius:8px;
            cursor:pointer;
            font-size:14px;
            font-weight:600;
            transition:0.3s;
        }

        .nav-links a:hover,
        .nav-links button:hover{
            background:#e0e7ff;
        }

        /* DASHBOARD */

        .dashboard{
            padding:40px;
        }

        .welcome-card{
            background:white;
            padding:30px;
            border-radius:20px;
            box-shadow:0 5px 15px rgba(0,0,0,0.08);
            margin-bottom:30px;
        }

        .welcome-card h1{
            color:#333;
            margin-bottom:10px;
        }

        .welcome-card p{
            color:#777;
            font-size:15px;
        }

        /* CARDS */

        .card-container{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
            gap:25px;
        }

        .card{
            background:white;
            padding:30px;
            border-radius:18px;
            text-align:center;
            box-shadow:0 5px 15px rgba(0,0,0,0.08);
            transition:0.3s;
        }

        .card:hover{
            transform:translateY(-5px);
        }

        .card i{
            font-size:45px;
            color:#4f46e5;
            margin-bottom:15px;
        }

        .card h3{
            margin-bottom:10px;
            color:#333;
        }

        .card p{
            color:#777;
            font-size:14px;
            margin-bottom:20px;
        }

        .card a{
            text-decoration:none;
            background:#4f46e5;
            color:white;
            padding:10px 18px;
            border-radius:8px;
            display:inline-block;
            transition:0.3s;
            font-size:14px;
            font-weight:600;
        }

        .card a:hover{
            background:#4338ca;
        }

        /* DARK MODE */

        .dark-mode{
            background:#111827;
            color:white;
        }

        .dark-mode .welcome-card,
        .dark-mode .card{
            background:#1f2937;
            color:white;
        }

        .dark-mode .welcome-card p,
        .dark-mode .card p{
            color:#d1d5db;
        }

        .dark-mode .card h3,
        .dark-mode .welcome-card h1{
            color:white;
        }

        .dark-mode .navbar{
            background:#111827;
        }

        /* RESPONSIVE */

        @media(max-width:768px){

            .navbar{
                padding:20px;
                flex-direction:column;
                gap:15px;
            }

            .dashboard{
                padding:20px;
            }
        }

    </style>

</head>

<body>

    <!-- NAVBAR -->

    <div class="navbar">

        <div class="logo">
            <i class="fa-solid fa-ticket"></i> Event Organizer
        </div>

        <div class="nav-links">

            <a href="create_event.php">
                <i class="fa-solid fa-plus"></i> Create Event
            </a>

            <a href="manage_events.php">
                <i class="fa-solid fa-list"></i> Manage Events
            </a>

            <a href="generate_report.php">
                <i class="fa-solid fa-file-arrow-down"></i> Reports
            </a>

            <a href="../index.php">
                <i class="fa-solid fa-house"></i> Home
            </a>

            <a href="../auth/logout.php">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>

            <button id="darkModeBtn">
                🌙 Dark Mode
            </button>

        </div>

    </div>

    <!-- DASHBOARD -->

    <div class="dashboard">

        <div class="welcome-card">

            <h1>
                Welcome, <?php echo $_SESSION['fullname']; ?> 👋
            </h1>

            <p>
                Manage your events, track attendees, and generate reports easily from your organizer dashboard.
            </p>

        </div>

        <!-- DASHBOARD CARDS -->

        <div class="card-container">

            <div class="card">

                <i class="fa-solid fa-calendar-plus"></i>

                <h3>Create Events</h3>

                <p>
                    Add new events and publish them for attendees.
                </p>

                <a href="create_event.php">
                    Open
                </a>

            </div>

            <div class="card">

                <i class="fa-solid fa-clipboard-list"></i>

                <h3>Manage Events</h3>

                <p>
                    Edit, update, or remove events from the system.
                </p>

                <a href="manage_events.php">
                    Open
                </a>

            </div>

            <div class="card">

                <i class="fa-solid fa-chart-column"></i>

                <h3>Generate Reports</h3>

                <p>
                    Download ticket holders and booking reports.
                </p>

                <a href="generate_report.php">
                    Open
                </a>

            </div>

        </div>

    </div>

    <!-- DARK MODE SCRIPT -->

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