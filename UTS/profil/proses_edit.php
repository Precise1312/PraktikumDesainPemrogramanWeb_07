<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$nama = trim($_POST['nama'] ?? '');

if ($nama === '') {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Nama wajib diisi.'];
    header('Location: edit_profil.php');
    exit;
}
if (mb_strlen($nama) > 255) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Nama maksimal 255 karakter.'];
    header('Location: edit_profil.php');
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE users SET nama = :nama WHERE id = :id");
    $stmt->execute([
        'nama' => $nama,
        'id'   => $_SESSION['user_id'],   // dari session, bukan dari form
    ]);

    $_SESSION['nama'] = $nama;            // supaya nama di header ikut berubah
    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Profil berhasil diperbarui.'];
    header('Location: index.php');
    exit;
} catch (PDOException $e) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Terjadi kesalahan saat menyimpan profil.'];
    header('Location: edit_profil.php');
    exit;
}