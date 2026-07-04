<?php
// File: actions/add_user.php
session_start();
include '../config/database.php';

// Cek akses lagi (Double Protection)
if($_SESSION['role'] != 'ayah'){ header("location:../index.php"); exit(); }

$nama     = $_POST['nama'];
$username = $_POST['username'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Enkripsi Wajib!
$role     = $_POST['role'];

// Cek apakah username sudah dipakai?
$cek = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
if(mysqli_num_rows($cek) > 0){
    echo "<script>alert('Username sudah terpakai! Ganti yang lain.'); window.history.back();</script>";
} else {
    // Insert Data
    $stmt = $conn->prepare("INSERT INTO users (nama, username, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nama, $username, $password, $role);
    
    if($stmt->execute()){
        header("location:../pages/users.php?pesan=sukses");
    } else {
        echo "Gagal: " . $conn->error;
    }
}
?>