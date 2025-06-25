<?php
// Path yang benar: Keluar satu level (../) untuk menemukan file di folder utama
include 'login_required.php';
// Memastikan hanya role kasir atau admin yang bisa akses
check_role(['kasir', 'admin']);

include '../koneksi.php';

// Query yang efisien untuk mengambil semua data pesanan
$sql = "SELECT 
            o.order_id, 
            u.name AS nama_pelanggan, 
            o.order_date, 
            o.order_status,
            o.total_price,
            GROUP_CONCAT(CONCAT(oi.item_name, ' (x', oi.quantity, ')') SEPARATOR '<br>') AS items_details
        FROM orders o
        JOIN users u ON o.user_id = u.id
        LEFT JOIN order_items oi ON o.order_id = oi.order_id
        GROUP BY o.order_id
        ORDER BY o.order_date DESC";

$result = $conn->query($sql);

$kasirName = htmlspecialchars($_SESSION['user']['name']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan - Panel Kasir</title>

    <link rel="stylesheet" href="assets/css/order_admin.css">
    <link rel="stylesheet" href="../admin/assets/css/admin.css">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-header">
            <h2><i class="fas fa-cash-register"></i> <span>Kasir</span></h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="orders_admin.php" class="active"><i class="fas fa-file-invoice-dollar icon"></i> <span class="menu-text">Kelola Pesanan</span></a></li>
            <li><a href="laporan_harian.php"><i class="fas fa-chart-line icon"></i> <span class="menu-text">Laporan Harian</span></a></li></ul>
        </ul>
        <div class="sidebar-footer">
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </aside>

    <main class="main-content">
        <header class="header">
            <h1>Manajemen Pesanan Masuk</h1>
            <div class="admin-info">
                Selamat Datang, <strong><?= $kasirName ?></strong>
            </div>
        </header>

        <section class="content-area">
            <div class="table-wrapper">
                <table class="content-table" style="width:100%;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Pelanggan</th>
                            <th>Tanggal</th>
                            <th>Item Dipesan</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>#<?= $row['order_id'] ?></td>
                                <td><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
                                <td><?= date('d M Y, H:i', strtotime($row['order_date'])) ?></td>
                                <td><?= $row['items_details'] ?? 'Tidak ada item' ?></td>
                                <td>$<?= number_format($row['total_price'], 2, ',', '.') ?></td>
                                <td>
                                    <?php
                                        $status_class = 'status-pending'; // default
                                        if (strtolower($row['order_status']) == 'completed') {
                                            $status_class = 'status-completed';
                                        } elseif (strtolower($row['order_status']) == 'cancelled') {
                                            $status_class = 'status-cancelled';
                                        }
                                    ?>
                                    <span class="status-badge <?= $status_class ?>"><?= htmlspecialchars($row['order_status']) ?></span>
                                </td>
                                <td class="actions">
                                    <a href="detail_pesanan.php?id=<?= $row['order_id'] ?>" class="details-btn">Detail</a>
                                    <a href="hapus_pesan.php?id=<?= $row['order_id'] ?>" class="delete-btn" onclick="return confirm('Anda yakin ingin menghapus pesanan ini?');">Hapus</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align:center;">Belum ada pesanan masuk.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

</body>
</html>