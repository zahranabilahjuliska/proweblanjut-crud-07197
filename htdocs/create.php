<?php
$base_path = './';
include $base_path . 'koneksi.php';

// Ambil table dari GET atau POST
$table = $_GET['table'] ?? $_POST['table'] ?? 'user';

// Tentukan judul dan redirect
if ($table == 'barang') {
    $page_title = 'Tambah Barang';
    $redirect   = 'pages/data_barang.php';
} else {
    $page_title = 'Tambah User';
    $redirect   = 'pages/data_users.php';
}

$error = '';

// Proses form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($table == 'barang') {
        $nama_produk = $_POST['nama_produk'];
        $harga       = $_POST['harga'];
        $stok        = $_POST['stok'];
        $gambar      = '';

        // Proses upload gambar
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            $allowed     = ['jpg', 'jpeg', 'png', 'webp'];
            $ext         = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
            $max_size    = 2 * 1024 * 1024; // 2MB

            if (!in_array($ext, $allowed)) {
                $error = 'Format gambar tidak didukung! Gunakan JPG, PNG, atau WEBP.';
            } elseif ($_FILES['gambar']['size'] > $max_size) {
                $error = 'Ukuran gambar maksimal 2MB!';
            } else {
                // Buat nama file unik agar tidak bentrok
                $gambar    = time() . '_' . uniqid() . '.' . $ext;
                $upload_to = __DIR__ . '/foto/' . $gambar;
                move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_to);
            }
        }

        if (empty($error)) {
            $conn->query("INSERT INTO barang (nama_produk, harga, stok, gambar)
                          VALUES ('$nama_produk', '$harga', '$stok', '$gambar')");
            header("Location: $redirect");
            exit;
        }

    } else {
        $name  = $_POST['name'];
        $email = $_POST['email'];
        $passw = $_POST['passw'];
        $conn->query("INSERT INTO user (name, email, passw) VALUES ('$name', '$email', '$passw')");
        header("Location: $redirect");
        exit;
    }
}

include $base_path . 'includes/header.php';
?>

<div class="card form-max">
    <div class="card-header">
        <h3><?= $page_title ?></h3>
    </div>

    <?php if ($error): ?>
        <div style="padding:10px 14px; border-radius:7px; font-size:13px; margin-bottom:16px;
                    background:#fff5f5; color:#ef4444; border:1px solid #fecaca;">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <!-- Simpan nilai table agar tidak hilang saat POST -->
        <input type="hidden" name="table" value="<?= $table ?>">

        <?php if ($table == 'barang'): ?>
            <div class="form-group">
                <label>Nama Produk</label>
                <input type="text" name="nama_produk"
                       value="<?= htmlspecialchars($_POST['nama_produk'] ?? '') ?>"
                       placeholder="Masukkan nama produk" required>
            </div>
            <div class="form-group">
                <label>Harga (Rp)</label>
                <input type="number" name="harga"
                       value="<?= htmlspecialchars($_POST['harga'] ?? '') ?>"
                       placeholder="Contoh: 50000" min="0" required>
            </div>
            <div class="form-group">
                <label>Stok</label>
                <input type="number" name="stok"
                       value="<?= htmlspecialchars($_POST['stok'] ?? '') ?>"
                       placeholder="Jumlah stok" min="0" required>
            </div>
            <div class="form-group">
                <label>Gambar Produk <span style="color:#94a3b8; font-weight:400;">(opsional, maks. 2MB)</span></label>
                <input type="file" name="gambar" accept=".jpg,.jpeg,.png,.webp"
                       style="padding:6px; border:1px solid #e2e8f0; border-radius:7px; width:100%; font-size:13px;">
                <small style="color:#94a3b8; font-size:12px;">Format: JPG, PNG, WEBP</small>
            </div>
            <!-- Preview gambar sebelum upload -->
            <div id="preview-wrap" style="display:none; margin-bottom:16px;">
                <img id="preview-img" src="#" alt="Preview"
                     style="width:100px; height:100px; object-fit:cover; border-radius:8px; border:1px solid #e2e8f0;">
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

<script>
// Preview gambar sebelum diupload
const inputGambar = document.querySelector('input[name="gambar"]');
if (inputGambar) {
    inputGambar.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview-img').src = e.target.result;
                document.getElementById('preview-wrap').style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    });
}
</script>

<?php include $base_path . 'includes/footer.php'; ?>