<?php
session_start();
// Cek apakah user sudah login
if(!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Include file koneksi database Anda
include 'koneksi.php';

// Ambil semua data menu dari tabel menu_items
$q = $conn->query("SELECT * FROM menu_items");

// Periksa apakah query berhasil
if (!$q) {
    die("Query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Menu - Lamperie Restaurant</title>
    <link rel="website icon" type="png" href="images/icon-lamperie.png" />
    
    <!-- Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;700&display=swap"
      rel="stylesheet"
    />
    <!-- Icons -->
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Link CSS Asli Anda -->
    <link rel="stylesheet" href="css/menu.css" />

    <!-- CSS TAMBAHAN UNTUK KERANJANG (VERSI SIMPLE & TIDAK NABRAK NAVBAR) -->
    <style>
        /* Agar body tidak bisa discroll saat keranjang terbuka */
        body.cart-open {
            overflow: hidden;
        }

        /* --- Overlay Hitam (Latar Belakang Gelap) --- */
        .cart-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 998; 
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .cart-overlay.active {
            display: block;
            opacity: 1;
        }

        /* --- Sidebar Keranjang (Versi Lebih Kecil & Di Bawah Navbar) --- */
        .cart-sidebar {
            position: fixed;
            /* PERUBAHAN UTAMA DI SINI: */
            top: 85px; /* Turunkan 85px agar di bawah navbar (sesuaikan tinggi navbar Anda) */
            right: -320px; /* Sembunyi di kanan */
            width: 300px; /* Lebar lebih kecil (Simple) */
            height: calc(100vh - 85px); /* Tinggi menyesuaikan sisa layar */
            
            background-color: #fff;
            z-index: 999;
            box-shadow: -2px 5px 15px rgba(0,0,0,0.1);
            transition: right 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); /* Animasi smooth */
            display: flex;
            flex-direction: column;
            color: #333;
            font-family: 'Poppins', sans-serif;
            border-top-left-radius: 15px; /* Sudut melengkung biar manis */
            border-bottom-left-radius: 15px;
        }
        
        .cart-sidebar.active {
            right: 0; /* Muncul */
        }

        /* Header Keranjang */
        .cart-header {
            background-color: #ff8c00;
            color: white;
            padding: 15px; /* Padding lebih kecil */
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: bold;
            font-size: 16px; /* Font lebih kecil */
            border-top-left-radius: 15px;
        }
        .close-cart-btn {
            cursor: pointer;
            background: none;
            border: none;
            color: white;
            font-size: 20px;
        }

        /* Isi Item Keranjang */
        .cart-items-container {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
            background-color: #fcfcfc;
        }

        .cart-item {
            background: white;
            padding: 12px; /* Lebih compact */
            margin-bottom: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .cart-item-details h4 {
            margin: 0 0 3px 0;
            font-size: 13px; /* Font nama menu lebih kecil */
            font-weight: 600;
            color: #333;
        }
        .cart-item-details p {
            margin: 0;
            font-size: 11px;
            color: #888;
        }

        .cart-item-price {
            font-weight: bold;
            color: #ff8c00;
            font-size: 14px;
        }

        /* Footer Keranjang */
        .cart-footer {
            background: white;
            padding: 15px;
            border-top: 1px solid #eee;
            border-bottom-left-radius: 15px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 12px;
            color: #222;
        }
        .checkout-btn {
            width: 100%;
            background-color: #ff8c00;
            color: white;
            border: none;
            padding: 12px; /* Tombol lebih ramping */
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .checkout-btn:hover {
            background-color: #e07b00;
        }

        /* Floating Cart Button */
        .floating-cart-btn {
            position: fixed;
            bottom: 25px;
            right: 25px;
            background-color: #ff8c00;
            color: white;
            width: 55px;
            height: 55px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            cursor: pointer;
            z-index: 990;
            transition: transform 0.2s;
        }
        .floating-cart-btn:hover {
            transform: scale(1.05);
        }
        .cart-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background-color: #d32f2f;
            color: white;
            font-size: 11px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            border: 2px solid white;
        }
    </style>
  </head>
  <body>
    <?php include 'navbar.php'; ?>

    <div class="bg-image">
      <h1>MENU</h1>
    </div>

    <!-- Kategori Menu Lingkaran -->
    <div class="circular-categories-container">
      <div class="circular-categories-row">
        <div class="category-item-circle"><a href="#MainCourse"><img src="images/menu/Main-corse-circle.jpeg" alt="Main Course" /><h5>Main Course</h5></a></div>
        <div class="category-item-circle"><a href="#Appetizer"><img src="images/menu/Appetizer/MozzarellaSticks.png" alt="Appetizer"/><h5>Appetizer</h5></a></div>
        <div class="category-item-circle"><a href="#snacks"><img src="images/menu/snacks/f5b15dbb-8cf6-4b6b-8efd-0d7dba4c6e16.jpg" alt="Snacks"/><h5>Snacks</h5></a></div>
        <div class="category-item-circle"><a href="#dessert"><img src="images/menu/dessert&cemilan-circle.jpeg" alt="Dessert"/><h5>Dessert</h5></a></div>
        <div class="category-item-circle"><a href="#NonCoffee"><img src="images/menu/Non-Coffee/banner,.jpg" alt="Non-Coffee"/><h5>Non-Coffee</h5></a></div>
        <div class="category-item-circle"><a href="#coffee"><img src="images/menu/coffee-&-Non-Coffee-circle.jpeg" alt="Coffee"/><h5>Coffee</h5></a></div>
        <div class="category-item-circle"><a href="#juice"><img src="images/menu/Juice/apple_juice.jpg" alt="Juice"/><h5>Juice</h5></a></div>
      </div>
    </div>

    <!-- FUNGSI PHP UNTUK MENAMPILKAN ITEM -->
    <?php
    // Fungsi bantu untuk reset pointer dan loop kategori
    function renderCategory($conn, $categoryName, $displayId) {
        global $q; 
        $q->data_seek(0); 
        
        echo '<h1 class="' . str_replace(' ', '', $categoryName) . '" id="' . $displayId . '">' . strtoupper($categoryName) . '</h1>';
        echo '<div class="menu-grid">';
        
        $found = false;
        while ($menu = $q->fetch_assoc()) {
            if ($menu['category'] == $categoryName) {
                $found = true;
                $imgSrc = !empty($menu['image_url']) 
                    ? "images/menu/" . $menu['category'] . "/" . $menu['image_url'] 
                    : "images/menu/placeholder.png";
                
                ?>
                <!-- MENU CARD -->
                <div class="menu-card" 
                     data-id="<?= $menu['menu_item_id'] ?>" 
                     data-name="<?= $menu['item_name'] ?>" 
                     data-price="<?= $menu['price'] ?>">
                     
                    <img src="<?= $imgSrc ?>" alt="<?= $menu['item_name'] ?>" class="menu-image">
                    <div class="menu-title"><?= $menu['item_name'] ?></div>
                    <div class="menu-description"><?= $menu['description'] ?></div>
                    <div class="menu-price">$<?= number_format($menu['price'], 2) ?></div>
                    
                    <!-- KONTROL PESANAN -->
                    <div class="order-controls" onclick="event.stopPropagation()">
                        <button class="decrease-order" onclick="updateCart(<?= $menu['menu_item_id'] ?>, -1)">-</button>
                        <span class="order-quantity" id="qty-<?= $menu['menu_item_id'] ?>">0</span>
                        <button class="increase-order" onclick="updateCart(<?= $menu['menu_item_id'] ?>, 1)">+</button>
                    </div>
                </div>
                <?php
            }
        }
        if (!$found) echo "<p>No items in this category.</p>";
        echo '</div>';
    }

    renderCategory($conn, 'Main Course', 'MainCourse');
    renderCategory($conn, 'Appetizer', 'Appetizer');
    renderCategory($conn, 'Snacks', 'snacks');
    renderCategory($conn, 'Dessert', 'dessert');
    renderCategory($conn, 'Non-Coffee', 'NonCoffee');
    renderCategory($conn, 'Coffee', 'coffee');
    renderCategory($conn, 'Juice', 'juice');
    ?>

    <!-- =========================================== -->
    <!-- KERANJANG BELANJA (UPDATED: SIMPLE & LOWER) -->
    <!-- =========================================== -->
    
    <div class="cart-overlay" id="cartOverlay" onclick="toggleCart()"></div>

    <div class="cart-sidebar" id="cartSidebar">
        <div class="cart-header">
            <span><i class="fas fa-shopping-bag"></i> Keranjang</span>
            <button class="close-cart-btn" onclick="toggleCart()">&times;</button>
        </div>
        
        <div class="cart-items-container" id="cartItemsList">
            <p style="text-align:center; margin-top:20px; color:#999; font-size: 13px;">Keranjang kosong.</p>
        </div>

        <div class="cart-footer">
            <div class="total-row">
                <span>Total:</span>
                <span id="cartTotal">$0.00</span>
            </div>
            <button class="checkout-btn">Checkout</button>
        </div>
    </div>

    <!-- Floating Cart Button -->
    <div class="floating-cart-btn" onclick="toggleCart()">
        <i class="fas fa-shopping-cart" style="font-size: 22px;"></i>
        <div class="cart-badge" id="floatingBadge">0</div>
    </div>

    <?php include 'footer.php'; ?>
    
    <!-- JAVASCRIPT LOGIKA KERANJANG -->
    <script>
        let cart = {};

        function updateCart(id, change) {
            const card = document.querySelector(`.menu-card[data-id='${id}']`);
            const name = card.getAttribute('data-name');
            const price = parseFloat(card.getAttribute('data-price'));

            if (!cart[id]) {
                cart[id] = { name: name, price: price, qty: 0 };
            }

            cart[id].qty += change;
            if (cart[id].qty < 0) cart[id].qty = 0;
            if (cart[id].qty === 0) {
                delete cart[id];
            }

            updateUI(id);
            
            // Buka keranjang hanya jika menambah item
            if (change > 0) {
                openCart();
            }
        }

        function updateUI(id) {
            const qtyDisplay = document.getElementById(`qty-${id}`);
            if (qtyDisplay) {
                qtyDisplay.innerText = cart[id] ? cart[id].qty : 0;
            }
            renderSidebar();
        }

        function renderSidebar() {
            const list = document.getElementById('cartItemsList');
            const totalEl = document.getElementById('cartTotal');
            const badgeEl = document.getElementById('floatingBadge');
            
            list.innerHTML = ''; 
            
            let grandTotal = 0;
            let totalItems = 0;
            let hasItems = false;

            for (let itemId in cart) {
                const item = cart[itemId];
                if (item.qty > 0) {
                    hasItems = true;
                    const subtotal = item.price * item.qty;
                    grandTotal += subtotal;
                    totalItems += item.qty;

                    const itemHTML = `
                        <div class="cart-item">
                            <div class="cart-item-details">
                                <h4>${item.name}</h4>
                                <p>$${item.price.toFixed(2)} x ${item.qty}</p>
                            </div>
                            <div class="cart-item-price">
                                $${subtotal.toFixed(2)}
                            </div>
                        </div>
                    `;
                    list.innerHTML += itemHTML;
                }
            }

            if (!hasItems) {
                list.innerHTML = '<p style="text-align:center; margin-top:20px; color:#999; font-size: 13px;">Keranjang kosong.</p>';
            }

            totalEl.innerText = '$' + grandTotal.toFixed(2);
            
            badgeEl.innerText = totalItems;
            badgeEl.style.display = totalItems > 0 ? 'flex' : 'none';
        }

        function toggleCart() {
            const sidebar = document.getElementById('cartSidebar');
            const overlay = document.getElementById('cartOverlay');
            
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
            document.body.classList.toggle('cart-open');
        }

        function openCart() {
            document.getElementById('cartSidebar').classList.add('active');
            document.getElementById('cartOverlay').classList.add('active');
            document.body.classList.add('cart-open');
        }
    </script>
  </body>
</html>