<?php
session_start();
session_destroy();

// Hapus cookie remember me jika ada
setcookie('remember_email', '', time() - 3600, '/');
setcookie('remember_passw', '', time() - 3600, '/');

header("Location: login.php");
exit;
?>