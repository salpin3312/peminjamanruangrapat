<?php
session_start();
if ($_SESSION['role'] != 'user') {
    header("Location: ../views/login.php");
    exit();
}

// Koneksi ke database
require_once '../config/config.php';

// Ambil data statistik
$available_rooms_query = "SELECT COUNT(*) as available_rooms FROM rooms WHERE status = 'available'";
$booked_rooms_query = "SELECT COUNT(*) as booked_rooms FROM rooms WHERE status = 'booked'";
$successful_bookings_query = "SELECT COUNT(*) as successful_bookings FROM bookings WHERE status = 'success'";

$available_rooms_result = $conn->query($available_rooms_query);
$booked_rooms_result = $conn->query($booked_rooms_query);
$successful_bookings_result = $conn->query($successful_bookings_query);

$available_rooms = $available_rooms_result->fetch_assoc()['available_rooms'];
$booked_rooms = $booked_rooms_result->fetch_assoc()['booked_rooms'];
$successful_bookings = $successful_bookings_result->fetch_assoc()['successful_bookings'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda</title>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>

<body>
    <div class="wrapper">
        <aside id="sidebar">
            <div class="d-flex">
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

        <div class="main p-3">
            <h3 class="text-center">Beranda</h3>

            <!-- Statistik Ruangan -->
            <div class="d-flex justify-content-center flex-wrap">
                <div class="card" style="width: 18rem; margin: 10px;">
                    <div class="card-body">
                        <h5 class="card-title">Ruangan Tersedia</h5>
                        <p class="card-text"><?php echo $available_rooms; ?> Ruangan</p>
                    </div>
                </div>

                <div class="card" style="width: 18rem; margin: 10px;">
                    <div class="card-body">
                        <h5 class="card-title">Ruangan Terbooking</h5>
                        <p class="card-text"><?php echo $booked_rooms; ?> Ruangan</p>
                    </div>
                </div>

                <div class="card" style="width: 18rem; margin: 10px;">
                    <div class="card-body">
                        <h5 class="card-title">Peminjaman Sukses</h5>
                        <p class="card-text"><?php echo $successful_bookings; ?> Peminjaman</p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous">
    </script>
    <script src="../assets/js/dashboard.js"></script>
</body>

</html>