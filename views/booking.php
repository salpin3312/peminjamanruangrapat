<?php
session_start();
if ($_SESSION['role'] != 'user') {
    header("Location: ../views/login.php");
    exit();
}

// Koneksi ke database
require_once '../config/config.php';

// Validasi dan ambil room_id
if (isset($_GET['room_id'])) {
    $room_id = $_GET['room_id'];

    // Prepared statement untuk mengambil data ruangan
    $stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ?");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $room_result = $stmt->get_result();
    $room = $room_result->fetch_assoc();

    if (!$room) {
        die("Ruangan tidak ditemukan.");
    }
} else {
    die("Ruangan tidak dipilih.");
}

// Proses booking setelah form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_SESSION['id'])) {
        $user_id = $_SESSION['id']; // ID user yang sedang login
        $booking_date = $_POST['booking_date']; // Tanggal booking
        $booking_time_start = $_POST['booking_time_start']; // Waktu mulai
        $booking_time_end = $_POST['booking_time_end']; // Waktu selesai
        $status = 'pending';

        // Validasi input kosong
        if (empty($booking_date) || empty($booking_time_start) || empty($booking_time_end)) {
            die("Semua field harus diisi.");
        }

        // Prepared statement untuk insert booking
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, room_id, booking_date, booking_time_start, booking_time_end, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissss", $user_id, $room_id, $booking_date, $booking_time_start, $booking_time_end, $status);

        if ($stmt->execute()) {
            // Update status ruangan menjadi booked
            $update_stmt = $conn->prepare("UPDATE rooms SET status = ? WHERE id = ?");
            $update_status = 'booked';
            $update_stmt->bind_param("si", $update_status, $room_id);
            $update_stmt->execute();

            // Redirect ke riwayat peminjaman
            header("Location: riwayat_peminjaman.php");
            exit();
        } else {
            echo "Terjadi kesalahan saat peminjaman ruangan.";
        }
    } else {
        die("User ID tidak ditemukan. Harap login kembali.");
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pinjam Ruangan</title>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Booking Ruangan: <?php echo htmlspecialchars($room['room_name']); ?></h2>
        <div class="card">
            <div class="card-body">
                <p><strong>Deskripsi:</strong> <?php echo htmlspecialchars($room['room_description']); ?></p>
                <p><strong>Kapasitas:</strong> <?php echo htmlspecialchars($room['room_capacity']); ?> orang</p>
                <p><strong>Status:</strong>
                    <?php echo $room['status'] == 'available' ? 'Tersedia' : 'Tidak Tersedia'; ?></p>

                <!-- Form untuk memilih waktu booking -->
                <form method="POST">
                    <div class="mb-3">
                        <label for="booking_date" class="form-label">Tanggal Booking</label>
                        <input type="date" class="form-control" id="booking_date" name="booking_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="booking_time_start" class="form-label">Waktu Mulai</label>
                        <input type="time" class="form-control" id="booking_time_start" name="booking_time_start"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="booking_time_end" class="form-label">Waktu Selesai</label>
                        <input type="time" class="form-control" id="booking_time_end" name="booking_time_end" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Pinjam Ruangan</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>