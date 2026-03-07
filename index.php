<?php 
include 'config.php'; 
$result = $conn->query("SELECT * FROM users"); 
?> 
<!DOCTYPE html> 
<html> 
<head> 
    <title>CRUD PHP MySQL</title> 
</head> 
<body> 
    <h2>Daftar Pengguna</h2> 
    <a href="create.php">Tambah Pengguna</a> 
    <table border="1"> 
        <tr> 
            <th>ID</th><th>Nama</th><th>Email</th> 
            <th>Password</th><th>Aksi</th> 
        </tr> 
        <?php while ($row = $result->fetch_assoc()): ?> 
        <tr> 
            <td><?= $row['id'] ?></td> 
            <td><?= $row['name'] ?></td> 
            <td><?= $row['email'] ?></td> 
            <td><?= $row['passw'] ?></td> 
            <td> 
                <a href="edit.php?id=<?= $row['id'] ?>">Edit</a> | 
                <a href="delete.php?id=<?= $row['id'] ?>" onclick="return 
confirm('Hapus data?')">Hapus</a> 
            </td> 
        </tr> 
        <?php endwhile; ?> 
    </table> 
</body> 
</html> 