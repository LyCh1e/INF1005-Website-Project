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

$searchTerm = "";
$restaurants = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['search'])) {
    $searchTerm = trim($_POST['search']);
    $searchWildcard = "%" . $searchTerm . "%";
    $stmt = $conn->prepare("SELECT name, address, phone, cuisine, website FROM restaurant_reviews WHERE name LIKE ? OR address LIKE ? OR cuisine LIKE ?");
    $stmt->bind_param("sss", $searchWildcard, $searchWildcard, $searchWildcard);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $restaurants[] = $row;
    }

    $stmt->close();
} else {
    $query = "SELECT name, address, phone, cuisine, website FROM restaurant_reviews";
    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        $restaurants[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Reviews in Singapore</title>
    <link rel="stylesheet" href="styles.css">
    <?php
    include "inc/head.inc.php";
    ?>
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
        .review-text {
            font-style: italic;
            color: #555;
        }
        .action-buttons{
            padding-right: 10px;
        }
    </style>
</head>

<body>
    <header>
        <?php
        include "inc/nav.inc.php";
        ?>
    </header>
    
    <main class="container mt-5">
        <h1 class="text-center mb-4" style="padding-top: 15px">Restaurant Reviews in Singapore</h1>
        
        <section class="search-section">
            <div class="d-flex justify-content-center">
                <form method="POST" action="" class="d-flex">
                    <div class="input-group">
                        <label for="search-input" class="visually-hidden">Search for a restaurant</label>
                        <input type="text" id="search-input" name="search" class="form-control me-2" 
                               placeholder="Search for a restaurant"
                               value="<?php echo htmlspecialchars($searchTerm ?? ''); ?>">
                        <button type="submit" class="btn" aria-label="Search" style="background: none; border: none;">
                            <i class="fas fa-search" style="font-size: 30px; color: rgb(0, 78, 74);"></i>
                        </button>
                    </div>
                </form>
            </div>
        </section>
        
        <section class="add-review-section">
            <?php if (isset($_SESSION['fname'])): ?>
                <h5>
                    <p>Can't find the restaurant? <a href="new_review.php" style="color: rgb(0, 78, 74);">Click here to
                            add the review!</a></p>
                </h5>
            <?php else: ?>
                <div class="login-prompt">
                    <h5>
                        <p>Want to write a review? <a href="login.php">Please Login!</a></p>
                    </h5>
                </div>
            <?php endif; ?>
        </section>
        
        <section class="restaurant-listings">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
                <?php if (!empty($restaurants)): ?>
                    <?php foreach ($restaurants as $restaurant): ?>
                        <?php
                        $avg_stmt = $conn->prepare("SELECT AVG(rating) as avgRating, AVG(restaurantPricing) as avgRP FROM reviews WHERE restaurantName = ?");
                        $avg_stmt->bind_param("s", $restaurant['name']);
                        $avg_stmt->execute();
                        $avg_result = $avg_stmt->get_result();
                        $avgRow = $avg_result->fetch_assoc();
                        $average_rating = $avgRow['avgRating'] ?? 0;
                        $avgRP = floor($avgRow['avgRP'] ?? 0);
                        $pricingSymbols = str_repeat("$", $avgRP); ?>
                        <article class="col">
                            <div class="card shadow-lg w-auto">
                                <header>
                                    <h2 class="card-title"><?= htmlspecialchars($restaurant['name']) ?></h2>
                                </header>
                                <figure>
                                    <iframe title="Restaurant location map" style="width: 100%; height: 150px; margin: 20px 0;" 
                                            loading="lazy" referrerpolicy="no-referrer-when-downgrade" 
                                            src="https://www.google.com/maps/embed/v1/place?key=AIzaSyD74Wi1gaUSYAwobsBDQj4K_6DUvZi1-W0&q=<?= urlencode($restaurant['name']) ?>">
                                    </iframe>
                                </figure>
                                <div class="restaurant-details">
                                    <address>
                                        <p class="card-text"><i class="fas fa-map-marker-alt" aria-hidden="true"></i> <a
                                                href="https://www.google.com/maps/search/?q=<?= urlencode($restaurant['address']) ?>"
                                                target="_blank"><?= htmlspecialchars($restaurant['address']) ?></a></p>
                                        <p class="card-text"><i class="fas fa-phone" aria-hidden="true"></i> <?= htmlspecialchars($restaurant['phone']) ?></p>
                                    </address>
                                    <p class="card-text"><i class="fas fa-utensils" aria-hidden="true"></i> <?= htmlspecialchars($restaurant['cuisine']) ?></p>
                                    
                                    <div class="rating-info">
                                        <?php if ($average_rating > 0): ?>
                                            <p class="card-text">
                                                <i class="fas fa-star" aria-hidden="true"></i> 
                                                <span class="rating">Average Rating: <?= number_format($average_rating, 1) ?> <i class="fas fa-star" style="color: gold" aria-hidden="true"></i></span>
                                            </p>
                                            <p class="card-text">
                                                <i class="fas fa-money-bill" aria-hidden="true"></i> 
                                                <span class="pricing">Average Pricing: <?= $pricingSymbols; ?></span>
                                            </p>
                                        <?php else: ?>
                                            <p class="card-text"><small>(No reviews yet)</small></p>
                                        <?php endif; ?>
                                        <p class="card-text"><i class="fas fa-globe" aria-hidden="true"></i> <a
                                        href="<?= htmlspecialchars($restaurant['website']) ?>" target="_blank">Website</a></p>
                                        <br>
                                    </div>

                                    <div class="action-buttons">
                                        <a href="reviews.php?restaurant=<?php echo urlencode($restaurant['name']); ?>"
                                            class="btn d-flex justify-content-center"
                                            style="background-color: rgb(0, 78, 74); color: white">View Reviews</a>
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No restaurants found in the database.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
    <?php include "inc/footer.inc.php"; ?>
</body>

</html>

<?php
$conn->close(); ?>