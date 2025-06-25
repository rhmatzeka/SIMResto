<?php
// Path yang benar: Keluar satu level (../) untuk menemukan file di folder utama
include 'login_required.php';
check_role(['kasir', 'admin']);

include '../koneksi.php';

if (!isset($_GET['id'])) {
    header('Location: orders_admin.php');
    exit;
}
$order_id = (int)$_GET['id'];

// Ambil data utama pesanan dan data pengguna
// Mengambil semua kolom dari tabel 'orders' termasuk info diskon
$sql_order = "SELECT o.*, u.name as customer_name, u.email as customer_email, u.phone as customer_phone FROM orders o JOIN users u ON o.user_id = u.id WHERE o.order_id = ?";
$stmt_order = $conn->prepare($sql_order);
$stmt_order->bind_param("i", $order_id);
$stmt_order->execute();
$result_order = $stmt_order->get_result();
if ($result_order->num_rows === 0) {
    header('Location: orders_admin.php');
    exit;
}
$order = $result_order->fetch_assoc();

// Ambil semua item yang ada di dalam pesanan ini dari tabel 'order_items'
// Kode ini sudah benar menggunakan tabel 'order_items'
$sql_items = "SELECT oi.item_name, oi.quantity, oi.price_per_item, oi.subtotal FROM order_items oi WHERE oi.order_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$result_items = $stmt_items->get_result();

$kasirName = htmlspecialchars($_SESSION['user']['name']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan #<?= $order['order_id'] ?> - Panel Kasir</title>

    <link rel="stylesheet" href="assets/css/detail_pesanan.css">
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
            <li><a href="laporan_harian.php"><i class="fas fa-chart-line icon"></i> <span class="menu-text">Laporan Harian</span></a></li>
        </ul>
        <div class="sidebar-footer">
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </aside>

    <main class="main-content">
        <header class="header">
            <h1>Detail Pesanan #<?= $order['order_id'] ?></h1>
            <div class="admin-info">Selamat Datang, <strong><?= $kasirName ?></strong></div>
        </header>

        <section class="content-area printable-area">
            <div class="order-details-container">
                <div class="header-print" style="display: none;">
                    <h2>Struk Pesanan - Lamperie Resto</h2>
                    <p>Pesanan #<?= $order['order_id'] ?></p>
                </div>
                
                <div class="detail-section">
                    <h3>Informasi Pelanggan</h3>
                    <dl class="detail-grid">
                        <dt>Nama:</dt> <dd><?= htmlspecialchars($order['customer_name']) ?></dd>
                        <dt>Email:</dt> <dd><?= htmlspecialchars($order['customer_email']) ?></dd>
                        <dt>Telepon:</dt> <dd><?= htmlspecialchars($order['customer_phone'] ?? '-') ?></dd>
                    </dl>
                </div>
                
                <div class="detail-section">
                    <h3>Informasi Pesanan</h3>
                    <dl class="detail-grid">
                        <dt>ID Pesanan:</dt> <dd>#<?= $order['order_id'] ?></dd>
                        <dt>Tanggal Pesan:</dt> <dd><?= date('d F Y, H:i', strtotime($order['order_date'])) ?></dd>
                        <dt>Status:</dt> <dd><?= htmlspecialchars($order['order_status']) ?></dd>
                        <dt>Metode Pembayaran:</dt> <dd><?= htmlspecialchars($order['payment_method']) ?></dd>
                        
                        <?php if (!empty($order['discount_code'])): ?>
                            <dt>Kode Diskon:</dt> 
                            <dd><?= htmlspecialchars($order['discount_code']) ?></dd>
                            
                            <dt>Potongan Harga:</dt> 
                            <dd>-$<?= number_format($order['discount_amount'], 2) ?></dd>
                        <?php endif; ?>
                    </dl>
                </div>

                <div class="detail-section">
                    <h3>Rincian Item Dipesan</h3>
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Nama Item</th>
                                <th class="text-right">Jumlah</th>
                                <th class="text-right">Harga Satuan</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result_items && $result_items->num_rows > 0): ?>
                                <?php while($item = $result_items->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['item_name']) ?></td>
                                    <td class="text-right"><?= $item['quantity'] ?></td>
                                    <td class="text-right">$<?= number_format($item['price_per_item'], 2) ?></td>
                                    <td class="text-right">$<?= number_format($item['subtotal'], 2) ?></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="text-align: center;">Tidak ada item ditemukan untuk pesanan ini. (Pastikan data pesanan ini ada di 'order_items')</td>
                                </tr>
                            <?php endif; ?>
                            
                            <?php if (!empty($order['discount_amount']) && $order['discount_amount'] > 0): ?>
                                <tr class="total-row-intermediate">
                                    <td colspan="3" class="text-right">Subtotal Pesanan</td>
                                    <td class="text-right">$<?= number_format($order['total_price'] + $order['discount_amount'], 2) ?></td>
                                </tr>
                                <tr class="total-row-intermediate">
                                    <td colspan="3" class="text-right">Diskon</td>
                                    <td class="text-right">-$<?= number_format($order['discount_amount'], 2) ?></td>
                                </tr>
                            <?php endif; ?>

                            <tr class="total-row">
                                <td colspan="3" class="text-right">Total Keseluruhan</td>
                                <td class="text-right"><strong>$<?= number_format($order['total_price'], 2) ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="action-buttons">
                    <button onclick="window.print()" class="btn btn-print"><i class="fas fa-print"></i> Cetak / Simpan</button>
                     <a href="orders_admin.php" class="btn btn-back">← Kembali</a>
                </div>
            </div>
        </section>
    </main>
</body>
</html>