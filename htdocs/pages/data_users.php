<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$base_path  = '../';
$page_title = 'Data Users';

include $base_path . 'koneksi.php';
include $base_path . 'includes/header.php';

// Ambil semua user - Prepared Statement via PDO
$users = $pdo->query("SELECT id, name, email FROM user ORDER BY id ASC");
$rows  = $users->fetchAll();
?>

<div class="card">
    <div class="card-header">
        <h3>&#128100; Daftar Users</h3>
        <a href="../create.php?table=user" class="btn btn-primary">&#43; Tambah User</a>
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
            <?php if (count($rows) > 0): ?>
                <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name'],  ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') ?></td>
                    <!-- Password tidak ditampilkan demi keamanan -->
                    <td><span style="color:#94a3b8;font-size:13px;">••••••••</span></td>
                    <td style="display:flex;gap:8px;">
                        <a href="../edit.php?table=user&id=<?= (int) $row['id'] ?>"
                           class="btn btn-warning">&#9998; Edit</a>

                        <!-- Hapus pakai POST + CSRF, bukan link GET -->
                        <form method="POST" action="../delete.php"
                              onsubmit="return confirm('Yakin ingin menghapus user ini?')"
                              style="display:inline;">
                            <?= csrf_input() ?>
                            <input type="hidden" name="table" value="user">
                            <input type="hidden" name="id"    value="<?= (int) $row['id'] ?>">
                            <button type="submit" class="btn btn-danger">&#128465; Hapus</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <p>Belum ada data users. <a href="../create.php?table=user">Tambah sekarang</a></p>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include $base_path . 'includes/footer.php'; ?>