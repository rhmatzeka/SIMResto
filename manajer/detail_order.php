<?php
session_start();

// Memastikan hanya role manajer yang bisa akses
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'manajer') {
    // Jika tidak ada sesi user atau rolenya bukan manajer, arahkan ke halaman login
    header("Location: ../login.php");
    exit();
}

require_once '../koneksi.php';

// Cek apakah ID pesanan ada di URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Jika tidak ada ID, tampilkan pesan error dan hentikan skrip
    die("Error: ID Pesanan tidak ditemukan.");
}

$order_id = $_GET['id'];

// --- 1. Ambil Informasi Utama Pesanan ---
$query_order = "SELECT o.*, u.name as customer_name 
                FROM orders o
                JOIN users u ON o.user_id = u.id
                WHERE o.order_id = ?";

$stmt_order = $conn->prepare($query_order);
$stmt_order->bind_param("i", $order_id);
$stmt_order->execute();
$result_order = $stmt_order->get_result();
$order = $result_order->fetch_assoc();

if (!$order) {
    die("Error: Pesanan dengan ID " . htmlspecialchars($order_id) . " tidak ditemukan.");
}

// --- 2. Ambil Rincian Item Pesanan ---
$query_items = "SELECT mi.item_name, oi.quantity, oi.price_per_item, oi.subtotal
                FROM order_items oi
                JOIN menu_items mi ON oi.menu_item_id = mi.menu_item_id
                WHERE oi.order_id = ?";

$stmt_items = $conn->prepare($query_items);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$result_items = $stmt_items->get_result();

$order_items = [];
while ($item = $result_items->fetch_assoc()) {
    $order_items[] = $item;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan #<?php echo htmlspecialchars($order['order_id']); ?> - LAMPERIE</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            color: white;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .sidebar .active {
            background-color: #007bff;
        }
        .content {
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-lg-2 d-md-block sidebar collapse bg-dark">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard_manajer.php">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="laporan_penjualan.php">
                                <i class="fas fa-chart-line me-2"></i>Laporan Penjualan
                            </a>
                        </li>
                         <li class="nav-item">
                            <a class="nav-link" href="manajemen_menu.php">
                                <i class="fas fa-utensils me-2"></i>Manajemen Menu
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manajemen_user.php">
                                <i class="fas fa-users me-2"></i>Manajemen User
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4 content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Detail Pesanan #<?php echo htmlspecialchars($order['order_id']); ?></h1>
                    <a href="laporan_penjualan.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Laporan
                    </a>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Informasi Pesanan</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>ID Pesanan:</strong> <?php echo htmlspecialchars($order['order_id']); ?></p>
                                <p><strong>Nama Pelanggan:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                                <p><strong>Tanggal Pesan:</strong> <?php echo date('d M Y, H:i', strtotime($order['order_date'])); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Metode Pembayaran:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
                                <p><strong>Status Pesanan:</strong> <span class="badge bg-info"><?php echo htmlspecialchars($order['order_status']); ?></span></p>
                                <p><strong>Kode Diskon:</strong> <?php echo !empty($order['discount_code']) ? htmlspecialchars($order['discount_code']) : '-'; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Rincian Item Dipesan</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">No.</th>
                                        <th scope="col">Nama Item</th>
                                        <th scope="col" class="text-center">Jumlah</th>
                                        <th scope="col" class="text-end">Harga Satuan</th>
                                        <th scope="col" class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $nomor = 1;
                                    foreach ($order_items as $item): 
                                    ?>
                                    <tr>
                                        <td><?php echo $nomor++; ?></td>
                                        <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                                        <td class="text-center"><?php echo htmlspecialchars($item['quantity']); ?></td>
                                        <td class="text-end">$<?php echo number_format($item['price_per_item'], 2, '.', ','); ?></td>
                                        <td class="text-end">$<?php echo number_format($item['subtotal'], 2, '.', ','); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Diskon</strong></td>
                                        <td class="text-end"><strong>- $<?php echo number_format($order['discount_amount'], 2, '.', ','); ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Total Akhir</strong></td>
                                        <td class="text-end"><strong>$<?php echo number_format($order['total_price'], 2, '.', ','); ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>