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
              We've got reviews for hundreds of restaurants across Singapore, covering everything from cozy cafes to fancy fine dining. <br>
              Read what others are saying about their favorite spots, discover hidden gems, and find the perfect place for your next night out.</p>
            <p class="intro">We provide booking services as well as seamless online ordering, so you can easily reserve a table or grab your favorite dish without leaving the comfort of your home.</p>
        </main> 
        
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
                <div class="w3-col s4 tablink">$$$+</div>
              </a>
            </nav>

            <section id="Budget" class="w3-container menu w3-padding">
              <h3>Budget Options</h3>
              <!-- Budget menu items go here -->
            </section>

            <section id="Standard" class="w3-container menu w3-padding" style="display:none">
                <h3>Standard Options</h3>
                <!-- Standard menu items go here -->
            </section>

            <section id="Premium" class="w3-container menu w3-padding" style="display:none">
                <h3>Premium Options</h3>
                <!-- Premium menu items go here -->
            </section>

            <figure>
              <img src="/w3images/coffeehouse2.jpg" style="width:100%;max-width:1000px;margin-top:32px;" alt="Coffee House">
              <figcaption>Enjoy our cozy coffee house atmosphere!</figcaption>
            </figure>
          </div>
        </article>

        <?php include 'inc/footer.inc.php'; ?>  
           
    </body>
</html>
    