<?php
session_start();
include('../config/config.php'); // Koneksi ke database

// Periksa apakah user sudah login
if (!isset($_SESSION['id'])) {
    echo "User ID tidak ditemukan. Harap login kembali.";
    header("Location: login.php");
    exit();
}

// Periksa apakah ada parameter 'id' dalam URL
if (isset($_GET['id'])) {
    $booking_id = $_GET['id'];

    // Query untuk menghapus riwayat peminjaman berdasarkan booking_id
    $query = "DELETE FROM bookings WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ii', $booking_id, $_SESSION['id']);
    
    // Eksekusi query
    if (mysqli_stmt_execute($stmt)) {
        echo "Peminjaman berhasil dihapus.";
    } else {
        echo "Gagal menghapus peminjaman.";
    }
    header("Location: riwayat_peminjaman.php"); // Redirect ke halaman riwayat peminjaman
    exit();
} else {
    echo "ID peminjaman tidak ditemukan.";
    header("Location: riwayat_peminjaman.php");
    exit();
}
?>