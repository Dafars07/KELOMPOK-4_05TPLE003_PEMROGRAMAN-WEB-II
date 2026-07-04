<?php
// File: actions/auth_login.php
session_start();
include '../config/database.php';

// Menangkap data input
$username = $_POST['username'];
$password = $_POST['password'];

// 1. Cek apakah username ada di database
// Menggunakan Prepared Statement untuk keamanan (Anti SQL Injection)
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// 2. Jika user ditemukan
if($result->num_rows > 0){
    $data = $result->fetch_assoc();

    // 3. Verifikasi Password (Hash vs Input)
    if(password_verify($password, $data['password'])){
        
        // Login Sukses: Simpan data penting ke Session
        $_SESSION['id'] = $data['id'];
        $_SESSION['nama'] = $data['nama'];
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $data['role']; // Penting untuk pembeda hak akses
        $_SESSION['status'] = "login";

        // Redirect ke Dashboard
        header("location:../pages/dashboard.php");

    } else {
        // Password Salah
        header("location:../index.php?pesan=gagal");
    }

} else {
    // Username tidak ditemukan
    header("location:../index.php?pesan=gagal");
}

$stmt->close();
?>