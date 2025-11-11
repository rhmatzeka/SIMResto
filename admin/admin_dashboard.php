<?php
include 'admin_only.php'; 
include '../koneksi.php';

// --- PENGAMBILAN DATA UNTUK STATISTIK DASHBOARD ---

// 1. Menghitung Total Pendapatan (asumsi semua pesanan dibayar)
$queryRevenue = "SELECT SUM(total_price) as total_revenue FROM orders";
$resultRevenue = mysqli_query($conn, $queryRevenue);
$revenueData = mysqli_fetch_assoc($resultRevenue);
// Format sebagai mata uang Rupiah, atau ganti dengan format lain jika perlu
$totalRevenue = "$ " . number_format($revenueData['total_revenue'] ?? 0, 0, ',', '.');

// 2. Menghitung Jumlah Total Pesanan
$queryOrders = "SELECT COUNT(order_id) as total_orders FROM orders";
$resultOrders = mysqli_query($conn, $queryOrders);
$totalOrders = mysqli_fetch_assoc($resultOrders)['total_orders'] ?? 0;

// 3. Menghitung Jumlah Menu
$queryMenuItems = "SELECT COUNT(menu_item_id) as total_items FROM menu_items";
$resultMenuItems = mysqli_query($conn, $queryMenuItems);
$totalMenuItems = mysqli_fetch_assoc($resultMenuItems)['total_items'] ?? 0;

// 4. Menghitung Jumlah Pengguna (yang bukan admin)
$queryUsers = "SELECT COUNT(id) as total_users FROM users WHERE role = 'user'";
$resultUsers = mysqli_query($conn, $queryUsers);
$totalUsers = mysqli_fetch_assoc($resultUsers)['total_users'] ?? 0;

// 5. Mengambil 5 Pesanan Terbaru
$queryRecentOrders = "
    SELECT o.order_id, u.name as customer_name, o.order_date, o.total_price, o.order_status
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.order_date DESC
    LIMIT 5";
$resultRecentOrders = mysqli_query($conn, $queryRecentOrders);


// Ambil nama admin dari session
$adminName = htmlspecialchars($_SESSION['user']['name']);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Lamperie Resto</title>

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
            <li><a href="#" class="active"><i class="fas fa-tachometer-alt icon"></i> <span class="menu-text">Dashboard</span></a></li>
            <li><a href="form_menu.php"><i class="fas fa-plus-square icon"></i> <span class="menu-text">Tambah Menu</span></a></li>
            <li><a href="data_menu.php"><i class="fas fa-utensils icon"></i> <span class="menu-text">Data Menu</span></a></li>
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
            <h1>Dashboard</h1>
            <div class="admin-info">
                Welcome, <strong><?= $adminName ?></strong>
            </div>
        </header>

        <section class="content-area">
            <div class="summary-cards">
                <div class="card card-revenue">
                    <div class="icon-wrapper"><i class="fas fa-wallet"></i></div>
                    <div class="card-info">
                        <h3 id="total-revenue-value"><?= $totalRevenue ?></h3>
                        <p>Total Revenue</p>
                    </div>
                </div>
                <div class="card card-orders">
                    <div class="icon-wrapper"><i class="fas fa-shopping-cart"></i></div>
                    <div class="card-info">
                        <h3><?= $totalOrders ?></h3>
                        <p>Number of Orders</p>
                    </div>
                </div>
                <div class="card card-menu">
                    <div class="icon-wrapper"><i class="fas fa-utensils"></i></div>
                    <div class="card-info">
                        <h3><?= $totalMenuItems ?></h3>
                        <p>Total Menu Items</p>
                    </div>
                </div>
                <div class="card card-users">
                    <div class="icon-wrapper"><i class="fas fa-users"></i></div>
                    <div class="card-info">
                        <h3><?= $totalUsers ?></h3>
                        <p>Total Customers</p>
                    </div>
                </div>
            </div>

            <div class="recent-orders">
                <h2>Latest Orders</h2>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer Name</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($resultRecentOrders) > 0): ?>
                                <?php while($order = mysqli_fetch_assoc($resultRecentOrders)): ?>
                                    <tr>
                                        <td>#<?= $order['order_id'] ?></td>
                                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                        <td><?= date('d M Y, H:i', strtotime($order['order_date'])) ?></td>
                                        <td>$ <?= number_format($order['total_price'], 2, '.', ',') ?></td>
                                        <td>
                                            <span class="status pending"><?= htmlspecialchars($order['order_status']) ?></span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align:center;">Belum ada pesanan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>

<script>
    // Menambahkan pembungkus agar skrip berjalan setelah halaman siap
    document.addEventListener('DOMContentLoaded', function() {
    
        // Fungsi untuk memperbarui total pendapatan
        function updateTotalRevenue() {
            // Ambil data terbaru dari server dengan sintaks fetch yang sudah diperbaiki
            fetch('get_revenue.php', { cache: 'no-cache' }) // <-- KOMA SUDAH DITAMBAHKAN DI SINI
                .then(response => response.json()) 
                .then(data => {
                    const revenueElement = document.getElementById('total-revenue-value');
                    
                    if (revenueElement) {
                        revenueElement.textContent = data.formatted_revenue;
                    }
                })
                .catch(error => console.error('Gagal mengambil data pendapatan:', error));
        }

        // Jalankan fungsi updateTotalRevenue setiap 10 detik
        setInterval(updateTotalRevenue, 10000); 

    });
</script>
</body>
</html>