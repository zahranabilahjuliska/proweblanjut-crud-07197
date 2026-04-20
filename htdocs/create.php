<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$base_path = './';
include $base_path . 'koneksi.php';

$table = $_GET['table'] ?? $_POST['table'] ?? 'user';

if ($table === 'barang') {
    $page_title = 'Tambah Barang';
    $redirect   = 'pages/data_barang.php';
} else {
    $page_title = 'Tambah User';
    $redirect   = 'pages/data_users.php';
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validasi CSRF token
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Permintaan tidak valid. Silakan coba lagi.';
    } else {
        unset($_SESSION['csrf_token']);

        if ($table === 'barang') {
            $nama_produk = trim($_POST['nama_produk'] ?? '');
            $harga       = (int) ($_POST['harga'] ?? 0);
            $stok        = (int) ($_POST['stok'] ?? 0);
            $gambar      = '';

            // Proses upload gambar jika ada
            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
                $allowed_ext  = ['jpg', 'jpeg', 'png', 'webp'];
                $allowed_mime = ['image/jpeg', 'image/png', 'image/webp'];
                $max_size     = 2 * 1024 * 1024; // 2MB

                $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));

                // Validasi MIME type dari konten file (bukan hanya ekstensi)
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime  = finfo_file($finfo, $_FILES['gambar']['tmp_name']);
                finfo_close($finfo);

                if (!in_array($ext, $allowed_ext)) {
                    $error = 'Format gambar tidak didukung! Gunakan JPG, PNG, atau WEBP.';
                } elseif (!in_array($mime, $allowed_mime)) {
                    $error = 'Tipe file tidak sesuai! Pastikan file benar-benar gambar.';
                } elseif ($_FILES['gambar']['size'] > $max_size) {
                    $error = 'Ukuran gambar maksimal 2MB!';
                } else {
                    // Nama file acak agar tidak bisa ditebak
                    $gambar    = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
                    $upload_to = __DIR__ . '/foto/' . $gambar;
                    if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_to)) {
                        $error  = 'Gagal menyimpan gambar.';
                        $gambar = '';
                    }
                }
            }

            if (empty($error)) {
                // Prepared statement - cegah SQL Injection
                $stmt = $pdo->prepare(
                    "INSERT INTO barang (nama_produk, harga, stok, gambar) VALUES (?, ?, ?, ?)"
                );
                $stmt->execute([$nama_produk, $harga, $stok, $gambar]);
                header("Location: $redirect");
                exit;
            }

        } else {
            $name  = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $passw = $_POST['passw'] ?? '';

            if (empty($name) || empty($email) || empty($passw)) {
                $error = 'Semua field wajib diisi!';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Format email tidak valid!';
            } elseif (strlen($passw) < 6) {
                $error = 'Password minimal 6 karakter!';
            } else {
                // Cek duplikat email
                $cek = $pdo->prepare("SELECT id FROM user WHERE email = ?");
                $cek->execute([$email]);
                if ($cek->fetch()) {
                    $error = 'Email sudah digunakan!';
                } else {
                    // Hash password sebelum disimpan
                    $hashed = password_hash($passw, PASSWORD_DEFAULT);
                    $stmt   = $pdo->prepare(
                        "INSERT INTO user (name, email, passw) VALUES (?, ?, ?)"
                    );
                    $stmt->execute([$name, $email, $hashed]);
                    header("Location: $redirect");
                    exit;
                }
            }
        }
    }
}

include $base_path . 'includes/header.php';
?>

<div class="card form-max">
    <div class="card-header">
        <h3><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></h3>
    </div>

    <?php if ($error): ?>
        <div style="padding:10px 14px;border-radius:7px;font-size:13px;margin-bottom:16px;
                    background:#fff5f5;color:#ef4444;border:1px solid #fecaca;">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <?= csrf_input() ?>
        <input type="hidden" name="table"
               value="<?= htmlspecialchars($table, ENT_QUOTES, 'UTF-8') ?>">

        <?php if ($table === 'barang'): ?>
            <div class="form-group">
                <label>Nama Produk</label>
                <input type="text" name="nama_produk"
                       value="<?= htmlspecialchars($_POST['nama_produk'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="Masukkan nama produk" required>
            </div>
            <div class="form-group">
                <label>Harga (Rp)</label>
                <input type="number" name="harga"
                       value="<?= htmlspecialchars($_POST['harga'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="Contoh: 50000" min="0" required>
            </div>
            <div class="form-group">
                <label>Stok</label>
                <input type="number" name="stok"
                       value="<?= htmlspecialchars($_POST['stok'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="Jumlah stok" min="0" required>
            </div>
            <div class="form-group">
                <label>Gambar Produk
                    <span style="color:#94a3b8;font-weight:400;">(opsional, maks. 2MB)</span>
                </label>
                <input type="file" name="gambar" accept=".jpg,.jpeg,.png,.webp"
                       style="padding:6px;border:1px solid #e2e8f0;border-radius:7px;
                              width:100%;font-size:13px;">
                <small style="color:#94a3b8;font-size:12px;">Format: JPG, PNG, WEBP</small>
            </div>
            <!-- Preview gambar sebelum upload -->
            <div id="preview-wrap" style="display:none;margin-bottom:16px;">
                <img id="preview-img" src="#" alt="Preview"
                     style="width:100px;height:100px;object-fit:cover;
                            border-radius:8px;border:1px solid #e2e8f0;">
            </div>

        <?php else: ?>
            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="name"
                       value="<?= htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="Masukkan nama" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email"
                       value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="Masukkan email" required>
            </div>
            <div class="form-group">
                <label>Password
                    <span style="color:#94a3b8;font-weight:400;">(min. 6 karakter)</span>
                </label>
                <input type="password" name="passw"
                       placeholder="Masukkan password" required>
            </div>
        <?php endif; ?>

        <div style="display:flex;gap:10px;margin-top:8px;">
            <button type="submit" class="btn btn-primary">&#10003; Simpan</button>
            <a href="<?= htmlspecialchars($redirect, ENT_QUOTES, 'UTF-8') ?>"
               class="btn btn-secondary">&#8592; Batal</a>
        </div>
    </form>
</div>

<script>
// Preview gambar sebelum diupload
const inputGambar = document.querySelector('input[name="gambar"]');
if (inputGambar) {
    inputGambar.addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('preview-img').src = e.target.result;
                document.getElementById('preview-wrap').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });
}
</script>

<?php include $base_path . 'includes/footer.php'; ?>