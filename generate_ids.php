<?php
echo "<pre>"; // Agar tampilan lebih rapi
include 'koneksi.php';

// Ambil semua pengguna yang BELUM punya member_id
$result = $conn->query("SELECT id FROM users WHERE member_id IS NULL OR member_id = ''");

if ($result->num_rows > 0) {
    echo "Memproses " . $result->num_rows . " pengguna lama...\n\n";

    // Siapkan statement untuk update agar lebih aman dan efisien
    $stmt = $conn->prepare("UPDATE users SET member_id = ? WHERE id = ?");

    while ($user = $result->fetch_assoc()) {
        $user_id = $user['id'];
        
        // Membuat nomor unik dengan format LP-TAHUN-000ID
        $tahun = date('Y');
        $member_id = "LP-" . $tahun . "-" . str_pad($user_id, 4, '0', STR_PAD_LEFT);

        // Update database
        $stmt->bind_param("si", $member_id, $user_id);
        if ($stmt->execute()) {
            echo "SUCCESS: Pengguna ID #$user_id diberi Member ID: $member_id\n";
        } else {
            echo "FAILED: Gagal untuk pengguna ID #$user_id: " . $stmt->error . "\n";
        }
    }
    $stmt->close();
    echo "\nSelesai! Semua pengguna lama sudah memiliki Member ID.";
} else {
    echo "Tidak ada pengguna lama yang perlu diberi Member ID baru.";
}

$conn->close();
echo "</pre>";
?>