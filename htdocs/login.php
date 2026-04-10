<?php
session_start();
include 'koneksi.php';

// Jika sudah login via session, langsung ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: pages/dashboard.php");
    exit;
}

// Jika belum login tapi ada cookie remember me, login otomatis
if (isset($_COOKIE['remember_email']) && isset($_COOKIE['remember_passw'])) {
    $cookie_email = $_COOKIE['remember_email'];
    $cookie_passw = $_COOKIE['remember_passw'];

    $stmt = $conn->prepare("SELECT id, name, passw FROM user WHERE email = ?");
    $stmt->bind_param("s", $cookie_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if ($cookie_passw === $user['passw']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name']    = $user['name'];
            header("Location: pages/dashboard.php");
            exit;
        }
    }
    $stmt->close();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = trim($_POST['email']);
    $passw    = $_POST['passw'];
    $remember = isset($_POST['remember']);

    if (empty($email) || empty($passw)) {
        $error = 'Email dan password wajib diisi!';
    } else {
        $stmt = $conn->prepare("SELECT id, name, passw FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if ($passw === $user['passw']) {
                // Simpan session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name']    = $user['name'];

                // Jika remember me dicentang, simpan cookie 7 hari
                if ($remember) {
                    setcookie('remember_email', $email, time() + (86400 * 7), '/');
                    setcookie('remember_passw', $passw, time() + (86400 * 7), '/');
                } else {
                    // Jika tidak dicentang, hapus cookie jika ada
                    setcookie('remember_email', '', time() - 3600, '/');
                    setcookie('remember_passw', '', time() - 3600, '/');
                }

                header("Location: pages/dashboard.php");
                exit;
            } else {
                $error = 'Password salah!';
            }
        } else {
            $error = 'Email tidak ditemukan!';
        }
        $stmt->close();
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
            <div class="alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email"
                       value="<?= htmlspecialchars($_COOKIE['remember_email'] ?? $_POST['email'] ?? '') ?>"
                       placeholder="Masukkan email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="passw"
                       placeholder="Masukkan password" required>
            </div>

            <!-- Checkbox Remember Me -->
            <div class="remember-me">
                <input type="checkbox" name="remember" id="remember"
                       <?= isset($_COOKIE['remember_email']) ? 'checked' : '' ?>>
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