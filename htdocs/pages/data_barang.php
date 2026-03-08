<?php
$base_path  = '../';
$page_title = 'Data Barang';

include $base_path . 'koneksi.php';
include $base_path . 'includes/header.php';

$barang = $conn->query("SELECT * FROM products");
?>

<div class="card">
    <div class="card-header">
        <h3>&#128230; Daftar Barang</h3>
        <a href="../create.php?table=products" class="btn btn-primary">&#43; Tambah Barang</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Produk</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($barang->num_rows > 0): ?>
                <?php while ($row = $barang->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['nama_produk']) ?></td>
                    <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                    <td>
                        <span class="badge <?= $row['stok'] > 10 ? 'badge-green' : 'badge-yellow' ?>">
                            <?= $row['stok'] ?> pcs
                        </span>
                    </td>
                    <td style="display:flex; gap:8px;">
                        <a href="../edit.php?table=products&id=<?= $row['id'] ?>" class="btn btn-warning">&#9998; Edit</a>
                        <a href="../delete.php?table=products&id=<?= $row['id'] ?>"
                           class="btn btn-danger"
                           onclick="return confirm('Yakin ingin menghapus barang ini?')">&#128465; Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <p>Belum ada data barang. <a href="../create.php?table=products">Tambah sekarang</a></p>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include $base_path . 'includes/footer.php'; ?>