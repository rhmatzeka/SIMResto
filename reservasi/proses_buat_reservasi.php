<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../koneksi.php'; // Sesuaikan path jika perlu

header('Content-Type: application/json');

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $customer_name = htmlspecialchars($_POST['customer_name'] ?? '');
    $customer_email = htmlspecialchars($_POST['customer_email'] ?? '');
    $reservation_datetime_str = $_POST['reservation_datetime'] ?? '';
    $num_of_people = intval($_POST['num_of_people'] ?? 0);
    $table_number = htmlspecialchars($_POST['table_number'] ?? '');
    $special_request = htmlspecialchars($_POST['special_request'] ?? '');

    // Validasi input
    if (
        empty($customer_name) || empty($customer_email) || empty($reservation_datetime_str) ||
        $num_of_people <= 0 || empty($table_number)
    ) {
        $response = [
            'status' => 'error',
            'message' => 'Semua kolom wajib diisi, termasuk pemilihan meja.'
        ];
        echo json_encode($response);
        exit;
    }

    try {
        // Konversi waktu
        $reservation_datetime = new DateTime($reservation_datetime_str);
        $selectedHour = (int) $reservation_datetime->format('H');

        // Validasi jam buka (09:00 - 22:00)
        if ($selectedHour < 9 || $selectedHour >= 22) {
            $response = [
                'status' => 'error',
                'message' => 'Waktu reservasi hanya tersedia dari jam 09:00 sampai 22:00.'
            ];
            echo json_encode($response);
            exit;
        }

        // Durasi reservasi 2 jam
        $reservation_end_datetime = clone $reservation_datetime;
        $reservation_end_datetime->modify('+2 hours');

        // Cek apakah meja sudah dipesan
        $stmt_check = $conn->prepare("
            SELECT id FROM reservations 
            WHERE table_number = ? 
            AND status IN ('Pending', 'Confirmed') 
            AND (
                (? < DATE_ADD(reservation_datetime, INTERVAL 2 HOUR)) 
                AND (? > reservation_datetime)
            )
        ");
        $start = $reservation_datetime->format('Y-m-d H:i:s');
        $end = $reservation_end_datetime->format('Y-m-d H:i:s');

        $stmt_check->bind_param("sss", $table_number, $start, $end);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            $response = [
                'status' => 'error',
                'message' => 'Maaf, meja yang Anda pilih sudah dipesan pada waktu tersebut. Silakan pilih meja atau waktu lain.'
            ];
            echo json_encode($response);
            exit;
        }
        $stmt_check->close();

        // Lakukan insert reservasi
        $stmt = $conn->prepare("
            INSERT INTO reservations 
            (customer_name, customer_email, reservation_datetime, num_of_people, table_number, special_request, status) 
            VALUES (?, ?, ?, ?, ?, ?, 'Pending')
        ");

        $stmt->bind_param(
            "sssiss",
            $customer_name,
            $customer_email,
            $reservation_datetime_str,
            $num_of_people,
            $table_number,
            $special_request
        );

        if ($stmt->execute()) {
            $response = [
                'status' => 'success',
                'message' => 'Reservasi Anda telah berhasil dikirim! Kami akan segera mengkonfirmasinya.'
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Gagal menyimpan reservasi: ' . $stmt->error
            ];
        }

        $stmt->close();
    } catch (Exception $e) {
        $response = [
            'status' => 'error',
            'message' => 'Terjadi error: ' . $e->getMessage()
        ];
    }
} else {
    $response = [
        'status' => 'error',
        'message' => 'Akses tidak valid.'
    ];
}

$conn->close();
echo json_encode($response);
exit;
?>