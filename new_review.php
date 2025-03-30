<?php
session_start();

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
if ($_SESSION['admin'] == "No") {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = htmlspecialchars($_POST["name"]);
        $email = htmlspecialchars($_POST["email"]);
        $rating = intval($_POST["rating"]);
        $restaurantName = htmlspecialchars(string: $_POST["restaurantName"]);
        $comment = htmlspecialchars($_POST["comment"]);
        $price = htmlspecialchars($_POST["restaurantPricing"]);

        if (!empty($name) && !empty($comment) && $rating >= 1 && $rating <= 5) {
            // Set time to SGT
            date_default_timezone_set('Asia/Singapore');
            $currentTime = new DateTime();
            $currentTime = $currentTime->format('Y-m-d H:i:s');
            $stmt = $conn->prepare("INSERT INTO reviews (name, email, restaurantName, rating, comment, restaurantPricing, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssisis", $name, $email, $restaurantName, $rating, $comment, $price, $currentTime);
            if ($stmt->execute()) {
                echo "<script>alert('Review submitted successfully!');window.location.href='reviews.php?restaurant=" . htmlspecialchars($_GET['restaurant']) . "';</script>";
            } else {
                echo "<script>alert('Error submitting review. Please try again.');</script>";
            }

            $stmt->close();
        }
    }
} else {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $restaurantName = htmlspecialchars(string: $_POST["restaurantName"]);
        $address = isset($_POST["address"]) ? htmlspecialchars($_POST["address"]) : "";
        $phone = isset($_POST["phone"]) ? htmlspecialchars($_POST["phone"]) : "";
        $website = isset($_POST["website"]) ? htmlspecialchars($_POST["website"]) : "";
        $cuisine = isset($_POST["cuisine"]) ? htmlspecialchars($_POST["cuisine"]) : "";

        $noOfReviews = 1;
        $priceDescription = "1";
        $reviewsLink = "1";
        $openingHours = "1";
        $price = 0;
        $rating = 0;
        $stmtCheck = $conn->prepare("SELECT id, rating, noOfReviews FROM restaurant_reviews WHERE name = ?");
        $stmtCheck->bind_param("s", $restaurantName);
        $stmtCheck->execute();
        $result = $stmtCheck->get_result();
        $stmtCheck->close();

        if ($result->num_rows > 0) {
            // Restaurant exists, update rating and review count
            $row = $result->fetch_assoc();
            $newNoOfReviews = intval($row['noOfReviews']) + 1;
            $newRating = (floatval($row['rating']) * intval($row['noOfReviews']) + $rating) / $newNoOfReviews;

            $stmtUpdate = $conn->prepare("UPDATE restaurant_reviews 
                                              SET rating = ?, noOfReviews = ? 
                                              WHERE name = ?");
            $stmtUpdate->bind_param("dis", $newRating, $newNoOfReviews, $restaurantName);
            if ($stmtUpdate->execute()) {
                echo "<script>alert('Restaurant exists!');window.location.href='new_restaurants.php'</script>";
            } else {
                echo "<script>alert('Error updating restaurant review. Please try again.');</script>";
            }
            $stmtUpdate->close();
        } else {
            // Insert new restaurant
            $stmt2 = $conn->prepare("INSERT INTO restaurant_reviews 
                    (name, address, phone, priceRange, rating, noOfReviews, reviewsLink, website, cuisine, openingHours, priceDescription)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt2->bind_param(
                "sssdsisssss",
                $restaurantName,
                $address,
                $phone,
                $price,
                $rating,
                $noOfReviews,
                $reviewsLink,
                $website,
                $cuisine,
                $openingHours,
                $priceDescription
            );
            if ($stmt2->execute()) {
                echo "<script>alert('Restaurant created successfully!');window.location.href='new_restaurants.php'</script>";
            } else {
                echo "<script>alert('Error creating new restaurant. Please try again.');</script>";
            }
            $stmt2->close();

        }
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
    <title>New Review Form</title>
</head>

<body>
    <?php
    include "inc/nav.inc.php";
    ?>
    <main>
        <?php if (isset($_GET['restaurant'])): ?>
            <div class="container mt-5">
                <h1 class="text-center">Write a Review</h1>
                <?php if ($_SESSION['admin'] == "Yes"): ?>
                    <h2 class="text-center">Admins cannot write review!</h2>
                <?php else: ?>
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
                            <label class="form-label"><strong>Restaurant Name:</strong></label>
                            <input type="text" name="restaurantName" class="form-control" aria-label="Rname"
                                value="<?= htmlspecialchars($_GET['restaurant']) ?>" disabled>
                            <input type="hidden" name="restaurantName" value="<?= htmlspecialchars($_GET['restaurant']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><strong>Rating:</strong> (1-5)</label>
                            <select name="rating" class="form-select" aria-label="rating" required>
                                <option value="5">★★★★★</option>
                                <option value="4">★★★★</option>
                                <option value="3">★★★</option>
                                <option value="2">★★</option>
                                <option value="1">★</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><strong>Pricing:</strong></label>
                            <select name="restaurantPricing" class="form-select" aria-label="pricing" required>
                                <option value="3">$$$</option>
                                <option value="2">$$</option>
                                <option value="1">$</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><strong>Comment:</strong></label>
                            <textarea name="comment" class="form-control" rows="4" aria-label="comment" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Submit Review</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <?php if (!isset($_GET['restaurant']) && $_SESSION['admin'] == "Yes"): ?>
            <div class="container mt-5">
                <h1 class="text-center">Add new restaurant</h1>
                <form method="POST" class="mb-4">
                    <div class="mb-3">
                        <label class="form-label"><strong>Restaurant Name:</strong></label>
                        <input type="text" name="restaurantName" class="form-control" aria-label="Rname" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><strong>Website:</strong></label>
                        <input type="text" name="website" class="form-control" aria-label="web" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><strong>Address:</strong></label>
                        <input type="text" name="address" class="form-control" aria-label="add" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><strong>Phone Number:</strong></label>
                        <input type="text" name="phone" class="form-control" aria-label="phone" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><strong>Cuisine:</strong></label>
                        <input type="text" name="cuisine" class="form-control" aria-label="cusine" required>
                    </div>
                    <button type="submit" class="btn btn-success">Add Restaurant</button>
                </form>
            </div>
        <?php endif; ?>
    </main>
    <?php
    include "inc/footer.inc.php";
    ?>

</body>

</html>