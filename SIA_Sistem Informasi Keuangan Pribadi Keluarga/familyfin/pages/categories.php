<?php
session_start();
include '../config/database.php';
if($_SESSION['role'] != 'ayah'){ header("location:dashboard.php"); exit(); }

$q_pemasukan = mysqli_query($conn, "SELECT * FROM categories WHERE tipe='pemasukan'");
$q_pengeluaran = mysqli_query($conn, "SELECT * FROM categories WHERE tipe='pengeluaran'");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Kategori & Budget</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="background-color: #f4f6f9;">

<nav class="navbar navbar-dark bg-dark mb-4">
  <div class="container">
    <span class="navbar-brand mb-0 h1"><i class="fas fa-chart-line"></i> Manajemen Kategori & Budget</span>
    <a href="dashboard.php" class="btn btn-outline-light btn-sm">Kembali</a>
  </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white"><h5 class="mb-0">Tambah Kategori</h5></div>
                <div class="card-body">
                    <form action="../actions/add_category.php" method="POST">
                        <div class="mb-3">
                            <label>Nama Kategori</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Tipe</label>
                            <select name="tipe" class="form-select" id="tipeSelect">
                                <option value="pengeluaran">Pengeluaran</option>
                                <option value="pemasukan">Pemasukan</option>
                            </select>
                        </div>
                        <div class="mb-3" id="inputBudget">
                            <label>Batas Anggaran (Rp)</label>
                            <input type="number" name="budget" class="form-control" placeholder="0 jika tidak dibatasi">
                            <small class="text-muted">Target maksimal per bulan.</small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Simpan</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white"><h5 class="mb-0">Daftar Kategori</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <h6 class="text-success border-bottom pb-2">Pemasukan</h6>
                            <ul class="list-group list-group-flush">
                                <?php while($row = mysqli_fetch_assoc($q_pemasukan)): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?php echo $row['nama_kategori']; ?>
                                    <a href="../actions/delete_category.php?id=<?php echo $row['id']; ?>" class="text-danger" onclick="return confirm('Hapus?')"><i class="fas fa-times"></i></a>
                                </li>
                                <?php endwhile; ?>
                            </ul>
                        </div>
                        <div class="col-6">
                            <h6 class="text-danger border-bottom pb-2">Pengeluaran (Budget)</h6>
                            <ul class="list-group list-group-flush">
                                <?php while($row = mysqli_fetch_assoc($q_pengeluaran)): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <?php echo $row['nama_kategori']; ?>
                                        <?php if($row['batas_anggaran'] > 0): ?>
                                            <br><small class="text-muted" style="font-size: 0.75rem;">
                                                Max: Rp <?php echo number_format($row['batas_anggaran'],0,',','.'); ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                    <a href="../actions/delete_category.php?id=<?php echo $row['id']; ?>" class="text-danger" onclick="return confirm('Hapus?')"><i class="fas fa-times"></i></a>
                                </li>
                                <?php endwhile; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>