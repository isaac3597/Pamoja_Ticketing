<?php
session_start();

include '../config/db.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$organizer_id = $_SESSION['user_id'];

$sql = "SELECT * FROM events
        WHERE organizer_id='$organizer_id'
        ORDER BY id DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Events</title>

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
            padding:18px 40px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            flex-wrap:wrap;
            box-shadow:0 4px 10px rgba(0,0,0,0.1);
        }

        .logo{
            color:white;
            font-size:24px;
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
            padding:10px 18px;
            border:none;
            border-radius:8px;
            font-size:14px;
            font-weight:600;
            cursor:pointer;
            transition:0.3s;
        }

        .nav-links a:hover,
        .nav-links button:hover{
            background:#e0e7ff;
        }

        /* PAGE */

        .container{
            padding:40px;
        }

        .page-title{
            margin-bottom:30px;
        }

        .page-title h1{
            color:#111827;
            font-size:35px;
            margin-bottom:10px;
        }

        .page-title p{
            color:#6b7280;
            font-size:16px;
        }

        /* EVENTS GRID */

        .events-grid{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(350px,1fr));
            gap:30px;
        }

        /* EVENT CARD */

        .event-card{
            background:white;
            border-radius:20px;
            overflow:hidden;
            box-shadow:0 5px 20px rgba(0,0,0,0.08);
            transition:0.3s;
        }

        .event-card:hover{
            transform:translateY(-5px);
        }

        .event-image{
            width:100%;
            height:220px;
            object-fit:cover;
        }

        .event-content{
            padding:25px;
        }

        .event-content h2{
            color:#111827;
            margin-bottom:12px;
            font-size:24px;
        }

        .event-description{
            color:#6b7280;
            margin-bottom:20px;
            line-height:1.7;
        }

        .event-info{
            margin-bottom:20px;
        }

        .event-info p{
            margin-bottom:10px;
            color:#374151;
            font-size:15px;
        }

        .stats-grid{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:15px;
            margin-top:20px;
            margin-bottom:25px;
        }

        .stat-box{
            background:#eef2ff;
            padding:15px;
            border-radius:12px;
            text-align:center;
        }

        .stat-box h3{
            color:#4f46e5;
            font-size:20px;
            margin-bottom:5px;
        }

        .stat-box p{
            color:#555;
            font-size:13px;
        }

        /* BUTTONS */

        .card-buttons{
            display:flex;
            gap:12px;
            flex-wrap:wrap;
        }

        .report-btn,
        .delete-btn{
            flex:1;
            text-align:center;
            text-decoration:none;
            padding:12px;
            border-radius:10px;
            font-size:14px;
            font-weight:600;
            transition:0.3s;
        }

        .report-btn{
            background:#4f46e5;
            color:white;
        }

        .report-btn:hover{
            background:#4338ca;
        }

        .delete-btn{
            background:#ef4444;
            color:white;
        }

        .delete-btn:hover{
            background:#dc2626;
        }

        /* EMPTY STATE */

        .empty-state{
            background:white;
            padding:50px;
            text-align:center;
            border-radius:20px;
            box-shadow:0 5px 20px rgba(0,0,0,0.08);
        }

        .empty-state i{
            font-size:60px;
            color:#4f46e5;
            margin-bottom:20px;
        }

        .empty-state h2{
            margin-bottom:10px;
            color:#111827;
        }

        .empty-state p{
            color:#6b7280;
            margin-bottom:25px;
        }

        .empty-state a{
            display:inline-block;
            text-decoration:none;
            background:#4f46e5;
            color:white;
            padding:12px 24px;
            border-radius:10px;
            font-weight:600;
        }

        /* DARK MODE */

        .dark-mode{
            background:#111827;
            color:white;
        }

        .dark-mode .navbar{
            background:#0f172a;
        }

        .dark-mode .event-card,
        .dark-mode .empty-state{
            background:#1f2937;
        }

        .dark-mode .page-title h1,
        .dark-mode .event-content h2,
        .dark-mode .empty-state h2{
            color:white;
        }

        .dark-mode .event-description,
        .dark-mode .page-title p,
        .dark-mode .empty-state p{
            color:#d1d5db;
        }

        .dark-mode .event-info p{
            color:#e5e7eb;
        }

        .dark-mode .stat-box{
            background:#374151;
        }

        .dark-mode .stat-box p{
            color:#d1d5db;
        }

        /* RESPONSIVE */

        @media(max-width:768px){

            .navbar{
                padding:20px;
                flex-direction:column;
                gap:15px;
            }

            .container{
                padding:20px;
            }

            .events-grid{
                grid-template-columns:1fr;
            }
        }

    </style>

</head>

<body>

    <!-- NAVBAR -->

    <div class="navbar">

        <div class="logo">
            <i class="fa-solid fa-calendar-check"></i> Event Manager
        </div>

        <div class="nav-links">

            <a href="create_event.php">
                <i class="fa-solid fa-plus"></i> Create Event
            </a>

            <a href="manage_events.php">
                <i class="fa-solid fa-list"></i> Manage Events
            </a>

            <a href="../auth/logout.php">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>

            <button id="darkModeBtn">
                🌙 Dark Mode
            </button>

        </div>

    </div>

    <!-- PAGE CONTENT -->

    <div class="container">

        <div class="page-title">

            <h1>Manage My Events</h1>

            <p>
                View event performance, ticket sales, and revenue reports.
            </p>

        </div>

        <?php if(mysqli_num_rows($result) > 0) { ?>

            <div class="events-grid">

                <?php while($row = mysqli_fetch_assoc($result)) { ?>

                    <?php

                    // Tickets sold
                    $ticket_sql = mysqli_query($conn, "
                        SELECT SUM(quantity) AS total_tickets
                        FROM tickets
                        WHERE event_id='{$row['id']}'
                    ");

                    $ticket_data = mysqli_fetch_assoc($ticket_sql);

                    // Revenue
                    $revenue_sql = mysqli_query($conn, "
                        SELECT SUM(total_price) AS revenue
                        FROM tickets
                        WHERE event_id='{$row['id']}'
                    ");

                    $revenue_data = mysqli_fetch_assoc($revenue_sql);

                    ?>

                    <div class="event-card">

                        <img
                            src="../assets/uploads/<?php echo $row['image']; ?>"
                            class="event-image"
                        >

                        <div class="event-content">

                            <h2>
                                <?php echo $row['title']; ?>
                            </h2>

                            <p class="event-description">
                                <?php echo $row['description']; ?>
                            </p>

                            <div class="event-info">

                                <p>
                                    <strong>📍 Location:</strong>
                                    <?php echo $row['location']; ?>
                                </p>

                                <p>
                                    <strong>📅 Date:</strong>
                                    <?php echo $row['event_date']; ?>
                                </p>

                                <p>
                                    <strong>🎫 Regular Tickets:</strong>
                                    <?php echo $row['regular_tickets']; ?>
                                </p>

                                <p>
                                    <strong>⭐ VIP Tickets:</strong>
                                    <?php echo $row['vip_tickets']; ?>
                                </p>

                                <p>
                                    <strong>👑 VVIP Tickets:</strong>
                                    <?php echo $row['vvip_tickets']; ?>
                                </p>

                            </div>

                            <!-- STATS -->

                            <div class="stats-grid">

                                <div class="stat-box">

                                    <h3>
                                        <?php echo $ticket_data['total_tickets'] ?? 0; ?>
                                    </h3>

                                    <p>Tickets Sold</p>

                                </div>

                                <div class="stat-box">

                                    <h3>
                                        KSH <?php echo $revenue_data['revenue'] ?? 0; ?>
                                    </h3>

                                    <p>Total Revenue</p>

                                </div>

                            </div>

                            <!-- BUTTONS -->

                            <div class="card-buttons">

                                <a
                                    href="event_report.php?id=<?php echo $row['id']; ?>"
                                    class="report-btn"
                                >
                                    <i class="fa-solid fa-file-lines"></i>
                                    Generate Report
                                </a>

                                <a
                                    href="delete_event.php?id=<?php echo $row['id']; ?>"
                                    class="delete-btn"
                                    onclick="return confirm('Delete this event?')"
                                >
                                    <i class="fa-solid fa-trash"></i>
                                    Delete
                                </a>

                            </div>

                        </div>

                    </div>

                <?php } ?>

            </div>

        <?php } else { ?>

            <div class="empty-state">

                <i class="fa-solid fa-calendar-xmark"></i>

                <h2>No Events Found</h2>

                <p>
                    You have not created any events yet.
                </p>

                <a href="create_event.php">
                    Create Your First Event
                </a>

            </div>

        <?php } ?>

    </div>

    <!-- DARK MODE -->

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