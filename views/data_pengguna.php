<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include('../config/config.php');

// Menampilkan pesan sukses jika status = success
if (isset($_GET['status']) && $_GET['status'] == 'success') {
    echo "<script>alert('Pengguna berhasil ditambahkan!');</script>";
}

// Cek apakah admin yang mengakses
if ($_SESSION['role'] != 'admin') {
    header("Location: ../views/login.php");
    exit();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Hapus pengguna berdasarkan ID
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $user_id = $_GET['id'];

    // Query untuk menghapus pengguna
    $query = "DELETE FROM users WHERE id = $user_id";
    if (mysqli_query($conn, $query)) {
        // Reset ID setelah penghapusan
        $reset_query = "ALTER TABLE users AUTO_INCREMENT = 1";
        mysqli_query($conn, $reset_query);

        // Redirect setelah berhasil menghapus
        header("Location: ../views/data_pengguna.php?status=deleted");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>

<body>
    <div class="wrapper">
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
            <a href="../views/admin_dashboard.php" class="btn btn-primary mt-3">Kembali</a>
            <h2 class="text-center">Data Pengguna</h2>
            <hr>
            <!-- Form untuk menambahkan pengguna baru -->
            <h4>Tambah Pengguna Baru</h4>
            <form method="POST" action="../actions/manage_users.php" class="mb-4">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" id="username" name="username" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select name="role" id="role" class="form-select">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <button type="submit" name="submit" class="btn btn-primary">Tambah Pengguna</button>
            </form>
            <hr>


            <!-- Tabel Data Pengguna -->
            <h4 class="mt-5">Daftar Pengguna</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
            // Query untuk menampilkan daftar pengguna
            $query_users = "SELECT * FROM users WHERE role = 'user' OR role = 'admin'";
            $result_users = mysqli_query($conn, $query_users);
            $no = 1;

            while ($user_data = mysqli_fetch_assoc($result_users)) {
            ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $user_data['username']; ?></td>
                        <td><?php echo ucfirst($user_data['role']); ?></td>
                        <td>
                            <a href="edit_user.php?id=<?php echo $user_data['id']; ?>" class="btn btn-warning">Edit</a>
                            <a href="?action=delete&id=<?php echo $user_data['id']; ?>" class="btn btn-danger"
                                onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>

            <!-- Tombol kembali ke admin dashboard -->
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/dashboard.js"></script>
</body>

</html>