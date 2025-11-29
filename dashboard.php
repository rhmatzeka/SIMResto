<?php
if(!isset($_SESSION)) {
    session_start();
}

if(!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

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
// --- BATAS KODE YANG DIPERBARUI ---?>

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
        <img src="images/arji1.jpeg" alt="" class="img-navbar"
      /></a>
      <div class="navbar-nav">
        <a href="dashboard.php">Home</a>
        <a href="#about">About Us</a>
        <a href="menulogin.php">Menu</a>
        <a href="#location">Location</a>
        <a href="#contact">Contact Us</a>
        <a href="reservasi/dashboard_reservasi.php">Reservasi</a>
      </div>
      <div class="navbar-extra">
    <div class="dropdown">
        <a href="" class="dropbtn"><i data-feather="phone"></i></a>
        <div class="dropdown-content">
            <a href="">+62877</a>
            <a href="">+62856</a>
        </div>
    </div>
    <a href="#" id="hamburger-menu"><i data-feather="menu"></i></a>
    <a href="#" id="moon-btn" onclick="setDarkMode(true)">
        <i data-feather="moon"></i>
    </a>
    <a href="#" id="sun-btn" onclick="setDarkMode(false)" style="display: none">
        <i data-feather="sun"></i>
    </a>
        <div class="dropdown">
            <a href="#" class="dropbtn">Hallo, <?php echo htmlspecialchars($_SESSION['user']['name']); ?>!</a>
            <div class="dropdown-content">
                <a href="profil.php">My Profile</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
</div>
    </nav>

    <section class="hero" id="home">
      <main class="content">
        <h1>let's take a break to get energy</h1>
        <p>Modern, Aunthentic Restaurant Food and Beverage Restaurant. We serve</p>
      </main>
    </section>
          <!-- end discount -->

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
        <h2><span class="team-members">- Team 5 -</span>Our Master Chefs</h2>
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

    <section id="location" class="contact">
      <h2>Visit <span>Us</span></h2>
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

    <!-- <?php include 'content_dashboard.php'; ?> -->
    <script>
      feather.replace();
    </script>
    <script src="js/script.js"></script>    
  </body>
</html>
