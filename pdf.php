<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
$config = parse_ini_file('/var/www/private/db-config.ini');
if (!$config) {
    die("Failed to read database config file.");
} else {
    $conn = new mysqli(
        $config['servername'],
        $config['username'],
        $config['password'],
        $config['dbname']
    );
}

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_SESSION['fname']) && isset($_GET['id'])) {
    $fname = $_SESSION['fname'];
    $booking_id = $_GET['id'];
    $sql = "SELECT * FROM bookings WHERE id = '$booking_id'";
    $result = $conn->query($sql);
    $booking_details = "";

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $booking_details .= "<p><strong>Restaurant Name: </strong>" . $row['restaurantName'] . "</p>";
        $booking_details .= "<p><strong>Booking under: </strong>" .$row['phoneNumber'] ."</p>";
        $booking_details .= "<p><strong>Date: </strong>" . $row['date'] . "</p>";
        $booking_details .= "<p><strong>Time: </strong>" . $row['time'] . "</p>";
    } else {
        $booking_details = "<p>No booking found!</p>";
    }
}

require_once('TCPDF-main/tcpdf.php'); 

// Create new PDF
$pdf = new TCPDF();
$pdf->AddPage();

// PDF Content
$html = '
<div style="text-align:center">
    <h1><strong>Gastronome\'s Guide</strong></h1>
<p>Where all your restaurant and foodie needs are satiated</p>
<h2>Booking Confirmation</h2>
<p>Below is your booking information, '. $fname .'</p>
'. $booking_details.'
</div>';
// Add HTML content to PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Output PDF (Download)
$pdf->Output("order_confirmation.pdf", "I");

?>