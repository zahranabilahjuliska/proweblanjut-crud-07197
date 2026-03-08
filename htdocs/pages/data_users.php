<?php
$base_path  = '../';
$page_title = 'Data Users';

include $base_path . 'koneksi.php';
include $base_path . 'includes/header.php';

$users = $conn->query("SELECT * FROM users");
?>

<div class="card">
    <div class="card-header">
        <h3>&#128100; Daftar Users</h3>
        <a href="../create.php?table=users" class="btn btn-primary">&#43; Tambah User</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Password</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($users->num_rows > 0): ?>
                <?php while ($row = $users->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= $row['passw'] ?></td>
                    <td style="display:flex; gap:8px;">
                        <a href="../edit.php?table=users&id=<?= $row['id'] ?>" class="btn btn-warning">&#9998; Edit</a>
                        <a href="../delete.php?table=users&id=<?= $row['id'] ?>"
                           class="btn btn-danger"
                           onclick="return confirm('Yakin ingin menghapus user ini?')">&#128465; Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <p>Belum ada data users. <a href="../create.php?table=users">Tambah sekarang</a></p>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include $base_path . 'includes/footer.php'; ?>