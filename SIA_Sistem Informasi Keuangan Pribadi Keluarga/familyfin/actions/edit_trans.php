<?php
// File: actions/edit_trans.php
session_start();
include '../config/database.php';

// Pastikan tombol update ditekan
if(isset($_POST['update_transaksi'])) {
    $id         = $_POST['id_transaksi']; // ID yang mau diedit
    $tipe       = $_POST['tipe'];
    $kategori   = $_POST['kategori'];
    $jumlah     = $_POST['jumlah'];
    $keterangan = $_POST['keterangan'];
    $tanggal    = $_POST['tanggal'];

    // Query Update menggunakan Prepared Statement (Aman)
    $query = "UPDATE transactions SET tipe=?, kategori=?, jumlah=?, keterangan=?, tanggal=? WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssissi", $tipe, $kategori, $jumlah, $keterangan, $tanggal, $id);

    if($stmt->execute()) {
        header("location:../pages/dashboard.php?pesan=update_sukses");
    } else {
        header("location:../pages/dashboard.php?pesan=gagal");
    }
    $stmt->close();
}
?>