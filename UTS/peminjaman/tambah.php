<?php
require __DIR__ . '/../includes/auth.php';
$page_title = "Tambah Buku";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$daftarAnggota = $pdo->query(
    "SELECT id, no_anggota, nama FROM anggota ORDER BY nama"
)->fetchAll(PDO::FETCH_ASSOC);

$daftarBuku = $pdo->query(
    "SELECT id, judul, stok FROM buku WHERE stok > 0 ORDER BY judul"
)->fetchAll(PDO::FETCH_ASSOC);
?>

        <section>
            <h2>Tambah Peminjaman</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo $flash['pesan']; ?></p>
            <?php endif; ?>

            <form id="form-tambah" method="post" action="proses_tambah.php">
                <p>
                    <label for="anggota_id">Nama Peminjam</label><br>
                    <select id="anggota_id" name="anggota_id" required>
                    <option value="">Pilih anggota</option>
                     <?php foreach ($daftarAnggota as $a): ?>
                        <option value="<?= (int) $a['id'] ?>"><?= htmlspecialchars($a['nama']) ?></option>
                     <?php endforeach; ?>
                    </select>
                </p>
                <p>
                    <label for="buku_id">Buku Dipinjam</label><br>
                    <select id="buku_id" name="buku_id" required>
                    <option value="">Pilih Buku</option>
                     <?php foreach ($daftarBuku as $a): ?>
                        <option value="<?= (int) $a['id'] ?>"><?= htmlspecialchars($a['judul']) ?></option>
                     <?php endforeach; ?>
                    </select>
                </p>
                <p>
                    <label for="tanggal_pinjam">tanggal Peminjaman</label><br>
                    <input type="text" value="<?= date('d-m-Y') ?>", readonly>
                </p>
                <p>
                    <label for="tanggal_jatuh_tempo">tanggal Jatuh Tempo</label><br>
                    <input type="text" value="<?= date('d-m-Y', strtotime('+ 7 days')) ?>", readonly>
                </p>
                <p>
                    <label for="status">status</label><br>
                    <input type="text" value="dipinjam", readonly>
                </p>
                <p>
                    <button type="submit">Simpan</button>
                </p>
            </form>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>