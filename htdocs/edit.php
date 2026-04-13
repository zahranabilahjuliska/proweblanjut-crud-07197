<?php
$base_path = './';
include $base_path . 'koneksi.php';

$table = $_GET['table'] ?? $_POST['table'] ?? 'user';
$id    = $_GET['id']    ?? $_POST['id']    ?? 0;

// Tentukan judul dan redirect
if ($table == 'barang') {
    $page_title = 'Edit Barang';
    $redirect   = 'pages/data_barang.php';
    $data       = $conn->query("SELECT * FROM barang WHERE id=$id")->fetch_assoc();
} else {
    $page_title = 'Edit User';
    $redirect   = 'pages/data_users.php';
    $data       = $conn->query("SELECT * FROM user WHERE id=$id")->fetch_assoc();
}

$error        = '';
$upload_dir   = __DIR__ . '/foto/';   // path absolut folder uploads
$upload_url   = 'foto/';              // path relatif untuk src img

// Proses form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($table == 'barang') {
        $nama_produk = $_POST['nama_produk'];
        $harga       = $_POST['harga'];
        $stok        = $_POST['stok'];
        $gambar      = $data['gambar']; // default: pakai gambar lama

        // Jika ada upload gambar baru
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            $allowed  = ['jpg', 'jpeg', 'png', 'webp'];
            $ext      = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
            $max_size = 2 * 1024 * 1024; // 2MB

            if (!in_array($ext, $allowed)) {
                $error = 'Format gambar tidak didukung! Gunakan JPG, PNG, atau WEBP.';
            } elseif ($_FILES['gambar']['size'] > $max_size) {
                $error = 'Ukuran gambar maksimal 2MB!';
            } else {
                // Hapus gambar lama jika ada
                if (!empty($data['gambar']) && file_exists($upload_dir . $data['gambar'])) {
                    unlink($upload_dir . $data['gambar']);
                }
                // Upload gambar baru
                $gambar = time() . '_' . uniqid() . '.' . $ext;
                move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_dir . $gambar);
            }
        }

        // Jika centang hapus gambar
        if (isset($_POST['hapus_gambar'])) {
            if (!empty($data['gambar']) && file_exists($upload_dir . $data['gambar'])) {
                unlink($upload_dir . $data['gambar']);
            }
            $gambar = '';
        }

        if (empty($error)) {
            $conn->query("UPDATE barang SET
                          nama_produk='$nama_produk',
                          harga='$harga',
                          stok='$stok',
                          gambar='$gambar'
                          WHERE id=$id");
            header("Location: $redirect");
            exit;
        }

    } else {
        $name  = $_POST['name'];
        $email = $_POST['email'];
        $passw = $_POST['passw'];
        $conn->query("UPDATE user SET name='$name', email='$email', passw='$passw' WHERE id=$id");
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
        <input type="hidden" name="table" value="<?= $table ?>">
        <input type="hidden" name="id"    value="<?= $id ?>">

        <?php if ($table == 'barang'): ?>
            <div class="form-group">
                <label>Nama Produk</label>
                <input type="text" name="nama_produk"
                       value="<?= htmlspecialchars($data['nama_produk']) ?>" required>
            </div>
            <div class="form-group">
                <label>Harga (Rp)</label>
                <input type="number" name="harga"
                       value="<?= $data['harga'] ?>" min="0" required>
            </div>
            <div class="form-group">
                <label>Stok</label>
                <input type="number" name="stok"
                       value="<?= $data['stok'] ?>" min="0" required>
            </div>

            <!-- Gambar saat ini -->
            <div class="form-group">
                <label>Gambar Saat Ini</label>
                <?php if (!empty($data['gambar']) && file_exists($upload_dir . $data['gambar'])): ?>
                    <div style="margin-bottom:10px;">
                        <img src="<?= $upload_url . htmlspecialchars($data['gambar']) ?>"
                             alt="Gambar produk"
                             style="width:100px; height:100px; object-fit:cover;
                                    border-radius:8px; border:1px solid #e2e8f0;">
                    </div>
                    <label style="display:flex; align-items:center; gap:6px; font-size:13px;
                                  color:#ef4444; font-weight:400; cursor:pointer;">
                        <input type="checkbox" name="hapus_gambar" value="1"
                               style="accent-color:#ef4444;">
                        Hapus gambar ini
                    </label>
                <?php else: ?>
                    <div style="width:100px; height:100px; background:#f1f5f9; border-radius:8px;
                                display:flex; align-items:center; justify-content:center;
                                font-size:28px; border:1px solid #e2e8f0; margin-bottom:10px;">
                        &#128247;
                    </div>
                    <small style="color:#94a3b8; font-size:12px;">Belum ada gambar</small>
                <?php endif; ?>
            </div>

            <!-- Upload gambar baru -->
            <div class="form-group">
                <label>Ganti Gambar <span style="color:#94a3b8; font-weight:400;">(opsional, maks. 2MB)</span></label>
                <input type="file" name="gambar" accept=".jpg,.jpeg,.png,.webp"
                       style="padding:6px; border:1px solid #e2e8f0; border-radius:7px;
                              width:100%; font-size:13px;">
                <small style="color:#94a3b8; font-size:12px;">Format: JPG, PNG, WEBP</small>
            </div>

            <!-- Preview gambar baru -->
            <div id="preview-wrap" style="display:none; margin-bottom:16px;">
                <label style="font-size:13px; font-weight:600; margin-bottom:6px; display:block;">
                    Preview Gambar Baru
                </label>
                <img id="preview-img" src="#" alt="Preview"
                     style="width:100px; height:100px; object-fit:cover;
                            border-radius:8px; border:1px solid #e2e8f0;">
            </div>

        <?php else: ?>
            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="name"
                       value="<?= htmlspecialchars($data['name']) ?>" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email"
                       value="<?= htmlspecialchars($data['email']) ?>" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="passw"
                       value="<?= $data['passw'] ?>" required>
            </div>
        <?php endif; ?>

        <div style="display:flex; gap:10px; margin-top:8px;">
            <button type="submit" class="btn btn-primary">&#10003; Update</button>
            <a href="<?= $redirect ?>" class="btn btn-secondary">&#8592; Batal</a>
        </div>
    </form>
</div>

<script>
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