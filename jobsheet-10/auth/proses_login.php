<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require __DIR__ . '/../includes/koneksi.php';

$maksGagal   = 5;     // percobaan gagal berturut-turut sebelum dikunci
$durasiKunci = 300;   // lama kunci dalam detik (5 menit)

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$ingat    = !empty($_POST['ingat']);
$kunci    = strtolower($username);   // kunci penghitung percobaan per username

function kembali_ke_login(string $pesan): void
{
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => $pesan];
    header('Location: login.php');
    exit;
}

// ---- Latihan 3: tolak jika username sedang dikunci -------------------
$info = $_SESSION['gagal_login'][$kunci] ?? ['jumlah' => 0, 'terakhir' => 0];
if ($info['jumlah'] >= $maksGagal) {
    $sisa = $durasiKunci - (time() - $info['terakhir']);
    if ($sisa > 0) {
        kembali_ke_login('Terlalu banyak percobaan gagal. Coba lagi dalam ' . ceil($sisa / 60) . ' menit.');
    }
    $info = ['jumlah' => 0, 'terakhir' => 0];   // masa kunci sudah habis
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
$stmt->execute(['username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    unset($_SESSION['gagal_login'][$kunci]);   // berhasil: reset penghitung
    session_regenerate_id(true);               // cegah session fixation

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nama']    = $user['nama'];
    $_SESSION['role']    = $user['role'];

    // ---- Latihan 2: "Ingat Saya" ---------------------------------------
    if ($ingat) {
        $selector  = bin2hex(random_bytes(12));   // 24 karakter, penanda baris
        $validator = bin2hex(random_bytes(32));   // rahasia, TIDAK disimpan polos di DB

        $pdo->prepare(
            "INSERT INTO remember_tokens (user_id, selector, token_hash, expires_at)
             VALUES (:user_id, :selector, :token_hash, NOW() + INTERVAL '30 days')"
        )->execute([
            'user_id'    => $user['id'],
            'selector'   => $selector,
            'token_hash' => hash('sha256', $validator),
        ]);

        setcookie('remember', $selector . ':' . $validator, [
            'expires'  => time() + 60 * 60 * 24 * 30,
            'path'     => '/',
            'httponly' => true,                       // tidak terbaca JavaScript
            'secure'   => !empty($_SERVER['HTTPS']),  // hanya lewat HTTPS jika tersedia
            'samesite' => 'Lax',
        ]);
    }

    header('Location: ../index.php');
    exit;
}

// ---- Latihan 3: catat percobaan gagal --------------------------------
// Dihitung untuk username apa pun (ada/tidak di database) supaya penyerang
// tidak bisa membedakan username yang valid.
$info['jumlah']++;
$info['terakhir'] = time();
$_SESSION['gagal_login'][$kunci] = $info;

if ($info['jumlah'] >= $maksGagal) {
    $pesan = 'Terlalu banyak percobaan gagal. Coba lagi dalam ' . ($durasiKunci / 60) . ' menit.';
} elseif ($info['jumlah'] >= 3) {
    $pesan = 'Username atau password salah. Peringatan: sisa '
           . ($maksGagal - $info['jumlah']) . ' percobaan sebelum dikunci sementara.';
} else {
    $pesan = 'Username atau password salah.';
}
kembali_ke_login($pesan);