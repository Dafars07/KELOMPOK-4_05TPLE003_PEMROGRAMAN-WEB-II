<?php
session_start();
include '../config/database.php';

if($_SESSION['role'] == 'ayah'){
    $nama = $_POST['nama'];
    $tipe = $_POST['tipe'];
    // Tangkap nilai budget, jika kosong anggap 0
    $budget = $_POST['budget'] ?: 0; 
    
    // Gunakan Prepared Statement agar aman dari error karakter aneh
    $stmt = $conn->prepare("INSERT INTO categories (nama_kategori, tipe, batas_anggaran) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $nama, $tipe, $budget);
    $stmt->execute();
    
    header("location:../pages/categories.php");
}
?>