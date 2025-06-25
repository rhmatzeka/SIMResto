<?php
session_start();
// Pastikan user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

require_once 'koneksi.php';

// Pastikan order_id diterima dari URL dan valid
$order_id = $_GET['order_id'] ?? 0;
if ($order_id <= 0) {
    die("Order ID tidak valid.");
}

// Ambil data user yang sedang login
$user_id = $_SESSION['user']['id'];

// =================================================================
// PERBAIKAN 1: Mengambil data order utama, pastikan pesanan ini milik user yg login
// =================================================================
$stmt_order = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
$stmt_order->bind_param("ii", $order_id, $user_id);
$stmt_order->execute();
$result_order = $stmt_order->get_result();
$order_details = $result_order->fetch_assoc();
$stmt_order->close();

// Jika pesanan tidak ada atau bukan milik user ini, hentikan eksekusi
if (!$order_details) {
    die("Detail pesanan tidak ditemukan atau Anda tidak memiliki akses.");
}

// =================================================================
// PERBAIKAN 2: Mengambil semua item dari tabel 'order_items'
// =================================================================
$stmt_items = $conn->prepare("SELECT item_name, quantity, price_per_item, subtotal FROM order_items WHERE order_id = ?");
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$result_items = $stmt_items->get_result();
$order_items = [];
while ($item = $result_items->fetch_assoc()) {
    $order_items[] = $item;
}
$stmt_items->close();

// Hitung subtotal sebelum diskon
$subtotal_before_discount = $order_details['total_price'] + $order_details['discount_amount'];

?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Detail Pesanan #<?php echo $order_id; ?></title>
  <style>
      /* Anda bisa meletakkan style ini di file CSS terpisah */
      body { font-family: 'Poppins', sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
      .container { max-width: 500px; margin: auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
      h2, h3 { color: #333; }
      section { margin-bottom: 25px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
      section:last-child { border-bottom: none; }
      ul { list-style: none; padding: 0; }
      li { display: flex; justify-content: space-between; margin-bottom: 10px; }
      .btn { display: block; width: 100%; padding: 15px; background-color: #E84545; color: white; text-align: center; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 20px; }
  </style>
</head>
<body>
  <div class="container">
    <?php if ($order_details): ?>
        <h2>Detail Pesanan #<?php echo htmlspecialchars($order_id); ?></h2>
        <p class="subtitle">Status: <strong><?php echo htmlspecialchars($order_details['order_status']); ?></strong></p>

        <section>
          <h3>Rincian Pesanan</h3>
          <ul>
            <?php if (!empty($order_items)): ?>
                <?php foreach ($order_items as $item): ?>
                    <li>
                        <span><?php echo htmlspecialchars($item['quantity']); ?>x <?php echo htmlspecialchars($item['item_name']); ?></span>
                        <strong>$<?php echo number_format($item['subtotal'], 2); ?></strong>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>Tidak ada item dalam pesanan ini.</li>
            <?php endif; ?>
          </ul>
        </section>

        <section>
          <h3>Rincian Biaya</h3>
          <ul>
            <li>
                <span>Subtotal</span>
                <span>$<?php echo number_format($subtotal_before_discount, 2); ?></span>
            </li>
            <?php if ($order_details['discount_amount'] > 0): ?>
                <li style="color: green;">
                    <span>Diskon (<?php echo htmlspecialchars($order_details['discount_code']); ?>)</span>
                    <span>-$<?php echo number_format($order_details['discount_amount'], 2); ?></span>
                </li>
            <?php endif; ?>
            <li>
                <strong>Total</strong>
                <strong>$<?php echo number_format($order_details['total_price'], 2); ?></strong>
            </li>
          </ul>
        </section>

        <p><strong>Metode Pembayaran:</strong> <?php echo htmlspecialchars($order_details['payment_method']); ?></p>
        <a href="menulogin.php" class="btn">Pesan Lagi</a>

    <?php else: ?>
        <p>Detail pesanan tidak ditemukan atau Order ID tidak valid.</p>
    <?php endif; ?>
  </div>
</body>
</html>