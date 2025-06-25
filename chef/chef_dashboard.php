<?php
include 'login_req.php';
check_role(['kitchen']);

include '../koneksi.php';

// Inisialisasi tanggal yang dipilih
$selected_date = null;
if (isset($_GET['tanggal']) && !empty($_GET['tanggal'])) {
    $selected_date = $_GET['tanggal']; // Ambil tanggal dari parameter URL
} else {
    $selected_date = date('Y-m-d'); // Default ke tanggal hari ini
}

// Fungsi untuk mendapatkan pesanan yang digrup berdasarkan status item dan TANGGAL
function getGroupedOrdersByItemStatus($conn, $status, $date_filter) {
    // Subquery untuk menemukan order_id yang memiliki SETIDAKNYA SATU item dengan status yang diinginkan
    $sql = "
        SELECT
            o.order_id,
            o.order_date,
            oi.item_name,
            oi.quantity,
            ois.status AS item_status,
            oi.order_item_id
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        LEFT JOIN order_item_status ois ON oi.order_item_id = ois.order_item_id
        WHERE DATE(o.order_date) = ? AND (ois.status = ? OR (ois.status IS NULL AND ? = 'pending'))
        ORDER BY o.order_date ASC, o.order_id ASC, oi.order_item_id ASC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $date_filter, $status, $status);
    $stmt->execute();
    $result = $stmt->get_result();

    $grouped_orders = [];
    while ($row = $result->fetch_assoc()) {
        $order_id = $row['order_id'];
        if (!isset($grouped_orders[$order_id])) {
            $grouped_orders[$order_id] = [
                'order_id' => $order_id,
                'order_date' => $row['order_date'],
                'items' => [],
            ];
        }
        $grouped_orders[$order_id]['items'][] = [
            'order_item_id' => $row['order_item_id'],
            'item_name' => $row['item_name'],
            'quantity' => $row['quantity'],
            'item_status' => $row['item_status'],
        ];
    }
    return $grouped_orders;
}

// Perbarui status pesanan keseluruhan (Logika ini tetap sama)
if (isset($_POST['update_order_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = $_POST['new_status'];
    $current_date_from_form = $_POST['current_date'];

    $stmt_items = $conn->prepare("SELECT order_item_id FROM order_items WHERE order_id = ?");
    $stmt_items->bind_param("i", $order_id);
    $stmt_items->execute();
    $result_items = $stmt_items->get_result();

    while ($item_row = $result_items->fetch_assoc()) {
        $order_item_id = $item_row['order_item_id'];

        $stmt_check = $conn->prepare("SELECT id FROM order_item_status WHERE order_item_id = ?");
        $stmt_check->bind_param("i", $order_item_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            $stmt_update = $conn->prepare("UPDATE order_item_status SET status = ? WHERE order_item_id = ?");
            $stmt_update->bind_param("si", $new_status, $order_item_id);
            $stmt_update->execute();
        } else {
            $stmt_insert = $conn->prepare("INSERT INTO order_item_status (order_item_id, status) VALUES (?, ?)");
            $stmt_insert->bind_param("is", $order_item_id, $new_status);
            $stmt_insert->execute();
        }
    }
    // Redirect kembali ke halaman dengan tanggal yang sama
    header("Location: chef_dashboard.php?tanggal=" . urlencode($current_date_from_form));
    exit();
}

$chefName = htmlspecialchars($_SESSION['user']['name']);

// Panggil fungsi dengan filter tanggal
$pending_orders_grouped = getGroupedOrdersByItemStatus($conn, 'pending', $selected_date);
$preparing_orders_grouped = getGroupedOrdersByItemStatus($conn, 'preparing', $selected_date);
$finished_orders_grouped = getGroupedOrdersByItemStatus($conn, 'finished', $selected_date);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dapur - Lampere Resto</title>
    <link rel="stylesheet" href="css/chef.css">
    <link rel="stylesheet" href="../admin/assets/css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    </head>
<body>

    <aside class="sidebar">
        <div class="sidebar-header">
            <h2><i class="fas fa-utensils"></i> <span>Kitchen</span></h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="chef_dashboard.php" class="active"><i class="fas fa-clipboard-list icon"></i> <span class="menu-text">Order List</span></a></li>

            <li class="date-filter-sidebar-item">
                <form id="dateForm" action="chef_dashboard.php" method="GET" class="date-selector-form">
                    <label for="tanggal">
                        <i class="fas fa-calendar-alt icon"></i>
                    </label>
                    <input type="date" id="tanggal" name="tanggal" value="<?= htmlspecialchars($selected_date) ?>" required>
                    <button type="submit" style="display: none;">Filter</button>
                </form>
            </li>
            </ul>
        <div class="sidebar-footer">
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </aside>

    <main class="main-content">
        <header class="header">
            <h1>Panel Dapur</h1>
            <div class="admin-info">Selamat Datang, <strong><?= $chefName ?></strong></div>
        </header>

        <section class="content-area chef-dashboard">
            <div class="status-column pending-orders">
                <h2>Pesanan Masuk</h2>
                <?php if (count($pending_orders_grouped) > 0): ?>
                    <?php foreach ($pending_orders_grouped as $order): ?>
                        <div class="order-card">
                            <h3>Pesanan #<?= htmlspecialchars($order['order_id']) ?></h3>
                            <p class="order-date"><?= date('H:i', strtotime($order['order_date'])) ?></p>
                            <ul class="order-items-list">
                                <?php foreach ($order['items'] as $item): ?>
                                    <li>
                                        <span><?= htmlspecialchars($item['item_name']) ?></span>
                                        <span class="quantity">x<?= htmlspecialchars($item['quantity']) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <form action="chef_dashboard.php" method="POST" class="order-action-form">
                                <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['order_id']) ?>">
                                <input type="hidden" name="new_status" value="preparing">
                                <input type="hidden" name="current_date" value="<?= htmlspecialchars($selected_date) ?>">
                                <button type="submit" name="update_order_status" class="btn-prepare">Mulai Siapkan</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Tidak ada pesanan baru untuk tanggal ini.</p>
                <?php endif; ?>
            </div>

            <div class="status-column preparing-orders">
                <h2>Sedang Disiapkan</h2>
                <?php if (count($preparing_orders_grouped) > 0): ?>
                    <?php foreach ($preparing_orders_grouped as $order): ?>
                        <div class="order-card">
                            <h3>Pesanan #<?= htmlspecialchars($order['order_id']) ?></h3>
                            <p class="order-date"><?= date('H:i', strtotime($order['order_date'])) ?></p>
                            <ul class="order-items-list">
                                <?php foreach ($order['items'] as $item): ?>
                                    <li>
                                        <span><?= htmlspecialchars($item['item_name']) ?></span>
                                        <span class="quantity">x<?= htmlspecialchars($item['quantity']) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <form action="chef_dashboard.php" method="POST" class="order-action-form">
                                <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['order_id']) ?>">
                                <input type="hidden" name="new_status" value="finished">
                                <input type="hidden" name="current_date" value="<?= htmlspecialchars($selected_date) ?>">
                                <button type="submit" name="update_order_status" class="btn-finish">Selesai</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Tidak ada item sedang disiapkan untuk tanggal ini.</p>
                <?php endif; ?>
            </div>

            <div class="status-column finished-orders">
                <h2>Selesai</h2>
                <?php if (count($finished_orders_grouped) > 0): ?>
                    <?php foreach ($finished_orders_grouped as $order): ?>
                        <div class="order-card finished-card">
                            <h3>Pesanan #<?= htmlspecialchars($order['order_id']) ?></h3>
                            <p class="order-date"><?= date('H:i', strtotime($order['order_date'])) ?></p>
                            <ul class="order-items-list">
                                <?php foreach ($order['items'] as $item): ?>
                                    <li>
                                        <span><?= htmlspecialchars($item['item_name']) ?></span>
                                        <span class="quantity">x<?= htmlspecialchars($item['quantity']) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Tidak ada item yang selesai untuk tanggal ini.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <script>
        // JavaScript untuk otomatis submit form saat tanggal berubah
        document.getElementById('tanggal').addEventListener('change', function() {
            document.getElementById('dateForm').submit();
        });
    </script>

</body>
</html>