<?php
require __DIR__ . '/../includes/auth.php';
require_role('admin');
session_start();
require __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}
$id = $_POST['id'] ?? null;
if ($id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM anggota WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Anggota berhasil dihapus.'];
    } catch (PDOException $e) {
        if ($e->getCode() === '23503') {   // foreign_key_violation
            $_SESSION['flash'] = [
                'type'  => 'error',
                'pesan' => 'Anggota tidak bisa dihapus karena sudah punya riwayat peminjaman.'
            ];
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Anggota gagal dihapus.'];
        }
    }
}

header('Location: list.php');
exit;