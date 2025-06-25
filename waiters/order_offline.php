<h3 class="border-bottom pb-2 mb-3">food order (Offline)</h3>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title">Detail Pesanan Baru</h5>
        <form id="offlineOrderForm">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="customer_name" class="form-label">Nama Pelanggan (Opsional)</label>
                    <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Nama Pelanggan">
                </div>
                <div class="col-md-6">
                    <label for="table_number_order" class="form-label">Pilih Meja</label>
                    <select class="form-select" id="table_number_order" name="table_number" required>
                        <option value="">Memuat meja...</option>
                    </select>
                </div>
            </div>

            <hr>
            <h5>Pilih Menu</h5>
            <div id="menu-items-selection">
                <p class="text-muted">Memuat daftar menu...</p>
            </div>
            
            <hr>
            <h5>Keranjang Pesanan</h5>
            <table class="table table-bordered table-striped" style="background-color: #0F172B; color: white;">
                <thead>
                    <tr>
                        <th>Menu</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="order-cart">
                    <tr><td colspan="5" class="text-center">Belum ada item dipesan.</td></tr>
                </tbody>
                <tfoot>
                    <tr class="table-dark">
                        <td colspan="3" class="text-end">Total:</td>
                        <td id="grand-total">Rp 0.00</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>

            <div class="mb-3">
                <label for="payment_method" class="form-label">Metode Pembayaran</label>
                <select class="form-select" id="payment_method" name="payment_method" required>
                    <option value="">Pilih Metode Pembayaran</option>
                    <option value="Cash">Cash</option>
                    <option value="E-Wallet">E-Wallet</option>
                    <option value="Bank Transfer">Bank Transfer</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-utensils me-2"></i> Buat Pesanan</button>
        </form>
    </div>
</div>

<script>
// Pastikan script hanya dijalankan sekali jika elemen sudah ada
if (document.getElementById('offlineOrderForm')) {
    const tableSelect = document.getElementById('table_number_order');
    const menuItemsSelection = document.getElementById('menu-items-selection');
    const orderCart = document.getElementById('order-cart');
    const grandTotalElement = document.getElementById('grand-total');
    const offlineOrderForm = document.getElementById('offlineOrderForm');

    let cart = []; // Keranjang belanja
    let menuDataFlat = []; // Data menu yang dimuat (versi datar untuk pencarian)

    // Fungsi untuk memuat meja
    function loadTables() {
        if (typeof window.loadAvailableTablesForOrder === 'function') {
            window.loadAvailableTablesForOrder()
                .then(tables => {
                    tableSelect.innerHTML = '<option value="">Pilih Meja</option>';
                    if (tables.length > 0) {
                        tables.forEach(tableNum => {
                            const option = document.createElement('option');
                            option.value = tableNum;
                            option.textContent = `Meja ${tableNum}`;
                            tableSelect.appendChild(option);
                        });
                    } else {
                        tableSelect.innerHTML = '<option value="">Tidak ada meja tersedia</option>';
                        tableSelect.disabled = true;
                    }
                })
                .catch(error => {
                    tableSelect.innerHTML = '<option value="">Gagal memuat meja</option>';
                    console.error("Error loading tables:", error);
                    if (typeof window.showNotification === 'function') {
                        window.showNotification('Gagal memuat daftar meja.', 'danger');
                    }
                });
        } else {
            console.error("loadAvailableTablesForOrder is not defined in global scope.");
            tableSelect.innerHTML = '<option value="">Fungsi meja tidak tersedia</option>';
            if (typeof window.showNotification === 'function') {
                window.showNotification('Terjadi masalah internal: Fungsi loadAvailableTablesForOrder tidak ditemukan.', 'danger');
            }
        }
    }

    // Fungsi untuk memuat menu (tanpa image_url dan description)
    function loadMenu() {
        if (typeof window.loadMenuItems === 'function') {
            window.loadMenuItems()
                .then(categorizedMenu => {
                    menuItemsSelection.innerHTML = ''; // Kosongkan
                    menuDataFlat = []; // Reset data datar

                    // Iterasi kategori dan item
                    for (const category in categorizedMenu) {
                        if (categorizedMenu.hasOwnProperty(category)) {
                            const categoryItems = categorizedMenu[category];
                            
                            // Tambahkan nama kategori sebagai judul
                            menuItemsSelection.innerHTML += `<h6 class="mt-4 mb-2 text-primary">${category}</h6>`;
                            menuItemsSelection.innerHTML += `<div class="row">`; // Pembuka row untuk item dalam kategori

                            categoryItems.forEach(item => {
                                // Tambahkan item ke daftar datar untuk pencarian keranjang nanti
                                menuDataFlat.push(item); 
                                
                                // Menggunakan col-6 untuk 2 kolom per baris
                                const menuItemHtml = `
                                    <div class="col-6 mb-3"> 
                                        <div class="card h-100 shadow-sm">
                                            <div class="card-body p-2"> 
                                                <h6 class="card-title fs-6 mb-1">${item.item_name}</h6> 
                                                <p class="card-text small mb-2"><strong>Rp ${parseFloat(item.price).toFixed(2)}</strong></p> 
                                                <button type="button" class="btn btn-sm btn-primary add-to-cart" data-id="${item.menu_item_id}">
                                                    <i class="fas fa-plus-circle me-1"></i> Tambah
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                menuItemsSelection.innerHTML += menuItemHtml;
                            });
                            menuItemsSelection.innerHTML += `</div>`; // Penutup row untuk item dalam kategori
                        }
                    }
                    
                    if (menuDataFlat.length === 0) {
                        menuItemsSelection.innerHTML = '<p class="text-muted">Tidak ada menu yang tersedia.</p>';
                    } else {
                        // Tambahkan event listener setelah semua menu dimuat
                        document.querySelectorAll('.add-to-cart').forEach(button => {
                            button.addEventListener('click', addToCart);
                        });
                    }
                })
                .catch(error => {
                    menuItemsSelection.innerHTML = '<p class="text-danger">Gagal memuat menu.</p>';
                    console.error("Error loading menu:", error);
                    if (typeof window.showNotification === 'function') {
                        window.showNotification('Gagal memuat daftar menu.', 'danger');
                    }
                });
        } else {
            console.error("loadMenuItems is not defined in global scope.");
            menuItemsSelection.innerHTML = '<p class="text-danger">Fungsi menu tidak tersedia</p>';
            if (typeof window.showNotification === 'function') {
                window.showNotification('Terjadi masalah internal: Fungsi loadMenuItems tidak ditemukan.', 'danger');
            }
        }
    }

    // Fungsi menambah ke keranjang
    function addToCart(event) {
        const itemId = event.currentTarget.dataset.id;
        // Cari item di data datar
        const item = menuDataFlat.find(m => m.menu_item_id == itemId);

        if (item) {
            const existingItem = cart.find(c => c.menu_item_id == itemId);
            if (existingItem) {
                existingItem.quantity++;
            } else {
                cart.push({
                    menu_item_id: item.menu_item_id,
                    item_name: item.item_name,
                    price: parseFloat(item.price),
                    quantity: 1
                });
            }
            renderCart();
        }
    }

    // Fungsi mengubah kuantitas di keranjang
    function updateCartQuantity(itemId, change) {
        const itemIndex = cart.findIndex(c => c.menu_item_id == itemId);
        if (itemIndex > -1) {
            cart[itemIndex].quantity += change;
            if (cart[itemIndex].quantity <= 0) {
                cart.splice(itemIndex, 1); // Hapus jika qty 0 atau kurang
            }
            renderCart();
        }
    }

    // Fungsi menghapus item dari keranjang
    function removeFromCart(itemId) {
        cart = cart.filter(c => c.menu_item_id != itemId);
        renderCart();
    }

    // Fungsi menampilkan keranjang
    function renderCart() {
        orderCart.innerHTML = '';
        let grandTotal = 0;

        if (cart.length === 0) {
            orderCart.innerHTML = '<tr><td colspan="5" class="text-center">Belum ada item dipesan.</td></tr>';
            grandTotalElement.textContent = 'Rp 0.00';
            return;
        }

        cart.forEach(item => {
            const subtotal = item.price * item.quantity;
            grandTotal += subtotal;
            const row = `
                <tr>
                    <td>${item.item_name}</td>
                    <td>Rp ${item.price.toFixed(2)}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-light py-0 px-1" onclick="window.updateCartQuantity(${item.menu_item_id}, -1)">-</button>
                        ${item.quantity}
                        <button type="button" class="btn btn-sm btn-outline-light py-0 px-1" onclick="window.updateCartQuantity(${item.menu_item_id}, 1)">+</button>
                    </td>
                    <td>Rp ${subtotal.toFixed(2)}</td>
                    <td><button type="button" class="btn btn-sm btn-danger" onclick="window.removeFromCart(${item.menu_item_id})"><i class="fas fa-trash"></i></button></td>
                </tr>
            `;
            orderCart.innerHTML += row;
        });
        grandTotalElement.textContent = `Rp ${grandTotal.toFixed(2)}`;
    }

    // Expose functions to global scope for onclick events in dynamically loaded HTML
    // window.updateCartQuantity dan window.removeFromCart sudah diekspos di dashboard_waiters.php
    // jadi tidak perlu double exposure di sini

    // Submit form order offline
    offlineOrderForm.addEventListener('submit', function(e) {
        e.preventDefault();

        if (cart.length === 0) {
            window.showNotification('Keranjang pesanan kosong. Mohon tambahkan menu.', 'warning');
            return;
        }
        if (!tableSelect.value) {
            window.showNotification('Mohon pilih nomor meja.', 'warning');
            return;
        }

        const customerName = document.getElementById('customer_name').value;
        const tableNumber = tableSelect.value;
        const paymentMethod = document.getElementById('payment_method').value;

        const orderData = {
            customer_name: customerName,
            table_number: tableNumber,
            payment_method: paymentMethod,
            items: cart
        };

        fetch('proses_order_offline.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(orderData)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            window.showNotification(data.message, data.status === 'success' ? 'success' : 'danger');
            if (data.status === 'success') {
                cart = [];
                renderCart();
                offlineOrderForm.reset();
                loadTables(); // Refresh daftar meja (jika ada perubahan ketersediaan)
                
                // Refresh tampilan order pending dan status pembayaran jika sedang aktif
                const activePage = document.querySelector('#sidebar .nav-link.active');
                if (activePage) {
                    if (activePage.dataset.page === 'pending_orders') {
                        window.loadContent('pending_orders'); 
                    } else if (activePage.dataset.page === 'payment_status') {
                        window.loadContent('payment_status');
                    }
                }
            }
        })
        .catch(window.handleError);
    });

    // Panggil saat halaman dimuat
    loadTables();
    loadMenu();
    renderCart();
} // End if (document.getElementById('offlineOrderForm'))
</script>