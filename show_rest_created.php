<?php
session_start();
ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);
// if (!isset($_SESSION['fname'])) {
//     echo "Admins only!";
// } elseif ($_SESSION['admin'] == "No") {
//     echo "Admins only!";
// }
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

$stmt = $conn->prepare("SELECT * FROM add_restaurant");
$stmt->execute();
$result = $stmt->get_result();
$rest_req = $result->fetch_all(MYSQLI_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "inc/head.inc.php"; ?>
    <title>Restaurants to Create</title>
    <style>
        .card:hover {
            background-color: rgb(197, 197, 197);
            transition: background-color 0.3s ease;
        }
    </style>
</head>

<body>
    <?php include "inc/nav.inc.php"; ?>
    <main class="container mt-5">
        <strong>
            <h1 class="text-center mb-4">Restaurants Created by Admin</h1>
        </strong>
        <?php if ($_SESSION['admin'] == "Yes"): ?>
            <?php if (!empty($rest_req)): ?>
                <?php foreach ($rest_req as $requests): ?>
                    <?php if ($requests['admin_added'] == "Yes"): ?>
                        <div class="container d-flex justify-content-center align-items-center">
                            <div class="card mb-3">
                                <div class="card-body shadow">
                                    <a href='reviews.php?restaurant=<?php echo urlencode($requests['restaurantName']); ?>'
                                        style="text-decoration: none;">
                                        <h3 class="card-title"><strong>Restaurant Name:
                                            </strong><?= htmlspecialchars($requests['restaurantName']) ?></h3>
                                        <p class="card-text"><strong>Website: </strong><?= htmlspecialchars($requests['website']) ?></p>
                                        <p class="card-text"><strong>Address: </strong><?= htmlspecialchars($requests['address']) ?></p>
                                        <p class="card-text"><strong>Contact: </strong><?= htmlspecialchars($requests['phoneNumber']) ?>
                                        </p>
                                        <p class="card-text"><strong>Cuisine: </strong><?= htmlspecialchars($requests['cuisine']) ?></p>
                                    </a>
                                </div>
                            </div>
                        </div>

                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php else:?>
            <h2 class="text-center">Admins Only!</h2>
        <?php endif; ?>
    </main>
</body>