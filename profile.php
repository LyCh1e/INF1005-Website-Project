<?php
session_start();


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

$fname = $_SESSION['fname'];
$sql = "SELECT * FROM reviews WHERE name = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("s", $fname);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $reviews = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $reviews = [];
        echo "No reviews found.";
    }
    $stmt->close();
} else {
    echo "Error in preparing statement: " . $conn->error;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "inc/head.inc.php"; ?>
    <title>User Profile</title>
    <style>
    .card:hover {
      background-color: rgb(197, 197, 197);
      transition: background-color 0.3s ease; 
    }
  </style>
</head>

<body>
    <?php include "inc/nav.inc.php"; ?>
    <main class="container mt-5 ">
        <strong>
            <h1 class="text-center mb-4">User Profile</h1>
        </strong>
        <div class="container d-flex justify-content-center align-items-center">
            <!-- <div class="card mb-3"> -->
                <!-- <div class='card shadow-lg w-auto'> -->
                    <!-- <div class="card-body"> -->
                        <div>
                        <p><strong>First Name: </strong><?= htmlspecialchars($_SESSION['fname']) ?>
                        </p>
                        <p><strong>Last Name: </strong><?= htmlspecialchars($_SESSION['lname']) ?></p>
                        <p><strong>Email: </strong><?= htmlspecialchars($_SESSION['email']) ?></p>
                        <p><strong>Phone Number: </strong><?= htmlspecialchars($_SESSION['ph']) ?></p>
                </div>
                <!-- </div> -->
            <!-- </div> -->
        </div>
        <hr  style="border: 2px solid black;">
        <section id="userReview" class="w3-container menu w3-padding">
            <h2 style="text-align: center;">Reviews created</h2>
            <p style="text-align: center;">Want to view your bookings? <a href="booking.php" style='color: rgb(0, 78, 74)'>Click here!</a></p>
            <?php if (!empty($reviews)): ?>

                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
                    <?php foreach ($reviews as $row): ?>
                        <div class="col">
                            <div class='card shadow-lg w-auto'>
                                <div class="card-body">
                                <a href='reviews.php?restaurant=<?php echo urlencode($row['restaurantName']); ?>'
                                style="text-decoration: none;">
                                    <p class="card-text"><strong>Name: </strong><?= htmlspecialchars($row['name']) ?></p>
                                    <p class="card-text"><strong>Restaurant Name:
                                        </strong><?= htmlspecialchars($row['restaurantName']) ?>
                                    </p>
                                    <p class="card-text"><strong>Rating: </strong><?= str_repeat('<i class="fas fa-star" style="color: gold" aria-hidden="true"></i>', $row['rating']) ?></p>
                                    <p class="card-text"><strong>Pricing:
                                        </strong><?= str_repeat('$', $row['restaurantPricing']) ?></p>
                                    <p class="card-text"><strong>Comment: </strong><?= htmlspecialchars($row['comment']) ?></p>
                                    <p class="text-muted">Posted on <?= $row['created_at'] ?></p>
                                    <?php if (!is_null($row['edited_at'])): ?>
                                        <p class="text-muted"><small>Edited on <?= $row['edited_at'] ?></small></p>
                                    <?php endif; ?>
                                    </a>

                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
    <?php include "inc/footer.inc.php"; ?>
</body>

</html>