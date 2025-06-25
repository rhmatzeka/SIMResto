<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['order_id'])) {
    header("Location: dashboard.php"); 
    exit();
}

$order_id = intval($_GET['order_id']);
$user_id = $_SESSION['user']['id'];

require_once 'koneksi.php';

$query_order = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
$query_order->bind_param("ii", $order_id, $user_id);
$query_order->execute();
$order = $query_order->get_result()->fetch_assoc();

if (!$order) {
    header("Location: dashboard.php");
    exit();
}

$query_items = $conn->prepare("SELECT item_name, quantity, price_per_item, subtotal FROM order_items WHERE order_id = ?");
$query_items->bind_param("i", $order_id);
$query_items->execute();
$items = $query_items->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - LAMPERIE</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .container { max-width: 600px; margin: 40px auto; padding: 20px; font-family: Arial, sans-serif; }
        
        /* ======================================================= */
        /* --- PERBAIKAN FINAL DITAMBAHKAN DI SINI --- */
        /* ======================================================= */
        .order-summary { 
            background: #f9f9f9; 
            padding: 20px; 
            border-radius: 8px; 
            margin-bottom: 20px; 
            color: #000; /* <<-- TAMBAHKAN BARIS INI */
        }

        .order-items { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .order-items th, .order-items td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        .order-items th { background-color: #f2f2f2; color: #000; }
        .text-right { text-align: right !important; }
        .btn { display: inline-block; padding: 12px 25px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px; margin-top: 20px; text-align: center; }

        .order-items td {
            color: #000; 
        }
    </style>
</head>
<body>
    
    <div class="container">
        <h1>Order Confirmation</h1>
        <p style="color: white;">Thank you for your order! Your order has been placed successfully.</p>
        
        <div class="order-summary">
            <h2>Order #<?php echo $order['order_id']; ?></h2>
            <p><strong>Date:</strong> <?php echo date('F j, Y, g:i a', strtotime($order['order_date'])); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars(ucfirst($order['order_status'])); ?></p>
            <p><strong>Payment Method:</strong> <?php echo htmlspecialchars(ucfirst($order['payment_method'])); ?></p>
            
            <h3>Order Items:</h3>
            <?php if (!empty($items)): ?>
                <table class="order-items">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Quantity</th>
                            <th class="text-right">Price</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td class="text-right">$<?php echo number_format($item['price_per_item'], 2); ?></td>
                            <td class="text-right">$<?php echo number_format($item['subtotal'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>

                        <?php if(!empty($order['discount_amount']) && $order['discount_amount'] > 0): ?>
                        <tr>
                            <td colspan="3" class="text-right"><strong>Subtotal</strong></td>
                            <td class="text-right"><strong>$<?php echo number_format($order['total_price'] + $order['discount_amount'], 2); ?></strong></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-right"><strong>Discount</strong></td>
                            <td class="text-right" style="color: green;"><strong>-$<?php echo number_format($order['discount_amount'], 2); ?></strong></td>
                        </tr>
                        <?php endif; ?>

                        <tr>
                            <td colspan="3" class="text-right"><strong>Grand Total:</strong></td>
                            <td class="text-right"><strong>$<?php echo number_format($order['total_price'], 2); ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No items found for this order.</p>
            <?php endif; ?>
        </div>
        
        <a href="menulogin.php" class="btn">Back to Menu</a>
    </div>
</body>
</html>