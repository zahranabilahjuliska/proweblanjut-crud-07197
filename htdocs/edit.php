<?php
$base_path = './';
include $base_path . 'koneksi.php';

$table = $_GET['table'] ?? 'users';
$id    = $_GET['id'];

// Tentukan judul dan redirect
if ($table == 'products') {
    $page_title = 'Edit Barang';
    $redirect   = 'pages/data_barang.php';
    $data = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();
} else {
    $page_title = 'Edit User';
    $redirect   = 'pages/data_users.php';
    $data = $conn->query("SELECT * FROM users WHERE id=$id")->fetch_assoc();
}

// Proses form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($table == 'products') {
        $nama_produk = $_POST['nama_produk'];
        $harga       = $_POST['harga'];
        $stok        = $_POST['stok'];
        $conn->query("UPDATE products SET nama_produk='$nama_produk', harga='$harga', stok='$stok' WHERE id=$id");
    } else {
        $name  = $_POST['name'];
        $email = $_POST['email'];
        $passw = $_POST['passw'];
        $conn->query("UPDATE users SET name='$name', email='$email', passw='$passw' WHERE id=$id");
    }
    header("Location: $redirect");
    exit;
}

include $base_path . 'includes/header.php';
?>

<div class="card form-max">
    <div class="card-header">
        <h3><?= $page_title ?></h3>
    </div>

    <form method="POST">
        <?php if ($table == 'products'): ?>
            <div class="form-group">
                <label>Nama Produk</label>
                <input type="text" name="nama_produk" value="<?= htmlspecialchars($data['nama_produk']) ?>" required>
            </div>
            <div class="form-group">
                <label>Harga (Rp)</label>
                <input type="number" name="harga" value="<?= $data['harga'] ?>" min="0" required>
            </div>
            <div class="form-group">
                <label>Stok</label>
                <input type="number" name="stok" value="<?= $data['stok'] ?>" min="0" required>
            </div>
        <?php else: ?>
            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="name" value="<?= htmlspecialchars($data['name']) ?>" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($data['email']) ?>" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="passw" value="<?= $data['passw'] ?>" required>
            </div>
        <?php endif; ?>

        <div style="display:flex; gap:10px; margin-top:8px;">
            <button type="submit" class="btn btn-primary">&#10003; Update</button>
            <a href="<?= $redirect ?>" class="btn btn-secondary">&#8592; Batal</a>
        </div>
    </form>
</div>

<?php include $base_path . 'includes/footer.php'; ?>