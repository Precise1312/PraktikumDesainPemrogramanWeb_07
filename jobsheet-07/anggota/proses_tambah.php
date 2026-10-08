
<?php
session_start();

$nama = trim($_POST['nama'] ?? '');
$noAnggota = trim($_POST['no_anggota'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');
$noHp = trim($_POST['no_hp'] ?? '');

$errors = [];

// Validasi nama
if ($nama === '') {
    $errors[] = "Nama wajib diisi.";
}

// Validasi nomor anggota
if ($noAnggota === '') {
    $errors[] = "No. Anggota wajib diisi.";
}

// Validasi nomor anggota duplikat
foreach ($_SESSION['anggota'] ?? [] as $anggota) {
    if ($anggota['no_anggota'] === $noAnggota) {
        $errors[] = "No. Anggota sudah terdaftar.";
        break;
    }
}

// Validasi nomor HP jika diisi
if ($noHp !== '' && !preg_match('/^[0-9+\-\s]+$/', $noHp)) {
    $errors[] = "No. HP hanya boleh berisi angka, tanda +, dan tanda hubung.";
}

// Validasi alamat jika diisi
if ($alamat !== '' && strlen($alamat) < 5) {
    $errors[] = "Alamat minimal 5 karakter.";
}

// Jika terdapat kesalahan
if (!empty($errors)) {
    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => implode(' ', $errors)
    ];

    header('Location: tambah.php');
    exit;
}

// Inisialisasi session anggota
if (!isset($_SESSION['anggota'])) {
    $_SESSION['anggota'] = [];
}

// Menambahkan data anggota
$_SESSION['anggota'][] = [
    'nama' => $nama,
    'no_anggota' => $noAnggota,
    'alamat' => $alamat,
    'no_hp' => $noHp,
];

// Flash message berhasil
$_SESSION['flash'] = [
    'type' => 'success',
    'pesan' => 'Anggota berhasil ditambahkan.'
];

header('Location: list.php');
exit;