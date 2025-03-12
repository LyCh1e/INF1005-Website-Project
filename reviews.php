<?php
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

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if a specific restaurant was selected
$restaurantFilter = "";
if (isset($_GET['restaurant']) && !empty($_GET['restaurant'])) {
    $restaurantFilter = trim($_GET['restaurant']);
}

// Prepare SQL query
if ($restaurantFilter) {
    $stmt = $conn->prepare("SELECT * FROM reviews WHERE restaurantName = ? ORDER BY created_at DESC");
    $stmt->bind_param("s", $restaurantFilter);
} else {
    $stmt = $conn->prepare("SELECT * FROM reviews ORDER BY created_at DESC");
}

$stmt->execute();
$result = $stmt->get_result();
$reviews = $result->fetch_all(MYSQLI_ASSOC);

// Get average rating for selected restaurant
if ($restaurantFilter) {
    $avg_stmt = $conn->prepare("SELECT AVG(rating) AS average_rating FROM reviews WHERE restaurantName = ?");
    $avg_stmt->bind_param("s", $restaurantFilter);
} else {
    $avg_stmt = $conn->prepare("SELECT AVG(rating) AS average_rating FROM reviews");
}

$avg_stmt->execute();
$avg_result = $avg_stmt->get_result();
$average_rating = $avg_result->fetch_assoc()['average_rating'] ?? 0;

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<?php include "inc/head.inc.php"; ?>

<body>
    <?php include "inc/nav.inc.php"; ?>
    <main class="container mt-5">
        <h1>
            <?php if ($restaurantFilter): ?>
                <?= htmlspecialchars($restaurantFilter) ?>
            <?php else: ?>
                All Reviews
            <?php endif; ?>
            
            <?php if ($average_rating > 0): ?>
                (<span><small><?= number_format($average_rating, 1) ?> ⭐</small></span>)
            <?php else: ?>
                <small>(No reviews yet)</small>
            <?php endif; ?>
        </h1>

        <div style="display: inline-block; margin-right: 20px;">
            <?php if (isset($_SESSION['fname'])): ?>
                <p>
                    <a href="new_review.php?restaurant=<?= urlencode($_GET['restaurant']) ?>" class='btn' style='background-color: rgb(0, 146, 131); color: white'>Write a Review!</a>
                </p>
            <?php else: ?>
                <div>
                    <p>
                        Want to write a review?
                        <a href="login.php" style="color:rgb(0, 146, 131);">Please Login!</a>
                    </p>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $review): ?>
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

                        <?php if (isset($_SESSION['fname']) && $_SESSION['fname'] == $review['name']): ?>
                            <div class="icon-container" style="position: absolute; top: 20px; right: 20px;">
                            <p>
                            <a href="edit_review.php?id=<?= $review['id'] ?>&restaurantName=<?= urlencode($review['restaurantName']) ?>">
                            <i class="fas fa-edit" style="font-size: 35px; color: green;"></i></a>                            
                            <a href="delete_review.php?id=<?= $review['id'] ?>&restaurantName=<?= urlencode($review['restaurantName']) ?>">
                            <i class="fas fa-trash-alt" style="font-size: 35px; color: dark grey;"></i>
                            </a>
                            
                            </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No reviews found for this restaurant.</p>
        <?php endif; ?>
    </main>
</body>

<?php include "inc/footer.inc.php"; ?>

</html>
