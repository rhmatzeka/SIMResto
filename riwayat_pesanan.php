<?php
// Pastikan file koneksi.php sudah tersedia dan menghubungkan ke database
require_once 'koneksi.php';

// Pastikan user sudah login dan session user ID tersedia
// user_id diambil dari sesi login, seperti yang dilakukan di profil.php
$user_id = $_SESSION['user']['id'] ?? 0;

$user_orders = [];

if ($user_id > 0) {
    // Query untuk mendapatkan semua pesanan dari user yang sedang login
    $stmt_orders = $conn->prepare("SELECT order_id, order_date, total_price, payment_method, order_status FROM orders WHERE user_id = ? ORDER BY order_date DESC");
    $stmt_orders->bind_param("i", $user_id);
    $stmt_orders->execute();
    $result_orders = $stmt_orders->get_result();

    while ($order = $result_orders->fetch_assoc()) {
        // Ambil SEMUA item untuk pesanan ini
        $stmt_all_items = $conn->prepare("
            SELECT oi.item_name, oi.quantity, mi.image_url, mi.category
            FROM order_items oi
            JOIN menu_items mi ON oi.menu_item_id = mi.menu_item_id
            WHERE oi.order_id = ?
            ORDER BY oi.order_item_id ASC -- Urutkan untuk konsistensi, jika ada order_item_id
        ");
        $stmt_all_items->bind_param("i", $order['order_id']);
        $stmt_all_items->execute();
        $result_all_items = $stmt_all_items->get_result();

        $order_items_data = [];
        while ($item = $result_all_items->fetch_assoc()) {
            $order_items_data[] = $item;
        }
        $stmt_all_items->close();

        // Siapkan data untuk tampilan ringkas
        $order['display_summary_text'] = ''; // Akan menjadi satu string
        $order['first_item_image_url'] = 'placeholder.png';
        $order['first_item_category'] = 'default';

        if (!empty($order_items_data)) {
            // Ambil gambar dari item pertama
            $order['first_item_image_url'] = $order_items_data[0]['image_url'] ?? 'placeholder.png';
            $order['first_item_category'] = $order_items_data[0]['category'] ?? 'default';

            $temp_items_array = []; // Array sementara untuk menampung item yang akan ditampilkan
            $item_count_for_display = 0;
            foreach ($order_items_data as $item) {
                if ($item_count_for_display < 2) { // Ambil 2 item pertama
                    $temp_items_array[] = htmlspecialchars($item['quantity']) . 'x ' . htmlspecialchars($item['item_name']);
                    $item_count_for_display++;
                } else {
                    break; // Hentikan jika sudah mencapai 2 item yang akan ditampilkan
                }
            }

            // Gabungkan item yang akan ditampilkan dengan koma
            $order['display_summary_text'] = implode(', ', $temp_items_array);

            // Tambahkan elipsis jika ada lebih dari 2 item asli
            if (count($order_items_data) > 2) {
                $order['display_summary_text'] .= '...';
            }
        }

        $user_orders[] = $order;
    }
    $stmt_orders->close();
}
?>

<div class="container">
    <?php if (!empty($user_orders)): ?>
        <div class="orders-container">
            <?php foreach ($user_orders as $order): ?>
                <div class="order-card">
                    <div class="order-top">
                      <h4>Lamperie Restaurant</h4> <p class="order-date"><?php echo date('d M Y', strtotime($order['order_date'])); ?></p>
                    </div>
                    <div class="order-body">
                        <a href="profil.php?tab=order-detail&order_id=<?php echo $order['order_id']; ?>">
                            <img src="images/menu/<?php echo htmlspecialchars($order['first_item_category']); ?>/<?php echo htmlspecialchars($order['first_item_image_url']); ?>"
                                 alt="Item Image"
                                 class="order-item-image"> </a>
                      <div class="order-content">
                        <?php if (!empty($order['display_summary_text'])): ?>
                            <p><?php echo $order['display_summary_text']; ?></p>
                            <p class="price">$<?php echo number_format($order['total_price'], 2, ',', '.'); ?></p>
                        <?php else: ?>
                            <p>Tidak ada item dalam pesanan ini.</p>
                            <p class="price">$<?php echo number_format($order['total_price'], 2, ',', '.'); ?></p>
                        <?php endif; ?>

                        <div class="order-bottom">
                          <span class="status <?php echo htmlspecialchars(strtolower(str_replace(' ', '', $order['order_status']))); ?>">
                              <?php echo htmlspecialchars($order['order_status']); ?>
                          </span>
                          <a href="menulogin.php" class="btn">Pesan lagi</a>
                          </div>
                      </div>
                    </div>
                  </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Anda belum memiliki riwayat pesanan.</p>
    <?php endif; ?>
</div>