<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
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

// Fetch all reviews
$result = $conn->query("SELECT * FROM reviews ORDER BY created_at DESC");
$reviews = $result->fetch_all(MYSQLI_ASSOC);
$sql = "SELECT AVG(rating) AS average_rating FROM reviews";
$avg_review = $conn->query($sql);

// Check if the query was successful and fetch the result
if ($avg_review && $row = $avg_review->fetch_assoc()) {
    $average_rating = $row['average_rating'];
} else {
    $average_rating = 0; // In case there are no reviews
}
$conn->close();
?>

<!DOCTYPE html>

<html lang="en">
<?php
include "inc/head.inc.php";
?>

<body>
    <?php
    include "inc/nav.inc.php";
    ?>
    <main class="container mt-5">
        <h1>
            Reviews
            <?php if ($average_rating > 0): ?>
                (<span><small><?= number_format($average_rating, 1) ?> ⭐</small></span>)
            <?php else: ?>
                <small>(No reviews yet)</small>
            <?php endif; ?>
        </h1>
        <div style="display: inline-block; margin-right: 20px;">
            <?php if (isset($_SESSION['fname'])): ?>
                <p>
                    <a href="new_review.php" class="btn btn-primary">Write a Review!</a>
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
        <?php if (!empty($reviews)): ?>

            <?php foreach ($reviews as $review): ?>
                <?php if (isset($_SESSION['fname'])): ?>
                    <?php if ($_SESSION['fname'] == $review['name']): ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($review['name']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($review['restaurantName']) ?></p>
                                <p class="card-text"><?= str_repeat("⭐", $review['rating']) ?></p>
                                <p class="card-text"><?= htmlspecialchars($review['comment']) ?></p>
                                <p class="text-muted">Posted on <?= $review['created_at'] ?></p>
                                <?php if (!is_null($review['edited_at'])): ?>
                                    <p class="text-muted"><small>Edited on <?= $review['edited_at'] ?></small></p>
                                <?php endif; ?>
                                <p>
                                    <a href="edit_review.php?id=<?= $review['id'] ?>" class="btn btn-primary">Edit Review</a>

                                </p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($review['name']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($review['restaurantName']) ?></p>
                                <p class="card-text"><?= str_repeat("⭐", $review['rating']) ?></p>
                                <p class="card-text"><?= htmlspecialchars($review['comment']) ?></p>
                                <p class="text-muted">Posted on <?= $review['created_at'] ?></p>
                                <?php if (!is_null($review['edited_at'])): ?>
                                    <p class="text-muted"><small>Edited on <?= $review['edited_at'] ?></small></p>
                                <?php endif; ?>

                            </div>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($review['name']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($review['restaurantName']) ?></p>
                            <p class="card-text"><?= str_repeat("⭐", $review['rating']) ?></p>
                            <p class="card-text"><?= htmlspecialchars($review['comment']) ?></p>
                            <p class="text-muted">Posted on <?= $review['created_at'] ?></p>
                            <?php if (!is_null($review['edited_at'])): ?>
                                <p class="text-muted"><small>Edited on <?= $review['edited_at'] ?></small></p>
                            <?php endif; ?>

                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

        <?php else: ?>
            <p>No reviews yet. Be the first to write one!</p>
        <?php endif; ?>
    </main>



</body>
<?php
include "inc/footer.inc.php";
?>

</html>