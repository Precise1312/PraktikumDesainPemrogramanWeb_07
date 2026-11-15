<?php
require __DIR__ . '/../includes/auth.php';
$page_title = "Daftar Buku";
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
        "SELECT COUNT(*) FROM buku
         WHERE judul ILIKE :kw OR pengarang ILIKE :kw"
    );

    $hitung->execute([
        'kw' => '%' . $keyword . '%'
    ]);

    $totalRows = $hitung->fetchColumn();

    $stmt = $pdo->prepare(
        "SELECT * FROM buku
         WHERE judul ILIKE :kw OR pengarang ILIKE :kw
         ORDER BY id DESC
         LIMIT :limit OFFSET :offset"
    );

    $stmt->bindValue('kw', '%' . $keyword . '%');
} else {
    $totalRows = $pdo->query("SELECT COUNT(*) FROM buku")->fetchColumn();
    $stmt = $pdo->prepare("SELECT * FROM buku ORDER BY id DESC LIMIT :limit OFFSET :offset");
}
$stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue('offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$daftarBuku = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalPages = max(1, (int) ceil($totalRows / $perPage));
?>
        <section>
            <h2>Daftar Buku</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo $flash['pesan']; ?></p>
            <?php endif; ?>

            <form method="get" action="list.php" class="search-box">
                <label for="search-input">Cari Judul Buku atau Pengarang</label>
                <div class="search-row">
                    <input type="text" id="search-input" name="q"
                        value="<?php echo htmlspecialchars($keyword); ?>"
                        placeholder="Ketik judul buku atau pengarang...">
                    <button type="submit"><?php echo ikon('cari'); ?> Cari</button>
                </div>
            </form>

            <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Pengarang</th>
                        <th>Tahun</th>
                        <th>Kategori</th>
                        <th>ISBN</th>
                        <th>Tersedia</th>
                        <th>Ditambahkan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($daftarBuku)): ?>
                    <tr>
                        <td colspan="7">Belum ada data buku. Silakan tambah lewat menu "Tambah Buku".</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($daftarBuku as $buku): ?>
                        <tr>
                            <td><?php echo $buku['judul']; ?></td>
                            <td><?php echo $buku['pengarang']; ?></td>
                            <td><?php echo $buku['tahun']; ?></td>
                            <td><?php echo $buku['kategori']; ?></td>
                            <td><?php echo $buku['isbn']; ?></td>
                            <td><?php echo $buku['stok']; ?></td>
                            <td><?php echo date('d-m-Y H:i', strtotime($buku['tanggal_ditambahkan'])); ?></td>
                            <td class="kolom-aksi">
                                <div class="aksi-wrap">
                                    <a href="edit.php?id=<?php echo $buku['id']; ?>" class="btn-edit"><?php echo ikon('edit'); ?> Edit</a>
                                    <form class="form-hapus" method="post" action="hapus.php">
                                        <input type="hidden" name="id" value="<?php echo $buku['id']; ?>">
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