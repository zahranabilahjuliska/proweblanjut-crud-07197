<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

// Hanya terima metode POST, bukan GET
// Mencegah penghapusan data via link URL biasa
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die("Metode tidak diizinkan.");
}

// Validasi CSRF token - cegah serangan CSRF
if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    die("Akses tidak sah! Token CSRF tidak valid.");
}
unset($_SESSION['csrf_token']);

$table = $_POST['table'] ?? 'user';
$id    = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    header("Location: pages/dashboard.php");
    exit;
}

if ($table === 'barang') {
    // Ambil nama gambar dulu, lalu hapus file fisiknya
    $stmt = $pdo->prepare("SELECT gambar FROM barang WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if ($row && !empty($row['gambar'])) {
        $path = __DIR__ . '/foto/' . $row['gambar'];
        if (file_exists($path)) {
            unlink($path);
        }
    }

    // Hapus data dari database - Prepared Statement
    $del = $pdo->prepare("DELETE FROM barang WHERE id = ?");
    $del->execute([$id]);
    header("Location: pages/data_barang.php");

} else {
    // Tidak boleh menghapus akun diri sendiri
    if ($id === (int) $_SESSION['user_id']) {
        header("Location: pages/data_users.php");
        exit;
    }

    $del = $pdo->prepare("DELETE FROM user WHERE id = ?");
    $del->execute([$id]);
    header("Location: pages/data_users.php");
}
exit;