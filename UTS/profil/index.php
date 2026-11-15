<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/koneksi.php';
require_once __DIR__ . '/../includes/avatar.php';

$stmt = $pdo->prepare("SELECT nama, username, role, foto_url FROM users WHERE id = :id");
$stmt->execute(['id' => $_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: ../auth/logout.php');
    exit;
}

// Kolom keempat: jumlah peminjaman yang pernah dicatat user ini
$stmt = $pdo->prepare("SELECT COUNT(*) FROM peminjaman WHERE petugas_id = :id");
$stmt->execute(['id' => $_SESSION['user_id']]);
$jumlahTransaksi = (int) $stmt->fetchColumn();

$fotoUrl = $user['foto_url'] ?? '';
$inisial = mb_strtoupper(mb_substr($user['nama'], 0, 1));

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$page_title = "Profil";
include __DIR__ . '/../includes/header.php';
?>
        <section>
            <h2>Profil Saya</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo htmlspecialchars($flash['pesan']); ?></p>
            <?php endif; ?>

            <div class="profil-card">
                <div class="avatar-wrap">
                    <?php if ($fotoUrl !== ''): ?>
                        <img class="avatar" src="<?= htmlspecialchars(foto_src($fotoUrl, $base)) ?>" alt="Foto profil">
                    <?php else: ?>
                        <div class="avatar avatar-inisial"><?= htmlspecialchars($inisial) ?></div>
                    <?php endif; ?>
                </div>

                <p>
                    <label for="nama">Nama</label>
                    <input type="text" id="nama" value="<?= htmlspecialchars($user['nama']) ?>" readonly>
                </p>
                <p>
                    <label for="username">Username</label>
                    <input type="text" id="username" value="<?= htmlspecialchars($user['username']) ?>" readonly>
                </p>
                <p>
                    <label for="role">Peran</label>
                    <input type="text" id="role" value="<?= htmlspecialchars($user['role']) ?>" readonly>
                </p>
                <div class = "aksi-profil">
                    <a href="edit_profil.php" class="btn-edit-profil">
                        Edit Profil
                    </a>
                    <a href="../auth/logout.php" class="btn-logout">
                        Logout
                    </a>
                </div>

            </div>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>