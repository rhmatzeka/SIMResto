<?php
require_once '../koneksi.php';

// Ambil semua reservasi yang BELUM SELESAI (bukan Cancelled/Completed/No Show)
// dan urutkan dari yang paling dekat
$query = "
    SELECT 
        id, 
        customer_name, 
        customer_email,
        reservation_datetime, 
        num_of_people, 
        table_number, 
        special_request, 
        status,
        seated_at
    FROM reservations 
    WHERE status NOT IN ('Cancelled', 'Completed', 'No Show')
    ORDER BY 
        CASE 
            WHEN status IN ('Arrived', 'Seated') THEN 1
            WHEN status = 'Confirmed' THEN 2  
            ELSE 3 
        END,
        reservation_datetime ASC
";

$result = $conn->query($query);
$reservations = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
$conn->close();
?>

<h3 class="border-bottom pb-3 mb-4 ">
    Reservasi Aktif & Mendatang
</h3>

<?php if (empty($reservations)): ?>
    <div class="text-center py-5">
        <i class="far fa-calendar-times fa-4x text-muted mb-4"></i>
        <h5 class="text-muted">Tidak ada reservasi aktif saat ini</h5>
        <p class="text-muted">Semua reservasi sudah selesai atau belum ada yang masuk.</p>
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($reservations as $r): ?>
            <?php
            $datetime = new DateTime($r['reservation_datetime']);
            $today = new DateTime();
            $isToday = $datetime->format('Y-m-d') === $today->format('Y-m-d');
            $isPast = $datetime < $today && !in_array($r['status'], ['Arrived', 'Seated']);

            $cardClass = match($r['status']) {
                'Pending'   => 'border-warning',
                'Confirmed' => 'border-info',
                'Arrived', 'Seated' => 'border-success',
                default     => 'border-secondary'
            };

            $badgeClass = match($r['status']) {
                'Pending'   => 'bg-warning text-dark',
                'Confirmed' => 'bg-info text-white',
                'Arrived', 'Seated' => 'bg-success text-white',
                default     => 'bg-secondary'
            };
            ?>

            <div class="col-lg-6 col-xxl-4">
                <div class="card <?= $cardClass ?> shadow-sm h-100">
                    <div class="card-header <?= $badgeClass ?> text-white fw-bold d-flex justify-content-between align-items-center">
                        <span><?= htmlspecialchars($r['customer_name']) ?></span>
                        <small><?= $r['num_of_people'] ?> orang</small>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h6 class="mb-1">
                                    <i class="far fa-calendar-alt text-primary"></i>
                                    <?= $datetime->format('d M Y') ?>
                                </h6>
                                <h5 class="mb-0 text-primary">
                                    <?= $datetime->format('H:i') ?> WIB
                                    <?php if ($isToday): ?>
                                        <span class="badge bg-danger ms-2">HARI INI</span>
                                    <?php endif; ?>
                                </h5>
                            </div>
                            <span class="badge <?= $badgeClass ?> fs-6"><?= $r['status'] ?></span>
                        </div>

                        <?php if ($r['table_number']): ?>
                            <p class="mb-2">
                                <i class="fas fa-chair text-success"></i>
                                <strong>Meja <?= htmlspecialchars($r['table_number']) ?></strong>
                            </p>
                        <?php endif; ?>

                        <?php if ($r['special_request']): ?>
                            <p class="mb-2 text-muted small">
                                <em>"<?= htmlspecialchars($r['special_request']) ?>"</em>
                            </p>
                        <?php endif; ?>

                        <p class="mb-0 text-muted small">
                            <i class="far fa-envelope"></i> <?= htmlspecialchars($r['customer_email']) ?>
                        </p>
                    </div>

                    <div class="card-footer bg-light text-center">
                        <?php if ($r['status'] === 'Pending' || $r['status'] === 'Confirmed'): ?>
                            <button class="btn btn-success btn-sm me-2" onclick="confirmArrival(<?= $r['id'] ?>)">
                                Konfirmasi Datang
                            </button>
                        <?php endif; ?>

                        <?php if (in_array($r['status'], ['Arrived', 'Seated'])): ?>
                            <button class="btn btn-primary btn-sm" onclick="completeReservation(<?= $r['id'] ?>)">
                                Selesai & Kosongkan Meja
                            </button>
                        <?php endif; ?>

                        <?php if ($isPast && $r['status'] === 'Confirmed'): ?>
                            <small class="text-danger d-block mt-2">Telat! Belum dikonfirmasi</small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>

    
// Auto refresh setiap 15 detik
setInterval(() => {
    const active = document.querySelector('#sidebar .nav-link.active');
    if (active && active.dataset.page === 'reservations') {
        loadContent('reservations');
    }
}, 15000);
</script>