<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Aplikasi CRUD' ?></title>
    <link rel="stylesheet" href="<?= $base_path ?? '../' ?>css/style.css">
</head>
<body>

<?php include $base_path . 'includes/menu.php'; ?>

<div class="main">
    <!-- Topbar -->
    <div class="topbar">
        <h2><?= $page_title ?? 'Dashboard' ?></h2>
    </div>

    <!-- Content -->
    <div class="content">