<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/koneksi.php';
require_once __DIR__ . '/../includes/avatar.php';

// Ambil data user yang sedang login (id dari session, bukan dari URL)
$stmt = $pdo->prepare("SELECT nama, username, role, foto_url FROM users WHERE id = :id");
$stmt->execute(['id' => $_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: ../auth/logout.php');
    exit;
}

$fotoUrl = $user['foto_url'] ?? '';
$inisial = mb_strtoupper(mb_substr($user['nama'], 0, 1));

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$page_title = "Edit Profil";
include __DIR__ . '/../includes/header.php';
?>
        <section>
            <h2>Edit Profil</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo htmlspecialchars($flash['pesan']); ?></p>
            <?php endif; ?>

            <form id="form-profil" class="profil-edit" method="post" action="proses_edit.php">
                <div class="avatar-wrap">
                    <?php if ($fotoUrl !== ''): ?>
                        <img class="avatar" src="<?= htmlspecialchars(foto_src($fotoUrl, $base)) ?>" alt="Foto profil">
                    <?php else: ?>
                        <div class="avatar avatar-inisial"><?= htmlspecialchars($inisial) ?></div>
                    <?php endif; ?>
                    <a class="btn-foto" href="edit_foto.php">Edit foto profil</a>
                </div>
                <p>
                    <label for="nama">Nama</label><br>
                    <input type="text" id="nama" name="nama" maxlength="255" required
                           value="<?= htmlspecialchars($user['nama']) ?>">
                </p>
                <p>
                    <label for="username">Username</label><br>
                    <input type="text" id="username" value="<?= htmlspecialchars($user['username']) ?>" readonly>
                </p>
                <p>
                    <label for="role">Peran</label><br>
                    <input type="text" id="role" value="<?= htmlspecialchars($user['role']) ?>" readonly>
                </p>
                <p>
                    <button type="submit">Simpan Profil</button>
                    <a href="index.php" class="btn-batal">Batal</a>
                </p>
            </form>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>