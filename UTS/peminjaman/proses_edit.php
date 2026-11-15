<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}

$dendaPerHari   = 5000;
$id             = $_POST['id'] ?? '';
$anggotaId      = (int) ($_POST['anggota_id'] ?? 0);
$bukuId         = (int) ($_POST['buku_id'] ?? 0);
$tanggalKembali = trim($_POST['tanggal_kembali'] ?? '');

if (!ctype_digit((string) $id)) {
    header('Location: list.php');
    exit;
}

$errors = [];
if ($anggotaId < 1 || $bukuId < 1) {
    $errors[] = 'Pilih anggota dan buku terlebih dahulu.';
}
if ($tanggalKembali !== '') {
    $tgl = DateTime::createFromFormat('Y-m-d', $tanggalKembali);
    if (!$tgl || $tgl->format('Y-m-d') !== $tanggalKembali) {
        $errors[] = 'Format tanggal kembali tidak valid.';
    }
}

if ($errors) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header('Location: edit_profil.php');   // sebelumnya index.php
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("SELECT * FROM peminjaman WHERE id = :id FOR UPDATE");
    $stmt->execute(['id' => $id]);
    $lama = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$lama) {
        throw new DomainException('Data peminjaman tidak ditemukan.');
    }

    $stmt = $pdo->prepare("SELECT nama FROM anggota WHERE id = :id");
    $stmt->execute(['id' => $anggotaId]);
    $anggota = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT judul, stok FROM buku WHERE id = :id FOR UPDATE");
    $stmt->execute(['id' => $bukuId]);
    $buku = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$anggota || !$buku) {
        throw new DomainException('Anggota atau buku tidak ditemukan.');
    }

    $sudahKembali = $lama['tanggal_kembali'] !== null;
    $bukuBerubah  = $bukuId !== (int) $lama['buku_id'];

    if ($sudahKembali && $tanggalKembali === '') {
        throw new DomainException('Tanggal kembali yang sudah diisi tidak bisa dikosongkan.');
    }
    if ($tanggalKembali !== '' && $tanggalKembali < $lama['tanggal_pinjam']) {
        throw new DomainException('Tanggal kembali tidak boleh sebelum tanggal peminjaman.');
    }

        if ($bukuBerubah) {
        if ($sudahKembali) {
            throw new DomainException('Buku tidak bisa diganti karena peminjaman sudah dikembalikan.');
        }
        if ((int) $buku['stok'] < 1) {
            throw new DomainException('Stok buku "' . $buku['judul'] . '" sudah habis.');
        }
        // buku lama kembali ke rak, buku baru diambil
        $pdo->prepare("UPDATE buku SET stok = stok + 1 WHERE id = :id")
            ->execute(['id' => $lama['buku_id']]);
        $pdo->prepare("UPDATE buku SET stok = stok - 1 WHERE id = :id")
            ->execute(['id' => $bukuId]);
    }

    if (!$sudahKembali && $tanggalKembali !== '') {
        // baru dikembalikan sekarang: stok bertambah satu kali saja
        $pdo->prepare("UPDATE buku SET stok = stok + 1 WHERE id = :id")
            ->execute(['id' => $bukuId]);
    }
    if ($tanggalKembali !== '') {
        $tempo     = new DateTime($lama['tanggal_jatuh_tempo']);
        $kembali   = new DateTime($tanggalKembali);
        $hariTelat = $kembali > $tempo ? $tempo->diff($kembali)->days : 0;
        $denda     = $hariTelat * $dendaPerHari;
        $status    = 'dikembalikan';
        $tglDb     = $tanggalKembali;
    } else {
        $hariTelat = 0;
        $denda     = 0;
        $status    = 'dipinjam';
        $tglDb     = null;
    }

    $pdo->prepare(
        "UPDATE peminjaman
         SET anggota_id = :anggota_id, buku_id = :buku_id,
             nama_anggota_peminjam = :nama_anggota_peminjam, nama_buku_dipinjam = :nama_buku_dipinjam,
             tanggal_kembali = :tanggal_kembali, status = :status, denda = :denda
         WHERE id = :id"
    )->execute([
        'anggota_id'      => $anggotaId,
        'buku_id'         => $bukuId,
        'nama_anggota_peminjam'    => $anggota['nama'],
        'nama_buku_dipinjam'       => $buku['judul'],
        'tanggal_kembali' => $tglDb,
        'status'          => $status,
        'denda'           => $denda,
        'id'              => $id,
    ]);

    $pdo->commit();

    $pesan = 'Peminjaman berhasil diperbarui.';
    if ($hariTelat > 0) {
        $pesan .= ' Anggota dengan nama ' . $anggota['nama'] . ' Terlambat ' . $hariTelat . ' hari, denda Rp ' . number_format($denda, 0, ',', '.') . '.';
    }
    $_SESSION['flash'] = ['type' => 'success', 'pesan' => $pesan];
    header('Location: list.php');
    exit;

} catch (DomainException $e) {
    $pdo->rollBack();
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => $e->getMessage()];
    header('Location: edit.php?id=' . urlencode($id));
    exit;

} catch (PDOException $e) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Terjadi kesalahan saat menyimpan profil.'];
    header('Location: edit_profil.php');   // tambahkan ini
    exit;
}

header('Location: index.php');   // berhasil: ke halaman profil
exit;