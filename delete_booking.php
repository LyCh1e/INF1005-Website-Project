<?php
session_start();
$config = parse_ini_file('/var/www/private/db-config.ini');
if (!$config) {
    die("Failed to read database config file.");
}

if (isset($_GET['id']) && isset($_SESSION['fname'])) {
    $id = (int) $_GET['id'];
    
    $conn = new mysqli(
        $config['servername'], 
        $config['username'], 
        $config['password'], 
        $config['dbname']
    );

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $check_stmt = $conn->prepare("SELECT * FROM bookings WHERE id = ?");
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows === 0) {
        die("<script>alert('Error: Booking ID does not exist.'); window.location.href='booking.php';</script>");
    }
    $row = $result->fetch_assoc();    
    $check_stmt->close();

    // DELETE statement
    $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        echo "<script>alert('Booking deleted successfully!'); window.location.href='booking.php';</script>";
    } else {
        $error = $stmt->error;
        die("<script>alert('No matching record found or deletion failed. Please check for foreign key constraints.'); window.location.href='booking.php';</script>");
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('Unauthorized access.'); window.location.href='booking.php';</script>";
}
?>
