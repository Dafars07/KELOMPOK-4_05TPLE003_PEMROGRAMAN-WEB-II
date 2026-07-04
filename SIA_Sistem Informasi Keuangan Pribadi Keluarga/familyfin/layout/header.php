<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FamilyFin - Keuangan Keluarga</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f6f9; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .sidebar { min-height: 100vh; background: #2c3e50; color: white; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container">
    <a class="navbar-brand" href="#">FamilyFin</a>
    <div class="d-flex">
        <span class="navbar-text text-white me-3">
            Halo, <?php echo $_SESSION['nama']; ?> (<?php echo ucfirst($_SESSION['role']); ?>)
        </span>
        <a href="../logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
  </div>
</nav>
<div class="container"></div>