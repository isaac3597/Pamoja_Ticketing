<?php
session_start();
include '../config/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'organizer') {
    header('Location: ../auth/login.php');
    exit();
}

$error = "";

if(isset($_POST['create'])) {

    $title = $_POST['title'];
    $organizer = $_POST['organizer'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $event_date = $_POST['event_date'];

    $regular_price = $_POST['regular_price'];
    $vip_price = $_POST['vip_price'];
    $vvip_price = $_POST['vvip_price'];

    $regular_tickets = $_POST['regular_tickets'];
    $vip_tickets = $_POST['vip_tickets'];
    $vvip_tickets = $_POST['vvip_tickets'];

    $organizer_id = $_SESSION['user_id'];

    // IMAGE UPLOAD
    $image_name = $_FILES['image']['name'];
    $tmp_name = $_FILES['image']['tmp_name'];

    $folder = "../assets/uploads/" . $image_name;

    move_uploaded_file($tmp_name, $folder);

    // INSERT EVENT
    $sql = "INSERT INTO events(
                organizer_id,
                title,
                organizer,
                description,
                location,
                event_date,
                regular_price,
                vip_price,
                vvip_price,
                regular_tickets,
                vip_tickets,
                vvip_tickets,
                image
            )
            VALUES(
                '$organizer_id',
                '$title',
                '$organizer',
                '$description',
                '$location',
                '$event_date',
                '$regular_price',
                '$vip_price',
                '$vvip_price',
                '$regular_tickets',
                '$vip_tickets',
                '$vvip_tickets',
                '$image_name'
            )";

    if(mysqli_query($conn, $sql)) {

        header('Location: manage_events.php');
        exit();

    } else {

        $error = "Failed to create event";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create Event</title>

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

        /* FORM CONTAINER */

        .container{
            max-width:900px;
            margin:40px auto;
            background:white;
            padding:35px;
            border-radius:20px;
            box-shadow:0 5px 20px rgba(0,0,0,0.08);
        }

        .container h2{
            margin-bottom:25px;
            color:#333;
            text-align:center;
        }

        .error{
            background:#ffe5e5;
            color:#d8000c;
            padding:12px;
            border-radius:8px;
            margin-bottom:20px;
            text-align:center;
        }

        /* FORM */

        .form-grid{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:20px;
        }

        .full-width{
            grid-column:1 / 3;
        }

        .input-group{
            display:flex;
            flex-direction:column;
        }

        .input-group label{
            margin-bottom:8px;
            color:#555;
            font-weight:500;
        }

        .input-group input,
        .input-group textarea{
            padding:14px;
            border:1px solid #ccc;
            border-radius:10px;
            outline:none;
            font-size:14px;
            transition:0.3s;
        }

        .input-group textarea{
            resize:none;
            height:120px;
        }

        .input-group input:focus,
        .input-group textarea:focus{
            border-color:#4f46e5;
            box-shadow:0 0 5px rgba(79,70,229,0.3);
        }

        .section-title{
            margin-top:20px;
            margin-bottom:15px;
            color:#4f46e5;
            font-size:18px;
            font-weight:600;
        }

        .submit-btn{
            width:100%;
            padding:15px;
            border:none;
            background:#4f46e5;
            color:white;
            font-size:16px;
            border-radius:10px;
            cursor:pointer;
            margin-top:25px;
            transition:0.3s;
            font-weight:600;
        }

        .submit-btn:hover{
            background:#4338ca;
        }

        /* DARK MODE */

        .dark-mode{
            background:#111827;
            color:white;
        }

        .dark-mode .container{
            background:#1f2937;
        }

        .dark-mode .container h2,
        .dark-mode .section-title,
        .dark-mode .input-group label{
            color:white;
        }

        .dark-mode .input-group input,
        .dark-mode .input-group textarea{
            background:#374151;
            color:white;
            border:1px solid #555;
        }

        .dark-mode .navbar{
            background:#111827;
        }

        /* RESPONSIVE */

        @media(max-width:768px){

            .form-grid{
                grid-template-columns:1fr;
            }

            .full-width{
                grid-column:1;
            }

            .navbar{
                padding:20px;
                flex-direction:column;
                gap:15px;
            }

            .container{
                margin:20px;
                padding:25px;
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

            <a href="../index.php">
                <i class="fa-solid fa-house"></i> Home
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

    <!-- FORM -->

    <div class="container">

        <h2>Create New Event</h2>

        <?php if($error != "") { ?>

            <div class="error">
                <?php echo $error; ?>
            </div>

        <?php } ?>

        <form method="POST" enctype="multipart/form-data">

            <div class="form-grid">

                <div class="input-group full-width">

                    <label>Event Banner</label>

                    <input
                        type="file"
                        name="image"
                        accept="image/*"
                        required
                    >

                </div>

                <div class="input-group">

                    <label>Organizer Name</label>

                    <input
                        type="text"
                        name="organizer"
                        placeholder="Organizer Name"
                        required
                    >

                </div>

                <div class="input-group">

                    <label>Event Title</label>

                    <input
                        type="text"
                        name="title"
                        placeholder="Event Title"
                        required
                    >

                </div>

                <div class="input-group full-width">

                    <label>Description</label>

                    <textarea
                        name="description"
                        placeholder="Write event description here..."
                    ></textarea>

                </div>

                <div class="input-group">

                    <label>Location</label>

                    <input
                        type="text"
                        name="location"
                        placeholder="Event Location"
                    >

                </div>

                <div class="input-group">

                    <label>Event Date</label>

                    <input
                        type="date"
                        name="event_date"
                        required
                    >

                </div>

            </div>

            <!-- PRICING -->

            <div class="section-title">
                Ticket Pricing
            </div>

            <div class="form-grid">

                <div class="input-group">

                    <label>Regular Ticket Price</label>

                    <input
                        type="number"
                        step="0.01"
                        name="regular_price"
                        placeholder="Regular Price"
                        required
                    >

                </div>

                <div class="input-group">

                    <label>VIP Ticket Price</label>

                    <input
                        type="number"
                        step="0.01"
                        name="vip_price"
                        placeholder="VIP Price"
                        required
                    >

                </div>

                <div class="input-group full-width">

                    <label>VVIP Ticket Price</label>

                    <input
                        type="number"
                        step="0.01"
                        name="vvip_price"
                        placeholder="VVIP Price"
                        required
                    >

                </div>

            </div>

            <!-- CAPACITY -->

            <div class="section-title">
                Ticket Capacity
            </div>

            <div class="form-grid">

                <div class="input-group">

                    <label>Regular Tickets</label>

                    <input
                        type="number"
                        name="regular_tickets"
                        placeholder="Regular Capacity"
                        required
                    >

                </div>

                <div class="input-group">

                    <label>VIP Tickets</label>

                    <input
                        type="number"
                        name="vip_tickets"
                        placeholder="VIP Capacity"
                        required
                    >

                </div>

                <div class="input-group full-width">

                    <label>VVIP Tickets</label>

                    <input
                        type="number"
                        name="vvip_tickets"
                        placeholder="VVIP Capacity"
                        required
                    >

                </div>

            </div>

            <button type="submit" name="create" class="submit-btn">
                <i class="fa-solid fa-plus"></i> Create Event
            </button>

        </form>

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