<?php
$page_title = "Beranda";
include __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/koneksi.php';

$totalBuku    = (int) $pdo->query("SELECT COUNT(*) FROM buku")->fetchColumn();
$totalAnggota = (int) $pdo->query("SELECT COUNT(*) FROM anggota")->fetchColumn();
$sedangPinjam = (int) $pdo->query("SELECT COUNT(*) FROM peminjaman WHERE status = 'dipinjam'")->fetchColumn();
?>
    <section class="hero">
        <div class="hero-teks">
            <h2>Selamat Datang <br>di Sistem Perpustakaan Mini</h2>
            <p>Aplikasi sederhana untuk mengelola data buku, anggota, dan peminjaman perpustakaan.</p>
        </div>
        <div class="hero-gambar">
            <img src="<?= $base ?>assets/img/buku.png" alt=""
                onerror="this.parentElement.hidden = true">
    </div>
    </section>

    <section class="ringkasan">
        <h2>Ringkasan</h2>
        <div class="stat-grid">
            <article class="stat-card">
                <span class="stat-ikon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 4h6a4 4 0 0 1 4 4v13a3 3 0 0 0-3-3H2z"/><path d="M22 4h-6a4 4 0 0 0-4 4v13a3 3 0 0 1 3-3h7z"/></svg>
                </span>
                <div>
                    <p class="stat-label">Total Buku</p>
                    <p class="stat-angka"><?= $totalBuku ?></p>
                </div>
            </article>

            <article class="stat-card">
                <span class="stat-ikon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </span>
                <div>
                    <p class="stat-label">Total Anggota</p>
                    <p class="stat-angka"><?= $totalAnggota ?></p>
                </div>
            </article>

            <article class="stat-card">
                <span class="stat-ikon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/><path d="m9 16 2 2 4-4"/></svg>
                </span>
                <div>
                    <p class="stat-label">Sedang Dipinjam</p>
                    <p class="stat-angka"><?= $sedangPinjam ?></p>
                </div>
            </article>
        </div>
    </section>
<?php include __DIR__ . '/includes/footer.php'; ?>