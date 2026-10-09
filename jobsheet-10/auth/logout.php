<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Latihan 2: cabut token "Ingat Saya" dari database dan hapus cookie-nya,
// kalau tidak, pengguna akan langsung login lagi otomatis.
if (!empty($_COOKIE['remember'])) {
    $selector = explode(':', $_COOKIE['remember'], 2)[0];
    setcookie('remember', '', time() - 3600, '/');

    require __DIR__ . '/../includes/koneksi.php';
    $pdo->prepare("DELETE FROM remember_tokens WHERE selector = :selector")
        ->execute(['selector' => $selector]);
}

$_SESSION = [];
session_destroy();
header('Location: login.php');
exit;