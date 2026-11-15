<?php
// Daftar foto profil yang tersedia. SESUAIKAN dengan nama file yang kamu upload.
// Path relatif terhadap root proyek (tanpa ../ di depan).
const DAFTAR_AVATAR = [
    'assets/img/avatar/cat.jpeg',
    'assets/img/avatar/woman.png',
    'assets/img/avatar/man.jpeg',
];

// Ubah nilai dari database menjadi alamat yang bisa dipakai di <img src>.
function foto_src(?string $foto, string $base): string
{
    $foto = trim((string) $foto);
    if ($foto === '') {
        return '';
    }
    return $base . $foto;        // path assets: tambahkan prefix ../ sesuai folder
}