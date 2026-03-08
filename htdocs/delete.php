<?php
include 'koneksi.php';

$table = $_GET['table'] ?? 'users';
$id    = $_GET['id'];

if ($table == 'products') {
    $conn->query("DELETE FROM products WHERE id=$id");
    header("Location: pages/data_barang.php");
} else {
    $conn->query("DELETE FROM users WHERE id=$id");
    header("Location: pages/data_users.php");
}
exit;
?>