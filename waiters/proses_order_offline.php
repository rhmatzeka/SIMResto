<?php
session_start(); // Perlu session untuk user_id
require_once '../koneksi.php'; // Sesuaikan path

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Invalid request.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pastikan user adalah waiters
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'waiters') {
        $response['message'] = 'Akses ditolak: Hanya waiters yang dapat membuat pesanan.';
        echo json_encode($response);
        exit();
    }

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    $customer_name = htmlspecialchars($data['customer_name'] ?? 'Pelanggan Offline');
    $table_number = htmlspecialchars($data['table_number'] ?? ''); // Ini sekarang VARCHAR
    $payment_method = htmlspecialchars($data['payment_method'] ?? '');
    $items = $data['items'] ?? [];
    $user_id = $_SESSION['user']['id']; // ID waiters yang membuat pesanan

    if (empty($table_number) || empty($payment_method) || empty($items)) {
        $response['message'] = 'Meja, metode pembayaran, dan item pesanan tidak boleh kosong.';
        echo json_encode($response);
        exit();
    }

    $conn->begin_transaction();
    try {
        $total_price = 0;
        // Hitung total harga berdasarkan harga aktual dari database
        foreach ($items as $item) {
            $menu_item_id = intval($item['menu_item_id']);
            $quantity = intval($item['quantity']);

            $stmt_price = $conn->prepare("SELECT price FROM menu_items WHERE menu_item_id = ?");
            $stmt_price->bind_param("i", $menu_item_id);
            $stmt_price->execute();
            $result_price = $stmt_price->get_result();
            $menu_item = $result_price->fetch_assoc();
            $stmt_price->close();

            if (!$menu_item) {
                throw new Exception("Menu item tidak ditemukan: ID " . $menu_item_id);
            }
            $total_price += $menu_item['price'] * $quantity;
        }

        // 1. Masukkan ke tabel orders
        // Ubah binding parameter jika `table_number` di `orders` sudah `VARCHAR(10)`
        $stmt_order = $conn->prepare("INSERT INTO orders (user_id, table_number, customer_name, total_price, payment_method, order_status) VALUES (?, ?, ?, ?, ?, 'Pending')");
        $stmt_order->bind_param("issdss", $user_id, $table_number, $customer_name, $total_price, $payment_method);
        // ^^^ i (int:user_id), s (string:table_number), s (string:customer_name), d (double:total_price), s (string:payment_method), s (string:order_status)
        
        if (!$stmt_order->execute()) {
            throw new Exception("Gagal membuat order: " . $stmt_order->error);
        }
        $order_id = $conn->insert_id;
        $stmt_order->close();

        // 2. Masukkan ke tabel order_items
        $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, menu_item_id, item_name, quantity, price_per_item, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($items as $item) {
            $menu_item_id = intval($item['menu_item_id']);
            $quantity = intval($item['quantity']);
            $item_name = htmlspecialchars($item['item_name']);
            $price_per_item = $item['price']; // Ambil harga dari keranjang

            $subtotal = $price_per_item * $quantity;
            $stmt_item->bind_param("iisddd", $order_id, $menu_item_id, $item_name, $quantity, $price_per_item, $subtotal);
            if (!$stmt_item->execute()) {
                throw new Exception("Gagal menambahkan item pesanan: " . $stmt_item->error);
            }
        }
        $stmt_item->close();

        $conn->commit();
        $response = ['status' => 'success', 'message' => 'Pesanan berhasil dibuat!'];

    } catch (Exception $e) {
        $conn->rollback();
        $response = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()];
    }
} else {
    $response['message'] = 'Metode permintaan tidak valid.';
}

$conn->close();
echo json_encode($response);
exit();
?>