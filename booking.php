<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "inc/head.inc.php"; ?>
    <title>Restaurant Booking</title>
</head>
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
                <?php if (isset($_SESSION['fname'])): ?>
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
                    </div>
                    <div class="mb-3">
                        <label for="restaurantName" class="form-label">Restaurant Name</label>
                        <select required class="form-control" id="restaurantName" name="restaurantName">
                            <option value="" selected disabled>Select a restaurant</option>
                            <?php
                            $config = parse_ini_file('/var/www/private/db-config.ini');
                            if ($config) {
                                $conn = new mysqli(
                                    $config['servername'],
                                    $config['username'],
                                    $config['password'],
                                    $config['dbname']
                                );

                                if ($conn->connect_error) {
                                    die("Connection failed: " . $conn->connect_error);
                                }

                                // Fetch restaurant names
                                $query = "SELECT DISTINCT restaurantName FROM reviews ORDER BY restaurantName ASC";
                                $result = mysqli_query($conn, $query);

                                if ($result) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<option value='" . htmlspecialchars($row['restaurantName']) . "'>" . htmlspecialchars($row['restaurantName']) . "</option>";
                                    }
                                }

                                mysqli_close($conn);
                            } else {
                                echo "<option disabled>Database error</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input required type="date" class="form-control" id="date" name="date" min="<?= date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="time" class="form-label">Time</label>
                        <input required type="time" class="form-control" id="time" name="time" required>
                    </div>
                    <button type="submit" class="btn" style='background-color: rgb(0, 146, 131); color: white'>Book Now</button>
                    <?php else: ?>
                        <h5><small><a href="login.php">Please login to make booking.</a></small></h5>
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
                            if (!isset($_SESSION['fname'])) {
                                echo "<tr><td colspan='4'>Please login to view bookings.</td></tr>";
                                exit;
                            }                            
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

                            if ($result) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    // If logged-in user is the one who made the booking
                                    if (isset($_SESSION['fname'])) {
                                        if ($_SESSION['fname'] == $row['name']) {
                                            echo "<tr>
                                    <td>" . htmlspecialchars($row['name']) . "</td>
                                    <td>" . htmlspecialchars($row['restaurantName']) . "</td>
                                    <td>" . htmlspecialchars($row['date']) . "</td>
                                    <td>" . htmlspecialchars($row['time']) . "</td>
                                    <td>
                                        <a href='edit_booking.php?id=" . htmlspecialchars($row['id']) . "'>
                                            <i class='fas fa-edit' style='font-size: 35px; color: green;'></i></a>
                                        <a href='delete_booking.php?id=" . urlencode($row['id']) . "' onclick='return confirm(\"Are you sure?\")'>
                                            <i class='fas fa-trash-alt' style='font-size: 35px; color: dark grey;'></i></a>
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