<?php
// login.php - Login dengan Remember Me tanpa database
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Auto-login dari cookie jika tersedia
if (isset($_COOKIE['remember_user']) && !isset($_SESSION['user_logged_in'])) {
    // Decode data user dari cookie
    $user_data = json_decode($_COOKIE['remember_user'], true);
    
    if ($user_data && isset($user_data['email'])) {
        // Set session otomatis
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_email'] = $user_data['email'];
        $_SESSION['user_name'] = $user_data['name'] ?? '';
        
        // Redirect ke dashboard
        header('Location: dashboard.php');
        exit;
    }
}

// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember_me']);
    
    if (empty($email) || empty($password)) {
        $error = "Email dan password harus diisi";
    } else {
        // CONTOH VALIDASI SEDERHANA
        // Dalam praktik nyata, cek ke database
        // Ini hanya untuk demo
        $valid_email = "user@example.com";
        $valid_password = "password123";
        $user_name = "John Doe";
        
        if ($email === $valid_email && $password === $valid_password) {
            // Login berhasil - Set session
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $user_name;
            
            // Jika centang "Ingat Saya"
            if ($remember) {
                // Simpan data user ke cookie
                $user_data = [
                    'email' => $email,
                    'name' => $user_name
                ];
                
                // Set cookie untuk 30 hari
                setcookie(
                    'remember_user',
                    json_encode($user_data),
                    time() + (30 * 24 * 60 * 60), // 30 hari
                    '/',
                    '',
                    false, // set true jika pakai HTTPS
                    true   // httponly - tidak bisa diakses JavaScript
                );
            }
            
            // Redirect ke dashboard
            header('Location: dashboard.php');
            exit;
        } else {
            $error = "Email atau password salah";
        }
    }
}

// Ambil email dari cookie jika ada (untuk auto-fill)
$saved_email = '';
if (isset($_COOKIE['remember_user'])) {
    $user_data = json_decode($_COOKIE['remember_user'], true);
    $saved_email = $user_data['email'] ?? '';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Selamat Datang</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 50px 40px;
            width: 100%;
            max-width: 450px;
        }
        
        h1 {
            color: #1a202c;
            font-size: 2rem;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .subtitle {
            color: #718096;
            font-size: 1rem;
            margin-bottom: 40px;
        }
        
        .form-group {
            margin-bottom: 24px;
        }
        
        label {
            display: block;
            color: #1a202c;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }
        
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f7fafc;
        }
        
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
            background: white;
        }
        
        input[type="email"]::placeholder,
        input[type="password"]::placeholder {
            color: #a0aec0;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            gap: 8px;
        }
        
        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #667eea;
        }
        
        .remember-me label {
            margin: 0;
            font-weight: 500;
            color: #4a5568;
            cursor: pointer;
            font-size: 0.9rem;
            user-select: none;
        }
        
        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .register-link {
            text-align: center;
            margin-top: 24px;
            color: #718096;
        }
        
        .register-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .register-link a:hover {
            text-decoration: underline;
        }
        
        .error-message {
            background: #fed7d7;
            color: #c53030;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        
        .info-box {
            background: #bee3f8;
            color: #2c5282;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Selamat Datang</h1>
        <p class="subtitle">Masuk ke akun kamu untuk melanjutkan</p>
        
        <!-- Info Demo -->
        <div class="info-box">
            <strong>Demo:</strong> Email: user@example.com | Password: password123
        </div>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    placeholder="Masukkan email"
                    value="<?= htmlspecialchars($saved_email) ?>"
                    required
                >
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Masukkan password"
                    required
                >
            </div>
            
            <div class="remember-me">
                <input 
                    type="checkbox" 
                    id="remember_me" 
                    name="remember_me"
                    <?= !empty($saved_email) ? 'checked' : '' ?>
                >
                <label for="remember_me">Ingat saya</label>
            </div>
            
            <button type="submit" class="btn-login">Login</button>
        </form>
        
        <div class="register-link">
            Belum punya akun? <a href="register.php">Daftar di sini</a>
        </div>
    </div>
</body>
</html>