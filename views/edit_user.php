<?php
session_start();
include('../config/config.php');

// Pastikan hanya admin yang dapat mengakses halaman ini
if ($_SESSION['role'] != 'admin') {
    header("Location: ../views/login.php");
    exit();
}

// Ambil ID pengguna yang ingin diedit
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Ambil data pengguna dari database
    $query = "SELECT * FROM users WHERE id = '$user_id' AND role = 'user'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    // Jika pengguna tidak ditemukan
    if (!$user) {
        header("Location: data_pengguna.php");
        exit();
    }
} else {
    header("Location: data_pengguna.php");
    exit();
}

// Proses update data pengguna
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_username = $_POST['username'];
    $new_password = $_POST['password'];

    // Enkripsi password baru jika diubah
    if (!empty($new_password)) {
        $new_password = password_hash($new_password, PASSWORD_DEFAULT);  // Gunakan password_hash() untuk menghash password
        $update_query = "UPDATE users SET username = ?, password = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, 'ssi', $new_username, $new_password, $user_id);
    } else {
        $update_query = "UPDATE users SET username = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, 'si', $new_username, $user_id);
    }

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['username'] = $new_username;  // Update session username
        header("Location: data_pengguna.php");
        exit();
    } else {
        $error_message = "Terjadi kesalahan saat memperbarui akun.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengguna</title>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <aside id="sidebar" class="d-flex flex-column">
            <div class="d-flex">
                <button class="toggle-btn" type="button">
                    <i class="lni lni-grid-alt"></i>
                </button>
                <div class="sidebar-logo">
                    <a href="admin_dashboard.php">DPMPTSP KOTA BANDUNG</a>
                </div>
            </div>
            <ul class="sidebar-nav">
                <li class="sidebar-item">
                    <a href="admin_dashboard.php" class="sidebar-link">
                        <i class="lni lni-user"></i>
                        <span>Beranda</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="data_pengguna.php" class="sidebar-link">
                        <i class="lni lni-agenda"></i>
                        <span>Data Pengguna</span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <div class="main p-4">
            <h2 class="text-center">Edit Pengguna</h2>

            <!-- Menampilkan pesan error -->
            <?php if (isset($error_message)) { ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
            <?php } ?>

            <!-- Form untuk mengubah username dan password -->
            <form method="POST" action="edit_user.php?id=<?php echo $user['id']; ?>">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username"
                        value="<?php echo $user['username']; ?>" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password Baru (Kosongkan jika tidak ingin mengubah)</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>

                <button type="submit" class="btn btn-primary" style="height: 40px; width: 200px;">Perbarui
                    Pengguna</button>
            </form>

            <a href="data_pengguna.php" class="btn btn-secondary mt-3" style="height: 40px; width: 200px;">Kembali</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/dashboard.js"></script>
</body>

</html>