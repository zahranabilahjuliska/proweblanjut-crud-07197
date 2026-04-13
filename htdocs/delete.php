<?php
include 'koneksi.php';

$table = $_GET['table'] ?? 'user';
$id    = $_GET['id'];

if ($table == 'barang') {
    // Hapus file gambar jika ada
    $row = $conn->query("SELECT gambar FROM barang WHERE id=$id")->fetch_assoc();
    if (!empty($row['gambar']) && file_exists(__DIR__ . '/foto/' . $row['gambar'])) {
        unlink(__DIR__ . '/foto/' . $row['gambar']);
    }
    $conn->query("DELETE FROM barang WHERE id=$id");
    header("Location: pages/data_barang.php");
} else {
    $conn->query("DELETE FROM user WHERE id=$id");
    header("Location: pages/data_users.php");
}
exit;
?>