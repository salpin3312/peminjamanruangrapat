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
    <title>Admin Dashboard</title>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>

<body>
    <div class="wrapper d-flex">
        <!-- Sidebar -->
        <aside id="sidebar" class="d-flex flex-column">
            <div class="d-flex justify-content-between ">
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
            <div class="text-center mb-4">
                <h1>Welcome to Admin Dashboard</h1>
                <p>Welcome, <?php echo $_SESSION['username']; ?></p>
                <p>You are logged in as an Admin.</p>
            </div>

            <!-- Statistik Ruangan -->
            <div class="d-flex justify-content-evenly flex-wrap">
                <div class="card card-statistics" style="width: 18rem; margin-bottom: 20px;">
                    <div class="card-body">
                        <h5 class="card-title">Ruangan Tersedia</h5>
                        <p class="card-text"><?php echo $available_rooms; ?> Ruangan</p>
                    </div>
                </div>

                <div class="card card-statistics" style="width: 18rem; margin-bottom: 20px;">
                    <div class="card-body">
                        <h5 class="card-title">Ruangan Terbooking</h5>
                        <p class="card-text"><?php echo $booked_rooms; ?> Ruangan</p>
                    </div>
                </div>

                <div class="card card-statistics" style="width: 18rem; margin-bottom: 20px;">
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