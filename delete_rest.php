<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$config = parse_ini_file('/var/www/private/db-config.ini');
if (!$config) {
    die("Failed to read database config file.");
}

if (isset($_GET['id']) && isset($_SESSION['email'])) {
    $id = (int) $_GET['id'];
    $name = $_GET["restaurantName"];

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
    $check_stmt = $conn->prepare("SELECT * FROM add_restaurant WHERE id = ?");
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();


    if ($result->num_rows === 0) {
        die("<script>alert('Error: Review ID does not exist.'); window.location.href='reviews.php';</script>");
    }
    $row = $result->fetch_assoc();
    $rName = $row["restaurantName"];
    $check_stmt->close();

    // DELETE statement
    $stmt2 = $conn->prepare("DELETE FROM restaurant_reviews WHERE name = ?");
    $stmt2->bind_param("s", $rName);
    $stmt2->execute();
    $stmt2->close();
    $stmt = $conn->prepare("DELETE FROM add_restaurant WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<script>alert('Restaurant deleted successfully!'); window.location.href='show_rest_created.php';</script>";
    } else {
        $error = $stmt->error;
        echo "<script>console.log('Error during deletion: $error');</script>";
        die("<script>alert('No matching record found or deletion failed. Please check for foreign key constraints.'); window.location.href='show_rest_created.php';</script>");
    }
    $stmt->close();

    $conn->close();
}
?>