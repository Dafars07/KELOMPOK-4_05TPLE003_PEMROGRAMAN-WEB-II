<?php
// File: generate_admin.php
include 'config/database.php';

$pass = password_hash("admin123", PASSWORD_DEFAULT);
$sql = "INSERT INTO users (nama, username, password, role) VALUES ('Ayah Budi', 'ayah', '$pass', 'ayah')";

try {
    // Coba jalankan query
    if(mysqli_query($conn, $sql)){
        echo "✅ SUKSES: User Ayah berhasil dibuat! Password: admin123";
    }
} catch (mysqli_sql_exception $e) {
    // Tangkap error jika duplikat (Error Code 1062)
    if ($e->getCode() == 1062) {
        echo "⚠️ INFO: User 'ayah' SUDAH ADA di database. <br>";
        echo "Silakan langsung login saja.";
    } else {
        // Jika error lain, tampilkan
        echo "❌ Error: " . $e->getMessage();
    }
}
?>