<!DOCTYPE html>
<html lang="en">
    <?php include "inc/head.inc.php"; ?>
    <title>Restaurant Booking</title>

    <body>
        <?php include "inc/nav.inc.php"; ?>
        <main class="container mt-5">
            <h1>Booking Page</h1>
            <ul class="nav nav-tabs" id="bookingTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="new-booking-tab" data-bs-toggle="tab" data-bs-target="#new-booking" type="button" role="tab">New Booking</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="view-bookings-tab" data-bs-toggle="tab" data-bs-target="#view-bookings" type="button" role="tab">View Bookings</button>
                </li>
            </ul>
            <div class="tab-content" id="bookingTabsContent">
                <!-- New Booking Form -->
                <div class="tab-pane fade show active" id="new-booking" role="tabpanel">
                    <form action="process_booking.php" method="POST" class="mt-3" style="width: 30%;">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input required type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="time" class="form-label">Phone Number</label>
                            <input required type="text" class="form-control" id="tel" name="phoneNumber" required>
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
                        <button type="submit" class="btn btn-primary">Book Now</button>
                    </form>
                </div>

                <!-- View Previous Bookings -->
                <div class="tab-pane fade" id="view-bookings" role="tabpanel">
                    <div class="mt-3">
                        <h3>Booked Restaurants</h3>
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
                                $query = "SELECT name, restuarant_name, date, time FROM bookings ORDER BY date DESC, time DESC";
                                $result = mysqli_query($conn, $query);
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>
                                            <td>{$row['name']}</td>
                                            <td>{$row['Restaurant Name']}</td>
                                            <td>{$row['date']}</td>
                                            <td>{$row['time']}</td>
                                          </tr>";
                                }
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
