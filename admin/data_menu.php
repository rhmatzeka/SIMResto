<?php
// Selalu sertakan keamanan dan koneksi di awal
include 'admin_only.php'; //
include '../koneksi.php'; //

// Ambil semua data menu dari database, diurutkan berdasarkan kategori
$result = mysqli_query($conn, "SELECT * FROM menu_items ORDER BY category, item_name"); //

// Ambil nama admin dari session untuk ditampilkan di header
$adminName = htmlspecialchars($_SESSION['user']['name']); //
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Menu - Admin Panel</title>

    <link rel="stylesheet" href="assets/css/data_menu.css">
    <link rel="stylesheet" href="assets/css/admin.css"> <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-header">
            <h2><i class="fas fa-mug-hot"></i> <span>Lamperie</span></h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt icon"></i> <span class="menu-text">Dashboard</span></a></li>
            <li><a href="form_menu.php"><i class="fas fa-plus-square icon"></i> <span class="menu-text">Add Menu</span></a></li>
            <li><a href="data_menu.php" class="active"><i class="fas fa-utensils icon"></i> <span class="menu-text">Data Menu</span></a></li>
            <li><a href="kelola_berita.php"><i class="fas fa-newspaper icon"></i> <span class="menu-text">Update Berita</span></a></li>
            <li><a href="kelola_diskon.php"><i class="fas fa-percent icon"></i> <span class="menu-text">Kelola Diskon</span></a></li>
            <li><a href="kelola_pengguna.php"><i class="fas fa-users icon"></i> <span class="menu-text">Kelola Pengguna</span></a></li>
        </ul>
        <div class="sidebar-footer">
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </aside>

    <main class="main-content">
        <header class="header">
            <h1>Menu Data Management</h1>
            <div class="admin-info">
                Welcome, <strong><?= $adminName ?></strong>
            </div>
        </header>

        <section class="content-area">
            <div class="page-header">
                <h2>List All Menus</h2>
                <a href="form_menu.php" class="add-new-btn"><i class="fas fa-plus"></i>  Add New Menu</a>
            </div>

            <div class="table-wrapper">
                <table class="content-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Item Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($result) > 0): ?>
                            <?php while($item = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= $item['menu_item_id'] ?></td>
                                <td><?= htmlspecialchars($item['item_name']) ?></td>
                                <td><?= htmlspecialchars($item['category']) ?></td>
                                <td>$ <?= number_format($item['price'], 2) ?></td>
                                <td class="actions">
                                    <a href="form_menu.php?edit=<?= $item['menu_item_id'] ?>" class="edit-btn">Edit</a>
                                    <a href="hapus_menu.php?id=<?= $item['menu_item_id'] ?>" class="delete-btn" onclick="return confirm('Apakah Anda yakin ingin menghapus item ini?');">
                                        Delete
                                    </a>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align:center;">Belum ada data menu.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

</body>
</html>