<?php
require 'db.php';

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // Aktivasyon kodunu kontrol et
    $stmt = $pdo->prepare("SELECT * FROM users WHERE activation_code = ?");
    $stmt->execute([$code]);
    $user = $stmt->fetch();

    if ($user) {
        // Kullanıcıyı aktif et
        $stmt = $pdo->prepare("UPDATE users SET is_active = 1 WHERE activation_code = ?");
        $stmt->execute([$code]);

        $message = "Hesabınız başarıyla aktif edilmiştir!";
    } else {
        $message = "Geçersiz aktivasyon kodu.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hesap Aktivasyonu</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Aktivasyon Sonucu</h1>
        <p class="message"><?= isset($message) ? $message : ''; ?></p>
        <a href="login.php" class="btn-primary">Giriş Yap</a>
    </div>
</body>
</html>
