
<?php
include 'koneksi.php';
$q = $conn->query("SELECT * FROM menu_items");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Menu</title>
    <link rel="website icon" type="png" href="images/icon-lamperie.png" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,300;0,400;0,700;1,700&display=swap"
      rel="stylesheet"
    />
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="css/menu.css" />
</head>
<body>
    <nav class="navbar">
      <a href="#home" class="navbar-logo">
        <img src="images/icon-lamperie.png" alt="" class="img-navbar"
      /></a>
      <div class="navbar-nav">
        <a href="index.php">Home</a>
        <a href="index.php #about">About Us</a>
        <a href="menu.php">Menu</a>
        <a href="index.php #location">Location</a>
        <a href="index.php #contact">Contact Us</a>
      </div>
      <div class="navbar-extra">
        <div class="dropdown">
          <a href="" class="dropbtn"><i data-feather="phone"></i></a>
          <div class="dropdown-content">
            <a href="#">+62</a>
            <a href="#">+62</a>
          </div>
        </div>
        <a href="#" id="hamburger"><i data-feather="menu"></i></a>
        <a href="#" id="moon-btn" onclick="setDarkMode(true)">
          <i data-feather="moon"></i>
        </a>
        <a
          href="#"
          id="sun-btn"
          onclick="setDarkMode(false)"
          style="display: none"
        >
          <i data-feather="sun"></i>
        </a>
        <a href="login.php"><i data-feather="user"></i></a>
      </div>
    </nav>

    <div class="bg-image">
      <h1>MENU</h1>
    </div>

    <div class="circular-categories-container">
      <div class="circular-categories-row">
        <div class="category-item-circle">
          <a href="#MainCourse">
            <img src="images/menu/Main-corse-circle.jpeg" alt="Main Course" />
            <h5>Main Course</h5>
          </a>
        </div>
        <div class="category-item-circle">
          <a href="#Appetizer">
            <img
              src="images/menu/Appetizer/MozzarellaSticks.png"
              alt="Appetizer"
            />
            <h5>Appetizer</h5>
          </a>
        </div>
        <div class="category-item-circle">
          <a href="#snacks">
            <img
              src="images/menu/snacks/f5b15dbb-8cf6-4b6b-8efd-0d7dba4c6e16.jpg"
              alt="Snacks"
            />
            <h5>Snacks</h5>
          </a>
        </div>
        <div class="category-item-circle">
          <a href="#dessert">
            <img src="images/menu/dessert&cemilan-circle.jpeg" alt="Dessert" />
            <h5>Dessert</h5>
          </a>
        </div>
        <div class="category-item-circle">
          <a href="#NonCoffee">
            <img
              src="images/menu/Non-Coffee/banner,.jpg"
              alt="Non-Coffee Drinks"
            />
            <h5>Non-Coffee</h5>
          </a>
        </div>
        <div class="category-item-circle">
          <a href="#coffee">
            <img
              src="images/menu/coffee-&-Non-Coffee-circle.jpeg"
              alt="Coffee Drinks"
            />
            <h5>Coffee</h5>
          </a>
        </div>
        <div class="category-item-circle">
          <a href="#juice">
            <img
              src="images/menu/Juice/apple_juice.jpg"
              alt="Juice Drinks"
            />
            <h5>Juice</h5>
          </a>
        </div>
      </div>
    </div>

    <h1 class="MainCourse" id="MainCourse">MAIN COURSE</h1>
    <div class="menu-grid">
        <?php
        $q->data_seek(0); // Reset pointer query ke awal
        while ($menu = $q->fetch_assoc()):
            if ($menu['category'] == 'Main Course'):
        ?>
            <div class="menu-card" onclick="toggleDescription(this)">
                <?php if (!empty($menu['image_url'])): ?>
                    <img src="images/menu/<?= $menu['category'] ?>/<?= $menu['image_url'] ?>" alt="<?= $menu['item_name'] ?>" class="menu-image">
                <?php else: ?>
                    <img src="images/menu/placeholder.png" alt="<?= $menu['item_name'] ?>" class="menu-image" />
                <?php endif; ?>
                <div class="menu-title"><?= $menu['item_name'] ?></div>
                <div class="menu-description"><?= $menu['description'] ?></div>
                <div class="menu-price" data-base-price="<?= $menu['price'] ?>">$<?= $menu['price'] ?></div>
                
            </div>
        <?php
            endif;
        endwhile;
        ?>
    </div>

    <h1 class="Appetizer" id="Appetizer">APPETIZER</h1>
    <div class="menu-grid">
        <?php
        $q->data_seek(0);
        while ($menu = $q->fetch_assoc()):
            if ($menu['category'] == 'Appetizer'):
        ?>
            <div class="menu-card" onclick="toggleDescription(this)">
                <?php if (!empty($menu['image_url'])): ?>
                    <img src="images/menu/<?= $menu['category'] ?>/<?= $menu['image_url'] ?>" alt="<?= $menu['item_name'] ?>" class="menu-image">
                <?php else: ?>
                    <img src="images/menu/placeholder.png" alt="<?= $menu['item_name'] ?>" class="menu-image" />
                <?php endif; ?>
                <div class="menu-title"><?= $menu['item_name'] ?></div>
                <div class="menu-description"><?= $menu['description'] ?></div>
                <div class="menu-price" data-base-price="<?= $menu['price'] ?>">$<?= $menu['price'] ?></div>
                
            </div>
        <?php
            endif;
        endwhile;
        ?>
    </div>

    <h1 class="snacks" id="snacks">SNACKS</h1>
    <div class="menu-grid">
        <?php
        $q->data_seek(0);
        while ($menu = $q->fetch_assoc()):
            if ($menu['category'] == 'Snacks'):
        ?>
            <div class="menu-card" onclick="toggleDescription(this)">
                <?php if (!empty($menu['image_url'])): ?>
                    <img src="images/menu/<?= $menu['category'] ?>/<?= $menu['image_url'] ?>" alt="<?= $menu['item_name'] ?>" class="menu-image">
                <?php else: ?>
                    <img src="images/menu/placeholder.png" alt="<?= $menu['item_name'] ?>" class="menu-image" />
                <?php endif; ?>
                <div class="menu-title"><?= $menu['item_name'] ?></div>
                <div class="menu-description"><?= $menu['description'] ?></div>
                <div class="menu-price" data-base-price="<?= $menu['price'] ?>">$<?= $menu['price'] ?></div>
                
            </div>
        <?php
            endif;
        endwhile;
        ?>
    </div>

    <h1 class="dessert" id="dessert">DESSERT</h1>
    <div class="menu-grid">
        <?php
        $q->data_seek(0);
        while ($menu = $q->fetch_assoc()):
            if ($menu['category'] == 'Dessert'):
        ?>
            <div class="menu-card" onclick="toggleDescription(this)">
                <?php if (!empty($menu['image_url'])): ?>
                    <img src="images/menu/<?= $menu['category'] ?>/<?= $menu['image_url'] ?>" alt="<?= $menu['item_name'] ?>" class="menu-image">
                <?php else: ?>
                    <img src="images/menu/placeholder.png" alt="<?= $menu['item_name'] ?>" class="menu-image" />
                <?php endif; ?>
                <div class="menu-title"><?= $menu['item_name'] ?></div>
                <div class="menu-description"><?= $menu['description'] ?></div>
                <div class="menu-price" data-base-price="<?= $menu['price'] ?>">$<?= $menu['price'] ?></div>
                
            </div>
        <?php
            endif;
        endwhile;
        ?>
    </div>

    <h1 class="non-coffee" id="NonCoffee">NON-COFFEE</h1>
    <div class="menu-grid">
        <?php
        $q->data_seek(0);
        while ($menu = $q->fetch_assoc()):
            if ($menu['category'] == 'Non-Coffee'):
        ?>
            <div class="menu-card" onclick="toggleDescription(this)">
                <?php if (!empty($menu['image_url'])): ?>
                    <img src="images/menu/<?= $menu['category'] ?>/<?= $menu['image_url'] ?>" alt="<?= $menu['item_name'] ?>" class="menu-image">
                <?php else: ?>
                    <img src="images/menu/placeholder.png" alt="<?= $menu['item_name'] ?>" class="menu-image" />
                <?php endif; ?>
                <div class="menu-title"><?= $menu['item_name'] ?></div>
                <div class="menu-description"><?= $menu['description'] ?></div>
                <div class="menu-price" data-base-price="<?= $menu['price'] ?>">$<?= $menu['price'] ?></div>
                
            </div>
        <?php
            endif;
        endwhile;
        ?>
    </div>

    <h1 class="coffee" id="coffee">COFFEE</h1>
    <div class="menu-grid">
        <?php
        $q->data_seek(0);
        while ($menu = $q->fetch_assoc()):
            if ($menu['category'] == 'Coffee'):
        ?>
            <div class="menu-card" onclick="toggleDescription(this)">
                <?php if (!empty($menu['image_url'])): ?>
                    <img src="images/menu/<?= $menu['category'] ?>/<?= $menu['image_url'] ?>" alt="<?= $menu['item_name'] ?>" class="menu-image">
                <?php else: ?>
                    <img src="images/menu/placeholder.png" alt="<?= $menu['item_name'] ?>" class="menu-image" />
                <?php endif; ?>
                <div class="menu-title"><?= $menu['item_name'] ?></div>
                <div class="menu-description"><?= $menu['description'] ?></div>
                <div class="menu-price" data-base-price="<?= $menu['price'] ?>">$<?= $menu['price'] ?></div>
                
            </div>
        <?php
            endif;
        endwhile;
        ?>
    </div>

    <h1 class="Juice" id="juice">JUICE</h1>
    <div class="menu-grid">
        <?php
        $q->data_seek(0);
        while ($menu = $q->fetch_assoc()):
            if ($menu['category'] == 'Juice'):
        ?>
            <div class="menu-card" onclick="toggleDescription(this)">
                <?php if (!empty($menu['image_url'])): ?>
                    <img src="images/menu/<?= $menu['category'] ?>/<?= $menu['image_url'] ?>" alt="<?= $menu['item_name'] ?>" class="menu-image">
                <?php else: ?>
                    <img src="images/menu/placeholder.png" alt="<?= $menu['item_name'] ?>" class="menu-image" />
                <?php endif; ?>
                <div class="menu-title"><?= $menu['item_name'] ?></div>
                <div class="menu-description"><?= $menu['description'] ?></div>
                <div class="menu-price" data-base-price="<?= $menu['price'] ?>">$<?= $menu['price'] ?></div>
                
            </div>
        <?php
            endif;
        endwhile;
        ?>
    </div>

    <!-- <div
      id="checkout-card"
      aria-live="polite"
      aria-atomic="true"
      aria-label="Checkout summary"
    >
      <h2>Your Order</h2>
      <div id="checkout-items"></div>
      <div id="checkout-total">Total: $0.00</div>
      <button id="checkout-button" onclick="proceedToCheckout()">
        Checkout
      </button>
    </div> -->
    <script>
      feather.replace();
    </script>
    <script src="js/menu.js"></script>
</body>
</html>