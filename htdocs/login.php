<?php
session_start();
include 'koneksi.php';

// Jika sudah login, langsung ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: pages/dashboard.php");
    exit;
}

// -------------------------------------------------------
// Auto-login via remember me cookie
// Cookie menyimpan ID user + token acak (bukan password)
// -------------------------------------------------------
if (isset($_COOKIE['remember_id']) && isset($_COOKIE['remember_token'])) {
    $cookie_id    = (int) $_COOKIE['remember_id'];
    $cookie_token = $_COOKIE['remember_token'];

    $stmt = $pdo->prepare("SELECT id, name, remember_token FROM user WHERE id = ?");
    $stmt->execute([$cookie_id]);
    $u = $stmt->fetch();

    if ($u && !empty($u['remember_token']) &&
        password_verify($cookie_token, $u['remember_token'])) {
        $_SESSION['user_id'] = $u['id'];
        $_SESSION['name']    = $u['name'];
        header("Location: pages/dashboard.php");
        exit;
    }

    // Token tidak valid, hapus cookie
    setcookie('remember_id',    '', time() - 3600, '/', '', false, true);
    setcookie('remember_token', '', time() - 3600, '/', '', false, true);
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validasi CSRF token - cegah serangan CSRF
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Permintaan tidak valid. Silakan coba lagi.';
    } else {
        unset($_SESSION['csrf_token']);

        $email    = trim($_POST['email'] ?? '');
        $passw    = $_POST['passw'] ?? '';
        $remember = isset($_POST['remember']);

        if (empty($email) || empty($passw)) {
            $error = 'Email dan password wajib diisi!';
        } else {
            // Prepared statement - cegah SQL Injection
            $stmt = $pdo->prepare("SELECT id, name, passw FROM user WHERE email = ?");
            $stmt->execute([$email]);
            $u = $stmt->fetch();

            // password_verify - cek password yang sudah di-hash
            if ($u && password_verify($passw, $u['passw'])) {
                $_SESSION['user_id'] = $u['id'];
                $_SESSION['name']    = $u['name'];

                if ($remember) {
                    // Buat token acak, simpan hash-nya di database
                    $raw_token    = bin2hex(random_bytes(32));
                    $hashed_token = password_hash($raw_token, PASSWORD_DEFAULT);

                    $upd = $pdo->prepare("UPDATE user SET remember_token = ? WHERE id = ?");
                    $upd->execute([$hashed_token, $u['id']]);

                    // Simpan ke cookie dengan HttpOnly (tidak bisa diakses JavaScript)
                    $expire = time() + (86400 * 7);
                    setcookie('remember_id',    $u['id'],   $expire, '/', '', false, true);
                    setcookie('remember_token', $raw_token, $expire, '/', '', false, true);
                } else {
                    setcookie('remember_id',    '', time() - 3600, '/', '', false, true);
                    setcookie('remember_token', '', time() - 3600, '/', '', false, true);
                }

                header("Location: pages/dashboard.php");
                exit;
            } else {
                // Pesan generik agar tidak bocorkan info akun
                $error = 'Email atau password salah!';
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
    <title>Login</title>
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
        .alert-danger {
            padding: 10px 14px;
            border-radius: 7px;
            font-size: 13px;
            margin-bottom: 16px;
            background: #fff5f5;
            color: #ef4444;
            border: 1px solid #fecaca;
        }
        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 4px 0 12px;
            font-size: 13px;
            color: #64748b;
        }
        .remember-me input[type="checkbox"] {
            width: 15px;
            height: 15px;
            accent-color: #4f46e5;
            cursor: pointer;
        }
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
        <h2>Selamat Datang</h2>
        <p class="subtitle">Masuk ke akun kamu untuk melanjutkan</p>

        <?php if ($error): ?>
            <div class="alert-danger">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <?= csrf_input() ?>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email"
                       value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="Masukkan email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="passw"
                       placeholder="Masukkan password" required>
            </div>

            <div class="remember-me">
                <input type="checkbox" name="remember" id="remember"
                       <?= isset($_COOKIE['remember_id']) ? 'checked' : '' ?>>
                <label for="remember">Ingat saya selama 7 hari</label>
            </div>

            <button type="submit" class="btn btn-primary btn-full">Login</button>
        </form>

        <div class="auth-footer">
            Belum punya akun? <a href="registrasi.php">Daftar di sini</a>
        </div>
    </div>
</body>
</html>