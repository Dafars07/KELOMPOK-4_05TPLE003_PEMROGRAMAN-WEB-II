<?php
// File: actions/delete_trans.php
session_start();
include '../config/database.php';

// Pastikan ada ID yang dikirim
if(isset($_GET['id'])) {
    $id = $_GET['id'];

    // Query Hapus
    $stmt = $conn->prepare("DELETE FROM transactions WHERE id = ?");
    $stmt->bind_param("i", $id);

    if($stmt->execute()) {
        header("location:../pages/dashboard.php?pesan=hapus_sukses");
    } else {
        header("location:../pages/dashboard.php?pesan=gagal");
    }
    $stmt->close();
}
?>