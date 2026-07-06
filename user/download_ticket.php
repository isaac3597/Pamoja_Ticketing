<?php

require('../fpdf/fpdf.php');

session_start();

include '../config/db.php';

// CHECK LOGIN
if(!isset($_SESSION['user_id'])) {

    header('Location: ../auth/login.php');

    exit();
}

$user_id = $_SESSION['user_id'];

$ticket_id = $_GET['id'];

// FETCH TICKET
$sql = "SELECT
            tickets.*,
            users.fullname,
            events.title,
            events.location,
            events.event_date
        FROM tickets
        JOIN users
        ON tickets.user_id = users.id
        JOIN events
        ON tickets.event_id = events.id
        WHERE tickets.id='$ticket_id'
        AND tickets.user_id='$user_id'";

$result = mysqli_query($conn, $sql);

$row = mysqli_fetch_assoc($result);

// CREATE SMALL RECTANGLE PDF
$pdf = new FPDF(
    'L',
    'mm',
    array(130,80)
);

$pdf->AddPage();
$pdf->SetAutoPageBreak(false);
$pdf->SetMargins(3,3,3);
// BORDER
$pdf->SetDrawColor(0,0,0);

$pdf->Rect(3,3,114,64);

// TITLE
$pdf->SetFont('Arial','B',13);
$pdf->Cell(114,7,$row['title'],0,1,'C');

// RESET COLOR
$pdf->SetTextColor(0,0,0);

// DETAILS
$pdf->SetFont('Arial','',9);

// NAME
$pdf->SetX(6);

$pdf->Cell(
    60,
    5,
    'Name: '.$row['fullname'],
    0,
    1
);

// CLASS
$pdf->SetX(6);

$pdf->Cell(
    60,
    5,
    'Class: '.$row['ticket_type'],
    0,
    1
);

// SEAT
$pdf->SetX(6);

$pdf->Cell(
    60,
    5,
    'Seat: '.$row['seat_number'],
    0,
    1
);

// QUANTITY
$pdf->SetX(6);

$pdf->Cell(
    60,
    5,
    'Qty: '.$row['quantity'],
    0,
    1
);

// DATE
$pdf->SetX(6);

$pdf->Cell(
    60,
    5,
    'Date: '.$row['event_date'],
    0,
    1
);

// LOCATION
$pdf->SetX(6);

$location = substr($row['location'],0,35);

$pdf->SetX(6);
$pdf->Cell(60,5,'Location: '.$location,0,1);

// AUTHORISED
$pdf->SetFont('Arial','B',11);

$pdf->SetTextColor(0,120,0);

$pdf->SetXY(6,46);

$pdf->Cell(
    60,
    6,
    'ACCESS: AUTHORISED',
    0,
    1
);

// QR CODE
$qr =
    '../assets/qrcodes/'.$row['qr_code'];

if(file_exists($qr)) {

    $pdf->Image(
        $qr,
        78,
        18,
        30,
        30
    );
}

// FOOTER
$pdf->SetFont('Arial','I',8);

$pdf->SetTextColor(100,100,100);

$pdf->SetXY(6,56);

$pdf->Cell(
    100,
    5,
    'Pamoja Events Ticketing Management System ',
    0,
    1
);

// DOWNLOAD PDF
$pdf->Output(
    'D',
    'Ticket_'.$ticket_id.'.pdf'
);

?>
```
