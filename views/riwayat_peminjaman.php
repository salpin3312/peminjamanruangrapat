<?php
session_start();
include('../config/config.php');

// Periksa apakah user sudah login
if (!isset($_SESSION['id'])) {
    echo "User ID tidak ditemukan. Harap login kembali.";
    header("Location: login.php");
    exit();
}

// Ambil id pengguna dari session
$user_id = $_SESSION['id'];

// Query untuk mengambil riwayat peminjaman berdasarkan user_id
$query = "SELECT b.id, r.room_name, b.booking_date, b.booking_time_start, b.booking_time_end, b.status
          FROM bookings b
          JOIN rooms r ON b.room_id = r.id
          WHERE b.user_id = ? 
          ORDER BY b.booking_date DESC, b.booking_time_start DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Handle penghapusan riwayat peminjaman
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    
    // Query untuk menghapus peminjaman berdasarkan ID
    $delete_query = "DELETE FROM bookings WHERE id = ? AND user_id = ?";
    $delete_stmt = mysqli_prepare($conn, $delete_query);
    mysqli_stmt_bind_param($delete_stmt, 'ii', $delete_id, $user_id);
    if (mysqli_stmt_execute($delete_stmt)) {
        header("Location: riwayat_peminjaman.php"); // Refresh halaman setelah penghapusan
        exit();
    } else {
        echo "Terjadi kesalahan saat menghapus riwayat peminjaman.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman</title>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
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

        <!-- Content Area -->
        <div class="main p-3 w-100">
            <h2>Riwayat Peminjaman</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Ruangan</th>
                        <th>Tanggal Peminjaman</th>
                        <th>Waktu Mulai</th>
                        <th>Waktu Selesai</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $no++ . "</td>";
                            echo "<td>" . htmlspecialchars($row['room_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['booking_date']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['booking_time_start']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['booking_time_end']) . "</td>";
                            echo "<td>" . ucfirst(htmlspecialchars($row['status'])) . "</td>";
                            // Menampilkan tombol "Coba Booking Lagi" jika statusnya "rejected"
                            if ($row['status'] == 'rejected') {
                                echo "<td>
                                        <a href='pinjam_ruangan.php' class='btn btn-warning'>Coba Booking Lagi</a>
                                        <a href='riwayat_peminjaman.php?delete_id=" . $row['id'] . "' class='btn btn-danger'>Hapus</a>
                                      </td>";
                            } else {
                                echo "<td>
                                        <a href='riwayat_peminjaman.php?delete_id=" . $row['id'] . "' class='btn btn-danger'>Hapus</a>
                                      </td>";
                            }
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center'>Tidak ada riwayat peminjaman.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <a href="user_dashboard.php" class="btn btn-primary">Kembali</a>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous">
    </script>
    <script src="../assets/js/dashboard.js"></script>
</body>

</html>