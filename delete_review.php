<?php
session_start();
$config = parse_ini_file('/var/www/private/db-config.ini');
if (!$config) {
    die("Failed to read database config file.");
}

if (isset($_GET['id']) && isset($_SESSION['fname'])) {
    $id = (int) $_GET['id'];
    echo "<script>console.log('Received ID: " . $id . "');</script>";
    
    $conn = new mysqli(
        $config['servername'], 
        $config['username'], 
        $config['password'], 
        $config['dbname']
    );

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if ID exists in database
    $check_stmt = $conn->prepare("SELECT * FROM reviews WHERE id = ?");
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows === 0) {
        die("<script>alert('Error: Review ID does not exist.'); window.location.href='reviews.php';</script>");
    }
    $row = $result->fetch_assoc();
    echo "<script>console.log('Found review: " . print_r($row, true) . "');</script>";
    
    $check_stmt->close();

    // DELETE statement
    $stmt = $conn->prepare("DELETE FROM reviews WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        echo "<script>alert('Review deleted successfully!'); window.location.href='reviews.php?restaurant=" . htmlspecialchars($_GET['restaurantName']) . "';</script>";
    } else {
        $error = $stmt->error; 
        echo "<script>console.log('Error during deletion: $error');</script>"; 
        die("<script>alert('No matching record found or deletion failed. Please check for foreign key constraints.'); window.location.href='reviews.php';</script>");
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('Unauthorized access.'); window.location.href='reviews.php?restaurant=" . htmlspecialchars($_GET['restaurantName']) . "';</script>";
}
?>
