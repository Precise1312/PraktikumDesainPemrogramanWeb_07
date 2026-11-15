<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/koneksi.php';

$json = file_get_contents(__DIR__ . '/../data/buku.json');
$data = json_decode($json, true);

if (!is_array($data)) {
    die("Gagal membaca buku.json: " . json_last_error_msg());
}

$stmt = $pdo->prepare(
    "INSERT INTO buku (judul, pengarang, tahun, isbn, stok, kategori)
     VALUES (:judul, :pengarang, :tahun, :isbn, :stok, :kategori)"
);

$pdo->beginTransaction();
try {
    foreach ($data as $b) {
        $stmt->execute([
            'judul'     => $b['judul'] ?? '',
            'pengarang' => $b['pengarang'] ?? '',
            'tahun'     => (int) ($b['tahun'] ?? 0),
            'isbn'      => $b['isbn'] ?? null,
            'stok'      => (int) ($b['stok'] ?? 0),
            'kategori'  => $b['kategori'] ?? null,
        ]);
    }
    $pdo->commit();
    echo "Berhasil memigrasi " . count($data) . " buku.";
} catch (PDOException $e) {
    $pdo->rollBack();
    echo "Migrasi dibatalkan: " . $e->getMessage();
}