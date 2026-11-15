<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/koneksi.php';
require_once __DIR__ . '/../includes/avatar.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$foto = trim($_POST['foto'] ?? '');

// Path datang dari form, jadi tidak boleh langsung dipercaya.
// Hanya path yang ada di daftar server yang boleh disimpan.
if (!in_array($foto, DAFTAR_AVATAR, true)) {
    $_SESSION['foto'] = $foto;   // supaya header langsung ikut berubah
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Pilih salah satu foto yang tersedia.'];
    header('Location: edit_foto.php');
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE users SET foto_url = :foto WHERE id = :id");
    $stmt->execute([
        'foto' => $foto,
        'id'   => $_SESSION['user_id'],
    ]);

    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Foto profil berhasil diperbarui.'];
    header('Location: index.php');
    exit;
} catch (PDOException $e) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Terjadi kesalahan saat menyimpan foto.'];
    header('Location: edit_foto.php');
    exit;
}