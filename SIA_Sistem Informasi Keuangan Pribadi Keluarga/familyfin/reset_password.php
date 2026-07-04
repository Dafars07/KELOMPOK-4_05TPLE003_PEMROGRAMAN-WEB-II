<?php
// File: reset_password.php
include 'config/database.php';

// 1. Kita siapkan password "admin123" yang sudah di-hash (dienkripsi) dengan benar
$password_baru = password_hash("admin123", PASSWORD_DEFAULT);

// 2. Kita update user yang username-nya 'ayah' (atau 'Ayah')
// Kita gunakan username 'Ayah' atau 'ayah' untuk memastikan kena sasarannya
$sql = "UPDATE users SET password = '$password_baru' WHERE username = 'ayah' OR username = 'Ayah'";

if(mysqli_query($conn, $sql)){
    echo "<h1>✅ BERHASIL!</h1>";
    echo "<p>Password untuk user 'ayah' sudah di-reset menjadi: <b>admin123</b></p>";
    echo "<p>Silakan <a href='index.php'>Login Kembali di sini</a></p>";
} else {
    echo "<h1>❌ GAGAL</h1>";
    echo "Error: " . mysqli_error($conn);
}
?>