<?php
include 'admin_only.php';
include '../koneksi.php';

$error = ''; // Variabel untuk menampung pesan error
$success = ''; // Variabel untuk pesan sukses

// Logika untuk UBAH STATUS diskon
if (isset($_GET['toggle_status']) && isset($_GET['id'])) {
    $id_toggle = (int)$_GET['id'];
    
    // Ambil status saat ini
    $stmt_cek = $conn->prepare("SELECT status FROM discounts WHERE id = ?");
    $stmt_cek->bind_param("i", $id_toggle);
    $stmt_cek->execute();
    $result = $stmt_cek->get_result();
    if ($result->num_rows > 0) {
        $current_data = $result->fetch_assoc();
        $new_status = ($current_data['status'] == 'aktif') ? 'tidak aktif' : 'aktif';

        // Update status baru
        $stmt_update = $conn->prepare("UPDATE discounts SET status = ? WHERE id = ?");
        $stmt_update->bind_param("si", $new_status, $id_toggle);
        if ($stmt_update->execute()) {
            $success = "Status berhasil diubah.";
        } else {
            $error = "Gagal mengubah status.";
        }
        $stmt_update->close();
    }
    $stmt_cek->close();
    // Redirect untuk membersihkan URL dari parameter GET
    header("Location: kelola_diskon.php");
    exit;
}


// Logika untuk TAMBAH diskon
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_diskon'])) {
    $kode_diskon = strtoupper($_POST['kode_diskon']);
    $deskripsi = $_POST['deskripsi'];
    $tipe_diskon = $_POST['tipe_diskon'];
    $nilai_diskon = $_POST['nilai_diskon'];
    $status = $_POST['status'];
    // Menggunakan kolom DATETIME baru
    $waktu_mulai = empty($_POST['waktu_mulai']) ? null : $_POST['waktu_mulai'];
    $waktu_berakhir = empty($_POST['waktu_berakhir']) ? null : $_POST['waktu_berakhir'];
    $nama_gambar = null;

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == UPLOAD_ERR_OK) {
        $file_info = $_FILES['gambar'];
        $nama_gambar = uniqid() . '-' . basename($file_info['name']);
        $target_path = "../images/discounts/" . $nama_gambar;

        if (!is_dir(dirname($target_path))) {
            mkdir(dirname($target_path), 0777, true);
        }
        if (!move_uploaded_file($file_info['tmp_name'], $target_path)) {
            $nama_gambar = null;
            $error = "Gagal mengunggah file gambar.";
        }
    }

    if (empty($error)) {
        // Query INSERT disesuaikan dengan nama kolom baru
        $stmt = $conn->prepare("INSERT INTO discounts (kode_diskon, deskripsi, tipe_diskon, nilai_diskon, gambar, status, waktu_mulai, waktu_berakhir) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $kode_diskon, $deskripsi, $tipe_diskon, $nilai_diskon, $nama_gambar, $status, $waktu_mulai, $waktu_berakhir);
    
        if(!$stmt->execute()){
            $error = "Gagal menyimpan. Kode Diskon mungkin sudah ada. Error: " . $stmt->error;
        } else {
            header("Location: kelola_diskon.php");
            exit;
        }
    }
}

// Logika untuk HAPUS diskon
if (isset($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];
    $q_gambar = $conn->query("SELECT gambar FROM discounts WHERE id = $id_hapus");
    if ($q_gambar && $q_gambar->num_rows > 0) {
        $data_gambar = $q_gambar->fetch_assoc();
        if (!empty($data_gambar['gambar']) && file_exists("../images/discounts/" . $data_gambar['gambar'])) {
            unlink("../images/discounts/" . $data_gambar['gambar']);
        }
    }
    $conn->query("DELETE FROM discounts WHERE id = $id_hapus");
    header("Location: kelola_diskon.php");
    exit;
}

$semua_diskon = $conn->query("SELECT * FROM discounts ORDER BY id DESC");
$adminName = htmlspecialchars($_SESSION['user']['name']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Diskon - Admin Panel</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        .form-diskon { background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 30px; display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .form-diskon .full-width { grid-column: 1 / -1; }
        .form-diskon input, .form-diskon select, .form-diskon button, .form-diskon label { width: 100%; padding: 10px; margin-bottom: 5px; border-radius: 5px; border: 1px solid #ccc; box-sizing: border-box;}
        .form-diskon label { border: none; padding: 10px 0 0 0; margin-bottom: -5px; font-weight: 500;}
        .form-diskon button { background: #27ae60; color: white; border: none; cursor: pointer; font-weight: 500; grid-column: 1 / -1;}
        .tabel-diskon { width: 100%; border-collapse: collapse; background-color: #fff; }
        .tabel-diskon th, .tabel-diskon td { border: 1px solid #ddd; padding: 10px; text-align: left; vertical-align: middle; font-size: 0.9rem; }
        .tabel-diskon thead { background-color: #f2f2f2; }
        .tabel-diskon img { max-width: 120px; height: auto; border-radius: 5px; }
        .status-aktif { color: green; font-weight: bold; }
        .status-tidak-aktif { color: red; font-weight: bold; }
        .action-links a { margin-right: 10px; text-decoration: none; }
        .action-links a.hapus { color: #e74c3c; }
        .action-links a.toggle { color: #3498db; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-header">
            <h2><i class="fas fa-mug-hot"></i> <span>Lamperie</span></h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt icon"></i> <span class="menu-text">Dashboard</span></a></li>
            <li><a href="form_menu.php"><i class="fas fa-plus-square icon"></i> <span class="menu-text">Tambah Menu</span></a></li>
            <li><a href="data_menu.php"><i class="fas fa-utensils icon"></i> <span class="menu-text">Data Menu</span></a></li>
            <li><a href="kelola_berita.php"><i class="fas fa-newspaper icon"></i> <span class="menu-text">Update Berita</span></a></li>
            <li class="active"><a href="kelola_diskon.php"><i class="fas fa-percent icon"></i> <span class="menu-text">Kelola Diskon</span></a></li>
            <li><a href="kelola_pengguna.php"><i class="fas fa-users icon"></i> <span class="menu-text">Kelola Pengguna</span></a></li>
        </ul>
        <div class="sidebar-footer">
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </aside>

    <main class="main-content">
        <header class="header">
            <h1>Manage Discounts & Promos</h1>
            <div class="admin-info">Welcome, <strong><?= $adminName ?></strong></div>
        </header>

        <section class="content-area">
            <div class="form-diskon">
                <h3 class="full-width">Add New Discount</h3>
                <?php if (!empty($error)) { echo "<p class='full-width' style='color:red;'>$error</p>"; } ?>
                <?php if (!empty($success)) { echo "<p class='full-width' style='color:green;'>$success</p>"; } ?>
                <form action="kelola_diskon.php" method="POST" enctype="multipart/form-data" class="full-width" style="display: contents;">
                    
                    <div class="form-group"><input type="text" name="kode_diskon" placeholder="Kode Diskon" required></div>
                    <div class="form-group"><input type="text" name="deskripsi" placeholder="Deskripsi Singkat" required></div>
                    <div class="form-group"><select name="tipe_diskon" required><option value="persen">Persen (%)</option><option value="tetap">Potongan Tetap ($)</option></select></div>
                    <div class="form-group"><input type="number" step="0.01" name="nilai_diskon" placeholder="Nilai" required></div>
                    
                    <div class="form-group">
                        <label for="waktu_mulai">Waktu Mulai (Opsional)</label>
                        <input type="datetime-local" id="waktu_mulai" name="waktu_mulai">
                    </div>
                    <div class="form-group">
                        <label for="waktu_berakhir">Waktu Berakhir (Opsional)</label>
                        <input type="datetime-local" id="waktu_berakhir" name="waktu_berakhir">
                    </div>
                    
                    <div class="form-group full-width"><label>Gambar Promo (Opsional)</label><input type="file" name="gambar" accept="image/*"></div>
                    <div class="form-group full-width"><select name="status" required><option value="aktif">Aktif</option><option value="tidak aktif">Tidak Aktif</option></select></div>
                    
                    <button type="submit" name="tambah_diskon">Simpan Diskon</button>
                </form>
            </div>

            <h2>Daftar Diskon</h2>
            <div class="table-wrapper">
                <table class="tabel-diskon">
                    <thead>
                        <tr>
                            <th>Gambar</th>
                            <th>Kode</th>
                            <th>Deskripsi</th>
                            <th>Nilai</th>
                            <th>Periode Aktif</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($semua_diskon && $semua_diskon->num_rows > 0): ?>
                            <?php while($diskon = $semua_diskon->fetch_assoc()): ?>
                                <tr>
                                    <td><img src="../images/discounts/<?= htmlspecialchars($diskon['gambar'] ?? 'placeholder.png') ?>" alt="Gambar Diskon"></td>
                                    <td><strong><?= htmlspecialchars($diskon['kode_diskon']) ?></strong></td>
                                    <td><?= htmlspecialchars($diskon['deskripsi']) ?></td>
                                    <td><?= ($diskon['tipe_diskon'] == 'persen') ? htmlspecialchars($diskon['nilai_diskon']) . '%' : 'Rp ' . number_format($diskon['nilai_diskon'], 0, ',', '.') ?></td>
                                    <td>
                                        <?php
                                            // DIUBAH: Format tanggal menjadi menyertakan jam dan menit (H:i)
                                            if (!empty($diskon['waktu_mulai'])) { echo "Mulai: " . date('d M Y, H:i', strtotime($diskon['waktu_mulai'])) . "<br>"; }
                                            if (!empty($diskon['waktu_berakhir'])) { echo "Selesai: " . date('d M Y, H:i', strtotime($diskon['waktu_berakhir'])); }
                                            if (empty($diskon['waktu_mulai']) && empty($diskon['waktu_berakhir'])) { echo 'Selamanya'; }
                                        ?>
                                    </td>
                                    <td class="status-<?= str_replace(' ', '-', $diskon['status']) ?>"><?= htmlspecialchars(ucfirst($diskon['status'])) ?></td>
                                    <td class="action-links">
                                        <a class="toggle" href="kelola_diskon.php?toggle_status=1&id=<?= $diskon['id'] ?>" onclick="return confirm('Anda yakin ingin mengubah status diskon ini?')">
                                            <?= ($diskon['status'] == 'aktif' ? 'Nonaktifkan' : 'Aktifkan') ?>
                                        </a>
                                        <a class="hapus" href="kelola_diskon.php?hapus=<?= $diskon['id'] ?>" onclick="return confirm('Anda yakin ingin menghapus diskon ini secara permanen?')">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="7" style="text-align:center;">Belum ada diskon.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>