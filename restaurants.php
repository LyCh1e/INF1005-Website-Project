<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

$searchTerm = "";
$restaurants = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['search'])) {
    $searchTerm = trim($_POST['search']);
    $stmt = $conn->prepare("SELECT restaurantName, AVG(rating) as avgRating, AVG(restaurantPricing) as avgRP FROM reviews WHERE restaurantName LIKE ? GROUP BY restaurantName");
    $searchWildcard = "%" . $searchTerm . "%";
    $stmt->bind_param("s", $searchWildcard);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $restaurants[] = $row;
    }

    $stmt->close();
} else {
    $query = "SELECT restaurantName, AVG(rating) as avgRating, AVG(restaurantPricing) as avgRP FROM reviews GROUP BY restaurantName";
    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        $restaurants[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "inc/head.inc.php"?>
    <title>Restaurant Reviews</title>
    
</head>
<body>
    <?php include "inc/nav.inc.php"; ?>

    <main class="container mt-5">
        <h1 class="text-center mb-4">Restaurant Reviews</h2>
        <div class="d-flex justify-content-center">
            <form method="POST" action="#" class="d-flex">
                <script>document.querySelector("form").setAttribute("action", "")</script>
                <div class="input-group">
                    <input type="text" name="search" class="form-control me-2" placeholder="Search for a restaurant"
                        value="<?php echo htmlspecialchars($searchTerm ?? ''); ?>">
                    <button type="submit" class="btn" style="background: none; border: none;" aria-label="Submit search button">
                        <i class="fas fa-search" style="font-size: 30px; color: rgb(0, 146, 131);"></i>
                    </button>
                </div>
            </form>
        </div>
        <div style="display: inline-block; margin-right: 20px;">
            <?php if (isset($_SESSION['fname'])): ?>
                <h2 style="font-size: 24px">
                    Can't find the restaurant? 
                    <a href="new_review.php" style="color: rgb(0, 146, 131);">Click here to add the review!</a>
                </h2>
            <?php else: ?>
                <div style="padding: 10px">
                    <h2 style="font-size: 24px">
                        Want to write a review? <a href="login.php">Please Login!</a>
                    </h2>               
                </div>
            <?php endif; ?>
        </div>

        <!-- Restaurant List -->
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
            <?php if (!empty($restaurants)): ?>
                <?php foreach ($restaurants as $row): ?>
                    <?php
                    $restaurantName = htmlspecialchars($row['restaurantName']);
                    $avgRating = number_format($row['avgRating'], 1);
                    $avgRP = floor($row['avgRP']);
                    $pricingSymbols = str_repeat("$", $avgRP);
                    ?>
                    <div class='col'>
                        <div class='card shadow-lg w-auto'>
                            <div class='card-body text-center'>
                                <h5 class='card-title'><?php echo $restaurantName; ?></h5>
                                <p class='card-text'>Average Rating: <strong><?php echo $avgRating; ?> ⭐️</strong></p>
                                <p class='card-text'>Average Pricing: <strong><?php echo $pricingSymbols; ?></strong></p>
                                <a href='reviews.php?restaurant=<?php echo urlencode($restaurantName); ?>'
                                    class='btn d-flex justify-content-center'
                                    style='background-color: #00766B; color: white'>View Reviews</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class='text-center text-danger'>No restaurants found.</p>
            <?php endif; ?>
        </div>
    </main>

    <?php include "inc/footer.inc.php"; ?> 
</body>

</html>

<?php $conn->close(); ?>