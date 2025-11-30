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
    <title>Menu - ARJI Restaurant</title>
    <link rel="website icon" type="png" href="arji.png" />
    
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
    <link rel="stylesheet" href="css/troli.css" />
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
    renderCategory($conn, 'snacks', 'snacks');
    renderCategory($conn, 'dessert', 'dessert');
    renderCategory($conn, 'non-coffee', 'nonCoffee');
    renderCategory($conn, 'coffee', 'coffee');
    renderCategory($conn, 'Juice', 'juice');
    ?>

    <!-- =========================================== -->
    <!-- KERANJANG BELANJA (VERSI FLOATING CARD) -->
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

    <!-- Floating Cart Button (Hanya muncul di sini) -->
    <div class="floating-cart-btn" onclick="toggleCart()">
        <i class="fas fa-shopping-cart" style="font-size: 22px;"></i>
        <div class="cart-badge" id="floatingBadge" style="display: none;">0</div>
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
        
        if (totalItems > 0) {
            badgeEl.style.display = 'flex';
            badgeEl.classList.add('pop');
            setTimeout(() => badgeEl.classList.remove('pop'), 200);
        } else {
            badgeEl.style.display = 'none';
        }
    }

    function toggleCart() {
        const sidebar = document.getElementById('cartSidebar');
        const overlay = document.getElementById('cartOverlay');
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
        document.body.classList.toggle('cart-open');
    }

    // --- LOGIKA CHECKOUT (DIPERBARUI UNTUK DEBUGGING) ---
    document.querySelector('.checkout-btn').addEventListener('click', function() {
        if (Object.keys(cart).length === 0) {
            alert("Keranjang belanja Anda kosong!");
            return;
        }

        let totalAmount = 0;
        for (let id in cart) {
            totalAmount += cart[id].price * cart[id].qty;
        }

        if(!confirm("Apakah Anda yakin ingin memproses pesanan senilai $" + totalAmount.toFixed(2) + "?")) {
            return;
        }

        // Kirim Data
        fetch('checkout_process.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ cart: cart, total: totalAmount })
        })
        .then(async response => {
            const text = await response.text(); // Baca respon mentah
            try {
                const data = JSON.parse(text); // Coba ubah ke JSON
                if (data.status === 'success') {
                    alert("Pesanan Berhasil! Silakan tunggu konfirmasi.");
                    cart = {};
                    renderSidebar();
                    document.querySelectorAll('.order-quantity').forEach(el => el.innerText = '0');
                    toggleCart();
                } else {
                    alert("Gagal: " + data.message);
                }
            } catch (e) {
                // Jika error JSON, tampilkan pesan error asli dari PHP (misal error database)
                console.error("Server Error Response:", text);
                alert("Terjadi Error di Server:\n" + text.substring(0, 150) + "...");
            }
        })
        .catch(error => {
            console.error('Network Error:', error);
            alert("Gagal menghubungi server. Pastikan file 'checkout_process.php' sudah ada.");
        });
    });
</script>
  </body>
</html>