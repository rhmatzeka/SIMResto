<?php
include 'admin_only.php';
include '../koneksi.php';

// Periksa apakah ID dikirim dan bukan ID admin yang sedang login
if (isset($_GET['id']) && $_GET['id'] != $_SESSION['user']['id']) {
    $id = (int)$_GET['id'];

    // PENTING: Karena adanya foreign key di tabel `orders` ke `users`,
    // Anda harus memutuskan apa yang terjadi pada pesanan milik user yang dihapus.
    // Di file `db_lamperieresto.sql`, constraint `fk_orders_users` TIDAK memiliki ON DELETE CASCADE.
    // Artinya, Anda TIDAK BISA menghapus user jika ia masih punya data pesanan.
    
    // Solusi 1: Hapus dulu semua pesanan milik user tersebut.
    // $conn->query("DELETE FROM orders WHERE user_id = $id");

    // Solusi 2 (Direkomendasikan): Ubah struktur database untuk menambahkan ON DELETE CASCADE.
    // ALTER TABLE `orders` DROP FOREIGN KEY `fk_orders_users`;
    // ALTER TABLE `orders` ADD CONSTRAINT `fk_orders_users` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
    // Jika sudah diubah, query di bawah ini akan otomatis menghapus pesanan terkait.

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if (!$stmt->execute()) {
        // Jika gagal (kemungkinan karena masih ada pesanan terkait)
        $_SESSION['pesan_error'] = "Gagal menghapus pengguna. Pastikan pengguna tidak memiliki data pesanan terkait atau atur ON DELETE CASCADE pada database.";
    } else {
         $_SESSION['pesan_sukses'] = "Pengguna berhasil dihapus.";
    }
} else {
    $_SESSION['pesan_error'] = "Aksi tidak valid atau Anda mencoba menghapus akun sendiri.";
}

header('Location: kelola_pengguna.php');
exit;