<?php
session_start();

$restaurantName = $phoneNumber = $website = $address = $cuisine = $adminAdded = "";
$success = true;
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["restaurantName"])) {
        $errorMsg .= "Restaurant Name is required.<br>";
        $success = false;
    } else {
        $restaurantName = sanitize_input($_POST["restaurantName"]);
    }

    if (empty($_POST["address"])) {
        $errorMsg .= "Address is required.<br>";
        $success = false;
    } else {
        $address = sanitize_input($_POST["address"]);
    }
    $phoneNumber = isset($_POST['phone']) ? sanitize_input($_POST['phone']) : '';
    $website = isset($_POST['website']) ? sanitize_input($_POST['website']) : '';
    $cuisine = isset($_POST['cuisine']) ? sanitize_input($_POST['cuisine']) : '';
    $adminAdded = isset($_POST['admin_added']) ? sanitize_input($_POST['admin_added']) : '';
    if ($success) {
        saveRest();
    }
}
function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
function saveRest()
{
    global $restaurantName, $phoneNumber, $website, $address, $cuisine, $adminAdded, $errorMsg, $success;
    // Create database connection.
    $config = parse_ini_file('/var/www/private/db-config.ini');
    if (!$config) {
        $errorMsg = "Failed to read database config file.";
        $success = false;
    } else {
        $conn = new mysqli(
            $config['servername'],
            $config['username'],
            $config['password'],
            $config['dbname']
        );
        // Check connection
        if ($conn->connect_error) {
            $errorMsg = "Connection failed: " . $conn->connect_error;
            $success = false;
        }
        $stmt = $conn->prepare("INSERT INTO add_restaurant
(restaurantName, phone, website, address, cuisine, admin_added) VALUES (?, ?, ?, ?, ?, ?)");
        // Bind & execute the query statement:
        $stmt->bind_param("ssssss", $restaurantName, $phoneNumber, $website, $address, $cuisine, $adminAdded);
        if (!$stmt->execute()) {
            $errorMsg = "Execute failed: (" . $stmt->errno . ") " .
                $stmt->error;
            $success = false;
        }
        echo "<script>alert('Add restaurant request sent successfully!'); window.location.href='new_restaurants.php';</script>";


        $stmt->close();
    }
    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "inc/head.inc.php"; ?>
    <title>Restaurants Not in Page</title>
</head>

<body>
    <?php include "inc/nav.inc.php"; ?>
    <main class="container mt-5">
        <strong>
            <h1 class="text-center mb-4">Send a request to add restaurant</h1>
        </strong>
        <?php if (isset($_SESSION['email']) && $_SESSION['admin'] == "No"): ?>
            <div class="container mt-5">
                <form method="POST" class="mb-4">
                    <div class="mb-3">
                        <label class="form-label"><strong>Restaurant Name:</strong></label>
                        <input type="text" name="restaurantName" class="form-control" aria-label="Rname" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><strong>Website:</strong></label>
                        <input type="text" name="website" class="form-control" aria-label="web">
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><strong>Address:</strong></label>
                        <input type="text" name="address" class="form-control" aria-label="add" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><strong>Phone Number:</strong></label>
                        <input type="text" name="phone" class="form-control" aria-label="phone">
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><strong>Cuisine:</strong></label>
                        <input type="text" name="cuisine" class="form-control" aria-label="cusine">
                    </div>
                    <div class="mb-3">
                        <input type="hidden" name="admin_added" class="form-control" aria-label="admin_added" value="No">
                    </div>
                    <button type="submit" class="btn btn-success">Send request</button>
                </form>
            </div>
        <?php elseif (isset($_SESSION['email']) && $_SESSION['admin'] == "Yes"): ?>
            <h2 class="text-center">Admins cannot send requests.</h2>
        <?php elseif (!isset($_SESSION['email'])): ?>
            <a href="login.php"> <h2 class="text-center">Please log in to send requests.</h2></a>
        <?php endif; ?>
    </main>
</body>