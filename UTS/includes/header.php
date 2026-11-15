<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Prefix relatif ke root proyek ini (bukan root domain) — supaya
// /assets, /index.php, dst tetap benar walau proyek diakses lewat
// subfolder (mis. dp2026.test/kode-praktikum/jobsheet-07/), bukan cuma
// lewat vhost yang document root-nya langsung folder ini.
$__jobsheetRoot = dirname(__DIR__);
$__scriptDir = dirname($_SERVER['SCRIPT_FILENAME']);
$__rel = ltrim(str_replace('\\', '/', substr($__scriptDir, strlen($__jobsheetRoot))), '/');
$base = $__rel === '' ? '' : str_repeat('../', substr_count($__rel, '/') + 1);

require_once __DIR__ . '/ikon.php';
require_once __DIR__ . '/avatar.php';
$fotoHeader    = foto_src($_SESSION['foto'] ?? '', $base);
$inisialHeader = mb_strtoupper(mb_substr($_SESSION['nama'] ?? '?', 0, 1));

// Halaman yang sedang dibuka, mis. "buku/list.php"
$__hal = ($__rel === '' ? '' : $__rel . '/') . basename($_SERVER['SCRIPT_NAME']);

$menu = [
    'Beranda'            => 'index.php',
    'Daftar Buku'        => 'buku/list.php',
    'Tambah Buku'        => 'buku/tambah.php',
    'Daftar Anggota'     => 'anggota/list.php',
    'Tambah Anggota'     => 'anggota/tambah.php',
    'Daftar Peminjaman'  => 'peminjaman/list.php',
    'Tambah Peminjaman'  => 'peminjaman/tambah.php',
    'Profil'             => 'profil/index.php',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIMPUS-Mini<?php echo isset($page_title) ? ' | ' . $page_title : ''; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Serif+4:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo $base; ?>assets/css/style.css"> 
    <link rel="stylesheet" href="<?php echo $base; ?>assets/css/style.css">
</head>
<body>
   <header>
    <div class="header-left">
        <img src="<?php echo $base; ?>assets/picture/pusheen.png" alt="Logo">
        <h1><a href="<?php echo $base; ?>index.php">Simpus Mini</a></h1>
    </div>
    <button type="button" id="nav-toggle-btn" class="nav-toggle-label" aria-label="Menu">&#9776;</button>
    <nav>
        <ul>
            <?php foreach ($menu as $label => $file):
                $aktif = $__hal === $file
                    || ($file === 'profil/index.php' && strpos($__hal, 'profil/') === 0);
                $adalahProfil = $file === 'profil/index.php';
            ?>
            <li>
                <?php if ($adalahProfil): ?>
                    <a href="<?php echo $base . $file; ?>"
                    class="nav-profil<?php echo $aktif ? ' aktif' : ''; ?>"
                    aria-label="<?php echo htmlspecialchars($label); ?>"
                    title="<?php echo htmlspecialchars($label); ?>">
                        <?php if ($fotoHeader !== ''): ?>
                            <img class="nav-avatar" src="<?= htmlspecialchars($fotoHeader) ?>" alt="">
                        <?php else: ?>
                            <span class="nav-avatar nav-avatar-inisial"><?= htmlspecialchars($inisialHeader) ?></span>
                        <?php endif; ?>
                    </a>
                <?php else: ?>
                    <a href="<?php echo $base . $file; ?>"<?php echo $aktif ? ' class="aktif"' : ''; ?>><?php echo $label; ?></a>
                <?php endif; ?>
            </li>
            <?php endforeach; ?>
        </ul>
    </nav>
</header>

    <main>