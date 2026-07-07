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
// CREATE TICKET PDF
$pdf = new FPDF('L', 'mm', array(130,80));

$pdf->AddPage();
$pdf->SetAutoPageBreak(false);
$pdf->SetMargins(4,4,4);

// OUTER BORDER
$pdf->SetDrawColor(70,70,70);
$pdf->SetLineWidth(0.5);
$pdf->Rect(3,3,124,74);

// ===============================
// HEADER
// ===============================

// Blue Header
$pdf->SetFillColor(79,70,229);
$pdf->Rect(3,3,124,15,'F');

// Logo (Font Awesome icon substitute)
$pdf->SetTextColor(255,255,255);
$pdf->SetFont('Arial','B',16);
$pdf->SetXY(6,7);
$pdf->Cell(60,5,'Pamoja Ticketing',0,0,'L');

$pdf->SetFont('Arial','',8);
$pdf->SetXY(6,12);
$pdf->Cell(60,4,'Electronic Event Ticket',0,0,'L');

// ===============================
// EVENT TITLE
// ===============================

$pdf->SetTextColor(0,0,0);

$pdf->SetFont('Arial','B',14);
$pdf->SetXY(6,22);
$pdf->Cell(72,7,$row['title'],0,1);

// ===============================
// TICKET DETAILS
// ===============================

$pdf->SetFont('Arial','',9);

$y = 31;

$pdf->SetXY(6,$y);
$pdf->Cell(30,5,'User Name:');
$pdf->SetFont('Arial','B',9);
$pdf->Cell(45,5,$row['fullname']);

$pdf->SetFont('Arial','',9);
$y += 6;

$pdf->SetXY(6,$y);
$pdf->Cell(30,5,'Ticket No:');
$pdf->SetFont('Arial','B',9);
$pdf->Cell(45,5,'TKT-'.$row['id']);

$pdf->SetFont('Arial','',9);
$y += 6;

$pdf->SetXY(6,$y);
$pdf->Cell(30,5,'Ticket Type:');
$pdf->SetFont('Arial','B',9);
$pdf->Cell(45,5,$row['ticket_type']);

$pdf->SetFont('Arial','',9);
$y += 6;

$pdf->SetXY(6,$y);
$pdf->Cell(30,5,'Seat Number:');
$pdf->SetFont('Arial','B',9);
$pdf->Cell(45,5,$row['seat_number']);

$pdf->SetFont('Arial','',9);
$y += 6;

$pdf->SetXY(6,$y);
$pdf->Cell(30,5,'Quantity:');
$pdf->SetFont('Arial','B',9);
$pdf->Cell(45,5,$row['quantity']);

$pdf->SetFont('Arial','',9);
$y += 6;

$pdf->SetXY(6,$y);
$pdf->Cell(30,5,'Event Date:');
$pdf->SetFont('Arial','B',9);
$pdf->Cell(45,5,$row['event_date']);

$pdf->SetFont('Arial','',9);
$y += 6;

$pdf->SetXY(6,$y);
$pdf->Cell(30,5,'Location:');
$pdf->SetFont('Arial','B',9);
$pdf->Cell(45,5,substr($row['location'],0,28));

// ===============================
// VALID BADGE
// ===============================

$pdf->SetFillColor(34,197,94);
$pdf->SetTextColor(255,255,255);

$pdf->SetXY(6,68);
$pdf->Cell(42,7,'VALID TICKET',0,0,'C',true);

// ===============================
// QR CODE
// ===============================

$qr = '../assets/qrcodes/'.$row['qr_code'];

if(file_exists($qr)){

    $pdf->Image($qr,88,22,30,30);
}

// QR Caption
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Arial','',8);

$pdf->SetXY(84,54);
$pdf->Cell(38,5,'Scan at Entrance',0,0,'C');

// ===============================
// FOOTER
// ===============================

$pdf->SetDrawColor(200,200,200);
$pdf->Line(4,64,126,64);

$pdf->SetFont('Arial','I',7);
$pdf->SetTextColor(120,120,120);

$pdf->SetXY(6,66);
$pdf->Cell(
    118,
    4,
    'Please present this ticket at the event entrance.',
    0,
    0,
    'C'
);



// DOWNLOAD PDF
$pdf->Output(
    'D',
    'Ticket_'.$ticket_id.'.pdf'
);

?>
```
