<?php
session_start();

include '../config/db.php';

// CHECK LOGIN
if(!isset($_SESSION['user_id'])) {

    header('Location: ../auth/login.php');

    exit();
}

$event_id = $_GET['id'];

// FETCH EVENT
$sql = "SELECT * FROM events WHERE id='$event_id'";

$result = mysqli_query($conn, $sql);

$event = mysqli_fetch_assoc($result);

/* BUY TICKET */

if(isset($_POST['buy'])) {

    $ticket_type = $_POST['ticket_type'];

    $quantity = $_POST['quantity'];

    $user_id = $_SESSION['user_id'];

    $seat_numbers = $_POST['seat_number'] ?? [];

    // VALIDATE SEATS
    if(count($seat_numbers) != $quantity) {

        $error =
            "Please select seats equal to ticket quantity";

    } else {

        $seat_number =
            implode(',', $seat_numbers);

        // DETERMINE PRICE + AVAILABLE TICKETS
        if($ticket_type == "Regular") {

            $price = $event['regular_price'];

            $available = $event['regular_tickets'];

        } elseif($ticket_type == "VIP") {

            $price = $event['vip_price'];

            $available = $event['vip_tickets'];

        } else {

            $price = $event['vvip_price'];

            $available = $event['vvip_tickets'];
        }

        $total_price = $price * $quantity;

        // CHECK AVAILABLE TICKETS
        if($quantity > $available) {

            $error = "Not enough tickets available";

        } else {

            // INSERT TICKET
            $insert = "INSERT INTO tickets(
                            user_id,
                            event_id,
                            ticket_type,
                            quantity,
                            total_price,
                            seat_number
                        )
                        VALUES(
                            '$user_id',
                            '$event_id',
                            '$ticket_type',
                            '$quantity',
                            '$total_price',
                            '$seat_number'
                        )";

            if(mysqli_query($conn, $insert)) {

                $ticket_id =
                    mysqli_insert_id($conn);

                // PASS CODE
                $pass_code =
                    "PASS-" . rand(100000,999999);

                // QR DATA
                $qr_data = "
EVENT: {$event['title']}
TYPE: {$ticket_type}
SEATS: {$seat_number}
TICKET ID: {$ticket_id}
USER ID: {$user_id}
PASS: {$pass_code}
STATUS: AUTHORISED
";

                include '../phpqrcode/qrlib.php';

                $file_name =
                    'ticket_'.$ticket_id.'.png';

                $file_path =
                    '../assets/qrcodes/'.$file_name;

                QRcode::png(
                    $qr_data,
                    $file_path
                );

                // SAVE QR
                mysqli_query($conn, "
                    UPDATE tickets
                    SET qr_code='$file_name'
                    WHERE id='$ticket_id'
                ");

                // UPDATE REMAINING TICKETS
                if($ticket_type == "Regular") {

                    $remaining =
                        $event['regular_tickets']
                        - $quantity;

                    mysqli_query($conn, "
                        UPDATE events
                        SET regular_tickets='$remaining'
                        WHERE id='$event_id'
                    ");

                } elseif($ticket_type == "VIP") {

                    $remaining =
                        $event['vip_tickets']
                        - $quantity;

                    mysqli_query($conn, "
                        UPDATE events
                        SET vip_tickets='$remaining'
                        WHERE id='$event_id'
                    ");

                } else {

                    $remaining =
                        $event['vvip_tickets']
                        - $quantity;

                    mysqli_query($conn, "
                        UPDATE events
                        SET vvip_tickets='$remaining'
                        WHERE id='$event_id'
                    ");
                }

                $success =
                    "Ticket purchased successfully";

            } else {

                $error =
                    "Error purchasing ticket";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Buy Ticket</title>

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
            transition:0.3s;
            cursor:pointer;
        }

        .nav-links a:hover,
        .nav-links button:hover{
            background:#e0e7ff;
        }

        /* PAGE */

        .container{
            max-width:1200px;
            margin:40px auto;
            padding:20px;
        }

        .page-title{
            text-align:center;
            margin-bottom:30px;
        }

        .page-title h1{
            font-size:40px;
            color:#111827;
        }

        .page-title p{
            color:#6b7280;
            margin-top:10px;
        }

        /* GRID */

        .content-grid{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:30px;
        }

        /* EVENT CARD */

        .event-card{
            background:white;
            border-radius:20px;
            overflow:hidden;
            box-shadow:0 5px 20px rgba(0,0,0,0.08);
        }

        .event-image{
            width:100%;
            height:300px;
            object-fit:cover;
        }

        .event-content{
            padding:25px;
        }

        .event-content h2{
            color:#111827;
            margin-bottom:15px;
            font-size:28px;
        }

        .event-content p{
            margin-bottom:12px;
            color:#4b5563;
            line-height:1.7;
        }

        /* FORM */

        .form-card{
            background:white;
            padding:30px;
            border-radius:20px;
            box-shadow:0 5px 20px rgba(0,0,0,0.08);
        }

        .form-card h2{
            margin-bottom:25px;
            color:#111827;
        }

        .input-group{
            margin-bottom:20px;
        }

        .input-group label{
            display:block;
            margin-bottom:8px;
            color:#374151;
            font-weight:500;
        }

        .input-group input,
        .input-group select{
            width:100%;
            padding:14px;
            border:1px solid #d1d5db;
            border-radius:10px;
            outline:none;
            transition:0.3s;
            font-size:15px;
        }

        .input-group input:focus,
        .input-group select:focus{
            border-color:#4f46e5;
            box-shadow:0 0 5px rgba(79,70,229,0.3);
        }

        /* ALERTS */

        .success{
            background:#dcfce7;
            color:#166534;
            padding:15px;
            border-radius:10px;
            margin-bottom:20px;
        }

        .error{
            background:#fee2e2;
            color:#991b1b;
            padding:15px;
            border-radius:10px;
            margin-bottom:20px;
        }

        /* SEATS */

        .seat-container{
            display:flex;
            flex-wrap:wrap;
            gap:12px;
            margin-top:10px;
        }

        .seat-box{
            background:#eef2ff;
            border:2px solid transparent;
            padding:12px 18px;
            border-radius:10px;
            cursor:pointer;
            font-weight:600;
            transition:0.3s;
        }

        .seat-box:hover{
            background:#c7d2fe;
        }

        .seat-box input{
            display:none;
        }

        .seat-box input:checked + span{
            color:#4f46e5;
        }

        /* TOTAL */

        .total-box{
            background:#eef2ff;
            padding:20px;
            border-radius:12px;
            margin-top:20px;
            text-align:center;
        }

        .total-box h3{
            color:#4f46e5;
            margin-bottom:10px;
        }

        .total-price{
            font-size:30px;
            font-weight:700;
            color:#111827;
        }

        /* BUTTON */

        .buy-btn{
            width:100%;
            background:#4f46e5;
            color:white;
            border:none;
            padding:15px;
            border-radius:10px;
            font-size:16px;
            font-weight:600;
            margin-top:25px;
            cursor:pointer;
            transition:0.3s;
        }

        .buy-btn:hover{
            background:#4338ca;
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
        .dark-mode .form-card{
            background:#1f2937;
        }

        .dark-mode .event-content h2,
        .dark-mode .form-card h2,
        .dark-mode .page-title h1{
            color:white;
        }

        .dark-mode .event-content p,
        .dark-mode .page-title p,
        .dark-mode .input-group label{
            color:#d1d5db;
        }

        .dark-mode .input-group input,
        .dark-mode .input-group select{
            background:#374151;
            color:white;
            border:1px solid #4b5563;
        }

        .dark-mode .total-price{
            color:white;
        }

        .dark-mode .seat-box{
            background:#374151;
        }

        .dark-mode .total-box{
            background:#312e81;
        }

        /* RESPONSIVE */

        @media(max-width:900px){

            .content-grid{
                grid-template-columns:1fr;
            }
        }

        @media(max-width:768px){

            .navbar{
                padding:20px;
                flex-direction:column;
                gap:15px;
            }

            .container{
                padding:15px;
            }

            .page-title h1{
                font-size:30px;
            }
        }

    </style>

</head>

<body>

    <!-- NAVBAR -->

    <div class="navbar">

        <div class="logo">

    <div style="font-size:24px;font-weight:700;">
        <i class="fa-solid fa-ticket"></i>
        Pamoja Ticketing
    </div>

   

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

        <div class="page-title">

            <h1>Buy Event Ticket</h1>

            <p>
                Secure your seat and enjoy the event experience.
            </p>

        </div>

        <?php if(isset($success)) { ?>

            <div class="success">
                <?php echo $success; ?>
            </div>

        <?php } ?>

        <?php if(isset($error)) { ?>

            <div class="error">
                <?php echo $error; ?>
            </div>

        <?php } ?>

        <div class="content-grid">

            <!-- EVENT DETAILS -->

            <div class="event-card">

                <img
                    src="../assets/uploads/<?php echo $event['image']; ?>"
                    class="event-image"
                >

                <div class="event-content">

                    <h2>
                        <?php echo $event['title']; ?>
                    </h2>

                    <p>
                        <?php echo $event['description']; ?>
                    </p>

                    <p>
                        <strong>📍 Location:</strong>
                        <?php echo $event['location']; ?>
                    </p>

                    <p>
                        <strong>📅 Date:</strong>
                        <?php echo $event['event_date']; ?>
                    </p>

                    <p>
                        <strong>🎫 Regular Tickets:</strong>
                        <?php echo $event['regular_tickets']; ?>
                    </p>

                    <p>
                        <strong>⭐ VIP Tickets:</strong>
                        <?php echo $event['vip_tickets']; ?>
                    </p>

                    <p>
                        <strong>👑 VVIP Tickets:</strong>
                        <?php echo $event['vvip_tickets']; ?>
                    </p>

                </div>

            </div>

            <!-- BUY FORM -->

            <div class="form-card">

                <h2>Ticket Purchase</h2>

                <form method="POST">

                    <div class="input-group">

                        <label>Ticket Type</label>

                        <select
                            name="ticket_type"
                            id="ticket_type"
                            required
                        >

                            <option value="">
                                Select Ticket Type
                            </option>

                            <option
                                value="Regular"
                                data-price="<?php echo $event['regular_price']; ?>"
                            >
                                Regular -
                                KSH <?php echo $event['regular_price']; ?>
                            </option>

                            <option
                                value="VIP"
                                data-price="<?php echo $event['vip_price']; ?>"
                            >
                                VIP -
                                KSH <?php echo $event['vip_price']; ?>
                            </option>

                            <option
                                value="VVIP"
                                data-price="<?php echo $event['vvip_price']; ?>"
                            >
                                VVIP -
                                KSH <?php echo $event['vvip_price']; ?>
                            </option>

                        </select>

                    </div>

                    <div class="input-group">

                        <label>M-Pesa Number</label>

                        <input
                            type="text"
                            name="phone"
                            placeholder="254712345678"
                            required
                        >

                    </div>

                    <div class="input-group">

                        <label>Number of Tickets</label>

                        <input
                            type="number"
                            id="quantity"
                            name="quantity"
                            min="1"
                            required
                        >

                    </div>

                    <!-- SEATS -->

                    <div class="input-group">

                        <label>Select Seats</label>

                        <div
                            class="seat-container"
                            id="seatContainer"
                        >

                        </div>

                    </div>

                    <!-- TOTAL -->

                    <div class="total-box">

                        <h3>Total Amount</h3>

                        <div
                            class="total-price"
                            id="total"
                        >
                            KSH 0
                        </div>

                    </div>

                    <input
                        type="hidden"
                        name="amount"
                        id="amount"
                    >

                    <button
                        type="submit"
                        name="buy"
                        class="buy-btn"
                    >
                        <i class="fa-solid fa-mobile-screen-button"></i>
                        Pay with M-Pesa
                    </button>

                </form>

            </div>

        </div>

    </div>

    <!-- JAVASCRIPT -->

    <script>

        const quantityInput =
            document.getElementById('quantity');

        const totalInput =
            document.getElementById('total');

        const ticketType =
            document.getElementById('ticket_type');

        const seatContainer =
            document.getElementById(
                'seatContainer'
            );

        // TOTAL
        function calculateTotal() {

            const selectedOption =
                ticketType.options[
                    ticketType.selectedIndex
                ];

            const price =
                selectedOption.getAttribute(
                    'data-price'
                );

            const quantity =
                quantityInput.value;

            const total =
                price * quantity;

            totalInput.innerHTML =
                "KSH " + total;

            document.getElementById(
                'amount'
            ).value = total;
        }

        quantityInput.addEventListener(
            'input',
            calculateTotal
        );

        ticketType.addEventListener(
            'change',
            calculateTotal
        );

        // BOOKED SEATS
        const bookedSeats = <?php

        $booked = [];

        $getBooked = mysqli_query(
            $conn,
            "SELECT seat_number
             FROM tickets
             WHERE event_id='$event_id'"
        );

        while($seat = mysqli_fetch_assoc($getBooked)) {

            $seatArray =
                explode(',', $seat['seat_number']);

            foreach($seatArray as $s) {

                $booked[] = trim($s);
            }
        }

        echo json_encode($booked);

        ?>;

        // GENERATE SEATS
        function generateSeats(prefix, total) {

            let seats = [];

            for(let i = 1; i <= total; i++) {

                seats.push(prefix + i);
            }

            return seats;
        }

        const regularSeats = generateSeats(
            'R',
            <?php echo (int)$event['regular_tickets']; ?>
        );

        const vipSeats = generateSeats(
            'V',
            <?php echo (int)$event['vip_tickets']; ?>
        );

        const vvipSeats = generateSeats(
            'VV',
            <?php echo (int)$event['vvip_tickets']; ?>
        );

        // LOAD SEATS
        function loadSeats() {

            seatContainer.innerHTML = '';

            let seats = [];

            const type =
                ticketType.value;

            if(type === 'Regular') {

                seats = regularSeats;

            } else if(type === 'VIP') {

                seats = vipSeats;

            } else if(type === 'VVIP') {

                seats = vvipSeats;
            }

            seats.forEach(seat => {

                if(bookedSeats.includes(seat)) {

                    return;
                }

                seatContainer.innerHTML += `

                <label class="seat-box">

                    <input
                        type="checkbox"
                        name="seat_number[]"
                        value="${seat}"
                    >

                    <span>${seat}</span>

                </label>

                `;
            });
        }

        ticketType.addEventListener(
            'change',
            loadSeats
        );

        // DARK MODE
        const darkBtn =
            document.getElementById(
                'darkModeBtn'
            );

        if(localStorage.getItem(
            'darkMode'
        ) === 'enabled') {

            document.body.classList.add(
                'dark-mode'
            );
        }

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