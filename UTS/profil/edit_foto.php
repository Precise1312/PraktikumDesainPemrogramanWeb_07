<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/koneksi.php';
require_once __DIR__ . '/../includes/avatar.php';

$stmt = $pdo->prepare("SELECT nama, foto_url FROM users WHERE id = :id");
$stmt->execute(['id' => $_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: ../auth/logout.php');
    exit;
}

$fotoSekarang = $user['foto_url'] ?? '';
$inisial      = mb_strtoupper(mb_substr($user['nama'], 0, 1));

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$page_title = "Edit Foto Profil";
include __DIR__ . '/../includes/header.php';

$srcSekarang = foto_src($fotoSekarang, $base);
// Hanya path yang ada di daftar yang dianggap "terpilih"
$nilaiAwal = in_array($fotoSekarang, DAFTAR_AVATAR, true) ? $fotoSekarang : '';
?>
        <section>
            <h2>Edit Foto Profil</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo htmlspecialchars($flash['pesan']); ?></p>
            <?php endif; ?>

            <form id="form-foto" class="foto-editor" method="post" action="proses_foto.php">
                <div class="foto-besar">
                    <img id="foto-besar" class="avatar avatar-besar" alt="Foto profil sekarang"
                         <?= $srcSekarang !== '' ? 'src="' . htmlspecialchars($srcSekarang) . '"' : 'hidden' ?>>
                    <div id="foto-besar-inisial" class="avatar avatar-besar avatar-inisial"
                         <?= $srcSekarang !== '' ? 'hidden' : '' ?>><?= htmlspecialchars($inisial) ?></div>
                </div>

                <div class="foto-pilihan">
                    <?php foreach (DAFTAR_AVATAR as $i => $path): ?>
                        <button type="button"
                                class="foto-opsi <?= $path === $nilaiAwal ? 'dipilih' : '' ?>"
                                data-foto="<?= htmlspecialchars($path) ?>"
                                data-src="<?= htmlspecialchars($base . $path) ?>"
                                aria-label="Pilih foto <?= $i + 1 ?>"
                                aria-pressed="<?= $path === $nilaiAwal ? 'true' : 'false' ?>">
                            <img src="<?= htmlspecialchars($base . $path) ?>" alt="Foto <?= $i + 1 ?>">
                        </button>
                    <?php endforeach; ?>
                </div>

                <input type="hidden" id="foto-terpilih" name="foto" value="<?= htmlspecialchars($nilaiAwal) ?>">

                <p class="foto-aksi">
                    <button type="submit">Simpan perubahan</button>
                    <a href="edit_profil.php" class="btn-batal">Batal</a>
                </p>
            </form>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>