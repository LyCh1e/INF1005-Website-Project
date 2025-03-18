//<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
//
//$config = parse_ini_file('/var/www/private/db-config.ini');
//if (!$config) {
// die("Failed to read database config file.");
//}
//
//$conn = new mysqli(
// $config['servername'],
// $config['username'],
// $config['password'],
// $config['dbname']
//);
//
//if ($conn->connect_error) {
// die("Connection failed: " . $conn->connect_error);
//}
//
//$stmt = $conn->prepare("SELECT restaurantName, AVG(rating) as avgRating, AVG(restaurantPricing) as avgRP FROM reviews GROUP BY restaurantName");
//$stmt->execute();
//$result = $stmt->get_result();
//$restaurants = $result->fetch_all(MYSQLI_ASSOC);
//?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'inc/head.inc.php'; ?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gastronome's Guide</title>
</head>

<body>
  <header>
    <?php include "inc/nav.inc.php"; ?>
    <?php include "inc/header.inc.php"; ?>
  </header>

  <!-- Main Content -->
  <main class="container-main mt-4" id="about">
    <h1 class="welcome" style="font-weight: bold;">Welcome to The Gastronome's Guide</h1>
    <p class="title">Craving something delicious? 🍽️ <br>
      Find your perfect meal with us! <br>
      We've got reviews for hundreds of restaurants across Singapore, covering everything from cozy cafes to fancy fine
      dining. <br>
      Read what others are saying about their favorite spots, discover hidden gems, and find the perfect place for your
      next night out.</p>
    <p class="intro">We provide booking services as well as seamless online ordering, so you can easily reserve a table
      or grab your favorite dish without leaving the comfort of your home.</p>
    <!-- Menu Container -->
    <article class="w3-container" id="discover">
      <div class="w3-content" style="max-width:1000px">
  
        <h1 class="discover mt-4">Discover New Restaurants</h1>
  
        <nav class="w3-row w3-center w3-card w3-padding">
          <a href="javascript:void(0)" onclick="openMenu(event, 'Budget');" id="myLink">
            <div class="w3-col s4 tablink">$</div>
          </a>
          <a href="javascript:void(0)" onclick="openMenu(event, 'Standard');">
            <div class="w3-col s4 tablink">$$</div>
          </a>
          <a href="javascript:void(0)" onclick="openMenu(event, 'Premium');">
            <div class="w3-col s4 tablink">$$$</div>
          </a>
        </nav>
  
        <section id="Budget" class="w3-container menu w3-padding">
          <h2>Budget Options</h2>
          <!-- Budget menu items go here -->
          <div class="row">
            <?php foreach ($restaurants as $row): ?>
              <?php
              $restaurantName = htmlspecialchars($row['restaurantName']);
              $avgRating = number_format($row['avgRating'], 1);
              $avgRP = floor($row['avgRP']);
              $pricingSymbols = str_repeat("$", $avgRP);
              ?>
              <?php if ($avgRP == 1): ?>
                <div class='col'>
                  <div class='card shadow-lg w-auto'>
                    <div class='card-body text-center'>
                    <a href='reviews.php?restaurant=<?php echo urlencode($restaurantName); ?>' style="text-decoration: none;">
                      <h5 class='card-title'><?php echo $restaurantName; ?></h5>
                      <p class='card-text'>Average Rating: <strong><?php echo $avgRating; ?> ⭐️</strong></p>
                      <p class='card-text'>Average Pricing: <strong><?php echo $pricingSymbols; ?></strong></p></a>
                    </div>
                  </div>
                </div>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        </section>
  
        <section id="Standard" class="w3-container menu w3-padding" style="display:none">
          <h3>Standard Options</h3>
          <!-- Standard menu items go here -->
          <div class="row">
            <?php foreach ($restaurants as $row): ?>
              <?php
              $restaurantName = htmlspecialchars($row['restaurantName']);
              $avgRating = number_format($row['avgRating'], 1);
              $avgRP = floor($row['avgRP']);
              $pricingSymbols = str_repeat("$", $avgRP);
              ?>
              <?php if ($avgRP == 2): ?>
                <div class='col'>
                  <div class='card shadow-lg w-auto'>
                    <div class='card-body text-center'>
                    <a href='reviews.php?restaurant=<?php echo urlencode($restaurantName); ?>' style="text-decoration: none;">
                      <h5 class='card-title'><?php echo $restaurantName; ?></h5>
                      <p class='card-text'>Average Rating: <strong><?php echo $avgRating; ?> ⭐️</strong></p>
                      <p class='card-text'>Average Pricing: <strong><?php echo $pricingSymbols; ?></strong></p></a>
                    </div>
                  </div>
                </div>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        </section>
  
        <section id="Premium" class="w3-container menu w3-padding" style="display:none">
          <h3>Premium Options</h3>
          <!-- Premium menu items go here -->
          <div class="row">
            <?php foreach ($restaurants as $row): ?>
              <?php
              $restaurantName = htmlspecialchars($row['restaurantName']);
              $avgRating = number_format($row['avgRating'], 1);
              $avgRP = floor($row['avgRP']);
              $pricingSymbols = str_repeat("$", $avgRP);
              ?>
              <?php if ($avgRP == 3): ?>
                <div class='col'>
                  <div class='card shadow-lg w-auto'>
                    <div class='card-body text-center'>
                    <a href='reviews.php?restaurant=<?php echo urlencode($restaurantName); ?>' style="text-decoration: none;">
                      <h5 class='card-title'><?php echo $restaurantName; ?></h5>
                      <p class='card-text'>Average Rating: <strong><?php echo $avgRating; ?> ⭐️</strong></p>
                      <p class='card-text'>Average Pricing: <strong><?php echo $pricingSymbols; ?></strong></p></a>
                    </div>
                  </div>
                </div>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        </section>
      </div>
    </article>
  </main>
  <?php include 'inc/footer.inc.php'; ?>
</body>

</html>