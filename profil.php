<?php
session_start();
require_once 'koneksi.php';

// Redirect jika belum login
if(!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Ambil data user terbaru dari database
$user_id = $_SESSION['user']['id'];
$query = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $query->fetch_assoc();
$_SESSION['user'] = $user;

$error = '';
$success = '';

// Tentukan konten mana yang akan ditampilkan (untuk riwayat/detail)
$order_content = ''; // Default tidak ada konten pesanan
if (isset($_GET['tab'])) {
    if ($_GET['tab'] === 'order-history') {
        $order_content = 'history'; // Akan menampilkan riwayat pesanan
    } elseif ($_GET['tab'] === 'order-detail') {
        $order_content = 'detail'; // Akan menampilkan detail pesanan
    }
}

// Proses update profil jika ada data POST
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone'] ?? '');

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "images/upload/"; // Directory to save profile pictures
        // Create directory if it doesn't exist
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_extension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
        $new_file_name = uniqid() . '.' . $file_extension; // Generate a unique filename
        $target_file = $target_dir . $new_file_name;
        $uploadOk = 1;
        $imageFileType = strtolower($file_extension);

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES['profile_picture']['tmp_name']);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            $error .= " File bukan gambar.";
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES['profile_picture']['size'] > 5000000) { // 5MB limit
            $error .= " Ukuran file terlalu besar.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
            $error .= " Hanya format JPG, JPEG, PNG & GIF yang diizinkan.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            $error .= " Foto profil gagal diunggah.";
        } else {
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
                // Delete old profile picture if exists
                if (!empty($user['profile_picture']) && file_exists($user['profile_picture'])) {
                    unlink($user['profile_picture']);
                }
                $conn->query("UPDATE users SET profile_picture='$target_file' WHERE id=$user_id");
                $success .= " Foto profil berhasil diunggah.";
            } else {
                $error .= " Terjadi kesalahan saat mengunggah foto profil.";
            }
        }
    }
    
    // Handle perubahan password jika diisi
    if(!empty($_POST['current_password']) && !empty($_POST['new_password'])) {
        if(password_verify($_POST['current_password'], $user['password'])) {
            if($_POST['new_password'] === $_POST['confirm_password']) {
                $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
                $conn->query("UPDATE users SET password='$new_password' WHERE id=$user_id");
                $success .= " Password berhasil diubah!";
            } else {
                $error = "Password baru tidak cocok!";
            }
        } else {
            $error = "Password saat ini salah!";
        }
    }
    
    // Update data lainnya
    if(empty($error) || (isset($error) && strpos($error, "Foto profil gagal diunggah.") === false && strpos($error, "File bukan gambar.") === false && strpos($error, "Ukuran file terlalu besar.") === false && strpos($error, "Hanya format JPG, JPEG, PNG & GIF yang diizinkan.") === false)) { 
        $conn->query("UPDATE users SET name='$name', email='$email', phone='$phone' WHERE id=$user_id");
        
        // Ambil data terbaru
        $query = $conn->query("SELECT * FROM users WHERE id = $user_id");
        $user = $query->fetch_assoc();
        $_SESSION['user'] = $user;
        
        if (empty($success) || (isset($success) && strpos($success, "Foto profil berhasil diunggah.") === false)) {
            $success = "Profil berhasil diperbarui!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Profil</title>
  <link rel="stylesheet" href="CSS/profil.css" />
  <link rel="stylesheet" href="CSS/riwayat_pesanan.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
  <div class="container light-style flex-grow-1 container-p-y">
    <div class="header-with-logo">
          <img src="images/icon-lamperie.png" alt="Logo" class="img-logo"> 
          <h4 class="font-weight-bold py-3 mb-4">Account Profile</h4>
      </div>

    <?php if(isset($error) && !empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if(isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="card overflow-hidden">
      <div class="row no-gutters row-bordered row-border-light">
        <div class="col-md-3 pt-0">
            <div class="profile-sidebar-header text-center p-3">
                <img src="<?php echo htmlspecialchars($user['profile_picture'] ?? 'images/default-profile.png'); ?>" alt="Profile Picture" class="user-profile-img-sidebar" id="profileImageDisplaySidebar">
                <h5 class="user-name-sidebar mt-2"><?php echo htmlspecialchars($user['name']); ?></h5>
                
                <p class="text-muted small mb-2">ID: <?= htmlspecialchars($user['member_id'] ?? 'Belum Ada'); ?></p>
                
                <div class="user-contact-info-sidebar">
                    <p class="user-email-sidebar mb-0"><?php echo htmlspecialchars($user['email']); ?></p>
                    <p class="user-phone-sidebar mt-0"><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></p>
                </div>
            </div>
          <hr class="my-0">
          <div class="list-group list-group-flush account-settings-links">
            <a class="list-group-item list-group-item-action mb-2 <?php echo (!isset($_GET['tab']) || $_GET['tab'] === 'account-general') ? 'active' : ''; ?>" data-toggle="list" href="#account-general">Umum</a>
            
            <a class="list-group-item list-group-item-action mb-2 <?php echo (isset($_GET['tab']) && $_GET['tab'] === 'account-change-password') ? 'active' : ''; ?>" data-toggle="list" href="#account-change-password">Ubah Kata Sandi</a>
            
            <a class="list-group-item list-group-item-action mb-2 <?php echo (isset($_GET['tab']) && $_GET['tab'] === 'account-info') ? 'active' : ''; ?>" data-toggle="list" href="#account-info">Info</a>
            
            <a class="list-group-item list-group-item-action <?php echo (isset($_GET['tab']) && ($_GET['tab'] === 'order-history' || $_GET['tab'] === 'order-detail')) ? 'active' : ''; ?>" href="profil.php?tab=order-history">Riwayat Pesanan</a>

            <button type="button" id="editProfileBtnSidebar" class="list-group-item list-group-item-action mt-2">Edit Profil</button>
          </div>
        </div>

        <div class="col-md-9">
          <form id="profileForm" method="POST" enctype="multipart/form-data">
          <div class="tab-content">
            <div class="tab-pane fade <?php echo (!isset($_GET['tab']) || $_GET['tab'] === 'account-general') ? 'active show' : ''; ?>" id="account-general">
               <div class="card-body">
                  <div class="form-group" id="nameEmailEditFields">
                      <label class="form-label text-white">Nama Lengkap</label>
                      <input type="text" class="form-control mb-1" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required readonly>
                      <label class="form-label mt-2 text-white">E-mail</label>
                      <input type="email" class="form-control mb-1" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required readonly>
                  </div>
                  <div class="form-group" id="profilePictureUpload" style="display: none;">
                      <label class="form-label text-white">Upload Foto Profil</label>
                      <input type="file" class="form-control-file" name="profile_picture" accept="image/*">
                      <small class="form-text text-muted text-white">Maksimal ukuran file 5MB. Format: JPG, JPEG, PNG, GIF.</small>
                  </div>
                </div>
              </div>

            <div class="tab-pane fade <?php echo (isset($_GET['tab']) && $_GET['tab'] === 'account-change-password') ? 'active show' : ''; ?>" id="account-change-password">
                <div class="card-body pb-2">
                  <div class="form-group">
                    <label class="form-label text-white">Kata Sandi Saat Ini</label>
                    <input type="password" class="form-control" name="current_password" readonly>
                  </div>
                  <div class="form-group">
                    <label class="form-label text-white">Kata Sandi Baru</label>
                    <input type="password" class="form-control" name="new_password" readonly>
                  </div>
                  <div class="form-group">
                    <label class="form-label text-white">Ulangi Kata Sandi Baru</label>
                    <input type="password" class="form-control" name="confirm_password" readonly>
                  </div>
                </div>
              </div>

            <div class="tab-pane fade <?php echo (isset($_GET['tab']) && $_GET['tab'] === 'account-info') ? 'active show' : ''; ?>" id="account-info">
                <div class="card-body pb-2">
                    <div class="form-group">
                      <label class="form-label text-white">Telepon</label>
                      <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" readonly>
                    </div>
                </div>
              </div>

            <div class="tab-pane fade <?php echo (isset($_GET['tab']) && ($_GET['tab'] === 'order-history' || $_GET['tab'] === 'order-detail')) ? 'active show' : ''; ?>" id="order-history-tab">
                <div class="card-body pb-2">
                    <?php if ($order_content === 'history'): ?>
                        <?php include 'riwayat_pesanan.php'; ?>
                    <?php elseif ($order_content === 'detail'): ?>
                        <?php include 'detail_pesanan.php'; ?>
                    <?php else: ?>
                        <?php include 'riwayat_pesanan.php'; ?>
                    <?php endif; ?>
                </div>
              </div>

            </div> <div id="saveCancelButtons" class="text-right mt-3 p-3" style="display: none;">
              <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
              <a href="profil.php" class="btn btn-default">Batal</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/feather-icons"></script>
  <script>
      feather.replace();

      $(document).ready(function() {
          var urlParams = new URLSearchParams(window.location.search);
          var activeTab = urlParams.get('tab');
          var orderIdParam = urlParams.get('order_id');
          
          // Variabel global untuk menyimpan status mode edit
          var isEditMode = false; 

          // Hapus semua kelas active/show dari tab dan tautan sidebar terlebih dahulu
          $('.list-group-item-action').removeClass('active');
          $('.tab-pane.fade').removeClass('active show');

          // Fungsi untuk mengatur readonly pada input form
          function setFormReadonly(isReadonly) {
              // Input di tab Umum (nama, email) hanya terlihat saat edit
              $('#nameEmailEditFields').toggle(!isReadonly); 
              $('#profilePictureUpload').toggle(!isReadonly);
              $('#account-general input[name="name"]').prop('readonly', isReadonly);
              $('#account-general input[name="email"]').prop('readonly', isReadonly);

              // Input password selalu readonly kecuali edit mode aktif
              $('#account-change-password input').prop('readonly', isReadonly);
              // Input telepon di tab Info
              $('#account-info input[name="phone"]').prop('readonly', isReadonly);
          }

          // Fungsi untuk menampilkan/menyembunyikan tombol Simpan/Batal
          function toggleSaveCancelButtons(show) {
              if (show) {
                  $('#saveCancelButtons').show();
              } else {
                  $('#saveCancelButtons').hide();
              }
          }

          // Fungsi untuk mengatur tampilan tombol Edit Profil di sidebar
          function toggleEditProfileButton(show) {
              if (show) {
                  $('#editProfileBtnSidebar').show();
              } else {
                  $('#editProfileBtnSidebar').hide();
              }
          }

          // Fungsi untuk masuk ke mode edit
          function enterEditMode() {
              isEditMode = true;
              setFormReadonly(false);
              toggleEditProfileButton(false);
              toggleSaveCancelButtons(true);
              // Pindah ke tab Umum saat masuk mode edit
              $('a[href="#account-general"]').tab('show');
          }

          // Fungsi untuk keluar dari mode edit (Simpan atau Batal)
          function exitEditMode() {
              isEditMode = false;
              setFormReadonly(true);
              toggleEditProfileButton(true);
              toggleSaveCancelButtons(false);
              // Pastikan input file dan nama/email tersembunyi
              $('#profilePictureUpload').hide();
              $('#nameEmailEditFields').hide();
          }

          // Inisialisasi tampilan berdasarkan tab aktif
          if (activeTab) {
              if (activeTab === 'account-general' || activeTab === 'account-change-password' || activeTab === 'account-info') {
                  $('a[href="#' + activeTab + '"]').addClass('active');
                  $('#' + activeTab).addClass('active show');
                  
                  <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && (isset($_GET['tab']) && ($_GET['tab'] === 'account-general' || $_GET['tab'] === 'account-change-password' || $_GET['tab'] === 'account-info'))): ?>
                      enterEditMode();
                  <?php else: ?>
                      exitEditMode(); 
                  <?php endif; ?>

              } else if (activeTab === 'order-history' || activeTab === 'order-detail') {
                  $('a[href="profil.php?tab=order-history"]').addClass('active');
                  $('#order-history-tab').addClass('active show');
                  exitEditMode(); 
              }
          } else {
              $('a[href="#account-general"]').addClass('active');
              $('#account-general').addClass('active show');
              exitEditMode(); 
          }

          // Handler untuk tombol "Edit Profil" di sidebar
          $('#editProfileBtnSidebar').on('click', function() {
              enterEditMode();
          });

          // Handler untuk klik tombol "Batal"
          $('#saveCancelButtons .btn-default').on('click', function(e) {
              e.preventDefault(); 
              exitEditMode();
              // Muat ulang halaman untuk mengembalikan nilai form asli dan tampilan default
              window.location.href = "profil.php?tab=" + (activeTab || 'account-general'); 
          });

          // Handler for profile picture input change to show preview
          $('input[name="profile_picture"]').on('change', function() {
              if (this.files && this.files[0]) {
                  var reader = new FileReader();
                  reader.onload = function(e) {
                      $('#profileImageDisplaySidebar').attr('src', e.target.result); // Update sidebar image
                  }
                  reader.readAsDataURL(this.files[0]);
              }
          });

          // Handler untuk klik link sidebar (selain tombol Edit Profil)
          $('.list-group-item-action:not(#editProfileBtnSidebar)').on('click', function() {
              var targetTabHref = $(this).attr('href');
              
              var isEditableTab = (targetTabHref === '#account-general' || targetTabHref === '#account-change-password' || targetTabHref === '#account-info');
              var isOrderTab = (targetTabHref === 'profil.php?tab=order-history');

              if (isEditableTab) {
                  if (!isEditMode) {
                      exitEditMode(); 
                  }
              } else if (isOrderTab) {
                  exitEditMode(); 
              }
          });

      });
  </script>
</body>

</html>