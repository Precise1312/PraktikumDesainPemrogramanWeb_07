<?php
require __DIR__ . '/../includes/auth.php';
$page_title = "Daftar peminjaman";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$perPage = 7;
$page = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;
$keyword = trim($_GET['q'] ?? '');

if ($keyword !== '') {
    $hitung = $pdo->prepare(
        "SELECT COUNT(*) FROM peminjaman
         WHERE nama_buku_dipinjam ILIKE :kw OR nama_anggota_peminjam ILIKE :kw"
    );

    $hitung->execute([
        'kw' => '%' . $keyword . '%'
    ]);

    $totalRows = $hitung->fetchColumn();

    $stmt = $pdo->prepare(
        "SELECT * FROM peminjaman
         WHERE nama_buku_dipinjam ILIKE :kw OR nama_anggota_peminjam ILIKE :kw
         ORDER BY id DESC
         LIMIT :limit OFFSET :offset"
    );

    $stmt->bindValue('kw', '%' . $keyword . '%');
} else {
    $totalRows = $pdo->query("SELECT COUNT(*) FROM peminjaman")->fetchColumn();
    $stmt = $pdo->prepare("SELECT * FROM peminjaman ORDER BY id DESC LIMIT :limit OFFSET :offset");
}
$stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue('offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$daftarPeminjaman = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalPages = max(1, (int) ceil($totalRows / $perPage));
?>
                <section>
            <h2>Daftar Peminjaman</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo $flash['pesan']; ?></p>
            <?php endif; ?>

            <form method="get" action="list.php" class="search-box">
                <label for="search-input">Cari Judul Buku atau Peminjam</label>
                <div class="search-row">
                    <input type="text" id="search-input" name="q"
                           value="<?php echo htmlspecialchars($keyword); ?>"
                           placeholder="Ketik judul buku atau peminjam...">
                    <button type="submit"><?php echo ikon('cari'); ?> Cari</button>
                </div>
            </form>

            <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Peminjam</th>
                        <th>Buku Dipinjam</th>
                        <th>Tgl Pinjam</th>
                        <th>Jatuh Tempo</th>
                        <th>Tgl Kembali</th>
                        <th>Status</th>
                        <th>Denda</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($daftarPeminjaman)): ?>
                    <tr>
                        <td colspan="8">Belum ada data. Silakan tambah lewat menu "Tambah Peminjaman".</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($daftarPeminjaman as $peminjaman): ?>
                        <tr>
                            <td><?php echo $peminjaman['nama_anggota_peminjam']; ?></td>
                            <td><?php echo $peminjaman['nama_buku_dipinjam']; ?></td>
                            <td class="tgl"><?php echo date('d-m-Y', strtotime($peminjaman['tanggal_pinjam'])); ?></td>
                            <td class="tgl"><?php echo date('d-m-Y', strtotime($peminjaman['tanggal_jatuh_tempo'])); ?></td>
                            <td class="tgl">
                                <?php echo !empty($peminjaman['tanggal_kembali'])
                                    ? date('d-m-Y', strtotime($peminjaman['tanggal_kembali']))
                                    : '-'; ?>
                            </td>
                            <td><?php echo $peminjaman['status']; ?></td>
                            <td><?php echo $peminjaman['denda']; ?></td>
                            <td class="kolom-aksi">
                                <div class="aksi-wrap">
                                    <a href="edit.php?id=<?php echo $peminjaman['id']; ?>" class="btn-edit"><?php echo ikon('edit'); ?> Edit</a>
                                    <form class="form-hapus" method="post" action="hapus.php">
                                        <input type="hidden" name="id" value="<?php echo $peminjaman['id']; ?>">
                                        <button type="submit" class="btn-hapus"><?php echo ikon('hapus'); ?> Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
            <nav class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="list.php?page=<?php echo $i; ?><?php echo $keyword !== '' ? '&q=' . urlencode($keyword) : ''; ?>"
                   class="<?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            </nav>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>