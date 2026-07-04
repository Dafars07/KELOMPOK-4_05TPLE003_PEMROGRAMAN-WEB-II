<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "family_fin";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi Error: " . mysqli_connect_error());
}
?>