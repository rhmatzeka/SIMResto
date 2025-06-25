<?php
// Ini akan dipanggil via AJAX, jadi tidak perlu session_start() lagi jika dashboard_waiters.php sudah memulainya
// dan tidak ada logic PHP yang memerlukan session secara langsung di sini.
// Namun, jika Anda menggunakan koneksi database di sini, pastikan koneksi.php sudah di-require
require_once '../koneksi.php'; // Sesuaikan path

// Pastikan hanya bisa diakses via AJAX atau dari script yang benar
// if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
//     header("Location: ../dashboard_waiters.php"); // Redirect jika diakses langsung
//     exit();
// }

// Ambil data reservasi
$date_now_start = date('Y-m-d 00:00:00');
$date_now_end = date('Y-m-d 23:59:59');

$stmt = $conn->prepare("
    SELECT id, customer_name, reservation_datetime, num_of_people, table_number, special_request, status, seated_at
    FROM reservations
    WHERE 
        (reservation_datetime BETWEEN ? AND ?) OR 
        (status IN ('Confirmed', 'Arrived', 'Seated')) -- Tampilkan reservasi relevan untuk hari ini/yang sedang aktif
    ORDER BY reservation_datetime ASC
");
$stmt->bind_param("ss", $date_now_start, $date_now_end);
$stmt->execute();
$result = $stmt->get_result();
$reservations = [];
while ($row = $result->fetch_assoc()) {
    $reservations[] = $row;
}
$stmt->close();
$conn->close();
?>

<h3 class="border-bottom pb-2 mb-3">Reservasi Pengguna</h3>
<div class="list-group">
    <?php if (empty($reservations)): ?>
        <p class="text-muted">Tidak ada reservasi aktif untuk saat ini.</p>
    <?php else: ?>
        <?php foreach ($reservations as $res): ?>
            <?php
            $statusBadgeClass = '';
            $actionButtons = '';
            $tableInfo = '';
            $reservationClass = '';

            if ($res['status'] === 'Pending') {
                $statusBadgeClass = 'bg-warning text-dark';
                $reservationClass = 'pending-reservation';
            } elseif ($res['status'] === 'Confirmed') {
                $statusBadgeClass = 'bg-info text-dark';
                $reservationClass = 'confirmed-reservation';
                // PERBAIKAN DI SINI: Gunakan string concatenation PHP
                $tableInfo = "(Meja " . ($res['table_number'] ?: 'Belum Ditentukan') . ")";
                // Tombol "Konfirmasi Kedatangan" untuk status Confirmed
                $actionButtons = '
                    <button type="button" class="btn btn-sm btn-success" 
                        onclick="confirmArrival(' . $res['id'] . ')">
                        <i class="fas fa-check-circle me-1"></i> Konfirmasi Kedatangan
                    </button>
                ';
            } elseif ($res['status'] === 'Arrived') {
                $statusBadgeClass = 'bg-success';
                $reservationClass = 'arrived-reservation';
                // PERBAIKAN DI SINI: Gunakan string concatenation PHP
                $tableInfo = "(Meja " . ($res['table_number'] ?: 'N/A') . ")";
                $actionButtons = '
                    <button type="button" class="btn btn-sm btn-secondary" onclick="completeReservation(' . $res['id'] . ')">
                        <i class="fas fa-handshake me-1"></i> Selesai
                    </button>
                ';
            } elseif ($res['status'] === 'Seated') { // Untuk reservasi lama yang mungkin masih 'Seated'
                $statusBadgeClass = 'bg-primary';
                $reservationClass = 'seated-reservation';
                // PERBAIKAN DI SINI: Gunakan string concatenation PHP
                $tableInfo = "(Meja " . ($res['table_number'] ?: 'N/A') . ")";
                $actionButtons = '
                    <button type="button" class="btn btn-sm btn-secondary" onclick="completeReservation(' . $res['id'] . ')">
                        <i class="fas fa-handshake me-1"></i> Selesai
                    </button>
                ';
            } elseif ($res['status'] === 'Cancelled') {
                $statusBadgeClass = 'bg-danger';
            } elseif ($res['status'] === 'No Show') {
                $statusBadgeClass = 'bg-secondary';
            } elseif ($res['status'] === 'Completed') {
                $statusBadgeClass = 'bg-dark';
                $reservationClass = 'completed-reservation';
            }
            ?>
            <div class="list-group-item d-flex justify-content-between align-items-center mb-2 shadow-sm <?php echo $reservationClass; ?>">
                <div>
                    <h5><?php echo htmlspecialchars($res['customer_name']); ?> <span class="badge <?php echo $statusBadgeClass; ?> status-badge"><?php echo htmlspecialchars($res['status']); ?></span> <?php echo $tableInfo; ?></h5>
                    <p class="mb-1">Waktu Reservasi: <?php echo (new DateTime($res['reservation_datetime']))->format('d M Y H:i'); ?></p>
                    <p class="mb-0">Jumlah Orang: <?php echo htmlspecialchars($res['num_of_people']); ?></p>
                    <?php if (!empty($res['special_request'])): ?>
                        <p class="mb-0 text-muted-small">Permintaan Khusus: <?php echo htmlspecialchars($res['special_request']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($res['seated_at'])): ?>
                        <p class="mb-0 text-muted-small">Ditempatkan Pada: <?php echo (new DateTime($res['seated_at']))->format('d M Y H:i'); ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <?php echo $actionButtons; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>