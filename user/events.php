<?php
include '../config/db.php';

$sql = "SELECT events.*, users.fullname
        FROM events
        JOIN users ON events.organizer_id = users.id
        WHERE event_date >= CURDATE()
        ORDER BY event_date ASC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>All Events</title>

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
            text-align:center;
            margin-bottom:40px;
        }

        .page-title h1{
            font-size:42px;
            color:#111827;
            margin-bottom:10px;
        }

        .page-title p{
            color:#6b7280;
            font-size:17px;
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
            height:230px;
            object-fit:cover;
        }

        .event-content{
            padding:25px;
        }

        .event-content h2{
            color:#111827;
            margin-bottom:12px;
            font-size:25px;
        }

        .event-description{
            color:#6b7280;
            line-height:1.7;
            margin-bottom:20px;
        }

        .event-info p{
            margin-bottom:10px;
            color:#374151;
            font-size:15px;
        }

        /* TICKET PRICES */

        .ticket-prices{
            display:grid;
            grid-template-columns:repeat(3,1fr);
            gap:12px;
            margin-top:20px;
            margin-bottom:20px;
        }

        .price-box{
            background:#eef2ff;
            padding:15px;
            border-radius:12px;
            text-align:center;
        }

        .price-box h3{
            color:#4f46e5;
            font-size:18px;
            margin-bottom:5px;
        }

        .price-box p{
            color:#555;
            font-size:13px;
        }

        /* SEATS */

        .seats-grid{
            display:grid;
            grid-template-columns:repeat(3,1fr);
            gap:12px;
            margin-bottom:25px;
        }

        .seat-box{
            background:#f3f4f6;
            padding:12px;
            border-radius:10px;
            text-align:center;
        }

        .seat-box h4{
            color:#111827;
            margin-bottom:5px;
            font-size:15px;
        }

        .seat-box p{
            color:#6b7280;
            font-size:14px;
        }

        /* BUTTON */

        .buy-btn{
            display:block;
            width:100%;
            text-align:center;
            text-decoration:none;
            background:#4f46e5;
            color:white;
            padding:14px;
            border-radius:10px;
            font-size:15px;
            font-weight:600;
            transition:0.3s;
        }

        .buy-btn:hover{
            background:#4338ca;
        }

        /* EMPTY STATE */

        .empty-state{
            background:white;
            padding:60px;
            text-align:center;
            border-radius:20px;
            box-shadow:0 5px 20px rgba(0,0,0,0.08);
        }

        .empty-state i{
            font-size:65px;
            color:#4f46e5;
            margin-bottom:20px;
        }

        .empty-state h2{
            margin-bottom:10px;
            color:#111827;
        }

        .empty-state p{
            color:#6b7280;
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

        .dark-mode .event-content h2,
        .dark-mode .page-title h1,
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

        .dark-mode .seat-box{
            background:#374151;
        }

        .dark-mode .seat-box h4{
            color:white;
        }

        .dark-mode .seat-box p{
            color:#d1d5db;
        }

        .dark-mode .price-box{
            background:#312e81;
        }

        .dark-mode .price-box p{
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

            .ticket-prices,
            .seats-grid{
                grid-template-columns:1fr;
            }

            .page-title h1{
                font-size:32px;
            }
        }

    </style>

</head>

<body>

    <!-- NAVBAR -->

    <div class="navbar">

        <div class="logo">
            <i class="fa-solid fa-ticket"></i> Pamoja Events
        </div>

        <div class="nav-links">

            <a href="../index.php">
                <i class="fa-solid fa-house"></i> Home
            </a>

            <a href="events.php">
                <i class="fa-solid fa-calendar-days"></i> Events
            </a>

            <a href="my_tickets.php">
                <i class="fa-solid fa-ticket-simple"></i> My Tickets
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

            <h1>Upcoming Events</h1>

            <p>
                Discover and book tickets for exciting upcoming events.
            </p>

        </div>

        <?php if(mysqli_num_rows($result) > 0) { ?>

            <div class="events-grid">

                <?php while($row = mysqli_fetch_assoc($result)) { ?>

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
                                    <strong>👤 Organizer:</strong>
                                    <?php echo $row['fullname']; ?>
                                </p>

                            </div>

                            <!-- TICKET PRICES -->

                            <div class="ticket-prices">

                                <div class="price-box">

                                    <h3>
                                        KSH <?php echo $row['regular_price']; ?>
                                    </h3>

                                    <p>Regular</p>

                                </div>

                                <div class="price-box">

                                    <h3>
                                        KSH <?php echo $row['vip_price']; ?>
                                    </h3>

                                    <p>VIP</p>

                                </div>

                                <div class="price-box">

                                    <h3>
                                        KSH <?php echo $row['vvip_price']; ?>
                                    </h3>

                                    <p>VVIP</p>

                                </div>

                            </div>

                            <!-- AVAILABLE SEATS -->

                            <div class="seats-grid">

                                <div class="seat-box">

                                    <h4>Regular</h4>

                                    <p>
                                        <?php echo $row['regular_tickets']; ?> Seats
                                    </p>

                                </div>

                                <div class="seat-box">

                                    <h4>VIP</h4>

                                    <p>
                                        <?php echo $row['vip_tickets']; ?> Seats
                                    </p>

                                </div>

                                <div class="seat-box">

                                    <h4>VVIP</h4>

                                    <p>
                                        <?php echo $row['vvip_tickets']; ?> Seats
                                    </p>

                                </div>

                            </div>

                            <!-- BUY BUTTON -->

                            <a
                                href="buy_ticket.php?id=<?php echo $row['id']; ?>"
                                class="buy-btn"
                            >
                                <i class="fa-solid fa-cart-shopping"></i>
                                Buy Ticket
                            </a>

                        </div>

                    </div>

                <?php } ?>

            </div>

        <?php } else { ?>

            <div class="empty-state">

                <i class="fa-solid fa-calendar-xmark"></i>

                <h2>No Events Available</h2>

                <p>
                    There are currently no upcoming events.
                </p>

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