<?php
session_start();
include 'koneksi.php';

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validasi CSRF token
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Permintaan tidak valid. Silakan coba lagi.';
    } else {
        unset($_SESSION['csrf_token']);

        $name    = trim($_POST['name'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $passw   = $_POST['passw'] ?? '';
        $confirm = $_POST['confirm_passw'] ?? '';

        // Validasi input
        if (empty($name) || empty($email) || empty($passw) || empty($confirm)) {
            $error = 'Semua field wajib diisi!';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Format email tidak valid!';
        } elseif (strlen($passw) < 6) {
            $error = 'Password minimal 6 karakter!';
        } elseif ($passw !== $confirm) {
            $error = 'Konfirmasi password tidak cocok!';
        } else {
            // Cek email sudah ada - Prepared Statement
            $cek = $pdo->prepare("SELECT id FROM user WHERE email = ?");
            $cek->execute([$email]);

            if ($cek->fetch()) {
                $error = 'Email sudah digunakan, coba yang lain!';
            } else {
                // Hash password sebelum disimpan ke database
                $hashed = password_hash($passw, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("INSERT INTO user (name, email, passw) VALUES (?, ?, ?)");
                if ($stmt->execute([$name, $email, $hashed])) {
                    $success = 'Registrasi berhasil! Silakan login.';
                } else {
                    $error = 'Gagal mendaftar, coba lagi.';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin-left: 0;
        }
        .auth-box {
            background: #fff;
            padding: 40px 36px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 420px;
        }
        .auth-box h2 {
            font-size: 22px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
            border-bottom: none;
            padding-bottom: 0;
        }
        .auth-box .subtitle {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 28px;
        }
        .alert {
            padding: 10px 14px;
            border-radius: 7px;
            font-size: 13px;
            margin-bottom: 16px;
        }
        .alert-danger  { background: #fff5f5; color: #ef4444; border: 1px solid #fecaca; }
        .alert-success { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .btn-full {
            width: 100%;
            justify-content: center;
            margin-top: 8px;
            padding: 10px;
            font-size: 14px;
        }
        .auth-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 13px;
            color: #64748b;
        }
        .auth-footer a {
            color: #4f46e5;
            font-weight: 600;
            text-decoration: none;
        }
        .auth-footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="auth-box">
        <h2>Buat Akun</h2>
        <p class="subtitle">Daftarkan diri untuk menggunakan aplikasi</p>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <?= csrf_input() ?>

            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="name"
                       value="<?= htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="Masukkan nama lengkap" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email"
                       value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="Masukkan email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="passw"
                       placeholder="Minimal 6 karakter" required>
            </div>
            <div class="form-group">
                <label>Konfirmasi Password</label>
                <input type="password" name="confirm_passw"
                       placeholder="Ulangi password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-full">Daftar</button>
        </form>

        <div class="auth-footer">
            Sudah punya akun? <a href="login.php">Login di sini</a>
        </div>
    </div>
</body>
</html>