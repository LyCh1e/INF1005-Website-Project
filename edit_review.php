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

// Get review ID
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    die("Invalid review ID. Please provide a valid review ID.");
}

$review_id = intval($_GET['id']);

// Fetch the review to edit
$stmt = $conn->prepare("SELECT * FROM reviews WHERE id = ?");
$stmt->bind_param("i", $review_id);
$stmt->execute();
$result = $stmt->get_result();
$review = $result->fetch_assoc();

if (!$review) {
    die("Review not found.");
}
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);
    $price = intval($_POST['restaurantPricing']);
    $update_stmt = $conn->prepare("UPDATE reviews SET rating = ?, comment = ?, restaurantPricing = ?, edited_at = NOW() WHERE id = ?");
    $update_stmt->bind_param("isii", $rating, $comment, $price, $review_id);

    if ($update_stmt->execute()) {
        echo "<script>alert('Review updated successfully!'); window.location.href='reviews.php?restaurant=" . htmlspecialchars($_GET['restaurantName']) . "';</script>";
        exit;
    } else {
        echo "<script>alert('Failed to update review. Please try again.');</script>";
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
</head>

<body>
    <?php
    include "inc/nav.inc.php";
    ?>
    <main>
        <div class="container mt-5">
            <h2 class="text-center">Edit Your Review</h2>
            <!-- Edit review form -->
            <form method="POST" class="mb-4">
                <div class="mb-3">
                    <label class="form-label"><strong>Name:</strong></label>
                    <input type="text" name="name" class="form-control"
                        value="<?= htmlspecialchars($_SESSION['fname']) ?>" disabled>
                    <input type="hidden" name="name" value="<?= htmlspecialchars($_SESSION['fname']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>Restaurant Name:</strong></label>
                    <input type="text" name="restaurantName" class="form-control"
                        value="<?= htmlspecialchars($review['restaurantName']) ?>" disabled>
                    <input type="hidden" name="restaurantName"
                        value="<?= htmlspecialchars($review['restaurantName']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>Rating:</strong> (1-5)</label>
                    <select name="rating" class="form-select" required>
                        <option value="5" <?= ($review['rating'] == 5) ? 'selected' : '' ?>>★★★★★</option>
                        <option value="4" <?= ($review['rating'] == 4) ? 'selected' : '' ?>>★★★★☆</option>
                        <option value="3" <?= ($review['rating'] == 3) ? 'selected' : '' ?>>★★★☆☆</option>
                        <option value="2" <?= ($review['rating'] == 2) ? 'selected' : '' ?>>★★☆☆☆</option>
                        <option value="1" <?= ($review['rating'] == 1) ? 'selected' : '' ?>>★☆☆☆☆</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>Pricing:</strong></label>
                    <select name="restaurantPricing" class="form-select" required>
                        <option value="3" <?= ($review['restaurantPricing'] == 3) ? 'selected' : '' ?>>$$$</option>
                        <option value="2" <?= ($review['restaurantPricing'] == 2) ? 'selected' : '' ?>>$$</option>
                        <option value="1" <?= ($review['restaurantPricing'] == 1) ? 'selected' : '' ?>>$</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>Comment:</strong></label>
                    <textarea name="comment" class="form-control" rows="4"
                        required><?= htmlspecialchars($review['comment']) ?></textarea>
                </div>
                <button type="submit" class="btn btn-success">Save Changes</button>
                <a href="reviews.php?restaurant=<?= urlencode($review['restaurantName']) ?>"
                    class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </main>
    <?php
    include "inc/footer.inc.php";
    ?>

</body>

</html>