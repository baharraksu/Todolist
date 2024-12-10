<?php
session_start();
include_once "db.php";
$successMessage = ""; // Başarı mesajı

if (isset($_GET['code'])) {
    $reset_code = $_GET['code'];

    // Reset kodunu kontrol et
    $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_code = ?");
    $stmt->execute([$reset_code]);
    $user = $stmt->fetch();

    if ($user) {
        if (isset($_POST['submit'])) {
            // Şifreyi al ve hashle
            $new_password = $_POST['new_password'];
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Şifreyi güncelle
            $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_code = NULL WHERE reset_code = ?");
            $stmt->execute([$hashed_password, $reset_code]);

            // Kullanıcıyı login sayfasına yönlendir
            header("Location: login.php");
            exit();
        }
    } else {
        echo "Geçersiz veya süresi dolmuş şifre sıfırlama kodu.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şifreyi Sıfırla</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1>Şifrenizi Sıfırlayın</h1>
        <form method="POST" action="reset_password.php?code=<?= $_GET['code']; ?>">
            <input type="password" name="new_password" placeholder="Yeni Şifre" required>
            <button type="submit" name="submit" class="btn-primary">Şifreyi Güncelle</button>
        </form>
    </div>
</body>

</html>