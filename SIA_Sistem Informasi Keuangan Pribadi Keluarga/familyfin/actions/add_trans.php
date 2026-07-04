<?php
// File: actions/add_trans.php
session_start();
include '../config/database.php';

// Cek jika tombol simpan ditekan
if(isset($_POST['simpan_transaksi'])) {
    $user_id    = $_SESSION['id']; // Mengambil ID user yang sedang login
    $tipe       = $_POST['tipe'];
    $kategori   = $_POST['kategori'];
    $jumlah     = $_POST['jumlah'];
    $keterangan = $_POST['keterangan'];
    $tanggal    = $_POST['tanggal'];

    // Query Insert
    $stmt = $conn->prepare("INSERT INTO transactions (user_id, tipe, kategori, jumlah, keterangan, tanggal) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ississ", $user_id, $tipe, $kategori, $jumlah, $keterangan, $tanggal);

    if($stmt->execute()) {
        // Berhasil, kembali ke dashboard
        header("location:../pages/dashboard.php?pesan=sukses");
    } else {
        // Gagal
        header("location:../pages/dashboard.php?pesan=gagal");
    }
    $stmt->close();
}
?>