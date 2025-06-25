<?php
// Pengecekan session_start() lebih baik di awal file, 
// sebelum output apapun, termasuk spasi atau HTML.
// Versi Anda sudah benar dengan if(!isset($_SESSION))
if(!isset($_SESSION)) {
    session_start();
}

// Pengecekan otentikasi pengguna
if(!isset($_SESSION['user'])) {
    // Sebaiknya tidak ada header("Location: ...") di file yang di-include seperti navbar.
    // Pengecekan ini lebih cocok ada di halaman utama yang meng-include navbar (misalnya, menulogin.php).
    // Jika navbar ini juga bisa dipakai di halaman publik yang tidak butuh login,
    // maka blok if ini harus dihilangkan atau disesuaikan.
    // Untuk saat ini, saya akan biarkan, tapi beri catatan.
    // header("Location: login.php"); 
    // exit(); 
    // Jika navbar ini khusus untuk halaman yang sudah login, maka ini OK.
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Navbar</title> <link rel="website icon" type="png" href="images/icon-lamperie.png" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,300;0,400;0,700;1,700&display=swap"
      rel="stylesheet"
    />
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="css/navbar.css" />
</head>
<body>
  <nav class="navbar">
      <a href="dashboard.php" class="navbar-logo"> <img src="images/icon-lamperie.png" alt="Lamperie Logo" class="img-navbar"/>
      </a>
      <div class="navbar-nav"> <a href="dashboard.php">Home</a>
        <a href="dashboard.php#about">About Us</a> <a href="menulogin.php">Menu</a>
        <a href="dashboard.php#location">Location</a>
        <a href="dashboard.php#contact">Contact Us</a>
        <a href="reservasi/dashboard_reservasi.php">Reservasi</a>
      </div>
      <div class="navbar-extra">
        <div class="dropdown">
            <a href="#" class="dropbtn" aria-label="Phone Contacts"><i data-feather="phone"></i></a>
            <div class="dropdown-content">
                <a href="tel:+6288">+6288xxxxxx</a> <a href="tel:+6255">+6255xxxxxx</a>
            </div>
        </div>
        <a href="#" id="hamburger" aria-label="Menu Toggle"><i data-feather="menu"></i></a>
        
        <a href="#" id="moon-btn" aria-label="Enable Dark Mode"> 
            <i data-feather="moon"></i>
        </a>
        <a href="#" id="sun-btn" style="display: none;" aria-label="Disable Dark Mode">
            <i data-feather="sun"></i>
        </a>
        
        <div class="dropdown">
            <a href="#" class="dropbtn">Hallo, <?php echo isset($_SESSION['user']['name']) ? htmlspecialchars($_SESSION['user']['name']) : 'User'; ?>!</a>
            <div class="dropdown-content">
                <a href="profil.php">My Profile</a>
                <a href="logout.php">Logout</a> </div>
        </div>
      </div>
    </nav>
    
    <script>
      // Panggil feather.replace() setelah DOM siap, idealnya di script utama.
      // Jika dipanggil di sini, pastikan tidak ada konflik.
      // feather.replace(); // Sebaiknya dipindah ke menulogin.js atau script utama halaman
    </script>
    </body>
</html>