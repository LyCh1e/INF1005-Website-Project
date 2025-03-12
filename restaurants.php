<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch unique restaurants with average rating
$query = "SELECT restaurantName, AVG(rating) as avgRating FROM reviews GROUP BY restaurantName";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<?php
include "inc/head.inc.php";
?>

<body>
    <?php include "inc/nav.inc.php"; ?> <!-- Include navigation bar -->

    <main class="container mt-5">
        <h2 class="text-center mb-4">Restaurant Reviews</h2>
        <div style="display: inline-block; margin-right: 20px;">
            <?php if (isset($_SESSION['fname'])): ?>
                <p>
                    <a href="new_review.php" class='btn btn-primary'>Write a Review!</a>
                </p>
            <?php else: ?>
                <div>
                    <p>
                        Want to write a review?
                        <a href="login.php">Please Login!</a>
                    </p>
                </div>
            <?php endif; ?>
        </div>

        <div class="row row-cols-1 row-cols-2 row-cols-3 row-cols-4 g-3">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $restaurantName = htmlspecialchars($row['restaurantName']);
                    $avgRating = number_format($row['avgRating'], 1);

                    echo "<div class='col'>
                            <div class='card shadow-lg w-auto'>
                                <div class='card-body text-center'>
                                    <h5 class='card-title'>$restaurantName</h5>
                                    <p class='card-text'>⭐️ Average Rating: <strong>$avgRating</strong></p>
                                    <a href='reviews.php?restaurant=" . urlencode($restaurantName) . "' class='btn btn-primary'>View Reviews</a>
                                </div>
                            </div>
                        </div>";
                }
            } else {
                echo "<p class='text-center'>No restaurants found.</p>";
            }
            ?>
        </div>
    </main>

    <?php include "inc/footer.inc.php"; ?> <!-- Include footer -->
</body>

</html>

<?php
$conn->close();
?>