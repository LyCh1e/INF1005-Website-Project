<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

// Get booking ID
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    die("Invalid booking ID. Please provide a valid booking ID.");
}

$booking_id = intval($_GET['id']);

// Fetch the review to edit
$stmt = $conn->prepare("SELECT * FROM bookings WHERE id = ?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

if (!$booking) {
    die("Booking not found.");
}
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ph = trim($_POST["ph"]);
    $date = trim($_POST['date']);
    $time = trim($_POST['time']);

    $update_stmt = $conn->prepare("UPDATE bookings SET date = ?, time = ?, phoneNumber = ? WHERE id = ?");
    $update_stmt->bind_param("sssi", $date, $time, $ph, $booking_id);

    if ($update_stmt->execute()) {
        echo "<script>alert('Booking updated successfully!'); window.location.href='booking.php';</script>";
        exit;
    } else {
        echo "<script>alert('Failed to update booking. Please try again.');</script>";
    }
    $update_stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    include "inc/head.inc.php";
    ?>
    <title>Edit Booking</title>
</head>

<body>
    <?php
    include "inc/nav.inc.php";
    ?>
    <main>
        <div class="container mt-5">
            <h1 class="text-center">Edit Your Booking</h1>
            <!-- Edit booking form -->
             <?php if($_SESSION['admin'] == "No"):?>
            <form method="POST" class="mb-4">
                <div class="mb-3">
                    <label class="form-label"><strong>Name:</strong></label>
                    <input type="text" name="name" class="form-control" aria-label="name"
                        value="<?= htmlspecialchars($_SESSION['fname']) ?>" disabled>
                    <input type="hidden" name="name" value="<?= htmlspecialchars($_SESSION['fname']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>Email:</strong></label>
                    <input type="text" name="email" class="form-control" aria-label="email"
                        value="<?= htmlspecialchars($_SESSION['email']) ?>" disabled>
                    <input type="hidden" name="email" value="<?= htmlspecialchars($_SESSION['email']) ?>">
                </div>
                <div class="mb-3">
                    <label for="ph" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" name="ph" aria-label="ph"
                        value="<?= htmlspecialchars($booking['phoneNumber']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>Restaurant Name:</strong></label>
                    <input type="text" name="restaurantName" class="form-control" aria-label="Rname"
                        value="<?= htmlspecialchars($booking['restaurantName']) ?>" disabled>
                    <input type="hidden" name="restaurantName"
                        value="<?= htmlspecialchars($booking['restaurantName']) ?>">
                </div>
                <div class="mb-3">
                    <label for="date" class="form-label">Date</label>
                    <input required type="date" class="form-control" aria-label="date" name="date" min="<?= date('Y-m-d'); ?>"
                        value="<?= htmlspecialchars($booking['date']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="time" class="form-label">Time</label>
                    <input required type="time" class="form-control" aria-label="time" name="time"
                        value="<?= htmlspecialchars($booking['time']) ?>" required>
                </div>

                <button type="submit" class="btn btn-success">Save Changes</button>
                <a href="booking.php" class="btn btn-secondary">Cancel</a>
            </form>
            <?php else:?>
                <h2 class="text-center">Admins cannot make booking!</h2>
            <?php endif;?>
        </div>
    </main>
    <?php
    include "inc/footer.inc.php";
    ?>

</body>

</html>