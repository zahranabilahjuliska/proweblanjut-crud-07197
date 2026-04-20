<?php
session_start();
include 'koneksi.php';

// Jika sudah login langsung ke dashboard
// Jika belum login arahkan ke halaman login
if (isset($_SESSION['user_id'])) {
    header("Location: pages/dashboard.php");
} else {
    header("Location: login.php");
}
exit;