<?php
session_start();
include '../config/database.php';

// Pastikan user sudah login
if(!isset($_SESSION['status']) || $_SESSION['status'] != "login"){
    header("location:../index.php");
    exit();
}

// 1. TAMBAH TARGET BARU (BISA SEMUA USER)
if(isset($_POST['tambah_target'])){
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $target = $_POST['target'];
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    
    // Validasi sederhana: Target tidak boleh 0 atau negatif
    if($target <= 0) {
        echo "<script>alert('Target uang harus lebih dari 0!'); window.location='../pages/savings.php';</script>";
        exit();
    }

    $query = "INSERT INTO savings (nama_target, target_jumlah, keterangan) VALUES ('$nama', '$target', '$keterangan')";
    
    if(mysqli_query($conn, $query)){
        header("location:../pages/savings.php?pesan=sukses_buat");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// 2. NABUNG / TOP UP (BISA SEMUA USER)
if(isset($_POST['topup_target'])){
    $id = $_POST['id_saving'];
    $jumlah = $_POST['jumlah_nabung'];
    
    if($jumlah <= 0) {
        echo "<script>alert('Jumlah uang harus lebih dari 0!'); window.location='../pages/savings.php';</script>";
        exit();
    }
    
    // Update saldo
    $query = "UPDATE savings SET terkumpul = terkumpul + $jumlah WHERE id = '$id'";
    mysqli_query($conn, $query);
    
    // Cek Status (Otomatis "Tercapai" jika lunas)
    $cek = mysqli_query($conn, "SELECT * FROM savings WHERE id='$id'");
    $data = mysqli_fetch_assoc($cek);
    
    if($data['terkumpul'] >= $data['target_jumlah']){
        mysqli_query($conn, "UPDATE savings SET status='tercapai' WHERE id='$id'");
    } else {
        // Jika saldo ditarik (minus) dan jadi kurang dari target, kembalikan ke aktif
        mysqli_query($conn, "UPDATE savings SET status='aktif' WHERE id='$id'");
    }

    header("location:../pages/savings.php?pesan=sukses_nabung");
}

// 3. HAPUS TARGET (HANYA AYAH)
if(isset($_GET['hapus_id'])){
    // Proteksi di backend
    if($_SESSION['role'] == 'ayah'){
        $id = $_GET['hapus_id'];
        mysqli_query($conn, "DELETE FROM savings WHERE id='$id'");
    }
    header("location:../pages/savings.php");
}
?>