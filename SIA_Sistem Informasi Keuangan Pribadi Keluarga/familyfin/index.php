<?php
session_start();
// Jika sudah login, lempar langsung ke dashboard
if(isset($_SESSION['status']) && $_SESSION['status'] == "login"){
    header("location:pages/dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - FamilyFin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card-login { width: 100%; max-width: 400px; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
        .card-header { background: #fff; border-bottom: none; padding-top: 30px; text-align: center; }
    </style>
</head>
<body>

<div class="card card-login bg-white">
    <div class="card-header">
        <h3 class="text-primary fw-bold"><i class="fas fa-wallet"></i> FamilyFin</h3>
        <p class="text-muted small">Sistem Keuangan Keluarga</p>
    </div>
    <div class="card-body p-4">
        
        <?php if(isset($_GET['pesan'])): ?>
            <div class="alert alert-danger py-2 small">
                <?php 
                    if($_GET['pesan'] == "gagal") echo "Username atau Password salah!";
                    else if($_GET['pesan'] == "logout") echo "Anda telah logout.";
                    else if($_GET['pesan'] == "belum_login") echo "Silakan login terlebih dahulu.";
                ?>
            </div>
        <?php endif; ?>

        <form action="actions/auth_login.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Masukan username" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Masukan password" required>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-block">MASUK SEKARANG</button>
            </div>
        </form>
    </div>
    <div class="card-footer text-center py-3 bg-light">
        <small class="text-muted">&copy; oleh Dafa RS</small>
    </div>
</div>

</body>
</html>