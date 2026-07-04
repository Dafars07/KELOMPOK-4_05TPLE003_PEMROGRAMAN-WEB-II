<?php
session_start();
include '../config/database.php';

if($_SESSION['role'] == 'ayah' && isset($_GET['id'])){
    $id = $_GET['id'];
    mysqli_query($conn, "DELETE FROM categories WHERE id='$id'");
    header("location:../pages/categories.php");
}
?>