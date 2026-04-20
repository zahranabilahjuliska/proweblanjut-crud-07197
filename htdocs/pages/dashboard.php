<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$base_path  = '../';
$page_title = 'Dashboard';

include $base_path . 'koneksi.php';
include $base_path . 'includes/header.php';

// Hitung total data menggunakan PDO
$total_users  = $pdo->query("SELECT COUNT(*) FROM user")->fetchColumn();
$total_barang = $pdo->query("SELECT COUNT(*) FROM barang")->fetchColumn();
$total_stok   = $pdo->query("SELECT COALESCE(SUM(stok), 0) FROM barang")->fetchColumn();
?>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue">&#128100;</div>
        <div class="stat-info">
            <p>Total Users</p>
            <h4><?= (int) $total_users ?></h4>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">&#128230;</div>
        <div class="stat-info">
            <p>Total Barang</p>
            <h4><?= (int) $total_barang ?></h4>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon yellow">&#128202;</div>
        <div class="stat-info">
            <p>Total Stok</p>
            <h4><?= (int) $total_stok ?></h4>
        </div>
    </div>
</div>

<!-- Tabel Users Terbaru -->
<div class="card">
    <div class="card-header">
        <h3>&#128100; Data Users Terbaru</h3>
        <a href="data_users.php" class="btn btn-secondary">Lihat Semua</a>
    </div>
    <?php
    $users = $pdo->query("SELECT id, name, email FROM user ORDER BY id DESC LIMIT 5");
    $rows  = $users->fetchAll();
    ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($rows) > 0): ?>
                <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name'],  ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><span class="badge badge-green">Aktif</span></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4" class="empty-state">Belum ada data users.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Tabel Barang Terbaru -->
<div class="card">
    <div class="card-header">
        <h3>&#128230; Data Barang Terbaru</h3>
        <a href="data_barang.php" class="btn btn-secondary">Lihat Semua</a>
    </div>
    <?php
    $barang = $pdo->query("SELECT * FROM barang ORDER BY id DESC LIMIT 5");
    $items  = $barang->fetchAll();
    ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Foto</th>
                <th>Nama Produk</th>
                <th>Harga</th>
                <th>Stok</th>
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
                                 style="width:50px;height:50px;object-fit:cover;
                                        border-radius:8px;border:1px solid #e2e8f0;">
                        <?php else: ?>
                            <div style="width:50px;height:50px;background:#f1f5f9;border-radius:8px;
                                        display:flex;align-items:center;justify-content:center;
                                        font-size:18px;border:1px solid #e2e8f0;">&#128247;</div>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['nama_produk'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                    <td>
                        <span class="badge <?= $row['stok'] > 10 ? 'badge-green' : 'badge-yellow' ?>">
                            <?= (int) $row['stok'] ?> pcs
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" class="empty-state">Belum ada data barang.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include $base_path . 'includes/footer.php'; ?>