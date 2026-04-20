<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$base_path = './';
include $base_path . 'koneksi.php';

$table = $_GET['table'] ?? $_POST['table'] ?? 'user';
$id    = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

if ($id <= 0) {
    header("Location: pages/dashboard.php");
    exit;
}

// Ambil data yang akan diedit - Prepared Statement
if ($table === 'barang') {
    $page_title = 'Edit Barang';
    $redirect   = 'pages/data_barang.php';
    $stmt       = $pdo->prepare("SELECT * FROM barang WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch();
} else {
    $page_title = 'Edit User';
    $redirect   = 'pages/data_users.php';
    $stmt       = $pdo->prepare("SELECT id, name, email FROM user WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch();
}

// Jika data tidak ditemukan, kembali ke dashboard
if (!$data) {
    header("Location: pages/dashboard.php");
    exit;
}

$error      = '';
$upload_dir = __DIR__ . '/foto/';
$upload_url = 'foto/';

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
            $gambar      = $data['gambar']; // default: pakai gambar lama

            // Proses upload gambar baru jika ada
            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
                $allowed_ext  = ['jpg', 'jpeg', 'png', 'webp'];
                $allowed_mime = ['image/jpeg', 'image/png', 'image/webp'];
                $max_size     = 2 * 1024 * 1024;

                $ext   = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
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
                    // Hapus gambar lama sebelum simpan yang baru
                    if (!empty($data['gambar']) && file_exists($upload_dir . $data['gambar'])) {
                        unlink($upload_dir . $data['gambar']);
                    }
                    $gambar = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
                    if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_dir . $gambar)) {
                        $error  = 'Gagal menyimpan gambar.';
                        $gambar = $data['gambar'];
                    }
                }
            }

            // Hapus gambar jika checkbox dicentang
            if (isset($_POST['hapus_gambar']) && empty($error)) {
                if (!empty($data['gambar']) && file_exists($upload_dir . $data['gambar'])) {
                    unlink($upload_dir . $data['gambar']);
                }
                $gambar = '';
            }

            if (empty($error)) {
                $upd = $pdo->prepare(
                    "UPDATE barang SET nama_produk=?, harga=?, stok=?, gambar=? WHERE id=?"
                );
                $upd->execute([$nama_produk, $harga, $stok, $gambar, $id]);
                header("Location: $redirect");
                exit;
            }

        } else {
            $name  = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $passw = $_POST['passw'] ?? '';

            if (empty($name) || empty($email)) {
                $error = 'Nama dan email wajib diisi!';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Format email tidak valid!';
            } else {
                // Cek email duplikat (kecuali milik user ini sendiri)
                $cek = $pdo->prepare("SELECT id FROM user WHERE email = ? AND id != ?");
                $cek->execute([$email, $id]);
                if ($cek->fetch()) {
                    $error = 'Email sudah digunakan oleh user lain!';
                } else {
                    if (!empty($passw)) {
                        // Ganti password hanya jika field diisi
                        if (strlen($passw) < 6) {
                            $error = 'Password baru minimal 6 karakter!';
                        } else {
                            $hashed = password_hash($passw, PASSWORD_DEFAULT);
                            $upd    = $pdo->prepare(
                                "UPDATE user SET name=?, email=?, passw=? WHERE id=?"
                            );
                            $upd->execute([$name, $email, $hashed, $id]);
                        }
                    } else {
                        // Kosong = biarkan password lama
                        $upd = $pdo->prepare("UPDATE user SET name=?, email=? WHERE id=?");
                        $upd->execute([$name, $email, $id]);
                    }

                    if (empty($error)) {
                        header("Location: $redirect");
                        exit;
                    }
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
        <input type="hidden" name="table" value="<?= htmlspecialchars($table, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="id"    value="<?= $id ?>">

        <?php if ($table === 'barang'): ?>
            <div class="form-group">
                <label>Nama Produk</label>
                <input type="text" name="nama_produk"
                       value="<?= htmlspecialchars($data['nama_produk'], ENT_QUOTES, 'UTF-8') ?>"
                       required>
            </div>
            <div class="form-group">
                <label>Harga (Rp)</label>
                <input type="number" name="harga"
                       value="<?= (int) $data['harga'] ?>" min="0" required>
            </div>
            <div class="form-group">
                <label>Stok</label>
                <input type="number" name="stok"
                       value="<?= (int) $data['stok'] ?>" min="0" required>
            </div>

            <!-- Tampilkan gambar saat ini -->
            <div class="form-group">
                <label>Gambar Saat Ini</label>
                <?php if (!empty($data['gambar']) && file_exists($upload_dir . $data['gambar'])): ?>
                    <div style="margin-bottom:10px;">
                        <img src="<?= $upload_url . htmlspecialchars($data['gambar'], ENT_QUOTES, 'UTF-8') ?>"
                             alt="Gambar produk"
                             style="width:100px;height:100px;object-fit:cover;
                                    border-radius:8px;border:1px solid #e2e8f0;">
                    </div>
                    <label style="display:flex;align-items:center;gap:6px;font-size:13px;
                                  color:#ef4444;font-weight:400;cursor:pointer;">
                        <input type="checkbox" name="hapus_gambar" value="1"
                               style="accent-color:#ef4444;">
                        Hapus gambar ini
                    </label>
                <?php else: ?>
                    <div style="width:100px;height:100px;background:#f1f5f9;border-radius:8px;
                                display:flex;align-items:center;justify-content:center;
                                font-size:28px;border:1px solid #e2e8f0;margin-bottom:10px;">
                        &#128247;
                    </div>
                    <small style="color:#94a3b8;font-size:12px;">Belum ada gambar</small>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Ganti Gambar
                    <span style="color:#94a3b8;font-weight:400;">(opsional, maks. 2MB)</span>
                </label>
                <input type="file" name="gambar" accept=".jpg,.jpeg,.png,.webp"
                       style="padding:6px;border:1px solid #e2e8f0;border-radius:7px;
                              width:100%;font-size:13px;">
                <small style="color:#94a3b8;font-size:12px;">Format: JPG, PNG, WEBP</small>
            </div>

            <!-- Preview gambar baru sebelum disimpan -->
            <div id="preview-wrap" style="display:none;margin-bottom:16px;">
                <label style="font-size:13px;font-weight:600;margin-bottom:6px;display:block;">
                    Preview Gambar Baru
                </label>
                <img id="preview-img" src="#" alt="Preview"
                     style="width:100px;height:100px;object-fit:cover;
                            border-radius:8px;border:1px solid #e2e8f0;">
            </div>

        <?php else: ?>
            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="name"
                       value="<?= htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8') ?>"
                       required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email"
                       value="<?= htmlspecialchars($data['email'], ENT_QUOTES, 'UTF-8') ?>"
                       required>
            </div>
            <div class="form-group">
                <label>Password Baru
                    <span style="color:#94a3b8;font-weight:400;">
                        (kosongkan jika tidak ingin mengubah)
                    </span>
                </label>
                <input type="password" name="passw"
                       placeholder="Masukkan password baru (min. 6 karakter)">
            </div>
        <?php endif; ?>

        <div style="display:flex;gap:10px;margin-top:8px;">
            <button type="submit" class="btn btn-primary">&#10003; Update</button>
            <a href="<?= htmlspecialchars($redirect, ENT_QUOTES, 'UTF-8') ?>"
               class="btn btn-secondary">&#8592; Batal</a>
        </div>
    </form>
</div>

<script>
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