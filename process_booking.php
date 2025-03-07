<?php
// Initialize variables with empty values
$name = $phoneNumber = $restaurantName = $date = $time = "";
$errors = [];

// Process only POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if coming from confirmation page
    if (isset($_POST["confirm"]) && $_POST["confirm"] == "yes") {
        // Retrieve data from POST
        $name = trim($_POST["name"]);
        $phoneNumber = trim($_POST["phoneNumber"]);
        $restaurantName = trim($_POST["restaurantName"]);
        $date = $_POST["date"];
        $time = $_POST["time"];
        
        // Prepare SQL statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO bookings (name, phone_number, restuarant_name, date, time) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $phoneNumber, $restaurantName, $date, $time);
        
        // Execute the statement
        if ($stmt->execute()) {
            // Redirect to a success page or booking page with success message
            header("Location: booking.php?success=1");
            exit();
        } else {
            // Handle database error
            $errors[] = "Database error: " . $conn->error;
        }
        
        $stmt->close();
    } else {
        // First submission from booking form - capture data for review
        $name = trim($_POST["name"]);
        $phoneNumber = trim($_POST["phoneNumber"]);
        $restaurantName = trim($_POST["restaurantName"]);
        $date = $_POST["date"];
        $time = $_POST["time"];
        
        // Basic validation
        if (empty($name)) {
            $errors[] = "Name is required";
        }
        
        if (empty($phoneNumber)) {
            $errors[] = "Phone number is required";
        }
        
        if (empty($restaurantName)) {
            $errors[] = "Restaurant name is required";
        }
        
        if (empty($date)) {
            $errors[] = "Date is required";
        }
        
        if (empty($time)) {
            $errors[] = "Time is required";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <?php include "inc/head.inc.php"; ?>
    <title>Confirm Restaurant Booking</title>
    
    <body>
        <?php include "inc/nav.inc.php"; ?>
        <main class="container mt-5">
            <header>
                <h1>Confirm Your Booking</h1>
            </header>
            
            <?php if (!empty($errors)): ?>
                <section aria-labelledby="error-title" role="alert" class="alert alert-danger">
                    <h2 id="error-title" class="visually-hidden">Error Messages</h2>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </section>
                <nav aria-label="Form navigation">
                    <a href="booking.php" class="btn btn-primary" role="button">Back to Booking Form</a>
                </nav>
            <?php else: ?>
                <article class="card">
                    <header class="card-header bg-primary text-white">
                        <h2>Booking Details</h2>
                    </header>
                    <section class="card-body">
                        <dl>
                            <div class="row mb-3">
                                <dt class="col-md-3">Name:</dt>
                                <dd class="col-md-9"><?php echo htmlspecialchars($name); ?></dd>
                            </div>
                            <div class="row mb-3">
                                <dt class="col-md-3">Phone Number:</dt>
                                <dd class="col-md-9"><?php echo htmlspecialchars($phoneNumber); ?></dd>
                            </div>
                            <div class="row mb-3">
                                <dt class="col-md-3">Restaurant:</dt>
                                <dd class="col-md-9"><?php echo htmlspecialchars($restaurantName); ?></dd>
                            </div>
                            <div class="row mb-3">
                                <dt class="col-md-3">Date:</dt>
                                <dd class="col-md-9"><time datetime="<?php echo htmlspecialchars($date); ?>"><?php echo htmlspecialchars($date); ?></time></dd>
                            </div>
                            <div class="row mb-3">
                                <dt class="col-md-3">Time:</dt>
                                <dd class="col-md-9"><time datetime="<?php echo htmlspecialchars($time); ?>"><?php echo htmlspecialchars($time); ?></time></dd>
                            </div>
                        </dl>
                    </section>
                    <footer class="card-footer">
                        <div class="d-flex justify-content-between">
                            <!-- Form for confirming booking -->
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" aria-label="Confirm booking form">
                                <input type="hidden" name="name" value="<?php echo htmlspecialchars($name); ?>">
                                <input type="hidden" name="phoneNumber" value="<?php echo htmlspecialchars($phoneNumber); ?>">
                                <input type="hidden" name="restaurantName" value="<?php echo htmlspecialchars($restaurantName); ?>">
                                <input type="hidden" name="date" value="<?php echo htmlspecialchars($date); ?>">
                                <input type="hidden" name="time" value="<?php echo htmlspecialchars($time); ?>">
                                <input type="hidden" name="confirm" value="yes">
                            </form>
                            
                            <!-- Back button to edit information -->
                            <nav aria-label="Form navigation">
                                <a id="confirm-booking" href="booking.php" type="submit" class="btn btn-success" role="button">Confirm</a>
                                <a id="cancel-booking" href="booking.php" class="btn btn-secondary" role="button">Cancel</a>
                            </nav>
                        </div>
                    </footer>
                </article>
            <?php endif; ?>
        </main>
        
        <?php include "inc/footer.inc.php"; ?>
    </body>
</html>