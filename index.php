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
        <main class="container mt-4" id="about">
            <h1>Welcome to The Gastronome's Guide</h1>
            <p class="title">Our website provides information on the top restaurants of the month</p>
            <p class="intro">We provide booking services as well as </p>
        </main> 
        
        <!-- Menu Container -->
        <article class="w3-container" id="menu">
          <div class="w3-content" style="max-width:700px">

            <h5 class="w3-center w3-padding-48"><span class="w3-tag w3-wide">THE MENU</span></h5>

            <nav class="w3-row w3-center w3-card w3-padding">
              <a href="javascript:void(0)" onclick="openMenu(event, 'Eat');" id="myLink">
                <div class="w3-col s6 tablink">Eat</div>
              </a>
              <a href="javascript:void(0)" onclick="openMenu(event, 'Drinks');">
                <div class="w3-col s6 tablink">Drink</div>
              </a>
            </nav>

            <section id="Eat" class="w3-container menu w3-padding-48 w3-card">
              <h5>Bread Basket</h5>
              <p class="w3-text-grey">Assortment of fresh baked fruit breads and muffins <strong>5.50</strong></p><br>

              <h5>Honey Almond Granola with Fruits</h5>
              <p class="w3-text-grey">Natural cereal of honey toasted oats, raisins, almonds and dates <strong>7.00</strong></p><br>

              <h5>Belgian Waffle</h5>
              <p class="w3-text-grey">Vanilla flavored batter with malted flour <strong>7.50</strong></p><br>

              <h5>Scrambled Eggs</h5>
              <p class="w3-text-grey">Scrambled eggs, roasted red pepper and garlic, with green onions <strong>7.50</strong></p><br>

              <h5>Blueberry Pancakes</h5>
              <p class="w3-text-grey">With syrup, butter and lots of berries <strong>8.50</strong></p>
            </section>

            <section id="Drinks" class="w3-container menu w3-padding-48 w3-card">
              <h5>Coffee</h5>
              <p class="w3-text-grey">Regular coffee <strong>2.50</strong></p><br>

              <h5>Chocolato</h5>
              <p class="w3-text-grey">Chocolate espresso with milk <strong>4.50</strong></p><br>

              <h5>Corretto</h5>
              <p class="w3-text-grey">Whiskey and coffee <strong>5.00</strong></p><br>

              <h5>Iced Tea</h5>
              <p class="w3-text-grey">Hot tea, except not hot <strong>3.00</strong></p><br>

              <h5>Soda</h5>
              <p class="w3-text-grey">Coke, Sprite, Fanta, etc. <strong>2.50</strong></p>
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
    