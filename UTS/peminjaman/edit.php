<?php
require __DIR__ . '/../includes/auth.php';
$page_title = "Edit Buku";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: list.php');
    exit;
}

$daftarAnggota = $pdo->query(
    "SELECT id, no_anggota, nama FROM anggota ORDER BY nama"
)->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM peminjaman WHERE id = :id");
$stmt->execute(['id' => $id]);
$peminjaman = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$peminjaman) {
    header('Location: list.php');
    exit;
}

$stmt = $pdo->prepare(
    "SELECT id, judul, stok FROM buku WHERE stok > 0 OR id = :id ORDER BY judul"
);
$stmt->execute(['id' => $peminjaman['buku_id']]);
$daftarBuku = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
        <section>
            <h2>Edit Peminjaman</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo $flash['pesan']; ?></p>
            <?php endif; ?>

            <form id="form-edit" method="post" action="proses_edit.php"
             onsubmit="return confirm('Yakin ingin memperbarui data peminjaman ini?');">
                <input type="hidden" name="id" value="<?php echo $peminjaman['id']; ?>">
                <p>
                    <label for="anggota_id">Peminjam</label><br>
                    <select id="anggota_id" name="anggota_id" required>
                    <option value="">Pilih anggota</option>
                     <?php foreach ($daftarAnggota as $a): ?>
                        <option value="<?= (int) $a['id'] ?>"
                        <?= (int) $a['id'] === (int)$peminjaman['anggota_id']? 'selected' : ''?>>
                        <?= htmlspecialchars($a['nama']) ?></option>
                     <?php endforeach; ?>
                    </select>
                </p>
                <p>
                    <label for="buku_id">Buku Dipinjam</label><br>
                    <select id="buku_id" name="buku_id" required>
                    <option value="">Pilih Buku</option>
                     <?php foreach ($daftarBuku as $a): ?>
                        <option value="<?= (int) $a['id'] ?>"
                        <?= (int) $a['id'] === (int)$peminjaman['buku_id']? 'selected' : ''?>>
                        <?= htmlspecialchars($a['judul']) ?></option>
                     <?php endforeach; ?>
                    </select>
                </p>
                <p>
                    <label for="tanggal_pinjam">tanggal Peminjaman</label><br>
                    <input type="text" value="<?= date('d-m-Y', strtotime($peminjaman['tanggal_pinjam'])) ?>" readonly>
                </p>
                <p>
                    <label for="tanggal_jatuh_tempo">tanggal Jatuh Tempo</label><br>
                    <input type="text" value="<?= date('d-m-Y', strtotime($peminjaman['tanggal_jatuh_tempo'])) ?>" readonly>
                </p>
                
                <p>
                    <div class="form-group">
                        <label for="tanggal_kembali">Tanggal Kembali</label><br>
                        <input
                            type="date"
                            id="tanggal_kembali"
                            name="tanggal_kembali"
                            value="<?= htmlspecialchars($peminjaman['tanggal_kembali'] ?? '') ?>"
                        >
                    </div>
                </p>    
                <p>
                    <button type="submit">Simpan</button>
                </p>
            </form>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>