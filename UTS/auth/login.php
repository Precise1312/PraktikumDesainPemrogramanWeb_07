<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$page_title = "Login";
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
        <section class="login-card">
            <div class="login-form">
                <h2>Login Petugas</h2>
                <p class="login-sub">Masuk untuk mengelola perpustakaan.</p>

                <?php if ($flash): ?>
                    <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo htmlspecialchars($flash['pesan']); ?></p>
                <?php endif; ?>

                <form method="post" action="proses_login.php">
                    <p>
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username"
                            placeholder="Masukkan username" autocomplete="username" required>
                    </p>
                    <p>
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password"
                            placeholder="Masukkan password" autocomplete="current-password" required>
                    </p>
                    <p>
                        <button type="submit">Masuk</button>
                    </p>
                    <div class="ingat-saya">
                        <input type="checkbox" id="ingat" name="ingat" value="1">
                        <label for="ingat">Ingat saya (30 hari)</label>
                    </div>
                </form>

                <p class="login-daftar">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
            </div>

            <div class="login-gambar">
                <img src="<?= $base ?>assets/img/cat.png" alt=""
                    onerror="this.parentElement.hidden = true">
            </div>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>