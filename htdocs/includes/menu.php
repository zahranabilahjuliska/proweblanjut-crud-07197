<?php
// Tentukan halaman aktif
$current = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
?>
<div class="sidebar">
    <div class="sidebar-title">MENU</div>
    <nav>
        <a href="<?= $base_path ?? '../' ?>pages/dashboard.php"
           class="<?= ($current == 'dashboard.php') ? 'active' : '' ?>">
            <span class="icon">&#128200;</span> Dashboard
        </a>
        <a href="<?= $base_path ?? '../' ?>pages/data_users.php"
           class="<?= ($current == 'data_users.php') ? 'active' : '' ?>">
            <span class="icon">&#128100;</span> Data Users
        </a>
        <a href="<?= $base_path ?? '../' ?>pages/data_barang.php"
           class="<?= ($current == 'data_barang.php') ? 'active' : '' ?>">
            <span class="icon">&#128230;</span> Data Barang
        </a>
    </nav>
</div>