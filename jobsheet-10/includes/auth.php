<?php
// Guard clause: di-include di baris paling atas setiap halaman yang
// membutuhkan login (sebelum header.php mengeluarkan output apa pun),
// agar header('Location: ...') masih bisa dipanggil.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) && !empty($_COOKIE['remember'])) {
    require_once __DIR__ . '/koneksi.php';   // menyediakan $pdo

    [$selector, $validator] = array_pad(explode(':', $_COOKIE['remember'], 2), 2, '');

    $stmt = $pdo->prepare(
        "SELECT u.id, u.nama, u.role, t.token_hash
         FROM remember_tokens t
         JOIN users u ON u.id = t.user_id
         WHERE t.selector = :selector AND t.expires_at > NOW()"
    );
    $stmt->execute(['selector' => $selector]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && hash_equals($row['token_hash'], hash('sha256', $validator))) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['nama']    = $row['nama'];
        $_SESSION['role']    = $row['role'];
    } else {
        // Cookie palsu atau sudah kedaluwarsa: hapus
        setcookie('remember', '', time() - 3600, '/');
    }
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

if (!function_exists('require_role')) {
    function require_role($roles): void
    {
        if (!in_array($_SESSION['role'] ?? '', (array) $roles, true)) {
            http_response_code(403);
            $page_title = 'Akses Ditolak';
            include __DIR__ . '/header.php';
            echo '<section><h2>Akses Ditolak</h2>'
               . '<p>Anda tidak memiliki izin untuk membuka halaman ini.</p>'
               . '<p><a href="../index.php">Kembali ke Beranda</a></p></section>';
            include __DIR__ . '/footer.php';
            exit;
        }
    }
}