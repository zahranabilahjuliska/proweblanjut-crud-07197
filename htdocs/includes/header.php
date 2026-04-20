<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Aplikasi CRUD', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="<?= $base_path ?? '../' ?>css/style.css">
</head>
<body>

<?php include $base_path . 'includes/menu.php'; ?>

<div class="main">
    <div class="topbar">
        <h2><?= htmlspecialchars($page_title ?? 'Dashboard', ENT_QUOTES, 'UTF-8') ?></h2>
    </div>
    <div class="content">