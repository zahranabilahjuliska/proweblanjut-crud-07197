<?php
session_start();
include 'koneksi.php';

// Hapus remember_token dari database saat logout
if (isset($_COOKIE['remember_id'])) {
    $id  = (int) $_COOKIE['remember_id'];
    $upd = $pdo->prepare("UPDATE user SET remember_token = NULL WHERE id = ?");
    $upd->execute([$id]);
}

// Hancurkan session sepenuhnya
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $p["path"], $p["domain"], $p["secure"], $p["httponly"]);
}
session_destroy();

// Hapus cookie remember me
setcookie('remember_id',    '', time() - 3600, '/', '', false, true);
setcookie('remember_token', '', time() - 3600, '/', '', false, true);

header("Location: login.php");
exit;