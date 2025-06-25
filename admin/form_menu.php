<?php
include 'admin_only.php';
include '../koneksi.php';

$is_edit = false;
$page_title = 'Add New Menu';
$menu_data = [
    'menu_item_id' => null,
    'item_name' => '',
    'description' => '',
    'price' => '',
    'category' => 'Main Course',
    'image_url' => ''
];

// Logika untuk mode Edit
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $is_edit = true;
    $id = (int)$_GET['edit'];
    $page_title = 'Edit Menu';

    $stmt = $conn->prepare("SELECT * FROM menu_items WHERE menu_item_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $menu_data = $result->fetch_assoc();
    } else {
        header('Location: data_menu.php');
        exit;
    }
}

// Logika untuk memproses form submission (Tambah atau Update)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_name = $_POST['item_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $image_name = $menu_data['image_url']; // Default ke gambar lama jika ada (untuk mode edit)

    // PERIKSA DAN PROSES UPLOAD GAMBAR JIKA ADA
    if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] == UPLOAD_ERR_OK) {
        $file_info = $_FILES['image_url'];
        // Beri nama unik untuk menghindari file dengan nama sama saling menimpa
        $image_name = uniqid() . '-' . basename($file_info['name']);

        // **KODE KRUSIAL: Path upload yang benar**
        // Keluar dulu dari folder 'admin' (dengan ../) lalu masuk ke 'images'
        $upload_dir = __DIR__ . "/../images/menu/" . $category . "/";

        // Buat direktori berdasarkan kategori jika belum ada
        if (!is_dir($upload_dir)) {
            // Parameter ketiga 'true' memungkinkan pembuatan folder secara rekursif
            if (!mkdir($upload_dir, 0777, true)) {
                die("GAGAL: Tidak bisa membuat direktori di: " . $upload_dir . ". Periksa izin folder!");
            }
        }

        $target_path = $upload_dir . $image_name;

        // Pindahkan file yang di-upload ke direktori tujuan
        if (!move_uploaded_file($file_info['tmp_name'], $target_path)) {
            die("GAGAL: File tidak bisa dipindahkan ke " . $target_path . ". Pastikan folder tujuan dapat ditulisi (writable) oleh server!");
        }
    }

    // Lanjutkan ke proses database
    if ($is_edit) {
        $id_to_update = (int)$_POST['id'];
        $stmt = $conn->prepare("UPDATE menu_items SET item_name=?, description=?, price=?, category=?, image_url=? WHERE menu_item_id=?");
        $stmt->bind_param("ssdssi", $item_name, $description, $price, $category, $image_name, $id_to_update);
    } else {
        $stmt = $conn->prepare("INSERT INTO menu_items (item_name, description, price, category, image_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdss", $item_name, $description, $price, $category, $image_name);
    }

    if ($stmt->execute()) {
        header('Location: data_menu.php');
        exit;
    } else {
        // Jika query gagal, tampilkan error
        die("Error database: " . $stmt->error);
    }
}

$adminName = htmlspecialchars($_SESSION['user']['name']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - Admin Panel</title>

    <link rel="stylesheet" href="assets/css/form_menu.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-header">
            <h2><i class="fas fa-mug-hot"></i> <span>Lamperie</span></h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt icon"></i> <span class="menu-text">Dashboard</span></a></li>
            <li><a href="form_menu.php" class="active"><i class="fas fa-plus-square icon"></i> <span class="menu-text">Add Menu</span></a></li>
            <li><a href="data_menu.php"><i class="fas fa-utensils icon"></i> <span class="menu-text">Data Menu</span></a></li>
            <li><a href="kelola_berita.php"><i class="fas fa-newspaper icon"></i> <span class="menu-text">Update Berita</span></a></li>
            <li><a href="kelola_diskon.php"><i class="fas fa-percent icon"></i> <span class="menu-text">Kelola Diskon</span></a></li>
        </ul>
        <div class="sidebar-footer">
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </aside>

    <main class="main-content">
        <header class="header">
            <h1><?= $page_title ?></h1>
            <div class="admin-info">Welcome, <strong><?= $adminName ?></strong></div>
        </header>

        <section class="content-area">
            <div class="form-container">
                <form method="POST" enctype="multipart/form-data" action="">
                    <?php if ($is_edit): ?>
                        <input type="hidden" name="id" value="<?= $menu_data['menu_item_id'] ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="item_name">Menu Name</label>
                        <input type="text" id="item_name" name="item_name" class="form-control" required value="<?= htmlspecialchars($menu_data['item_name']) ?>">
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="4"><?= htmlspecialchars($menu_data['description']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="price">Price ($)</label>
                        <input type="number" step="0.01" id="price" name="price" class="form-control" required value="<?= htmlspecialchars($menu_data['price']) ?>">
                    </div>

                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category" class="form-control">
                            <?php
                            $kategori = ['Main Course', 'Appetizer', 'Snacks', 'Dessert', 'Non-Coffee', 'Coffee', 'Juice'];
                            foreach ($kategori as $k) {
                                $selected = ($menu_data['category'] == $k) ? 'selected' : '';
                                echo "<option value=\"$k\" $selected>$k</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="image_url">Menu Picture</label>
                        <input type="file" id="image_url" name="image_url" class="form-control">
                        <?php if ($is_edit && !empty($menu_data['image_url'])): ?>
                            <small>Gambar saat ini:</small>
                            <img src="images/menu/<?= htmlspecialchars($menu_data['image_url']) ?>" alt="Gambar Menu" class="current-image">
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn-submit"><?= $is_edit ? 'Simpan Perubahan' : 'Add Menu' ?></button>
                    <a href="data_menu.php" class="btn-cancel">cancel</a>
                </form>
            </div>
        </section>
    </main>

</body>
</html>