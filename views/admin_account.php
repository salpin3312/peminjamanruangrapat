<?php
session_start();
include('../config/config.php');

// Pastikan hanya admin yang dapat mengakses halaman ini
if ($_SESSION['role'] != 'admin') {
    header("Location: ../views/login.php");
    exit();
}

// Ambil data akun admin dari database
$username = $_SESSION['username'];
$query = "SELECT * FROM users WHERE username = '$username' AND role = 'admin'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Proses update akun
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_username = $_POST['username'];
    $new_password = $_POST['password'];

    // Enkripsi password baru jika diubah
    if (!empty($new_password)) {
        $new_password = password_hash($new_password, PASSWORD_DEFAULT);  // Gunakan password_hash() untuk menghash password
        $update_query = "UPDATE users SET username = ?, password = ? WHERE username = ? AND role = 'admin'";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, 'sss', $new_username, $new_password, $username);
    } else {
        $update_query = "UPDATE users SET username = ? WHERE username = ? AND role = 'admin'";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, 'ss', $new_username, $username);
    }

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['username'] = $new_username;  // Update session username
        $success_message = "Akun berhasil diperbarui!";
    } else {
        $error_message = "Terjadi kesalahan saat memperbarui akun.";
    }
}


// Logout functionality
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Account Settings</title>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>

<body>
    <div class="wrapper ">
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
                            <a href="pinjam_ruangan.php" class="sidebar-link">
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
        <div class="main p-4">
            <h2 cls="text-center">Pengaturan Akun Admin</h2>

            <!-- Menampilkan pesan sukses atau error -->
            <?php if (isset($success_message)) { ?>
            <div class="alert alert-success" role="alert">
                <?php echo $success_message; ?>
            </div>
            <?php } ?>
            <?php if (isset($error_message)) { ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
            <?php } ?>

            <!-- Form untuk mengubah username dan password -->
            <form method="POST" action="admin_account.php">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username"
                        value="<?php echo $user['username']; ?>" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password Baru (Kosongkan jika tidak ingin mengubah)</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>

                <button type="submit" class="btn btn-primary">Perbarui Akun</button>
            </form>

            <!-- Tombol kembali ke admin dashboard -->
            <a href="admin_dashboard.php" class="btn btn-secondary mt-3" style="width: 135px;">Kembali</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous">
    </script>
    <script src="../assets/js/dashboard.js"></script>
</body>

</html>