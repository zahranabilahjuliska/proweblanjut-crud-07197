<?php
$base_path  = '../';
$page_title = 'Dashboard';

include $base_path . 'koneksi.php';
include $base_path . 'includes/header.php';

// Hitung total data
$total_users  = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$total_barang = $conn->query("SELECT COUNT(*) as total FROM products")->fetch_assoc()['total'];
$total_stok   = $conn->query("SELECT SUM(stok) as total FROM products")->fetch_assoc()['total'] ?? 0;
?>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue">&#128100;</div>
        <div class="stat-info">
            <p>Total Users</p>
            <h4><?= $total_users ?></h4>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">&#128230;</div>
        <div class="stat-info">
            <p>Total Barang</p>
            <h4><?= $total_barang ?></h4>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon yellow">&#128202;</div>
        <div class="stat-info">
            <p>Total Stok</p>
            <h4><?= $total_stok ?></h4>
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
    $users = $conn->query("SELECT * FROM users LIMIT 5");
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
            <?php if ($users->num_rows > 0): ?>
                <?php while ($row = $users->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><span class="badge badge-green">Aktif</span></td>
                </tr>
                <?php endwhile; ?>
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
    $barang = $conn->query("SELECT * FROM products LIMIT 5");
    ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Produk</th>
                <th>Harga</th>
                <th>Stok</th>
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
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4" class="empty-state">Belum ada data barang.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include $base_path . 'includes/footer.php'; ?>