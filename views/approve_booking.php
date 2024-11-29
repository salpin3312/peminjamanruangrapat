<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Logout functionality
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Koneksi ke database
require_once '../config/config.php';

// Ambil data peminjaman dengan status pending
$pending_bookings_query = "SELECT b.id as booking_id, u.username, r.room_name, b.booking_time_start, b.booking_time_end 
                           FROM bookings b 
                           JOIN users u ON b.user_id = u.id 
                           JOIN rooms r ON b.room_id = r.id 
                           WHERE b.status = 'pending'";
$pending_bookings_result = $conn->query($pending_bookings_query);

// Proses approve atau reject peminjaman
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $booking_id = $_POST['booking_id'];
    $action = $_POST['action'];

    if ($action == 'approve') {
        // Set status booking menjadi sukses
        $update_booking_query = "UPDATE bookings SET status = 'success' WHERE id = $booking_id";
        $update_room_query = "UPDATE rooms r 
                              JOIN bookings b ON r.id = b.room_id 
                              SET r.status = 'booked' WHERE b.id = $booking_id";
        $conn->query($update_booking_query);
        $conn->query($update_room_query);
    } else if ($action == 'reject') {
        // Set status booking menjadi rejected
        $update_booking_query = "UPDATE bookings SET status = 'rejected' WHERE id = $booking_id";
        $conn->query($update_booking_query);
    }

    header("Location: approve_booking.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
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
                <li class="sidebar-item">
                    <a href="admin_dashboard.php" class="sidebar-link">
                        <i class="lni lni-user"></i>
                        <span>Beranda</span>
                    </a>
                </li>
                <hr class="sidebar-separator">
                <li class="sidebar-item">
                    <a href="data_ruangan.php" class="sidebar-link">
                        <i class="lni lni-agenda"></i>
                        <span>Data Ruangan</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="data_pengguna.php" class="sidebar-link">
                        <i class="lni lni-agenda"></i>
                        <span>Data Pengguna</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="laporan.php" class="sidebar-link">
                        <i class="lni lni-agenda"></i>
                        <span>Laporan</span>
                    </a>
                </li>
                <hr class="sidebar-separator">
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                        data-bs-target="#auth" aria-expanded="false" aria-controls="auth">
                        <i class="lni lni-protection"></i>
                        <span>Peminjaman</span>
                    </a>
                    <ul id="auth" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                        <li class="sidebar-item">
                            <a href="approve_booking.php" class="sidebar-link">
                                <i class="lni lni-agenda"></i>
                                <span>Pinjam Ruangan</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="peminjaman_saya.php" class="sidebar-link">
                                <i class="lni lni-agenda"></i>
                                <span>Peminjaman Saya</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="sidebar-item">
                    <a href="admin_account.php" class="sidebar-link">
                        <i class="lni lni-cog"></i>
                        <span>Setting</span>
                    </a>
                </li>
            </ul>
            <div class="sidebar-footer">
                <a href="?logout=true" class="sidebar-link">
                    <i class="lni lni-exit"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="main p-3 w-100">
            <h2>Approve Peminjaman Ruangan</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Ruangan</th>
                        <th>Waktu Mulai</th>
                        <th>Waktu Selesai</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($booking = $pending_bookings_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($booking['username']); ?></td>
                        <td><?php echo htmlspecialchars($booking['room_name']); ?></td>
                        <td><?php echo htmlspecialchars($booking['booking_time_start']); ?></td>
                        <td><?php echo htmlspecialchars($booking['booking_time_end']); ?></td>
                        <td>
                            <form method="POST" style="display: inline-block;">
                                <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                <button type="submit" name="action" value="approve"
                                    class="btn btn-success">Approve</button>
                                <button type="submit" name="action" value="reject"
                                    class="btn btn-danger">Reject</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/dashboard.js"></script>

</body>

</html>