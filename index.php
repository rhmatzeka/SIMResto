<?php
include 'koneksi.php'; 

// Mengambil 3 berita terbaru
$result_berita = $conn->query("SELECT id, judul, konten, gambar, tanggal_post FROM berita ORDER BY tanggal_post DESC LIMIT 3");

// --- KODE DISKON YANG DIPERBARUI ---
// Query ini sekarang memeriksa status dan validitas waktu secara bersamaan.
$query_diskon = "
    SELECT kode_diskon, deskripsi, gambar, nilai_diskon, tipe_diskon 
    FROM discounts 
    ORDER BY id DESC 
    LIMIT 6";
$result_diskon = $conn->query($query_diskon);
// --- BATAS KODE YANG DIPERBARUI ---

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>LAMPERIE</title>
    <link rel="website icon" type="png" href="images/icon-lamperie.png" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,300;0,400;0,700;1,700&display=swap"
      rel="stylesheet"
    />
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="css/style.css" />
  </head>
  <body>
    <nav class="navbar">
      <a href="#home" class="navbar-logo">
        <img src="images/icon-lamperie.png" alt="" class="img-navbar"
      /></a>
      <div class="navbar-nav">
        <a href="#home">Home</a>
        <a href="#about">About Us</a>
        <a href="#menu">Menu</a>
        <a href="#location">Location</a>
        <a href="#contact">Contact Us</a>
        <a href="reservasi/dashboard_reservasi.php">Reservasi</a>
      </div>
      <div class="navbar-extra">
    <div class="dropdown">
        <a href="" class="dropbtn"><i data-feather="phone"></i></a>
        <div class="dropdown-content">
            <a href="#">+6289514509392</a>
            <a href="#">+62</a>
        </div>
    </div>
    <a href="#" id="hamburger-menu"><i data-feather="menu"></i></a>
    <a href="#" id="moon-btn" onclick="setDarkMode(true)">
        <i data-feather="moon"></i>
    </a>
    <a href="#" id="sun-btn" onclick="setDarkMode(false)" style="display: none">
        <i data-feather="sun"></i>
    </a>
        <a href="login.php"><i data-feather="user"></i></a>
      </div>
    </nav>

    <section class="hero" id="home">
      <main class="content">
        <h1>let's take a break to get energy</h1>
        <p>Modern, Aunthentic Restaurant Food and Beverage Restaurant. We serve</p>
        <a href="menu.php" class="detail-menu">Detail Menu's</a>
      </main>
    </section>

        <section id="news" class="news">
        <h2><span>Latest</span> News</h2>
        <p>Ikuti terus informasi dan promo terbaru dari kami.</p>
        <div class="row">
            <?php if ($result_berita && $result_berita->num_rows > 0): ?>
                <?php while($berita = $result_berita->fetch_assoc()): ?>
                    <div class="news-card">
                        <div class="news-image">
                            <img src="images/berita/<?= htmlspecialchars($berita['gambar'] ?? 'placeholder.png') ?>" alt="<?= htmlspecialchars($berita['judul']) ?>">
                        </div>
                        <div class="news-content">
                            <p class="news-date"><?= date('d F Y', strtotime($berita['tanggal_post'])) ?></p>
                            <h3><?= htmlspecialchars($berita['judul']) ?></h3>
                            <p class="news-excerpt">
                                <?php
                                    $potongan_konten = substr(strip_tags($berita['konten']), 0, 100);
                                    echo htmlspecialchars($potongan_konten) . '...';
                                ?>
                            </p>
                            <a href="detail_berita.php?id=<?= $berita['id'] ?>" class="read-more-btn">Baca Selengkapnya</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="color:white; text-align:center; width:100%;">Belum ada berita terbaru saat ini.</p>
            <?php endif; ?>
        </div>
    </section>

    <section class="discount" id="discount">
        <h2><span>Hot</span> Deals</h2>
        <p>Jangan lewatkan promo dan diskon spesial hanya untuk Anda!</p>
        <div class="horizontal-scroll-container">
            <div class="scroll-content">
                <?php if ($result_diskon && $result_diskon->num_rows > 0): ?>
                    <?php while($diskon = $result_diskon->fetch_assoc()): ?>
                        <div class="product-card main-card">
                            <div class="image-container">
                                <img src="images/discounts/<?= htmlspecialchars($diskon['gambar'] ?? 'placeholder.png') ?>" alt="<?= htmlspecialchars($diskon['deskripsi']) ?>" class="image-product-promo">
                            </div>
                            <div class="product-info">
                                <h1><?= htmlspecialchars($diskon['deskripsi']) ?></h1>
                                <?php if ($diskon['tipe_diskon'] == 'persen'): ?>
                                    <p class="subtitle">Diskon <?= (int)$diskon['nilai_diskon'] ?>%</p>
                                <?php else: ?>
                                    <p class="subtitle">Potongan $<?= number_format($diskon['nilai_diskon'], 2) ?></p>
                                <?php endif; ?>
                                <p class="promo-code">Kode: <span><?= htmlspecialchars($diskon['kode_diskon']) ?></span></p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="color: white; text-align: center; width: 100%;">Belum ada diskon yang tersedia saat ini.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

          <section id="about" class="about">
      <h2><span>About</span> Us</h2>
      <div class="row">
        <div class="about-img">
          <img src="images/menu/produk.jpg" alt="Tentang Kami" />
        </div>
        <div class="content">
          <h3>Why Choice Us</h3>
          <p>
            We blend culinary artistry with premium ingredients to create a dining experience that delights the senses. With a cozy yet sophisticated atmosphere, LAMPERIE is the perfect destination for those who appreciate quality, taste, and style. </p>
        </div>
      </div>
    </section>

    <!-- chef -->
     <div class="chefs-section">
        <h2><span class="team-members">- Team 3 -</span>Our Master Chefs</h2>
        <div class="chefs-container">
            <div class="chef-card">
                <div class="chef-image">
                    <img src="images/chef/koki1.png" alt="Chef 1">
                </div>
                <div class="chef-info">
                    <h3>Rahmat Ganteng</h3>
                    <p>Sous Chef</p>
                    <div class="social-icons">
                        <a href="#" aria-label="Facebook"><span class="icon"><i data-feather="github"></i></span></a>
                        <a href="#" aria-label="Twitter"><span class="icon"><i data-feather="instagram"></i></span></a>
                        <a href="#" aria-label="Instagram"><span class="icon"><i data-feather="linkedin"></i></span></a>
                    </div>
                </div>
            </div>
            <div class="chef-card">
                <div class="chef-image">
                    <img src="images/chef/koki2.png" alt="Chef 2">
                </div>
                <div class="chef-info">
                    <h3>Rahmat Eka</h3>
                    <p>Executive Chef</p>
                    <div class="social-icons">
                        <a href="#" aria-label="Facebook"><span class="icon"><i data-feather="github"></i></span></a>
                        <a href="#" aria-label="Twitter"><span class="icon"><i data-feather="instagram"></i></span></a>
                        <a href="#" aria-label="Instagram"><span class="icon"><i data-feather="linkedin"></i></span></a>
                    </div>
                </div>
            </div>
            <div class="chef-card">
                <div class="chef-image">
                    <img src="images/chef/koki3.png" alt="Chef 3">
                </div>
                <div class="chef-info">
                    <h3>Rahmat Ganz</h3>
                    <p>Chef de Partie</p>
                    <div class="social-icons">
                        <a href="https://github.com/rhmatzeka" aria-label="Facebook"><span class="icon"><i data-feather="github"></i></span></a>
                        <a href="https://www.instagram.com/rahmatdev.id/" aria-label="Twitter"><span class="icon"><i data-feather="instagram"></i></span></a>
                        <a href="https://www.linkedin.com/in/rahmatekasatria/" aria-label="Instagram"><span class="icon"><i data-feather="linkedin"></i></span></a>
                    </div>
                </div>
            </div>
            <!-- <div class="chef-card">
                <div class="chef-image">
                    <img src="images/chef4.jpg" alt="Chef 4">
                </div>
                <div class="chef-info">
                    <h3>Full Name</h3>
                    <p>Designation</p>
                    <div class="social-icons">
                        <a href="#" aria-label="Facebook"><span class="icon">f</span></a>
                        <a href="#" aria-label="Twitter"><span class="icon">tw</span></a>
                        <a href="#" aria-label="Instagram"><span class="icon">ig</span></a>
                    </div>
                </div>
            </div> -->
        </div>
    </div>
    <!-- end chef -->

    <section id="menu" class="menu">
      <h2>
        Best <span>Favorite</span><br />
        Menu
      </h2>
      <div class="row">
        <div class="menu-category">
          <h2 class="category-title">Food's</h2>
          <div class="scrollable-menu">
            <div class="menu-cards">
              <div class="menu-card">
                <img
                  src="images/menu/foods/burger.jpg"
                  alt="BigMac Burger"
                  class="menu-card-img"
                />
                <h3 class="menu-card-title">BigMac Burger</h3>
                <p class="menu-card-price">$17</p>
                <div class="menu-card-desc">
                  <p class="desc-text">
                    Big Mac Burger is a world famous double decker burger
                    for....
                    <a href="#" class="read-more-link">more</a>
                  </p>
                  <div class="full-desc">
                    <p>
                      BigMac Burger It features two juicy all-beef patties
                      layered with fresh shredded lettuce, sliced pickles,
                      chopped onions, and a slice of melty cheese, all stacked
                      between a three-part sesame seed bun. What truly sets the
                      Big Mac apart is its signature tangy Big Mac sauce a
                      creamy, slightly sweet, and zesty dressing that ties all
                      the flavors together. Hearty, satisfying, and instantly
                      recognizable, the Big Mac is a timeless fast-food classic.
                    </p>
                    <a href="#" class="read-less-link">close</a>
                  </div>
                </div>
              </div>
              <div class="menu-card">
                <img
                  src="images/menu/foods/dimsum.jpg"
                  alt="Beef Dimsum"
                  class="menu-card-img"
                />
                <h3 class="menu-card-title">Dimsum Beef</h3>
                <p class="menu-card-price">$10</p>
                <div class="menu-card-desc">
                  <p class="desc-text">
                    Dimsum with seasoned minced beef and herbs, served with
                    savory dipping sauce...
                    <a href="#" class="read-more-link">more</a>
                  </p>
                  <div class="full-desc">
                    <p>
                      Beef DimSum is a flavorful and tender steamed dumpling
                      made with minced beef, wrapped in a delicate dumpling skin
                      and cooked to perfection. These bite sized delights are
                      juicy, savory, and infused with Asian herbs and
                      seasonings. Often enjoyed with soy sauce, chili oil, or
                      vinegar dipping sauce, beef dimsum is a comforting and
                      satisfying dish perfect as an appetizer. Its soft texture
                      and rich flavor make it a popular choice for meat lovers.
                    </p>
                    <a href="#" class="read-less-link">close</a>
                  </div>
                </div>
              </div>
              <div class="menu-card">
                <img
                  src="images/menu/foods/fried-noodle.jpg"
                  alt="Fried Noodle"
                  class="menu-card-img"
                />
                <h3 class="menu-card-title">Fried Noodle with Shrimp</h3>
                <p class="menu-card-price">$16</p>
                <div class="menu-card-desc">
                  <p class="desc-text">
                    Stir-fried noodles with succulent shrimp and vegetables...
                    <a href="#" class="read-more-link">more</a>
                  </p>
                  <div class="full-desc">
                    <p>
                      Fried Noodles with Shrimp is a flavorful and aromatic
                      stir-fried noodle dish, commonly served in Asian
                      restaurants. This savory dish features yellow egg noodles
                      tossed in a delicious soy-based sauce.
                    </p>
                    <a href="#" class="read-less-link">close</a>
                  </div>
                </div>
              </div>
              <div class="menu-card">
                <img
                  src="images/menu/foods/ramen.jpg"
                  alt="Ramen"
                  class="menu-card-img"
                />
                <h3 class="menu-card-title">Ramen</h3>
                <p class="menu-card-price">$11</p>
                <div class="menu-card-desc">
                  <p class="desc-text">
                    Japanese noodle soup with shrimp, soft-boiled egg, and
                    vegetables.... <a href="#" class="read-more-link">more</a>
                  </p>
                  <div class="full-desc">
                    <p>
                      Shrimp Ramen is a comforting Japanese noodle soup that
                      features a rich and savory broth, springy ramen noodles,
                      and tender shrimp. This dish combines the umami depth of
                      the broth with the delicate sweetness of shrimp. served
                      with a variety of fresh toppings soft-boiled egg, nori
                      (seaweed), green onions, and vegetables.
                    </p>
                    <a href="#" class="read-less-link">close</a>
                  </div>
                </div>
              </div>
              <div class="menu-card">
                <img
                  src="images/menu/foods/salad.jpg"
                  alt="Steak with Tofu"
                  class="menu-card-img"
                />
                <h3 class="menu-card-title">Salad with Tofu</h3>
                <p class="menu-card-price">$13</p>
                <div class="menu-card-desc">
                  <p class="desc-text">
                    Salad served with tofu and fresh vegetables...
                    <a href="#" class="read-more-link">more</a>
                  </p>
                  <div class="full-desc">
                    <p>
                      Vegetable Salad with Tofu is a fresh, healthy, and
                      protein-packed dish perfect for vegetarians and clean
                      eaters. It features a colorful mix of crisp vegetables
                      such as lettuce, cucumber, cherry tomatoes, and carrots,
                      topped with tofu. With tangy vinaigrette, sesame-ginger,
                      or creamy peanut sauce. This salad is not only light and
                      refreshing but also filling and nutritious, making it a
                      great choice for a balanced meal or a wholesome appetizer.
                    </p>
                    <a href="#" class="read-less-link">close</a>
                  </div>
                </div>
              </div>
              <div class="menu-card">
                <img
                  src="images/menu/foods/steak.jpg"
                  alt="Steak Beef"
                  class="menu-card-img"
                />
                <h3 class="menu-card-title">Delicious Steak</h3>
                <p class="menu-card-price">$20</p>
                <div class="menu-card-desc">
                  <p class="desc-text">
                    Grilled beef steak with sauce and mashed potatoes and
                    sautéed vegetables...
                    <a href="#" class="read-more-link">more</a>
                  </p>
                  <div class="full-desc">
                    <p>
                      Grilled Beef Steak is a classic Western main course,
                      celebrated for its bold flavor and tender texture. This
                      dish features a premium cut of beef, expertly seasoned and
                      grilled to your preferred doneness whether juicy
                      medium-rare or well-done. Served with a rich sauce, such
                      as black pepper, mushroom, or red wine reduction, and
                      accompanied by sides like mashed potatoes, grilled
                      vegetables, or fries.
                    </p>
                    <a href="#" class="read-less-link">close</a>
                  </div>
                </div>
              </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section id="beverage" class="menu">
      <div class="row">
        <div class="menu-category">
          <h2 class="category-title">Beverage</h2>
          <div class="scrollable-menu">
            <div class="menu-cards">
              <div class="menu-card">
                <img
                  src="images/menu/drinks/coffeesmothies.jpg"
                  alt="Coffee Smothies"
                  class="menu-card-img"
                />
                <h3 class="menu-card-title">Coffee Smothies</h3>
                <p class="menu-card-price">$10</p>
                <div class="menu-card-desc">
                  <p class="desc-text">
                    Creamy and refreshing smoothie made with real coffee and
                    milk perfectly blended...
                    <a href="#" class="read-more-link">more</a>
                  </p>
                  <div class="full-desc">
                    <p>
                      A Coffee Smoothie is a refreshing and energizing blended
                      drink that combines the bold flavor of coffee with the
                      creamy richness of milk and natural sweetness of fruits or
                      sweeteners. Served cold and smooth, this drink is perfect
                      for coffee lovers who enjoy a chilled, nutritious pick me
                      up.
                    </p>
                    <a href="#" class="read-less-link">close</a>
                  </div>
                </div>
              </div>
              <div class="menu-card">
                <img
                  src="images/menu/drinks/juiceguava.jpg"
                  alt="Juice Guava"
                  class="menu-card-img"
                />
                <h3 class="menu-card-title">Guava Juice</h3>
                <p class="menu-card-price">$7</p>
                <div class="menu-card-desc">
                  <p class="desc-text">
                    Fresh, tropical juice made from ripe guava, rich in vitamin
                    C, and full of flavor...
                    <a href="#" class="read-more-link">more</a>
                  </p>
                  <div class="full-desc">
                    <p>
                      Guava Juice is a tropical and refreshing fruit drink made
                      from ripe guavas, known for their naturally sweet and
                      slightly tangy flavor. Rich in vitamin C, fiber, and
                      antioxidants, this juice is both delicious and nutritious.
                      It's typically blended until smooth, then strained for a
                      silky texture, and served chilled perfect for hot days or
                      as a revitalizing beverage
                    </p>
                    <a href="#" class="read-less-link">close</a>
                  </div>
                </div>
              </div>
              <div class="menu-card">
                <img
                  src="images/menu/drinks/sirsaklemon.jpg"
                  alt="Soursop Water"
                  class="menu-card-img"
                />
                <h3 class="menu-card-title">Soursop Water</h3>
                <p class="menu-card-price">$3</p>
                <div class="menu-card-desc">
                  <p class="desc-text">
                    Tropical drink made from fresh soursop fruit and water
                    naturally sweet and tangy...
                    <a href="#" class="read-more-link">more</a>
                  </p>
                  <div class="full-desc">
                    <p>
                      Soursop Water is a refreshing tropical drink made from the
                      pulp of the soursop fruit, also known as graviola. Known
                      for its creamy texture and sweet-tart flavor, soursop is
                      rich in vitamin C, antioxidants, and natural fiber.
                      Resulting in a smooth and hydrating beverage with a unique
                      tropical taste. Best served chilled, soursop water is both
                      soothing and revitalizing perfect for cooling down on a
                      warm day.
                    </p>
                    <a href="#" class="read-less-link">close</a>
                  </div>
                </div>
              </div>
              <div class="menu-card">
                <img
                  src="images/menu/drinks/smothiescocholate.jpg"
                  alt="Chocolatte Smothies"
                  class="menu-card-img"
                />
                <h3 class="menu-card-title">Chocolatte Smothies</h3>
                <p class="menu-card-price">$15</p>
                <div class="menu-card-desc">
                  <p class="desc-text">
                    Rich and creamy chocolate smoothie made with cocoa and milk
                    blend...
                    <a href="#" class="read-more-link">more</a>
                  </p>
                  <div class="full-desc">
                    <p>
                      A Chocolate Smoothie is a rich, creamy, and indulgent
                      blended drink perfect for chocolate lovers. Made with a
                      mix of milk, cocoa or chocolate syrup, and bananas or
                      yogurt for natural creaminess, this smoothie is both
                      delicious and satisfying. Whether enjoyed as a dessert
                      drink, snack, or even a breakfast treat, the chocolate
                      smoothie delivers a smooth, velvety texture with deep
                      chocolate flavor. It's often enhanced with peanut butter,
                      protein powder, or oats for extra nutrition and energy.
                    </p>
                    <a href="#" class="read-less-link">close</a>
                  </div>
                </div>
              </div>
              <div class="menu-card">
                <img
                  src="images/menu/drinks/smothiesstrawberry.jpg"
                  alt="Strawberry Smothies"
                  class="menu-card-img"
                />
                <h3 class="menu-card-title">Strawberry Smothies</h3>
                <p class="menu-card-price">$13</p>
                <div class="menu-card-desc">
                  <p class="desc-text">
                    Creamy and refreshing smoothie made with real strawberries
                    and milk blended...
                    <a href="#" class="read-more-link">more</a>
                  </p>
                  <div class="full-desc">
                    <p>
                      A Strawberry Smoothie is a refreshing and fruity blended
                      drink made with ripe strawberries, milk or yogurt, and
                      ice. Naturally sweet and slightly tangy, this smoothie is
                      both delicious and nutritious—packed with vitamin C,
                      antioxidants, and fiber. With its creamy texture and
                      bright pink color, it's perfect as a light breakfast,
                      healthy snack, or a cool treat on a sunny day. Often
                      enhanced with banana or honey, the strawberry smoothie is
                      a timeless favorite for all ages.
                    </p>
                    <a href="#" class="read-less-link">close</a>
                  </div>
                </div>
              </div>
              <div class="menu-card">
                <img
                  src="images/menu/drinks/latte.jpg"
                  alt="Creammy Latte"
                  class="menu-card-img"
                />
                <h3 class="menu-card-title">Creamy Latte</h3>
                <p class="menu-card-price">$14</p>
                <div class="menu-card-desc">
                  <p class="desc-text">
                    Rich and smooth espresso based drink blended with creamy
                    steamed milk... <a href="#" class="read-more-link">more</a>
                  </p>
                  <div class="full-desc">
                    <p>
                      A Creamy Latte is a smooth and velvety coffee drink made
                      with rich espresso and steamed milk, topped with a light
                      layer of milk foam. Known for its balanced flavor and
                      silky texture, this latte offers the perfect harmony
                      between bold coffee and mellow creaminess. The addition of
                      extra milk or cream gives it a luxurious mouthfeel, making
                      it a comforting choice for any time of day. Served hot or
                      iced, the creamy latte is a go-to classic for coffee
                      lovers who enjoy a mellow, indulgent sip.
                    </p>
                    <a href="#" class="read-less-link">close</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="location" class="contact">
      <h2>Visit <span>Us</span></h2>
      <h2 class="map-title">Address</h2>
      <div class="row">
        <iframe
          src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d3965.3630873750203!2d106.6918507476134!3d-6.347008103559609!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sid!2sid!4v1747843859236!5m2!1sid!2sid"
          allowfullscreen=""
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"
          class="map"
        ></iframe>
      </div>
      </section>
      <section id="contact" class="contact">
      <h2>Contact <span>Us</span></h2><br><br>
      <h2 class="form-title" id="location">Contact Us</h2>
      <div class="from">
        <form action="">
          <div class="input-group">
            <i data-feather="user"></i>
            <input type="text" placeholder="nama" />
          </div>
          <div class="input-group">
            <i data-feather="mail"></i>
            <input type="text" placeholder="email" />
          </div>
          <div class="input-group">
            <i data-feather="phone"></i>
            <input type="text" placeholder="no hp" />
          </div>
          <div class="input-group-text">
            <i data-feather="text"></i>
            <input type="text" placeholder="Enter your text here" />
          </div>
          <button type="submit" class="btn">Send Message</button>
        </form>
      </div>
    </section>

    <?php include 'footer.php'; ?>

    <div class="modal" id="item-detail-modal">
      <div class="modal-container">
        <a href="#" class="close-icon"><i data-feather="x"></i></a>
        <div class="modal-content">
          <img src="images/menu/foods/burger.jpg" alt="Product 1">
          <div class="product-content">
            <h3>Produk 1</h3>
            <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Cupiditate reprehenderit magnam sint officiis incidunt. Quisquam iste deserunt sed perspiciatis eligendi, qui quasi animi vero placeat.</p>
            <div class="product-star">
              <i data-feather="star" class="star"></i>
              <i data-feather="star" class="star"></i>
              <i data-feather="star" class="star"></i>
              <i data-feather="star" class="star"></i>
              <i data-feather="star"></i>
            </div>
            <div class="product-price">IDR 30K <span>50K</span></div>
            <a href="#"><i data-feather="shopping-cart"></i><span>add to cart</span></a>
          </div>
        </div>
      </div>
    </div>

    <script>
      feather.replace();
    </script>
    <script src="js/script.js"></script>
  </body>
</html>