<?php
session_start();
if (!isset($_SESSION['fname'])) {
    echo "Admins only!";
} elseif ($_SESSION['admin'] == "No") {
    echo "Admins only!";
}
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

$stmt = $conn->prepare("SELECT id, restaurantName, website, address, phoneNumber, cuisine, admin_added FROM add_restaurant");
$stmt->execute();
$result = $stmt->get_result();
$rest_req = $result->fetch_all(MYSQLI_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "inc/head.inc.php"; ?>
    <title>Restaurants to Create</title>
</head>

<body>
    <?php include "inc/nav.inc.php"; ?>
    <main class="container mt-5">
        <strong>
            <h1 class="text-center mb-4">Restaurants to Create</h1>
        </strong>
        <?php if (!empty($rest_req)): ?>
            <?php foreach ($rest_req as $requests): ?>
                <?php if ($requests['admin_added'] == "No"): ?>
                    <div class="container d-flex justify-content-center align-items-center">
                        <div class="card mb-3">
                            <div class="card-body shadow">
                                <h3 class="card-title"><strong>Restaurant Name:
                                    </strong><?= htmlspecialchars($requests['restaurantName']) ?></h3>
                                <p class="card-text"><strong>Website: </strong><?= htmlspecialchars($requests['website']) ?></p>
                                <p class="card-text"><strong>Address: </strong><?= htmlspecialchars($requests['address']) ?></p>
                                <p class="card-text"><strong>Contact: </strong><?= htmlspecialchars($requests['phoneNumber']) ?></p>
                                <p class="card-text"><strong>Cuisine: </strong><?= htmlspecialchars($requests['cuisine']) ?></p>
                            </div>
                            <div class="icon-container" style="position: absolute; top: 20px; right: 20px;">
                                <p>
                                    <a href="new_review.php?id=<?= $requests['id'] ?>&restaurantName=<?= urlencode($requests['restaurantName']) ?>"
                                        aria-label="create">
                                        <i class="fas fa-plus" style="font-size: 35px; color: green;"></i></a>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>

    </main>
</body>