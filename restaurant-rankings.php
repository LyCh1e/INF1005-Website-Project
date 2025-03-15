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

// Get filter parameters
$priceFilter = isset($_GET['price']) ? intval($_GET['price']) : 0;
$sortOrder = isset($_GET['sort']) && $_GET['sort'] === 'asc' ? 'ASC' : 'DESC';

// Base query to get restaurant rankings
$query = "SELECT restaurantName, 
          AVG(rating) as avgRating, 
          AVG(restaurantPricing) as avgPricing,
          COUNT(*) as reviewCount 
          FROM reviews";

// Add price filter if selected
if ($priceFilter > 0) {
    $query .= " WHERE ROUND(restaurantPricing) = ?";
}

$query .= " GROUP BY restaurantName
           ORDER BY avgRating $sortOrder, reviewCount DESC";

// Prepare and execute the query
$stmt = $conn->prepare($query);
if ($priceFilter > 0) {
    $stmt->bind_param("i", $priceFilter);
}
$stmt->execute();
$result = $stmt->get_result();
$restaurants = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "inc/head.inc.php"; ?>
    <title>Restaurant Rankings - The Gastronome's Guide</title>
</head>

<body>
    <header>
        <?php include "inc/nav.inc.php"; ?>
        <?php include "inc/header.inc.php"; ?>
    </header>

    <main class="container mt-4">
        <h1 class="text-center mb-4">Restaurant Rankings</h1>
        
        <!-- Filter options -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Filter Options</h5>
                <form action="restaurant-rankings.php" method="get" class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <label for="price" class="form-label">Price Range:</label>
                        <select name="price" id="price" class="form-select">
                            <option value="0" <?= $priceFilter == 0 ? 'selected' : '' ?>>All Prices</option>
                            <option value="1" <?= $priceFilter == 1 ? 'selected' : '' ?>>$ (Budget)</option>
                            <option value="2" <?= $priceFilter == 2 ? 'selected' : '' ?>>$$ (Standard)</option>
                            <option value="3" <?= $priceFilter == 3 ? 'selected' : '' ?>>$$$ (Premium)</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="sort" class="form-label">Sort Order:</label>
                        <select name="sort" id="sort" class="form-select">
                            <option value="desc" <?= $sortOrder == 'DESC' ? 'selected' : '' ?>>Highest to Lowest</option>
                            <option value="asc" <?= $sortOrder == 'ASC' ? 'selected' : '' ?>>Lowest to Highest</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn w-100" style="background-color: rgb(0, 146, 131); color: white">Apply Filters</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Rankings table -->
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Rank</th>
                        <th>Restaurant</th>
                        <th>Rating</th>
                        <th>Price Range</th>
                        <th>Reviews</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($restaurants)): ?>
                        <?php $rank = 1; ?>
                        <?php foreach ($restaurants as $restaurant): ?>
                            <?php 
                                $avgRating = number_format($restaurant['avgRating'], 1);
                                $avgPricing = round($restaurant['avgPricing']);
                                $pricingSymbols = str_repeat('$', $avgPricing);
                                $stars = '';
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= floor($avgRating)) {
                                        $stars .= '<i class="fas fa-star" style="color: gold;"></i>';
                                    } elseif ($i - $avgRating < 1 && $i - $avgRating > 0) {
                                        $stars .= '<i class="fas fa-star-half-alt" style="color: gold;"></i>';
                                    } else {
                                        $stars .= '<i class="far fa-star" style="color: gold;"></i>';
                                    }
                                }
                            ?>
                            <tr>
                                <td class="text-center">
                                    <?php if ($rank <= 3): ?>
                                        <span class="badge rounded-pill bg-<?= $rank == 1 ? 'warning text-dark' : ($rank == 2 ? 'secondary' : 'dark') ?> fs-5">
                                            <?= $rank ?>
                                        </span>
                                    <?php else: ?>
                                        <?= $rank ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($restaurant['restaurantName']) ?></strong>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="me-2"><?= $avgRating ?></span>
                                        <div><?= $stars ?></div>
                                    </div>
                                </td>
                                <td class="text-center"><?= $pricingSymbols ?></td>
                                <td class="text-center"><?= $restaurant['reviewCount'] ?></td>
                                <td class="text-center">
                                    <a href="reviews.php?restaurant=<?= urlencode($restaurant['restaurantName']) ?>" 
                                       class="btn btn-sm" style="background-color: rgb(0, 146, 131); color: white">
                                        See Reviews
                                    </a>
                                </td>
                            </tr>
                            <?php $rank++; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No restaurants found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <?php include "inc/footer.inc.php"; ?>

    <!-- Add Chart.js for the chart -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        <?php if (count($restaurants) > 1): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('restaurantChart').getContext('2d');
            
            // Limit to top 10 restaurants for chart
            const restaurantData = <?= json_encode(array_slice($restaurants, 0, 10)) ?>;
            
            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: restaurantData.map(item => item.restaurantName),
                    datasets: [
                        {
                            label: 'Average Rating',
                            data: restaurantData.map(item => parseFloat(item.avgRating)),
                            backgroundColor: 'rgba(0, 146, 131, 0.7)',
                            borderColor: 'rgb(0, 146, 131)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 5,
                            title: {
                                display: true,
                                text: 'Average Rating (out of 5)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Restaurant'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        title: {
                            display: true,
                            text: 'Top Restaurant Ratings Comparison'
                        }
                    }
                }
            });
        });
        <?php endif; ?>
    </script>
</body>
</html>
