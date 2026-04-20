<?php
// ============================================================
// KONEKSI DATABASE - PDO
// PDO digunakan agar semua query bisa pakai Prepared Statements
// sehingga aman dari serangan SQL Injection
// ============================================================
$host   = 'localhost';
$dbname = 'a122407197_crud';
$user   = 'root';
$pass   = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

// ============================================================
// FUNGSI CSRF TOKEN
// CSRF Token digunakan agar form tidak bisa dikirim dari
// luar website (mencegah serangan Cross-Site Request Forgery)
// ============================================================

// Buat token acak dan simpan di session
function generate_csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Cek apakah token yang dikirim dari form cocok dengan session
function validate_csrf_token(?string $token): bool {
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    // hash_equals mencegah timing attack
    return hash_equals($_SESSION['csrf_token'], $token);
}

// Kembalikan input hidden HTML berisi CSRF token
function csrf_input(): string {
    return '<input type="hidden" name="csrf_token" value="'
        . htmlspecialchars(generate_csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}