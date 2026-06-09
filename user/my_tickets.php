<?php
session_start();

include '../config/db.php';

// CHECK LOGIN
if(!isset($_SESSION['user_id'])) {

    header('Location: ../auth/login.php');

    exit();
}

$user_id = $_SESSION['user_id'];

// FETCH USER TICKETS + EVENT DETAILS
$sql = "SELECT
            tickets.*,
            events.title,
            events.description,
            events.location,
            events.event_date,
            events.image
        FROM tickets
        JOIN events
        ON tickets.event_id = events.id
        WHERE tickets.user_id='$user_id'
        ORDER BY tickets.id DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>My Tickets</title>

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
            flex-wrap:wrap;
            align-items:center;
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
            max-width:1300px;
            margin:auto;
            padding:40px 20px;
        }

        .page-header{
            text-align:center;
            margin-bottom:40px;
        }

        .page-header h1{
            font-size:42px;
            color:#111827;
            margin-bottom:10px;
        }

        .page-header p{
            color:#6b7280;
            font-size:16px;
        }

        /* GRID */

        .tickets-grid{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(400px,1fr));
            gap:30px;
        }

        /* CARD */

        .ticket-card{
            background:white;
            border-radius:20px;
            overflow:hidden;
            box-shadow:0 5px 20px rgba(0,0,0,0.08);
            transition:0.3s;
        }

        .ticket-card:hover{
            transform:translateY(-5px);
        }

        .ticket-image{
            width:100%;
            height:240px;
            object-fit:cover;
        }

        .ticket-content{
            padding:25px;
        }

        .ticket-title{
            font-size:28px;
            color:#111827;
            margin-bottom:15px;
        }

        .ticket-description{
            color:#6b7280;
            line-height:1.7;
            margin-bottom:20px;
        }

        .ticket-info{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:15px;
            margin-bottom:20px;
        }

        .info-box{
            background:#eef2ff;
            padding:15px;
            border-radius:12px;
        }

        .info-box h4{
            color:#4f46e5;
            font-size:14px;
            margin-bottom:8px;
        }

        .info-box p{
            color:#111827;
            font-size:15px;
            font-weight:600;
        }

        /* STATUS */

        .status-box{
            background:#dcfce7;
            color:#166534;
            padding:15px;
            border-radius:12px;
            text-align:center;
            font-weight:600;
            margin-bottom:20px;
        }

        /* QR */

        .qr-section{
            text-align:center;
            margin-top:20px;
        }

        .qr-section img{
            width:180px;
            border-radius:15px;
            padding:10px;
            background:white;
            box-shadow:0 4px 10px rgba(0,0,0,0.08);
        }

        /* BUTTON */

        .download-btn{
            display:block;
            width:100%;
            text-align:center;
            text-decoration:none;
            background:#4f46e5;
            color:white;
            padding:15px;
            border-radius:12px;
            font-size:15px;
            font-weight:600;
            margin-top:20px;
            transition:0.3s;
        }

        .download-btn:hover{
            background:#4338ca;
        }

        /* EMPTY STATE */

        .empty-state{
            background:white;
            padding:60px;
            border-radius:20px;
            text-align:center;
            box-shadow:0 5px 20px rgba(0,0,0,0.08);
        }

        .empty-state i{
            font-size:70px;
            color:#4f46e5;
            margin-bottom:20px;
        }

        .empty-state h2{
            color:#111827;
            margin-bottom:10px;
        }

        .empty-state p{
            color:#6b7280;
            margin-bottom:25px;
        }

        .browse-btn{
            display:inline-block;
            text-decoration:none;
            background:#4f46e5;
            color:white;
            padding:14px 24px;
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

        .dark-mode .ticket-card,
        .dark-mode .empty-state{
            background:#1f2937;
        }

        .dark-mode .ticket-title,
        .dark-mode .page-header h1,
        .dark-mode .empty-state h2{
            color:white;
        }

        .dark-mode .ticket-description,
        .dark-mode .page-header p,
        .dark-mode .empty-state p{
            color:#d1d5db;
        }

        .dark-mode .info-box{
            background:#312e81;
        }

        .dark-mode .info-box p{
            color:white;
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

            .page-header h1{
                font-size:32px;
            }

            .tickets-grid{
                grid-template-columns:1fr;
            }

            .ticket-info{
                grid-template-columns:1fr;
            }
        }

    </style>

</head>

<body>

    <!-- NAVBAR -->

    <div class="navbar">

        <div class="logo">
            <i class="fa-solid fa-ticket"></i>
            Pamoja Ticketing
        </div>

        <div class="nav-links">

            <a href="../index.php">
                <i class="fa-solid fa-house"></i>
                Home
            </a>

            <a href="events.php">
                <i class="fa-solid fa-calendar-days"></i>
                Events
            </a>

            <a href="my_tickets.php">
                <i class="fa-solid fa-ticket-simple"></i>
                My Tickets
            </a>

            <a href="../auth/logout.php">
                <i class="fa-solid fa-right-from-bracket"></i>
                Logout
            </a>

            <button id="darkModeBtn">
                🌙 Dark Mode
            </button>

        </div>

    </div>

    <!-- PAGE -->

    <div class="container">

        <div class="page-header">

            <h1>My Purchased Tickets</h1>

            <p>
                View your purchased event tickets and download PDFs.
            </p>

        </div>

        <?php if(mysqli_num_rows($result) > 0) { ?>

            <div class="tickets-grid">

                <?php while($row = mysqli_fetch_assoc($result)) { ?>

                    <div class="ticket-card">

                        <!-- EVENT IMAGE -->

                        <img
                            src="../assets/uploads/<?php echo $row['image']; ?>"
                            class="ticket-image"
                        >

                        <div class="ticket-content">

                            <!-- TITLE -->

                            <h2 class="ticket-title">
                                <?php echo $row['title']; ?>
                            </h2>

                            <!-- DESCRIPTION -->

                            <p class="ticket-description">
                                <?php echo $row['description']; ?>
                            </p>

                            <!-- TICKET INFO -->

                            <div class="ticket-info">

                                <div class="info-box">

                                    <h4>
                                        📍 Location
                                    </h4>

                                    <p>
                                        <?php echo $row['location']; ?>
                                    </p>

                                </div>

                                <div class="info-box">

                                    <h4>
                                        📅 Event Date
                                    </h4>

                                    <p>
                                        <?php echo $row['event_date']; ?>
                                    </p>

                                </div>

                                <div class="info-box">

                                    <h4>
                                        🎫 Ticket Type
                                    </h4>

                                    <p>
                                        <?php echo $row['ticket_type']; ?>
                                    </p>

                                </div>

                                <div class="info-box">

                                    <h4>
                                        🪑 Seat Number
                                    </h4>

                                    <p>
                                        <?php echo $row['seat_number']; ?>
                                    </p>

                                </div>

                                <div class="info-box">

                                    <h4>
                                        👥 Quantity
                                    </h4>

                                    <p>
                                        <?php echo $row['quantity']; ?>
                                    </p>

                                </div>

                                <div class="info-box">

                                    <h4>
                                        💰 Total Paid
                                    </h4>

                                    <p>
                                        KSH <?php echo $row['total_price']; ?>
                                    </p>

                                </div>

                            </div>

                            <!-- ACCESS STATUS -->

                            <div class="status-box">

                                ✅ ACCESS AUTHORISED

                            </div>

                            <!-- DOWNLOAD -->

                            <a
                                href="download_ticket.php?id=<?php echo $row['id']; ?>"
                                class="download-btn"
                            >

                                <i class="fa-solid fa-download"></i>

                                Download Ticket PDF

                            </a>

                            <!-- QR CODE -->

                            <div class="qr-section">

                                <img
                                    src="../assets/qrcodes/<?php echo $row['qr_code']; ?>"
                                >

                            </div>

                        </div>

                    </div>

                <?php } ?>

            </div>

        <?php } else { ?>

            <!-- EMPTY -->

            <div class="empty-state">

                <i class="fa-solid fa-ticket"></i>

                <h2>No Tickets Found</h2>

                <p>
                    You have not purchased any tickets yet.
                </p>

                <a
                    href="events.php"
                    class="browse-btn"
                >

                    Browse Events

                </a>

            </div>

        <?php } ?>

    </div>

    <!-- DARK MODE -->

    <script>

        const darkBtn =
            document.getElementById(
                'darkModeBtn'
            );

        // LOAD MODE
        if(localStorage.getItem(
            'darkMode'
        ) === 'enabled') {

            document.body.classList.add(
                'dark-mode'
            );
        }

        // TOGGLE
        darkBtn.addEventListener(
            'click',
            () => {

                document.body.classList.toggle(
                    'dark-mode'
                );

                if(document.body.classList.contains(
                    'dark-mode'
                )) {

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
            }
        );

    </script>

</body>
</html>