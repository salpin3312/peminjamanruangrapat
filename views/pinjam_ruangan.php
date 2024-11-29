<?php
session_start();
if ($_SESSION['role'] != 'user') {
    header("Location: ../views/login.php");
    exit();
}

// Koneksi ke database
require_once '../config/config.php';

// Ambil data ruangan yang tersedia
$rooms_query = "SELECT * FROM rooms";
$rooms_result = $conn->query($rooms_query);

// Proses booking setelah form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $room_id = $_POST['id'];
    $user_id = $_SESSION['id']; // ID user yang sedang login
    $booking_time_start = $_POST['booking_time_start'];
    $booking_time_end = $_POST['booking_time_end'];

    // Insert data ke tabel bookings
    $booking_query = "INSERT INTO bookings (user_id, room_id, booking_date, booking_time_start, booking_time_end, status) 
                      VALUES ($user_id, $room_id, CURDATE(), '$booking_time_start', '$booking_time_end', 'pending')";

    if ($conn->query($booking_query) === TRUE) {
        // Update status ruangan menjadi booked
        $update_room_query = "UPDATE rooms SET status = 'booked' WHERE id = $room_id";
        $conn->query($update_room_query);

        // Redirect ke riwayat peminjaman
        header("Location: riwayat_peminjaman.php");
        exit();
    } else {
        echo "Terjadi kesalahan saat peminjaman ruangan.";
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

    <div class="wrapper d-flex">
        <!-- Sidebar -->
        <aside id="sidebar" class="d-flex flex-column">
            <div class="d-flex justify-content-between">
                <button class="toggle-btn" type="button">
                    <i class="lni lni-grid-alt"></i>
                </button>
                <div class="sidebar-logo">
                    <ul style="margin-left: -40px; margin-bottom: -20px;">
                        <li><a href="#">DPMPTSP KOTA BANDUNG</a></li>
                        <li><?php echo htmlspecialchars($_SESSION['username']); ?></li>
                    </ul>
                </div>
            </div>
            <ul class="sidebar-nav">
                <hr>
                <li class="sidebar-item">
                    <a href="user_dashboard.php" class="sidebar-link">
                        <i class="lni lni-user"></i>
                        <span>Beranda</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="pinjam_ruangan.php" class="sidebar-link">
                        <i class="lni lni-agenda"></i>
                        <span>Pinjam Ruangan</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="riwayat_peminjaman.php" class="sidebar-link">
                        <i class="lni lni-history"></i>
                        <span>Riwayat Peminjaman</span>
                    </a>
                </li>
            </ul>
            <div class="sidebar-footer">
                <a href="../actions/logout.php" class="sidebar-link">
                    <i class="lni lni-exit"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="main p-3 w-100">
            <h2>Pinjam Ruangan</h2>
            <div class="row">
                <?php while ($room = $rooms_result->fetch_assoc()): ?>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><strong> <?php echo $room['room_name']; ?></strong></h5>
                            <p class="card-text"><?php echo $room['room_description']; ?></p>
                            <p class="card-text">Kapasitas: <?php echo $room['room_capacity']; ?> orang</p>

                            <!-- Status Ruangan -->
                            <p class="card-text">
                                <strong>Status:</strong>
                                <?php echo $room['status'] == 'available' ? 'Tersedia' : 'Tidak Tersedia'; ?>
                            </p>

                            <!-- Tombol untuk memilih ruangan -->
                            <?php if ($room['status'] == 'available'): ?>
                            <button class="btn btn-primary" onclick="showBookingForm(<?php echo $room['id']; ?>)">Pilih
                                Ruangan</button>
                            <?php else: ?>
                            <p>Ruangan sudah dibooking pada waktu lain.</p>
                            <?php endif; ?>

                            <!-- Form Booking -->
                            <div id="bookingForm_<?php echo $room['id']; ?>" class="mt-3" style="display: none;">
                                <form method="POST">
                                    <input type="hidden" name="id" value="<?php echo $room['id']; ?>">
                                    <div class="mb-3">
                                        <label for="booking_time_start" class="form-label">Waktu Mulai</label>
                                        <input type="time" class="form-control" name="booking_time_start" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="booking_time_end" class="form-label">Waktu Selesai</label>
                                        <input type="time" class="form-control" name="booking_time_end" required>
                                    </div>
                                    <button type="submit" class="btn btn-success">Pinjam Ruangan</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function showBookingForm(roomId) {
        document.getElementById('bookingForm_' + roomId).style.display = 'block';
    }
    </script>
    <script src="../assets/js/dashboard.js"></script>

</body>

</html>