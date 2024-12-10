<?php
session_start();
require 'db.php';

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Kullanıcıyı veritabanından çek
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        if ($user['is_active'] == 1) {
            // Başarılı giriş
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_nickname'] = $user['nickname'];
            header("Location: todo_list.php"); // Yönlendirme
            exit();
        } else {
            $error = "Hesabınız aktif edilmemiş. Lütfen e-posta adresinizi kontrol edin.";
        }
    } else {
        $error = "E-posta veya şifre yanlış.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1>Giriş Yap</h1>
        <?php if (isset($error)) : ?>
            <p class="error"><?= $error; ?></p>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <input type="email" name="email" placeholder="E-Posta" required>
            <input type="password" name="password" placeholder="Şifre" required>
            <button type="submit" name="submit" class="btn-primary">Giriş Yap</button>
        </form>
        <p>Hesabınız yok mu? <a href="register.php">Kayıt Olun</a></p>
        <p><a href="forgot_password.php">Şifremi Unuttum?</a></p>
    </div>
</body>

</html>