
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Debug Session</title>
</head>
<body>
    <h1>Debug Session</h1>

    <h2>Isi Session</h2>
    <pre><?php print_r($_SESSION); ?></pre>

    <p>
        <a href="index.php">Kembali ke Beranda</a>
    </p>
</body>
</html>