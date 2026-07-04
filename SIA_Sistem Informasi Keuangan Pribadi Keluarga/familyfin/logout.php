<?php
// File: logout.php
session_start();

// Hapus semua session
session_unset();
session_destroy();

// Kembalikan ke halaman login
header("location:index.php?pesan=logout");
?>