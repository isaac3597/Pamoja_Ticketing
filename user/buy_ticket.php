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

    // MULTIPLE SEATS
    $seat_numbers =
        $_POST['seat_number'] ?? [];

    // VALIDATE SEATS
    if(count($seat_numbers) != $quantity) {

        $error =
            "Please select seats equal to ticket quantity";

    } else {

        // CONVERT ARRAY TO STRING
        $seat_number =
            implode(',', $seat_numbers);

        // DETERMINE PRICE + AVAILABLE TICKETS
        if($ticket_type == "Regular") {

            $price =
                $event['regular_price'];

            $available =
                $event['regular_tickets'];

        } elseif($ticket_type == "VIP") {

            $price =
                $event['vip_price'];

            $available =
                $event['vip_tickets'];

        } else {

            $price =
                $event['vvip_price'];

            $available =
                $event['vvip_tickets'];
        }

        // TOTAL PRICE
        $total_price =
            $price * $quantity;

        // CHECK AVAILABLE TICKETS
        if($quantity > $available) {

            $error =
                "Not enough tickets available";

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

                // GET TICKET ID
                $ticket_id =
                    mysqli_insert_id($conn);

                // GENERATE PASS CODE
                $pass_code =
                    "PASS-" .
                    rand(100000,999999);

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

                // QR LIBRARY
                include '../phpqrcode/qrlib.php';

                // QR FILE
                $file_name =
                    'ticket_'.$ticket_id.'.png';

                $file_path =
                    '../assets/qrcodes/'.$file_name;

                // GENERATE QR
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
<html>

<head>

    <title>Buy Ticket</title>

    <link
        rel="stylesheet"
        href="../assets/style.css"
    >

</head>

<body>

<div class="container">

    <!-- NAVBAR -->
    <div class="navbar">

        <a href="../index.php">
            Home
        </a>

        <a href="events.php">
            Events
        </a>

        <a href="my_tickets.php">
            My Tickets
        </a>

        <a href="../auth/logout.php">
            Logout
        </a>

    </div>

    <h1>Buy Ticket</h1>

    <!-- SUCCESS -->
    <?php if(isset($success)) { ?>

        <div class="success">
            <?php echo $success; ?>
        </div>

    <?php } ?>

    <!-- ERROR -->
    <?php if(isset($error)) { ?>

        <div class="error">
            <?php echo $error; ?>
        </div>

    <?php } ?>

    <!-- EVENT CARD -->
    <div class="event-card">

        <img
            src="../assets/uploads/<?php echo $event['image']; ?>"
            class="event-image"
        >

        <h2>
            <?php echo $event['title']; ?>
        </h2>

        <p>
            <?php echo $event['description']; ?>
        </p>

        <p>
            <strong>Location:</strong>
            <?php echo $event['location']; ?>
        </p>

        <p>
            <strong>Date:</strong>
            <?php echo $event['event_date']; ?>
        </p>

        <p>
            <strong>Regular Tickets:</strong>
            <?php echo $event['regular_tickets']; ?>
        </p>

        <p>
            <strong>VIP Tickets:</strong>
            <?php echo $event['vip_tickets']; ?>
        </p>

        <p>
            <strong>VVIP Tickets:</strong>
            <?php echo $event['vvip_tickets']; ?>
        </p>

    </div>

    <!-- FORM -->
    <form method="POST">

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

        <label>M-Pesa Number</label>

        <input
            type="text"
            name="phone"
            placeholder="254712345678"
            required
        >

        <label>Number of Tickets</label>

        <input
            type="number"
            id="quantity"
            name="quantity"
            min="1"
            required
        >

        <!-- SEAT SELECTION -->
        <label>Select Seats</label>

        <div
            class="seat-container"
            id="seatContainer"
        >

        </div>

        <!-- TOTAL -->
        <label>Total Amount</label>

        <input
            type="hidden"
            name="amount"
            id="amount"
        >

        <input
            type="text"
            id="total"
            readonly
        >

        <button
            type="submit"
            name="buy"
        >
            Pay with M-Pesa
        </button>

    </form>

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

// CALCULATE TOTAL
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

    totalInput.value =
        "KSH " + total;

    document.getElementById(
        'amount'
    ).value = total;
}

// EVENTS
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

// SEAT GROUPS
// GENERATE SEATS DYNAMICALL
// GENERATE SEATS DYNAMICALLY
function generateSeats(prefix, total) {

    let seats = [];

    for(let i = 1; i <= total; i++) {

        seats.push(prefix + i);
    }

    return seats;
}

// TOTAL SEATS FROM DATABASE
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

        // SKIP BOOKED
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

            ${seat}

        </label>

        `;
    });
}

// LOAD WHEN TYPE CHANGES
ticketType.addEventListener(
    'change',
    loadSeats
);

</script>

</body>
</html>
```
