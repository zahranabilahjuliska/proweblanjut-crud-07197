<?php
// dashboard.php - Halaman setelah login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah user sudah login
if (!isset($_SESSION['user_logged_in'])) {
    header('Location: login.php');
    exit;
}

$user_email = $_SESSION['user_email'] ?? '';
$user_name = $_SESSION['user_name'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
            margin-bottom: 20px;
        }
        
        h1 {
            color: #1a202c;
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .welcome-text {
            color: #718096;
            margin-bottom: 30px;
            font-size: 1.1rem;
        }
        
        .user-info {
            background: #f7fafc;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        
        .user-info p {
            margin-bottom: 10px;
            color: #4a5568;
        }
        
        .user-info strong {
            color: #1a202c;
        }
        
        .btn-logout {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #f56565 0%, #c53030 100%);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(245, 101, 101, 0.4);
        }
        
        .btn-logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 101, 101, 0.5);
        }
        
        .success-box {
            background: #c6f6d5;
            color: #22543d;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .cookie-status {
            background: #e6fffa;
            border: 2px solid #81e6d9;
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
        }
        
        .cookie-status h3 {
            color: #234e52;
            margin-bottom: 10px;
            font-size: 1rem;
        }
        
        .cookie-status p {
            color: #285e61;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="success-box">
                ✅ Login berhasil!
            </div>
            
            <h1>Dashboard</h1>
            <p class="welcome-text">Selamat datang, <?= htmlspecialchars($user_name) ?>!</p>
            
            <div class="user-info">
                <p><strong>Email:</strong> <?= htmlspecialchars($user_email) ?></p>
                <p><strong>Session ID:</strong> <?= session_id() ?></p>
                <p><strong>Login Time:</strong> <?= date('d/m/Y H:i:s') ?></p>
            </div>
            
            <?php if (isset($_COOKIE['remember_user'])): ?>
                <div class="cookie-status">
                    <h3>🍪 Status "Ingat Saya"</h3>
                    <p>✓ Aktif - Kamu akan otomatis login saat membuka browser lagi</p>
                    <p style="margin-top: 5px; font-size: 0.85rem; opacity: 0.8;">
                        Cookie akan expired dalam 30 hari
                    </p>
                </div>
            <?php else: ?>
                <div class="cookie-status" style="background: #fff5f5; border-color: #feb2b2;">
                    <h3 style="color: #742a2a;">ℹ️ Status "Ingat Saya"</h3>
                    <p style="color: #9b2c2c;">Tidak aktif - Kamu harus login lagi setelah tutup browser</p>
                </div>
            <?php endif; ?>
            
            <div style="margin-top: 30px;">
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </div>
    </div>
</body>
</html>