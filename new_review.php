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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST["name"]);
    $rating = intval($_POST["rating"]);
    $comment = htmlspecialchars($_POST["comment"]);

    if (!empty($name) && !empty($comment) && $rating >= 1 && $rating <= 5) {
        $stmt = $conn->prepare("INSERT INTO reviews (name, rating, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $name, $rating, $comment);
        $stmt->execute();
        $stmt->close();
        echo "<script>alert('Review submitted successfully!');</script>";
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
    <meta charset="UTF-8">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
     <main>
    <title>Write a Review</title>
    </main>
</head>
<body>
<?php
    include "inc/nav.inc.php";
    ?>
    <div class="container mt-5">
        <h2 class="text-center">Write a Review</h2>

        <!-- Review Form -->
        <form method="POST" class="mb-4">
            <div class="mb-3">
                <label class="form-label"><strong>Name:</strong></label>
                <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($_SESSION['fname']) ?>">
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
            <button type="submit" class="btn btn-primary">Submit Review</button>
        </form>

        <!-- Display Reviews -->
        
    </div>
    <?php
include "inc/footer.inc.php";
?>

</body>
</html>
