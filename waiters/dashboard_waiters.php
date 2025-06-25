<?php
session_start();

// Cek role waiters
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'waiters') {
    $_SESSION['login_error'] = "Anda tidak memiliki akses ke halaman ini.";
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waiters Dashboard - LAMPERIE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <style>
        body {
            display: flex; /* Menggunakan flexbox untuk layout sidebar */
            min-height: 100vh;
            background-color: #f4f7f6;
        }
        #sidebar {
            width: 250px;
            background-color: #0F172B; /* Warna gelap untuk sidebar */
            color: white;
            padding: 20px;
            flex-shrink: 0; /* Mencegah sidebar mengecil */
        }
        #sidebar .nav-link {
            color: white;
            padding: 10px 15px;
            margin-bottom: 5px;
            border-radius: 5px;
        }
        #sidebar .nav-link:hover, #sidebar .nav-link.active {
            background-color: #FEA116; /* Warna accent saat hover/active */
            color: #0F172B;
        }
        #main-content {
            flex-grow: 1; /* Konten utama akan mengambil sisa ruang */
            padding: 20px;
            overflow-y: auto; /* Jika konten terlalu panjang */
        }
        .navbar-brand, .navbar-text {
            color: white !important;
        }
        .order-card, .list-group-item { border-left-width: 5px; }
        .border-ready { border-left-color: #198754; }
        .border-preparing { border-left-color: #0d6efd; }
        .status-badge { font-size: 0.8em; }

        /* Gaya untuk reservasi dan order list */
        .list-group-item.pending-reservation { border-left: 5px solid #ffc107; }
        .list-group-item.confirmed-reservation { border-left: 5px solid #0dcaf0; }
        .list-group-item.arrived-reservation { border-left: 5px solid #198754; background-color: #e9f7ef; }
        .list-group-item.completed-reservation { border-left: 5px solid #6c757d; background-color: #f8f9fa; }
        .text-muted-small { font-size: 0.8em; color: #6c757d; }
    </style>
</head>
<body>
    <div id="sidebar" class="d-flex flex-column">
        <h4 class="text-center mb-4"><i class="fas fa-utensils me-2"></i> LAMPERIE</h4>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="#" data-page="reservations"><i class="fas fa-clipboard-list me-2"></i> Reservasi Pengguna</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-page="offline_order"><i class="fas fa-cash-register me-2"></i> Pesan Makanan (Offline)</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-page="pending_orders"><i class="fas fa-hourglass-half me-2"></i> Pesanan Pending</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-page="payment_status"><i class="fas fa-credit-card me-2"></i> Status Pembayaran Meja</a>
            </li>
        </ul>
        <div class="mt-auto text-center">
            <p class="navbar-text">Welcome, <?php echo htmlspecialchars($_SESSION['user']['name']); ?></p>
            <a href="../logout.php" class="btn btn-outline-light btn-sm mt-2"><i class="fas fa-sign-out-alt me-1"></i> Logout</a>
        </div>
    </div>

    <div id="main-content">
        <div id="notification" class="mb-3"></div>
        <div id="content-area">
            <p class="text-muted">Pilih menu dari sidebar.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    const contentArea = document.getElementById('content-area');
    const notification = document.getElementById('notification');
    const navLinks = document.querySelectorAll('#sidebar .nav-link');

    // Fungsi untuk menampilkan notifikasi
    function showNotification(message, type) {
        notification.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
        setTimeout(() => { notification.innerHTML = '' }, 4000);
    }

    function handleError(error) {
        console.error('Fetch Error:', error);
        showNotification('Terjadi masalah koneksi atau server. Silakan cek konsol browser untuk detail.', 'danger');
    }

    // Fungsi untuk memuat konten ke dalam contentArea
    function loadContent(page) {
        let url = '';
        if (page === 'reservations') {
            url = 'get_reservasi_waiter_view.php'; // File baru untuk view reservasi waiters
        } else if (page === 'offline_order') {
            url = 'order_offline.php'; // File baru untuk form order offline
        } else if (page === 'pending_orders') {
            url = 'get_orders_waiter_view.php'; // File baru untuk view order pending waiters
        } else if (page === 'payment_status') {
            url = 'get_payment_status_view.php'; // File baru untuk view status pembayaran
        } else {
            contentArea.innerHTML = '<p class="text-danger">Halaman tidak ditemukan.</p>';
            return;
        }

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.text(); // Ambil sebagai teks/HTML
            })
            .then(html => {
                contentArea.innerHTML = html;
                // Inisialisasi ulang script jika ada di konten yang dimuat
                const scripts = contentArea.querySelectorAll('script');
                scripts.forEach(script => {
                    const newScript = document.createElement('script');
                    Array.from(script.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                    newScript.textContent = script.textContent;
                    script.parentNode.replaceChild(newScript, script);
                });
            })
            .catch(handleError);
    }

    // Event listener untuk sidebar navigation
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            navLinks.forEach(nav => nav.classList.remove('active'));
            this.classList.add('active');
            const page = this.dataset.page;
            loadContent(page);
        });
    });

    // Muat halaman default saat pertama kali dashboard dibuka
    document.addEventListener('DOMContentLoaded', function() {
        loadContent('reservations'); // Default view
    });

    // Fungsi global agar bisa diakses dari konten yang dimuat
    window.showNotification = showNotification;
    window.handleError = handleError;

    // --- Fungsi yang akan dipanggil dari konten yang dimuat (misalnya dari get_reservasi_waiter_view.php) ---
    window.confirmArrival = function(reservationId) {
        if (!confirm('Anda yakin ingin mengkonfirmasi kedatangan pelanggan ini? Status akan menjadi "Arrived".')) return;
        fetch(`proses_reservasi.php?action=confirm_arrival&id=${reservationId}`)
            .then(response => response.json())
            .then(data => {
                showNotification(data.message, data.status === 'success' ? 'success' : 'danger');
                if(data.status === 'success') {
                    loadContent('reservations'); // Refresh tampilan reservasi
                }
            })
            .catch(handleError);
    };

    window.completeReservation = function(reservationId) {
        if (!confirm('Anda yakin tamu reservasi ini sudah selesai? Ini akan menandai reservasi "Completed" dan mengosongkan meja.')) return;
        fetch(`proses_reservasi.php?action=complete&id=${reservationId}`)
            .then(response => response.json())
            .then(data => {
                showNotification(data.message, data.status === 'success' ? 'success' : 'danger');
                if(data.status === 'success') {
                    loadContent('reservations'); // Refresh tampilan reservasi
                    loadContent('payment_status'); // Refresh status pembayaran
                }
            })
            .catch(handleError);
    };

    window.deliverItem = function(orderItemId) {
        if (!confirm('Anda yakin ingin menandai item ini selesai (delivered)?')) return;
        fetch(`update_status.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=deliver&item_id=${orderItemId}`
        })
        .then(response => response.json())
        .then(data => {
            showNotification(data.message, data.status === 'success' ? 'success' : 'danger');
            if(data.status === 'success') {
                loadContent('pending_orders'); // Refresh tampilan pesanan
            }
        })
        .catch(handleError);
    };

    // Ini akan digunakan di `order_offline.php`
    window.loadAvailableTablesForOrder = function() {
        return fetch('get_tables_for_order.php') // File baru untuk meja tersedia untuk order
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.json();
            });
    };

    // Ini akan digunakan di `order_offline.php`
    window.loadMenuItems = function() {
        return fetch('get_menu_items.php') // File baru untuk menu items
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.json();
            });
    };
    // Ini akan digunakan di `get_payment_status_view.php`
    window.fetchPaymentStatus = function() {
        return fetch('get_payment_status.php') // File baru untuk status pembayaran
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.json();
            });
    };
    </script>
</body>
</html>