<?php
// Database connection
$config = parse_ini_file('/var/www/private/db-config.ini');
if (!$config) {
    die("Failed to read database config file.");
}
$conn = new mysqli(
    $config['servername'],
    $config['username'],
    $config['password'],
    $config['dbname']
);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phoneNumber = mysqli_real_escape_string($conn, $_POST['phoneNumber']);
    $restaurantName = mysqli_real_escape_string($conn, $_POST['restaurantName']);
    $date = $_POST['date'];
    $time = $_POST['time'];

    // Insert the new booking into the database
    $query = "INSERT INTO bookings (name, phoneNumber, restaurantName, date, time) 
              VALUES ('$name', '$phoneNumber', '$restaurantName', '$date', '$time')";
    
    if (mysqli_query($conn, $query)) {
        // Redirect back to the main page with a success message
        echo "<script>alert('Booking successful!'); window.location.href='booking.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    // Close connection
    mysqli_close($conn);
}
?>
