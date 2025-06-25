<?php
// Menggunakan file keamanan dari folder utama
include 'login_required.php';
// Memastikan hanya role kasir atau admin yang bisa akses
check_role(['kasir', 'admin']);

include '../koneksi.php';

// Inisialisasi variabel
$selected_date = null;
$report_data = [];

// Periksa apakah ada tanggal yang dipilih dari form
if (isset($_GET['tanggal'])) {
    $selected_date = $_GET['tanggal'];

    // 1. Ambil data ringkasan (Total Pendapatan & Jumlah Transaksi)
    $stmt_summary = $conn->prepare("SELECT SUM(total_price) as total_pendapatan, COUNT(order_id) as jumlah_transaksi FROM orders WHERE DATE(order_date) = ?");
    $stmt_summary->bind_param("s", $selected_date);
    $stmt_summary->execute();
    $report_data['summary'] = $stmt_summary->get_result()->fetch_assoc();

    // 2. Ambil rincian berdasarkan metode pembayaran
    $stmt_payment = $conn->prepare("SELECT payment_method, SUM(total_price) as total FROM orders WHERE DATE(order_date) = ? GROUP BY payment_method");
    $stmt_payment->bind_param("s", $selected_date);
    $stmt_payment->execute();
    $report_data['by_payment'] = $stmt_payment->get_result();

    // 3. Ambil rincian berdasarkan item terjual
    $stmt_items = $conn->prepare("
        SELECT item_name, SUM(quantity) as total_qty, SUM(subtotal) as total_revenue 
        FROM order_items 
        WHERE order_id IN (SELECT order_id FROM orders WHERE DATE(order_date) = ?)
        GROUP BY item_name 
        ORDER BY total_qty DESC
    ");
    $stmt_items->bind_param("s", $selected_date);
    $stmt_items->execute();
    $report_data['by_items'] = $stmt_items->get_result();
}

$kasirName = htmlspecialchars($_SESSION['user']['name']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Harian - Panel Kasir</title>
    <link rel="stylesheet" href="assets/css/laporan_harian.css">
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
            <li><a href="orders_admin.php"><i class="fas fa-file-invoice-dollar icon"></i> <span class="menu-text">Kelola Pesanan</span></a></li>
            <li><a href="laporan_harian.php" class="active"><i class="fas fa-chart-line icon"></i> <span class="menu-text">Laporan Harian</span></a></li>
        </ul>
        <div class="sidebar-footer">
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </aside>

    <main class="main-content">
        <header class="header">
            <h1>Laporan Keuangan Harian</h1>
            <div class="admin-info">Selamat Datang, <strong><?= $kasirName ?></strong></div>
        </header>

        <section class="content-area">
            <div class="report-container">
                <form action="laporan_harian.php" method="GET" class="date-selector-form">
                    <label for="tanggal">Pilih Tanggal Laporan:</label>
                    <input type="date" id="tanggal" name="tanggal" value="<?= $selected_date ?? date('Y-m-d') ?>" required>
                    <button type="submit">Tampilkan Laporan</button>
                </form>

                <?php if ($selected_date): // Tampilkan laporan hanya jika tanggal sudah dipilih ?>
                    <div class="print-header" style="display: none;">
                        <h2>Laporan Keuangan Harian</h2>
                        <p>Tanggal: <?= date('d F Y', strtotime($selected_date)) ?></p>
                    </div>

                    <div class="report-section">
                        <div class="summary-cards">
                            <div class="summary-card">
                                <h4>Total Pendapatan</h4>
                                <p>$<?= number_format($report_data['summary']['total_pendapatan'] ?? 0, 2) ?></p>
                            </div>
                            <div class="summary-card">
                                <h4>Jumlah Transaksi</h4>
                                <p><?= $report_data['summary']['jumlah_transaksi'] ?? 0 ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="report-section">
                        <h3>Berdasarkan Metode Pembayaran</h3>
                        <table class="detail-table">
                            <thead><tr><th>Metode</th><th>Total</th></tr></thead>
                            <tbody>
                                <?php while($row = $report_data['by_payment']->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['payment_method']) ?></td>
                                        <td>$<?= number_format($row['total'], 2) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="report-section">
                        <h3>Berdasarkan Item Terjual</h3>
                        <table class="detail-table">
                            <thead><tr><th>Nama Item</th><th>Jumlah Terjual</th><th>Total Pendapatan</th></tr></thead>
                            <tbody>
                                <?php while($row = $report_data['by_items']->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['item_name']) ?></td>
                                        <td><?= $row['total_qty'] ?></td>
                                        <td>$<?= number_format($row['total_revenue'], 2) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="action-buttons">
                        <button onclick="window.print()" class="btn-print"><i class="fas fa-print"></i> Cetak Laporan</button>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

</body>
</html>