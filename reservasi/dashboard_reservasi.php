<?php
session_start();
// ... (kode pengecekan login tetap di sini)
if (!isset($_SESSION['user'])) {
    $_SESSION['login_error'] = "Anda harus login terlebih dahulu untuk membuat reservasi.";
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Table - LAMPERIE Restaurant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <style>
        /* ... (CSS Anda tetap sama) ... */
        body { background-color: #f8f9fa; }
        .reservation-container { background-color: #0F172B; color: white; padding: 40px; border-radius: 8px; }
        .form-control[readonly] { background-color: #e9ecef; }
        .btn-book { background-color: #FEA116; border-color: #FEA116; color: white; font-weight: bold; width: 100%; padding: 12px; }
        .btn-book:hover { background-color: #e99002; border-color: #e99002; }
        .section-title { color: #FEA116; }
        .table-item {
            border: 1px solid #FEA116;
            padding: 10px;
            margin-bottom: 10px;
            cursor: pointer;
            border-radius: 5px;
            text-align: center;
            position: relative; /* Untuk posisi teks "Reserved" */
        }
        .table-item.selected {
            background-color: #FEA116;
            color: white;
        }
        .table-item.reserved {
            background-color: #6c757d; /* Abu-abu untuk meja yang tidak tersedia */
            border-color: #6c757d;
            cursor: not-allowed;
            color: #dee2e6;
        }
        .table-item.reserved .reserved-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(0, 0, 0, 0.6);
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 0.8em;
        }
    </style>
</head>
<body>
    <div class="container mt-4 mb-4">
        <div class="mb-4">
            <a href="../dashboard.php" class="btn btn-outline-dark"><i class="fas fa-arrow-left me-2"></i>Kembali ke Halaman Utama</a>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="reservation-container">
                    <div class="text-center mb-4">
                        <h5 class="section-title">Reservation</h5>
                        <h1 class="mb-4">Book A Table Online</h1>
                    </div>
                    
                    <div id="notification" class="mb-3"></div>

                    <form id="reservationForm" action="proses_buat_reservasi.php" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Your Name</label>
                                <input type="text" class="form-control" id="name" name="customer_name" value="<?php echo htmlspecialchars($_SESSION['user']['name']); ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Your Email</label>
                                <input type="email" class="form-control" id="email" name="customer_email" value="<?php echo htmlspecialchars($_SESSION['user']['email']); ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="datetime" class="form-label">Date & Time</label>
                                <input type="datetime-local" class="form-control" id="datetime" name="reservation_datetime" required>
                            </div>
                            <div class="col-md-6">
                                <label for="people" class="form-label">No Of People</label>
                                <select class="form-select" id="people" name="num_of_people" required>
                                    <option value="">Pilih Jumlah Orang</option>
                                    <option value="1">1 Orang</option>
                                    <option value="2">2 Orang</option>
                                    <option value="3">3 Orang</option>
                                    <option value="4">4 Orang</option>
                                    <option value="5">5+ Orang</option>
                                </select>
                            </div>
                            <div class="col-12 mt-3">
                                <label class="form-label">Pilih Meja</label>
                                <div class="row" id="tableSelection">
                                    </div>
                                <input type="hidden" name="table_number" id="selectedTable" required>
                            </div>
                            <div class="col-12">
                                <label for="request" class="form-label">Special Request</label>
                                <textarea class="form-control" id="request" name="special_request" rows="3"></textarea>
                            </div>
                            <div class="col-12 mt-4">
                                <button class="btn btn-book" type="submit" id="submitButton">BOOK NOW</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const datetimeInput = document.getElementById('datetime');
        const peopleSelect = document.getElementById('people');
        const tableSelectionDiv = document.getElementById('tableSelection');
        const selectedTableInput = document.getElementById('selectedTable');
        const reservationForm = document.getElementById('reservationForm');
        const submitButton = document.getElementById('submitButton');
        const notification = document.getElementById('notification');

        // Fungsi untuk mengatur batasan waktu
        function setDateTimeConstraints() {
            const now = new Date();
            const year = now.getFullYear();
            const month = (now.getMonth() + 1).toString().padStart(2, '0');
            const day = now.getDate().toString().padStart(2, '0');
            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');

            const minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
            datetimeInput.min = minDateTime;

            // Batasi waktu hanya dari jam 09:00 sampai 22:00
            datetimeInput.addEventListener('change', function() {
                const selectedDateTime = new Date(this.value);
                const selectedHour = selectedDateTime.getHours();

                if (selectedHour < 9 || selectedHour >= 22) {
                    alert('Waktu reservasi hanya tersedia dari jam 09:00 sampai 22:00.');
                    this.value = ''; // Kosongkan input jika di luar jam
                }
                fetchAvailableTables(); // Panggil saat waktu berubah
            });
        }

        // Fungsi untuk mengambil dan menampilkan meja yang tersedia
        function fetchAvailableTables() {
            const reservationDateTime = datetimeInput.value;
            const numOfPeople = peopleSelect.value;

            if (!reservationDateTime || !numOfPeople) {
                tableSelectionDiv.innerHTML = '<div class="col-12"><p>Pilih Tanggal & Waktu serta Jumlah Orang terlebih dahulu.</p></div>';
                selectedTableInput.value = '';
                return;
            }

            fetch(`meja_tersedia.php?datetime=${reservationDateTime}&people=${numOfPeople}`)
                .then(response => response.json())
                .then(data => {
                    tableSelectionDiv.innerHTML = ''; // Kosongkan pilihan meja sebelumnya
                    selectedTableInput.value = ''; // Reset meja terpilih

                    if (data.status === 'success' && data.tables.length > 0) {
                        data.tables.forEach(table => {
                            const colDiv = document.createElement('div');
                            colDiv.className = 'col-md-3 col-sm-4 col-6'; // Ukuran kolom responsif
                            const tableItem = document.createElement('div');
                            tableItem.className = 'table-item';
                            tableItem.dataset.tableNumber = table.table_number;
                            tableItem.dataset.capacity = table.capacity;

                            let tableStatusText = '';
                            if (table.is_reserved) {
                                tableItem.classList.add('reserved');
                                tableItem.title = `Reserved until ${new Date(table.reserved_until).toLocaleString()}`;
                                tableStatusText = `<span class="reserved-text">Reserved</span>`;
                            } else if (table.status === 'occupied') {
                                tableItem.classList.add('reserved'); // Gunakan kelas reserved untuk occupied juga
                                tableItem.title = 'Occupied';
                                tableStatusText = `<span class="reserved-text">Occupied</span>`;
                            } else {
                                tableItem.title = `Capacity: ${table.capacity}`;
                                tableItem.addEventListener('click', function() {
                                    if (!this.classList.contains('reserved')) {
                                        document.querySelectorAll('.table-item').forEach(item => {
                                            item.classList.remove('selected');
                                        });
                                        this.classList.add('selected');
                                        selectedTableInput.value = this.dataset.tableNumber;
                                    }
                                });
                            }
                            
                            tableItem.innerHTML = `<h5>Meja ${table.table_number}</h5> <small>Kapasitas: ${table.capacity}</small> ${tableStatusText}`;
                            colDiv.appendChild(tableItem);
                            tableSelectionDiv.appendChild(colDiv);
                        });
                    } else {
                        tableSelectionDiv.innerHTML = '<div class="col-12"><p class="text-danger">Tidak ada meja yang tersedia untuk tanggal, waktu, dan jumlah orang yang dipilih.</p></div>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching tables:', error);
                    tableSelectionDiv.innerHTML = '<div class="col-12"><p class="text-danger">Gagal memuat meja. Silakan coba lagi.</p></div>';
                });
        }

        // Panggil saat halaman dimuat
        setDateTimeConstraints();
        fetchAvailableTables(); // Panggil saat halaman pertama kali dimuat untuk menampilkan meja awal

        // Panggil saat tanggal/waktu atau jumlah orang berubah
        datetimeInput.addEventListener('change', fetchAvailableTables);
        peopleSelect.addEventListener('change', fetchAvailableTables);

        // Submit form dengan AJAX
        reservationForm.addEventListener('submit', function(event) {
            event.preventDefault();

            if (!selectedTableInput.value) {
                notification.innerHTML = `<div class="alert alert-warning">Mohon pilih meja terlebih dahulu.</div>`;
                return;
            }

            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';
            notification.innerHTML = ''; 

            const formData = new FormData(this);

            fetch('proses_buat_reservasi.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    notification.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                    reservationForm.reset();
                    selectedTableInput.value = ''; // Reset selected table
                    fetchAvailableTables(); // Perbarui tampilan meja setelah reservasi berhasil
                } else {
                    notification.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                notification.innerHTML = `<div class="alert alert-danger">Terjadi masalah koneksi. Silakan coba lagi.</div>`;
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = 'BOOK NOW';
            });
        });
    </script>
</body>
</html>