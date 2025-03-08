<?php
session_start();
?>
<!-- can try to not allow past booking -->
<!-- try time interval by 5 min? -->
<!DOCTYPE html>
<html lang="en">
<?php include "inc/head.inc.php"; ?>

<body>
    <?php include "inc/nav.inc.php"; ?>
    <main class="container mt-5">
        <h1>Restaurant Booking</h1>
        <ul class="nav nav-tabs" id="bookingTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="new-booking-tab" data-bs-toggle="tab" data-bs-target="#new-booking"
                    type="button" role="tab">New Booking</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="view-bookings-tab" data-bs-toggle="tab" data-bs-target="#view-bookings"
                    type="button" role="tab">Previous Bookings</button>
            </li>
        </ul>
        <div class="tab-content" id="bookingTabsContent">
            <!-- New Booking Form -->
            <div class="tab-pane fade show active" id="new-booking" role="tabpanel">
                <form action="processbooking.php" method="POST" class="mt-3" style="width: 30%;">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="<?= htmlspecialchars($_SESSION['fname']) ?>" disabled>
                        <input type="hidden" name="name" value="<?= htmlspecialchars($_SESSION['fname']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="ph" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" name="ph"
                            value="<?= htmlspecialchars($_SESSION['ph']) ?>">
                        <!-- can try to autofill phone number -->
                    </div>
                    <div class="mb-3">
                        <label for="time" class="form-label">Restaurant Name</label>
                        <input required type="text" class="form-control" id="time" name="restaurantName" required>
                    </div>
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input required type="date" class="form-control" id="date" name="date" required>
                    </div>
                    <div class="mb-3">
                        <label for="time" class="form-label">Time</label>
                        <input required type="time" class="form-control" id="time" name="time" required>
                    </div>
                    <?php if (!isset($_SESSION['fname'])): ?>
                        <h5><small><a href="login.php">Please login to make booking.</a></small></h5>
                    <?php else: ?>

                        <button type="submit" class="btn btn-primary">Book Now</button>
                    <?php endif; ?>
                </form>
            </div>

            <!-- View Previous Bookings -->
            <div class="tab-pane fade" id="view-bookings" role="tabpanel">
                <div class="mt-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Restaurant Name</th>
                                <th>Date</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Ensure session is started and logged-in user's data is accessible
                            if (!isset($_SESSION['fname'])) {
                                echo "<tr><td colspan='4'>Please login to view your bookings.</td></tr>";
                                exit;
                            }

                            // Assuming session has 'email' or 'user_id' as identifiers
                            
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

                            $query = "SELECT id, name, restaurantName, date, time FROM bookings ORDER BY date DESC, time DESC";
                            $result = mysqli_query($conn, $query);

                            // Check if there are bookings
                            if ($result) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    // Check if the logged-in user is the one who made the booking
                                    if (isset($_SESSION['fname'])) {
                                        if ($_SESSION['fname'] == $row['name']) {
                                            echo "<tr>
                                    <td>" . htmlspecialchars($row['name']) . "</td>
                                    <td>" . htmlspecialchars($row['restaurantName']) . "</td>
                                    <td>" . htmlspecialchars($row['date']) . "</td>
                                    <td>" . htmlspecialchars($row['time']) . "</td>
                                    <td>
                                        <a href='edit_booking.php?id=" . htmlspecialchars($row['id']) . "'>
                                            <img src='images/editicon.jpg' width='60' height='50' alt='editicon'>
                                        </a>

                                        <a href='delete_booking.php?id=" . urlencode($row['id']) . "' onclick='return confirm(\"Are you sure?\")'>
                                            <img src='images/deleteicon.png' width='60' height='50' alt='deleteicon'>
                                        </a>
                                    </td>
                                    </tr>";
                                        }
                                    }
                                }
                            } else {
                                echo "<tr><td colspan='5'>No bookings available.</td></tr>";
                            }

                            mysqli_close($conn);
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>
<?php include "inc/footer.inc.php"; ?>

</html>