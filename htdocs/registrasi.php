<?php
session_start();
include 'koneksi.php';

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $passw   = $_POST['passw'];
    $confirm = $_POST['confirm_passw'];

    if (empty($name) || empty($email) || empty($passw) || empty($confirm)) {
        $error = 'Semua field wajib diisi!';
    } elseif (strlen($passw) < 6) {
        $error = 'Password minimal 6 karakter!';
    } elseif ($passw !== $confirm) {
        $error = 'Konfirmasi password tidak cocok!';
    } else {
        // Cek email sudah ada
        $cek = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $cek->bind_param("s", $email);
        $cek->execute();
        $cek->store_result();

        if ($cek->num_rows > 0) {
            $error = 'Email sudah digunakan, coba yang lain!';
        } else {
            $stmt = $conn->prepare("INSERT INTO users (name, email, passw) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $passw);

            if ($stmt->execute()) {
                $success = 'Registrasi berhasil! Silakan login.';
            } else {
                $error = 'Gagal mendaftar, coba lagi.';
            }
            $stmt->close();
        }
        $cek->close();
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
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="name"
                       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                       placeholder="Masukkan nama lengkap" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
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