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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST["name"]);
    $rating = intval($_POST["rating"]);
    $restaurantName = htmlspecialchars($_POST["restaurantName"]);
    $comment = htmlspecialchars($_POST["comment"]);
    $price = htmlspecialchars($_POST["restaurantPricing"]);

    if (!empty($name) && !empty($comment) && $rating >= 1 && $rating <= 5) {
        $stmt = $conn->prepare("INSERT INTO reviews (`name`, `restaurantName`, `rating`, `comment`, `restaurantPricing`) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisi", $name, $restaurantName, $rating, $comment, $price);
        $stmt->execute();
        $stmt->close();
        if (isset($_GET['restaurant'])){
            echo "<script>alert('Review submitted successfully!');window.location.href='reviews.php?restaurant=" . htmlspecialchars($_GET['restaurant']) . "';</script>";
        }
        else{
            echo "<script>alert('Review submitted successfully!');window.location.href='restaurants.php'</script>";
        }
    } else {
        echo "<script>alert('Please fill all fields correctly.');</script>";
    }
}

// Fetch all reviews
$result = $conn->query("SELECT * FROM reviews ORDER BY created_at DESC");
$reviews = $result->fetch_all(MYSQLI_ASSOC);
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
        <?php if (isset($_GET['restaurant'])): ?>
        <div class="container mt-5">
            <h2 class="text-center">Write a Review</h2>
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
                        value="<?= htmlspecialchars($_GET['restaurant'] )?>" disabled>
                        <input type="hidden" name="restaurantName" value="<?= htmlspecialchars($_GET['restaurant']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>Rating:</strong> (1-5)</label>
                    <select name="rating" class="form-select" required>
                        <option value="5">★★★★★</option>
                        <option value="4">★★★★☆</option>
                        <option value="3">★★★☆☆</option>
                        <option value="2">★★☆☆☆</option>
                        <option value="1">★☆☆☆☆</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>Pricing:</strong></label>
                    <select name="restaurantPricing" class="form-select" required>
                        <option value="3">$$$</option>
                        <option value="2">$$</option>
                        <option value="1">$</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>Comment:</strong></label>
                    <textarea name="comment" class="form-control" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn btn-success">Submit Review</button>
            </form>
        </div>
        <?php else: ?>
            <div class="container mt-5">
            <h2 class="text-center">Write a Review</h2>
            <form method="POST" class="mb-4">
                <div class="mb-3">
                    <label class="form-label"><strong>Name:</strong></label>
                    <input type="text" name="name" class="form-control"
                        value="<?= htmlspecialchars($_SESSION['fname']) ?>" disabled>
                    <input type="hidden" name="name" value="<?= htmlspecialchars($_SESSION['fname']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>Restaurant Name:</strong></label>
                    <input type="text" name="restaurantName" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>Rating:</strong> (1-5)</label>
                    <select name="rating" class="form-select" required>
                        <option value="5">★★★★★</option>
                        <option value="4">★★★★☆</option>
                        <option value="3">★★★☆☆</option>
                        <option value="2">★★☆☆☆</option>
                        <option value="1">★☆☆☆☆</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>Comment:</strong></label>
                    <textarea name="comment" class="form-control" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn btn-success">Submit Review</button>
            </form>
        </div>
        <?php endif; ?>
    </main>
    <?php
    include "inc/footer.inc.php";
    ?>

</body>

</html>