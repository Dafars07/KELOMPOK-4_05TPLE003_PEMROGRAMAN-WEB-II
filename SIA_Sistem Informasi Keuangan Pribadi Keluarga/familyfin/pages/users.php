<?php
// File: pages/users.php
session_start();
include '../config/database.php';

// 1. KEAMANAN: Cek apakah yang akses adalah AYAH?
// Jika bukan Ayah, tendang balik ke dashboard
if($_SESSION['role'] != 'ayah'){
    echo "<script>alert('Anda tidak memiliki akses ke halaman ini!'); window.location='dashboard.php';</script>";
    exit();
}

// 2. Query Ambil Data Semua User
$query_users = mysqli_query($conn, "SELECT * FROM users ORDER BY role ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Pengguna - FamilyFin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="background-color: #f4f6f9;">

<nav class="navbar navbar-dark bg-dark mb-4">
  <div class="container">
    <span class="navbar-brand mb-0 h1">Panel Admin (Ayah)</span>
    <a href="dashboard.php" class="btn btn-outline-light btn-sm">Kembali ke Dashboard</a>
  </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-plus"></i> Tambah Anggota</h5>
                </div>
                <div class="card-body">
                    <form action="../actions/add_user.php" method="POST">
                        <div class="mb-3">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" placeholder="Misal: Ibu Ani" required>
                        </div>
                        <div class="mb-3">
                            <label>Username (untuk Login)</label>
                            <input type="text" name="username" class="form-control" placeholder="tanpa spasi" required>
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Peran (Role)</label>
                            <select name="role" class="form-select">
                                <option value="ibu">Ibu (Bisa Catat & Lihat)</option>
                                <option value="anak">Anak (Hanya Lihat)</option>
                                </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Daftarkan</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Daftar Anggota Keluarga</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($user = mysqli_fetch_assoc($query_users)): ?>
                            <tr>
                                <td><?php echo $user['nama']; ?></td>
                                <td><?php echo $user['username']; ?></td>
                                <td>
                                    <?php 
                                    if($user['role']=='ayah') echo '<span class="badge bg-dark">Ayah (Admin)</span>';
                                    elseif($user['role']=='ibu') echo '<span class="badge bg-success">Ibu</span>';
                                    else echo '<span class="badge bg-info">Anak</span>';
                                    ?>
                                </td>
                                <td>
                                    <?php if($user['role'] != 'ayah'): // Ayah tidak boleh menghapus dirinya sendiri ?>
                                        <a href="../actions/delete_user.php?id=<?php echo $user['id']; ?>" 
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Hapus user ini?');">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>