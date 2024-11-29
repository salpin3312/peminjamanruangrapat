<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include('../config/config.php');

// Cek apakah admin yang mengakses
if ($_SESSION['role'] != 'admin') {
    header("Location: ../views/login.php");
    exit();
}

// Cek apakah ID ruangan ada di URL
if (isset($_GET['id'])) {
    $room_id = $_GET['id'];

    // Ambil data ruangan dari database berdasarkan ID
    $query = "SELECT * FROM rooms WHERE id = $room_id";
    $result = mysqli_query($conn, $query);
    $room_data = mysqli_fetch_assoc($result);

    // Jika ruangan tidak ditemukan, arahkan kembali ke daftar ruangan
    if (!$room_data) {
        header("Location: data_ruangan.php");
        exit();
    }
}

// Proses pembaruan data ruangan
if (isset($_POST['submit'])) {
    $room_name = $_POST['room_name'];
    $room_capacity = $_POST['room_capacity'];
    $room_description = $_POST['room_description'];
    $status = $_POST['status'];

    // Query untuk memperbarui data ruangan
    $update_query = "UPDATE rooms SET room_name = '$room_name', room_capacity = '$room_capacity', room_description = '$room_description', status = '$status' WHERE id = $room_id";

    if (mysqli_query($conn, $update_query)) {
        header("Location: data_ruangan.php?status=updated");
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
    <title>Edit Ruangan</title>
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
                    <a href="../views/admin_dashboard.php">DPMPTSP KOTA BANDUNG</a>
                </div>
            </div>
            <ul class="sidebar-nav">
                <li class="sidebar-item">
                    <a href="../views/admin_dashboard.php" class="sidebar-link">
                        <i class="lni lni-user"></i>
                        <span>Beranda</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="../views/data_ruangan.php" class="sidebar-link">
                        <i class="lni lni-agenda"></i>
                        <span>Data Ruangan</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="../views/data_pengguna.php" class="sidebar-link">
                        <i class="lni lni-agenda"></i>
                        <span>Data Pengguna</span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <div class="main p-4">
            <a href="data_ruangan.php" class="btn btn-primary mt-3">Kembali</a>
            <h2 class="text-center">Edit Ruangan</h2>
            <hr>

            <!-- Form untuk mengedit ruangan -->
            <form method="POST">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="room_name" class="form-label">Nama Ruangan</label>
                        <input type="text" id="room_name" name="room_name" class="form-control"
                            value="<?php echo $room_data['room_name']; ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="room_capacity" class="form-label">Kapasitas</label>
                        <input type="number" id="room_capacity" name="room_capacity" class="form-control"
                            value="<?php echo $room_data['room_capacity']; ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="room_description" class="form-label">Deskripsi Ruangan</label>
                    <textarea id="room_description" name="room_description" class="form-control"
                        rows="4"><?php echo $room_data['room_description']; ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select" required>
                        <option value="available"
                            <?php echo ($room_data['status'] == 'available') ? 'selected' : ''; ?>>
                            Tersedia
                        </option>
                        <option value="booked" <?php echo ($room_data['status'] == 'booked') ? 'selected' : ''; ?>>
                            Terbook
                        </option>
                    </select>
                </div>

                <button type="submit" name="submit" class="btn btn-primary">Update Ruangan</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/dashboard.js"></script>
</body>

</html>