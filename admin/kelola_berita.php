<?php
include 'admin_only.php';
include '../koneksi.php';

// Logika untuk HAPUS berita
if (isset($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];
    // Ambil nama file gambar untuk dihapus dari server
    $q_gambar = $conn->query("SELECT gambar FROM berita WHERE id = $id_hapus");
    if ($q_gambar && $q_gambar->num_rows > 0) {
        $data_gambar = $q_gambar->fetch_assoc();
        $file_gambar = $data_gambar['gambar'];
        // Path yang benar untuk menghapus gambar dari folder utama
        if ($file_gambar && file_exists("../images/berita/" . $file_gambar)) {
            unlink("../images/berita/" . $file_gambar);
        }
    }
    $conn->query("DELETE FROM berita WHERE id = $id_hapus");
    header("Location: kelola_berita.php");
    exit;
}

// Logika untuk TAMBAH berita
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul'];
    $konten = $_POST['konten'];
    $nama_gambar = null; // Default gambar adalah null

    // Proses upload gambar jika ada file baru yang diunggah
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == UPLOAD_ERR_OK) {
        $nama_gambar = uniqid() . '-' . basename($_FILES['gambar']['name']);
        
        // **PATH PENYIMPANAN YANG SUDAH DIPERBAIKI**
        // Keluar dari folder 'admin' (../) lalu masuk ke 'images/berita/'
        $target_path = "../images/berita/" . $nama_gambar;

        // Pastikan folder ../images/berita/ sudah ada
        if (!is_dir(dirname($target_path))) {
            mkdir(dirname($target_path), 0777, true);
        }

        if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $target_path)) {
            $nama_gambar = null; // Jika gagal upload, jangan simpan nama gambar ke DB
        }
    }

    $stmt = $conn->prepare("INSERT INTO berita (judul, konten, gambar) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $judul, $konten, $nama_gambar);
    $stmt->execute();
    header("Location: kelola_berita.php");
    exit;
}

// Ambil semua data berita untuk ditampilkan
$semua_berita = $conn->query("SELECT * FROM berita ORDER BY tanggal_post DESC");
$adminName = htmlspecialchars($_SESSION['user']['name']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>News & Information Updates</title>
    <link rel="stylesheet" href="assets/css/kelola_berita.css">
    <link rel="stylesheet" href="assets/css/admin.css">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header"><h2><i class="fas fa-mug-hot"></i> <span>Lamperie</span></h2></div>
        <ul class="sidebar-menu">
            <li><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt icon"></i> <span class="menu-text">Dashboard</span></a></li>
            <li><a href="form_menu.php"><i class="fas fa-plus-square icon"></i> <span class="menu-text">Tambah Menu</span></a></li>
            <li><a href="data_menu.php"><i class="fas fa-utensils icon"></i> <span class="menu-text">Data Menu</span></a></li>
            <li><a href="kelola_berita.php" class="active"><i class="fas fa-newspaper icon"></i> <span class="menu-text">Update Berita</span></a></li>
            <li><a href="kelola_diskon.php"><i class="fas fa-percent icon"></i> <span class="menu-text">Kelola Diskon</span></a></li>
            <li><a href="kelola_pengguna.php"><i class="fas fa-users icon"></i> <span class="menu-text">Kelola Pengguna</span></a></li>
        </ul>
        <div class="sidebar-footer"><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></div>
    </aside>

    <main class="main-content">
        <header class="header">
            <h1>Update Berita & Informasi</h1>
            <div class="admin-info">Welcome, <strong><?= $adminName ?></strong></div>
        </header>

        <section class="content-area">
            <div class="form-berita">
                <h3>Add New News</h3>
                <form action="kelola_berita.php" method="POST" enctype="multipart/form-data">
                    <input type="text" name="judul" placeholder="News Title" required>
                    <textarea name="konten" rows="5" placeholder="Isi konten berita..." required></textarea>
                    <label>News Pictures (Opsional)</label>
                    <input type="file" name="gambar" accept="image/*">
                    <button type="submit">Save News</button>
                </form>
            </div>

            <h2>News List</h2>
            <div class="table-wrapper">
                <table class="tabel-berita">
                    <thead><tr><th>Picture</th><th>Title</th><th>Date</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php if ($semua_berita && $semua_berita->num_rows > 0): ?>
                            <?php while($berita = $semua_berita->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <img src="../images/berita/<?= htmlspecialchars($berita['gambar'] ?? 'placeholder.png') ?>" alt="Gambar Berita">
                                    </td>
                                    <td><?= htmlspecialchars($berita['judul']) ?></td>
                                    <td><?= date('d M Y', strtotime($berita['tanggal_post'])) ?></td>
                                    <td>
                                        <a href="kelola_berita.php?hapus=<?= $berita['id'] ?>" onclick="return confirm('Anda yakin ingin menghapus berita ini?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" style="text-align:center;">Belum ada berita.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>