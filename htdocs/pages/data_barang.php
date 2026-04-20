<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$base_path  = '../';
$page_title = 'Data Barang';

include $base_path . 'koneksi.php';
include $base_path . 'includes/header.php';

// Ambil semua barang - PDO
$barang = $pdo->query("SELECT * FROM barang ORDER BY id ASC");
$items  = $barang->fetchAll();
?>

<div class="card">
    <div class="card-header">
        <h3>&#128230; Daftar Barang</h3>
        <a href="../create.php?table=barang" class="btn btn-primary">&#43; Tambah Barang</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Gambar</th>
                <th>Nama Produk</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($items) > 0): ?>
                <?php foreach ($items as $row): ?>
                <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <td>
                        <?php if (!empty($row['gambar']) && file_exists('../foto/' . $row['gambar'])): ?>
                            <img src="../foto/<?= htmlspecialchars($row['gambar'], ENT_QUOTES, 'UTF-8') ?>"
                                 alt="<?= htmlspecialchars($row['nama_produk'], ENT_QUOTES, 'UTF-8') ?>"
                                 style="width:60px;height:60px;object-fit:cover;
                                        border-radius:8px;border:1px solid #e2e8f0;">
                        <?php else: ?>
                            <div style="width:60px;height:60px;background:#f1f5f9;border-radius:8px;
                                        display:flex;align-items:center;justify-content:center;
                                        font-size:22px;border:1px solid #e2e8f0;">&#128247;</div>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['nama_produk'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                    <td>
                        <span class="badge <?= $row['stok'] > 10 ? 'badge-green' : 'badge-yellow' ?>">
                            <?= (int) $row['stok'] ?> pcs
                        </span>
                    </td>
                    <td style="display:flex;gap:8px;">
                        <a href="../edit.php?table=barang&id=<?= (int) $row['id'] ?>"
                           class="btn btn-warning">&#9998; Edit</a>

                        <!-- Hapus pakai POST + CSRF -->
                        <form method="POST" action="../delete.php"
                              onsubmit="return confirm('Yakin ingin menghapus barang ini?')"
                              style="display:inline;">
                            <?= csrf_input() ?>
                            <input type="hidden" name="table" value="barang">
                            <input type="hidden" name="id"    value="<?= (int) $row['id'] ?>">
                            <button type="submit" class="btn btn-danger">&#128465; Hapus</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <p>Belum ada data barang. <a href="../create.php?table=barang">Tambah sekarang</a></p>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include $base_path . 'includes/footer.php'; ?>