
<?php
session_start();

// Mengosongkan seluruh data session
$_SESSION = [];

// Menghapus session dari server
session_destroy();

// Kembali ke halaman beranda
header('Location: index.php');
exit;