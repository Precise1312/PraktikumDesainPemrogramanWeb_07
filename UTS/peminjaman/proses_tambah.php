<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}

$anggotaId = (int) ($_POST['anggota_id'] ?? 0);
$bukuId    = (int) ($_POST['buku_id'] ?? 0);

if ($anggotaId < 1 || $bukuId < 1) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Pilih anggota dan buku terlebih dahulu.'];
    header('Location: tambah.php');
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("SELECT nama FROM anggota WHERE id = :id");
    $stmt->execute(['id' => $anggotaId]);
    $anggota = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT judul, stok FROM buku WHERE id = :id FOR UPDATE");
    $stmt->execute(['id' => $bukuId]);
    $buku = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$anggota || !$buku) {
        throw new DomainException('Anggota atau buku tidak ditemukan.');
    }
    if ((int) $buku['stok'] < 1) {
        throw new DomainException('Stok buku "' . $buku['judul'] . '" sudah habis.');
    }


    $pdo->prepare("UPDATE buku SET stok = stok - 1 WHERE id = :id")
        ->execute(['id' => $bukuId]);

    $pdo->prepare(
        "INSERT INTO peminjaman
            (anggota_id, buku_id, petugas_id, nama_anggota_peminjam, nama_buku_dipinjam, tanggal_jatuh_tempo)
         VALUES
            (:anggota_id, :buku_id, :petugas_id, :nama_anggota_peminjam, :nama_buku_dipinjam, CURRENT_DATE + 7)"
    )->execute([
        'anggota_id'   => $anggotaId,
        'buku_id'      => $bukuId,
        'petugas_id'   => $_SESSION['user_id'],
        'nama_anggota_peminjam' => $anggota['nama'],
        'nama_buku_dipinjam'    => $buku['judul'],
    ]);

    $pdo->commit();

        $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Peminjaman berhasil dicatat.'];
    header('Location: list.php');
    exit;

} catch (DomainException $e) {
    $pdo->rollBack();
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => $e->getMessage()];
    header('Location: tambah.php');
    exit;

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // SEMENTARA, hanya untuk debugging di komputer sendiri
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => $e->getCode() . ' - ' . $e->getMessage()];
    header('Location: tambah.php');
    exit;
}