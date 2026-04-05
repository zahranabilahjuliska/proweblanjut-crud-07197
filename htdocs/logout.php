<?php
// logout.php - Logout dan hapus session + cookie
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hapus semua session
$_SESSION = array();
session_destroy();

// Hapus cookie "remember_user" jika ada
if (isset($_COOKIE['remember_user'])) {
    setcookie(
        'remember_user',
        '',
        time() - 3600, // Set waktu ke masa lalu
        '/',
        '',
        false,
        true
    );
}

// Redirect ke halaman login
header('Location: login.php');
exit;
?>