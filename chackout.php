<?php
session_start();
if(!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Inisialisasi variabel untuk menyimpan data pesanan
$orderItems = null;
$grandTotal = 0;
$cartIsEmpty = true;

// Cek apakah data dikirim melalui metode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari POST request
    if (isset($_POST['orderData']) && isset($_POST['finalGrandTotal'])) {
        $orderDataJSON = $_POST['orderData'];
        $finalGrandTotalPOST = $_POST['finalGrandTotal'];

        $decodedOrderData = json_decode($orderDataJSON, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decodedOrderData) && !empty($decodedOrderData)) {
            $orderItems = $decodedOrderData;
            $grandTotal = floatval($finalGrandTotalPOST);
            $cartIsEmpty = false;
        }
    }
}

// Ambil data user dari session
$user_id = $_SESSION['user']['id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="css/chackout.css"> 
</head> 
<body>

    <div class="container">
        <header class="main-header">
            <h1>Your Order Summary</h1>
        </header>

        <main id="checkout-details">
            <div id="ordered-items-container">
                <div class="items-header">
                    <span class="item-name">Item Name</span>
                    <span class="item-qty">Quantity</span>
                    <span class="item-price">Subtotal</span>
                </div>
                <p id="empty-cart-message" style="display: none;">
                    Your cart is empty. Please return to the menu page to order.
                </p>
            </div>

            <div id="total-section">
                <div class="total-row">
                    <span class="total-label">Grand Total:</span>
                    <span id="grand-total-value" class="total-value">$0.00</span>
                </div>
            </div>

            <div id="discount-section">
                <h2>Have a Discount Code?</h2>
                <div class="discount-form">
                    <input type="text" id="discount-code-input" placeholder="Enter your code here">
                    <button id="apply-discount-button" class="button">Apply</button>
                </div>
                <p id="discount-message" style="display: none;"></p>
            </div>
            <div id="total-section">
                <div class="total-row">
                    <span class="total-label">Subtotal:</span>
                    <span id="subtotal-value" class="total-value">$0.00</span>
                </div>
                <div class="total-row" id="discount-row" style="display: none;">
                    <span class="total-label">Discount:</span>
                    <span id="discount-value" class="total-value" style="color: #27ae60;">-$0.00</span>
                </div>
            </div>

            <div id="payment-method-section">
                <h2>Select Payment Method</h2>
                <div class="payment-options">
                    <div>
                        <input type="radio" id="payment-cod" name="paymentMethod" value="COD">
                        <label for="payment-cod">Cash or Duel (COD)</label>
                        <div class="payment-details">
                            <p>You will pay in cash to the courier when the order arrives.</p>
                        </div>
                    </div>
                    <div>
                        <input type="radio" id="payment-transfer" name="paymentMethod" value="Bank Transfer">
                        <label for="payment-transfer">Bank Transfer</label>
                        <div class="payment-details">
                            <p>Please transfer to the following account:</p>
                            <p><strong>Bank ABC:</strong> 123-456-7890 on behalf of Your Restaurant Name</p>
                            <p><strong>Bank XYZ:</strong> 098-765-4321 on behalf of Your Restaurant Name</p>
                            <p style="margin-top: 0.5rem;">Please confirm after making the transfer.</p>
                        </div>
                    </div>
                    <div>
                        <input type="radio" id="payment-ewallet" name="paymentMethod" value="E-Wallet">
                        <label for="payment-ewallet">E-Wallet</label>
                        <div class="payment-details">
                            <p>You can pay using your favorite E-Wallet.</p>
                            <p>Scan the QR Code below (Example):</p>
                            <div class="qr-placeholder">
                                [Placeholder QR Code]
                            </div>
                            <p>Or pay to the number: 0812-3456-7890 (E-Wallet Account Name)</p>
                        </div>
                    </div>
                </div>
                <p id="payment-error-message" style="display: none;">
                    Please select a payment method.
                </p>
            </div>

            <div class="actions-section">
                <button id="confirm-order-button" class="button button-confirm" disabled>
                    Select Payment First
                </button>
                 <a href="menulogin.php" class="button button-back">
                    Back to Menu
                </a>
            </div>
        </main>

        <footer class="main-footer">
            <p>&copy; <span id="current-year"></span> Your Restaurant Name. All rights reserved.</p>
        </footer>
    </div>
    <script>
        const phpOrderItems = <?php echo json_encode($orderItems); ?>;
        const phpGrandTotal = <?php echo json_encode($grandTotal); ?>;
        const phpCartIsEmpty = <?php echo json_encode($cartIsEmpty); ?>;

        function formatDollar(amount) {
          return "$ " + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, "$&,");
        }
    </script>
    <script src="js/chackout.js?v=<?php echo time(); ?>"></script>
</body>
</html>