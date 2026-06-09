<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Pamoja Ticketing System</title>

    <link
        rel="icon"
        type="image/x-icon"
        href="assets/favicon.ico"
    >

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

            min-height:100vh;

            background:
                linear-gradient(
                    rgba(0,0,0,0.65),
                    rgba(0,0,0,0.65)
                ),
                url('assets/images/bg.jpg');

            background-size:cover;

            background-position:center;

            background-repeat:no-repeat;

            color:white;
        }

        /* NAVBAR */

        .navbar{

            width:100%;

            padding:20px 60px;

            display:flex;

            justify-content:space-between;

            align-items:center;

            flex-wrap:wrap;

            background:rgba(0,0,0,0.35);

            backdrop-filter:blur(10px);
        }

        .logo{

            font-size:28px;

            font-weight:700;

            color:white;
        }

        .nav-links{

            display:flex;

            gap:15px;

            flex-wrap:wrap;
        }

        .nav-links a{

            text-decoration:none;

            color:white;

            padding:10px 18px;

            border-radius:8px;

            transition:0.3s;

            font-weight:500;
        }

        .nav-links a:hover{

            background:#4f46e5;
        }

        /* HERO */

        .hero{

            min-height:85vh;

            display:flex;

            justify-content:center;

            align-items:center;

            text-align:center;

            padding:20px;
        }

        .hero-content{

            max-width:850px;
        }

        .hero-content h1{

            font-size:65px;

            margin-bottom:20px;

            line-height:1.2;
        }

        .hero-content p{

            font-size:20px;

            color:#e5e7eb;

            line-height:1.8;

            margin-bottom:35px;
        }

        /* BUTTONS */

        .hero-buttons{

            display:flex;

            justify-content:center;

            gap:20px;

            flex-wrap:wrap;
        }

        .btn{

            text-decoration:none;

            padding:15px 30px;

            border-radius:10px;

            font-size:16px;

            font-weight:600;

            transition:0.3s;
        }

        .btn-primary{

            background:#4f46e5;

            color:white;
        }

        .btn-primary:hover{

            background:#4338ca;

            transform:translateY(-3px);
        }

        .btn-secondary{

            background:white;

            color:#111827;
        }

        .btn-secondary:hover{

            background:#e5e7eb;

            transform:translateY(-3px);
        }

        /* FEATURES */

        .features{

            width:100%;

            padding:70px 40px;

            background:rgba(255,255,255,0.08);

            backdrop-filter:blur(10px);
        }

        .features-grid{

            display:grid;

            grid-template-columns:repeat(auto-fit,minmax(250px,1fr));

            gap:25px;

            max-width:1200px;

            margin:auto;
        }

        .feature-card{

            background:rgba(255,255,255,0.12);

            padding:35px 25px;

            border-radius:18px;

            text-align:center;

            transition:0.3s;
        }

        .feature-card:hover{

            transform:translateY(-5px);

            background:rgba(255,255,255,0.18);
        }

        .feature-card i{

            font-size:45px;

            margin-bottom:20px;

            color:#818cf8;
        }

        .feature-card h3{

            margin-bottom:15px;

            font-size:22px;
        }

        .feature-card p{

            color:#e5e7eb;

            line-height:1.7;
        }

        /* FOOTER */

        .footer{

            text-align:center;

            padding:25px;

            background:rgba(0,0,0,0.4);

            color:#d1d5db;
        }

        /* RESPONSIVE */

        @media(max-width:768px){

            .navbar{

                padding:20px;

                flex-direction:column;

                gap:15px;
            }

            .hero-content h1{

                font-size:42px;
            }

            .hero-content p{

                font-size:17px;
            }

            .features{

                padding:50px 20px;
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

            <a href="index.php">
                Home
            </a>

            <a href="auth/login.php">
                Login
            </a>

            <a href="auth/register.php">
                Register
            </a>

            <a href="user/events.php">
                Events
            </a>

            <a href="user/my_tickets.php">
                My Tickets
            </a>

        </div>

    </div>

    <!-- HERO -->

    <section class="hero">

        <div class="hero-content">

            <h1>

                Book Amazing Events
                With Ease

            </h1>

            <p>

                Pamoja Ticketing System helps you discover,
                book, and manage tickets for concerts,
                conferences, festivals, sports events,
                and more — all in one place.

            </p>

            <div class="hero-buttons">

                <a
                    href="user/events.php"
                    class="btn btn-primary"
                >

                    <i class="fa-solid fa-calendar-days"></i>

                    Explore Events

                </a>

                <a
                    href="auth/register.php"
                    class="btn btn-secondary"
                >

                    <i class="fa-solid fa-user-plus"></i>

                    Get Started

                </a>

            </div>

        </div>

    </section>

    <!-- FEATURES -->

    <section class="features">

        <div class="features-grid">

            <div class="feature-card">

                <i class="fa-solid fa-ticket"></i>

                <h3>
                    Easy Booking
                </h3>

                <p>
                    Book tickets online quickly and securely
                    from anywhere.
                </p>

            </div>

            <div class="feature-card">

                <i class="fa-solid fa-qrcode"></i>

                <h3>
                    QR Code Tickets
                </h3>

                <p>
                    Receive digital tickets with secure QR
                    code verification.
                </p>

            </div>

            <div class="feature-card">

                <i class="fa-solid fa-mobile-screen-button"></i>

                <h3>
                    M-Pesa Payments
                </h3>

                <p>
                    Pay conveniently using M-Pesa mobile
                    payment integration.
                </p>

            </div>

            <div class="feature-card">

                <i class="fa-solid fa-calendar-check"></i>

                <h3>
                    Live Events
                </h3>

                <p>
                    Discover exciting upcoming events and
                    reserve your seats instantly.
                </p>

            </div>

        </div>

    </section>

    <!-- FOOTER -->

    <div class="footer">

        © <?php echo date('Y'); ?>
        Pamoja Ticketing System.
        All Rights Reserved.

    </div>

</body>

</html>