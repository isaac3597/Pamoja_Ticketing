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
            events.event_date
        FROM tickets
        JOIN events
        ON tickets.event_id = events.id
        WHERE tickets.user_id='$user_id'
        ORDER BY tickets.id DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>

<head>

    <title>My Tickets</title>

    <link rel="stylesheet" href="../assets/style.css">

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

        <button id="darkModeBtn">
            🌙
        </button>

    </div>
    <br><br>


    <h1>My Purchased Tickets</h1>

    <?php if(mysqli_num_rows($result) > 0) { ?>

        <?php while($row = mysqli_fetch_assoc($result)) { ?>

            <div class="event-card">

                <!-- EVENT TITLE -->
                <h2>
                    <?php echo $row['title']; ?>
                </h2>

                <!-- EVENT DESCRIPTION -->
                <p>
                    <?php echo $row['description']; ?>
                </p>

                <!-- LOCATION -->
                <p>
                    <strong>Location:</strong>
                    <?php echo $row['location']; ?>
                </p>

                <!-- EVENT DATE -->
                <p>
                    <strong>Date:</strong>
                    <?php echo $row['event_date']; ?>
                </p>

                <!-- TICKET CLASS -->
                <p>
                    <strong>Ticket Class:</strong>
                    <?php echo $row['ticket_type']; ?>
                </p>

                <!-- QUANTITY -->
                <p>
                    <strong>Quantity:</strong>
                    <?php echo $row['quantity']; ?>
                </p>

                <!-- TOTAL PAID -->
                <p>
                    <strong>Total Paid:</strong>
                    KSH <?php echo $row['total_price']; ?>
                </p>

                <!-- ACCESS PASS -->
                <p>
                    <strong>Access Pass:</strong>
                    AUTHORISED
                </p>
                <br><br>

<a
    href="download_ticket.php?id=<?php echo $row['id']; ?>"
>

    <button
    style="
        padding:8px 18px;
        font-size:13px;
        width:auto;
        border-radius:8px;
        background:#2563eb;
        color:white;
        border:none;
        cursor:pointer;
    "
>
    Download Ticket PDF
</button>
</a>
                <!-- QR CODE -->
                <img
                    src="../assets/qrcodes/<?php echo $row['qr_code']; ?>"
                    width="200"
                    style="
                        margin-top:20px;
                        border-radius:10px;
                    "
                >

            </div>

        <?php } ?>

    <?php } else { ?>

        <!-- NO TICKETS -->
        <div class="event-card">

            <h2>
                No Tickets Found
            </h2>

            <p>
                You have not purchased any tickets yet.
            </p>

            <a href="events.php">

                <button>
                    Browse Events
                </button>

            </a>

        </div>

    <?php } ?>

</div>

<!-- DARK MODE SCRIPT -->
<script>

const darkBtn =
    document.getElementById('darkModeBtn');

// LOAD SAVED MODE
if(localStorage.getItem('darkMode') === 'enabled') {

    document.body.classList.add('dark-mode');
}

// DARK MODE BUTTON
if(darkBtn) {

    darkBtn.addEventListener('click', () => {

        document.body.classList.toggle('dark-mode');

        // SAVE MODE
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
}

</script>

</body>
</html>