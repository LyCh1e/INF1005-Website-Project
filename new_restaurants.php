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

$sql = "SELECT COUNT(*) AS count FROM restaurant_reviews";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if ($row['count'] == 0) { // Check if record in database
    $searchApiKey = 'sKRNEjqvbwRWJQWxJXARmEs2';
    $searchUrl = "https://www.searchapi.io/api/v1/search?engine=google_maps&q=restaurants+in+Singapore&api_key=sKRNEjqvbwRWJQWxJXARmEs2";
    $searchResponse = file_get_contents($searchUrl);
    $searchData = json_decode($searchResponse, true);

    if (isset($searchData['local_results'])) {
        foreach ($searchData['local_results'] as $result) {
            $name = $result['title'] ?? 'N/A';
            $address = $result['address'] ?? 'N/A';
            $phone = $result['phone'] ?? 'N/A';
            $priceRange = $result['price'] ?? 'N/A';
            $rating = $result['rating'] ?? 0;
            $noOfReviews = $result['reviews'] ?? 'N/A';
            $reviewsLink = $result['reviews_link'] ?? 'N/A';
            $website = $result['website'] ?? 'N/A';
            $cuisine = $result['type'] ?? 'N/A';
            $openingHours = isset($result['open_hours']) ? json_encode($result['open_hours']) : 'N/A';
            $priceDescription = $result['price_description'] ?? 'N/A';

            $stmt = $conn->prepare("
                INSERT INTO restaurant_reviews (name, address, phone, priceRange, rating, noOfReviews, website, cuisine, openingHours, priceDescription, reviewsLink)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "sssdsssssss",
                $name,
                $address,
                $phone,
                $priceRange,
                $rating,
                $noOfReviews,
                $website,
                $cuisine,
                $openingHours,
                $priceDescription,
                $reviewsLink
            );

            $stmt->execute();
        }
    } else {
        echo "No restaurants found in Singapore from the API.";
    }
} else {
    // Data already exists in the database, no need to call API
}

$sql = "SELECT * FROM restaurant_reviews";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $restaurants = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $restaurants = [];
    echo "No records found in the database.";
}

if (isset($stmt)) {
    $stmt->close();
}
$restaurantFilter = "";
if (isset($_GET['restaurant']) && !empty($_GET['restaurant'])) {
    $restaurantFilter = trim($_GET['restaurant']);
}

if ($restaurantFilter) {
    $stmtr = $conn->prepare("SELECT * FROM reviews WHERE restaurantName = ? ORDER BY created_at DESC");
    $stmtr->bind_param("s", $restaurantFilter);
} else {
    $stmtr = $conn->prepare("SELECT * FROM reviews ORDER BY created_at DESC");
}

$stmtr->execute();
$resultr = $stmtr->get_result();
$reviews = $resultr->fetch_all(MYSQLI_ASSOC);

// Get average rating for selected restaurant
if ($restaurantFilter) {
    $avg_stmt = $conn->prepare("SELECT AVG(rating) as avgRating, AVG(restaurantPricing) as avgRP FROM reviews WHERE restaurantName = ?");
    $avg_stmt->bind_param("s", $restaurantFilter);
} else {
    $avg_stmt = $conn->prepare("SELECT AVG(rating) AS avgRating, AVG(restaurantPricing) as avgRP FROM reviews");
}

$avg_stmt->execute();
$avg_result = $avg_stmt->get_result();
$avgRow = $avg_result->fetch_assoc();
$average_rating = $avgRow['avgRating'] ?? 0;
$avgRP = floor($avgRow['avgRP'] ?? 0);
$pricingSymbols = str_repeat("$", $avgRP);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Restaurant Reviews in Singapore</title>
    <style>
        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding-left: 10px;
            padding-bottom: 10px;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .rating {
            color: #ffc107;
        }

        .review-text {
            font-style: italic;
            color: #555;
        }
    </style>
    <?php
    include "inc/head.inc.php";
    ?>
</head>

<body>
    <?php
    include "inc/nav.inc.php";
    ?>
    <div class="container mt-5">
        <h1 class="text-center mb-4" style="padding-top: 10px">Restaurant Reviews in Singapore</h1>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
            <?php if (!empty($restaurants)): ?>
                <?php foreach ($restaurants as $restaurant): ?>
                    <div class='col'>
                    <div class='card shadow-lg w-auto'>
                            <h2 class="card-title"><?= htmlspecialchars($restaurant['name']) ?></h2>
                            <p class="card-text"><i class="fas fa-map-marker-alt"></i> <a
                                    href="https://www.google.com/maps/search/?q=<?= urlencode($restaurant['address']) ?>"
                                    target="_blank"><?= htmlspecialchars($restaurant['address']) ?></a></p>
                            <p class="card-text"><i class="fas fa-phone"></i> <?= htmlspecialchars($restaurant['phone']) ?></p>
                            <p class="card-text"><i class="fas fa-utensils"></i> <?= htmlspecialchars($restaurant['cuisine']) ?>
                            </p>
                            <p class="card-text">
                                <?php if ($average_rating > 0): ?>
                                    <i class="fas fa-star"></i> Average Rating: <?= number_format($average_rating, 1) ?> ⭐
                                <p>
                                <i class="fas fa-money-bill"></i> Average Pricing: <?= $pricingSymbols; ?>
                                </p>

                            <?php else: ?>
                                <small>(No reviews yet)</small>
                            <?php endif; ?></p>
                            <p class="card-text"><i class="fas fa-clock"></i> <strong>Opening Hours:</strong></p>
                            <ul>
                                <?php
                                $openHours = json_decode($restaurant['openingHours'], true);
                                if ($openHours) {
                                    foreach ($openHours as $day => $hours) {
                                        echo "<li><strong>$day:</strong> $hours</li>";
                                    }
                                } else {
                                    echo "<li>Not available</li>";
                                }
                                ?>
                            </ul>
                            <p class="card-text"><i class="fas fa-globe"></i> <a
                                    href="<?= htmlspecialchars($restaurant['website']) ?>" target="_blank">Website</a></p>
                            <a href="reviews.php?restaurant=<?php echo urlencode($restaurant['name']); ?>"
                                class="btn d-flex justify-content-center"
                                style="background-color: rgb(0, 146, 131); color: white">View Reviews</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No restaurants found in the database.</p>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>