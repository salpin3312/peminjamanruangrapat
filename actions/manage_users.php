<?php
session_start();
include('../config/config.php');

// Cek apakah admin yang mengakses
if ($_SESSION['role'] != 'admin') {
    header("Location: ../views/login.php");
    exit();
}

// Proses form saat disubmit
if (isset($_POST['submit'])) {
    // Ambil data dari form
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];  // Jangan lupa untuk meng-hash password
    $role = $_POST['role'];

    // Validasi input (misal: cek apakah password cukup panjang)
    if (strlen($password) < 6) {
        echo "Password terlalu pendek. Minimal 6 karakter.";
        exit();
    }

    // Hash password untuk keamanan
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Gunakan prepared statements untuk menghindari SQL injection
    $query = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'sss', $username, $hashed_password, $role);

    if (mysqli_stmt_execute($stmt)) {
        // Redirect ke data_pengguna.php dengan status success
        header("Location: ../views/data_pengguna.php?status=success");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Hapus pengguna berdasarkan ID
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $user_id = (int) $_GET['id'];  // Pastikan ID adalah integer

    // Validasi jika user_id tidak kosong dan aman
    if ($user_id > 0) {
        // Query untuk menghapus pengguna
        $query = "DELETE FROM users WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $user_id);

        if (mysqli_stmt_execute($stmt)) {
            // Reset ID setelah penghapusan
            $reset_query = "ALTER TABLE users AUTO_INCREMENT = 1";
            mysqli_query($conn, $reset_query);

            // Redirect setelah berhasil menghapus
            header("Location: ../views/data_pengguna.php?status=deleted");
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "ID pengguna tidak valid.";
    }
}
?>