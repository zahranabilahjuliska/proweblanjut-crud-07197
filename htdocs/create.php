<?php
$base_path = './';
include $base_path . 'koneksi.php';

$table = $_GET['table'] ?? 'users';

// Tentukan judul dan redirect
if ($table == 'products') {
    $page_title = 'Tambah Barang';
    $redirect   = 'pages/data_barang.php';
} else {
    $page_title = 'Tambah User';
    $redirect   = 'pages/data_users.php';
}

// Proses form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($table == 'products') {
        $nama_produk = $_POST['nama_produk'];
        $harga       = $_POST['harga'];
        $stok        = $_POST['stok'];
        $conn->query("INSERT INTO products (nama_produk, harga, stok) VALUES ('$nama_produk', '$harga', '$stok')");
    } else {
        $name  = $_POST['name'];
        $email = $_POST['email'];
        $passw = $_POST['passw'];
        $conn->query("INSERT INTO users (name, email, passw) VALUES ('$name', '$email', '$passw')");
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
                <input type="text" name="nama_produk" placeholder="Masukkan nama produk" required>
            </div>
            <div class="form-group">
                <label>Harga (Rp)</label>
                <input type="number" name="harga" placeholder="Contoh: 50000" min="0" required>
            </div>
            <div class="form-group">
                <label>Stok</label>
                <input type="number" name="stok" placeholder="Jumlah stok" min="0" required>
            </div>
        <?php else: ?>
            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="name" placeholder="Masukkan nama" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="Masukkan email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="passw" placeholder="Masukkan password" required>
            </div>
        <?php endif; ?>

        <div style="display:flex; gap:10px; margin-top:8px;">
            <button type="submit" class="btn btn-primary">&#10003; Simpan</button>
            <a href="<?= $redirect ?>" class="btn btn-secondary">&#8592; Batal</a>
        </div>
    </form>
</div>

<?php include $base_path . 'includes/footer.php'; ?>