<?php
// File: actions/delete_user.php
session_start();
include '../config/database.php';

if($_SESSION['role'] == 'ayah' && isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Hapus user berdasarkan ID
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    header("location:../pages/users.php");
}
?>