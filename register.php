<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
require 'vendor/autoload.php'; // Composer autoload
include 'db.php'; // Veritabanı bağlantısı

$successMessage = ""; // Başarı mesajı

if (isset($_POST['submit'])) {
    $email = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Şifreyi hashle
    $nickname = htmlspecialchars($_POST['nickname']);
    $activation_code = bin2hex(random_bytes(16)); // Benzersiz aktivasyon kodu

    try {
        // E-posta kontrolü
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            echo "Bu e-posta zaten kayıtlı.";
        } else {
            // Kullanıcıyı veritabanına ekle
            $stmt = $pdo->prepare("INSERT INTO users (email, password, nickname, activation_code) VALUES (?, ?, ?, ?)");
            $stmt->execute([$email, $password, $nickname, $activation_code]);

            // Aktivasyon linki
            $activation_link = "http://localhost/todolist/activate.php?code=$activation_code";

            // PHPMailer ile e-posta gönderimi
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'betuul.ceeliik@gmail.com'; // Gmail adresiniz
                $mail->Password = 'hyoo bxhf omec pahs'; // Gmail uygulama şifresi
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('betuul.ceeliik@gmail.com', 'TodoList');
                $mail->addAddress($email, $nickname);

                $mail->isHTML(true);
                $mail->Subject = 'Hesap Aktivasyonu';
                $mail->Body    = "
                    <p>Merhaba $nickname,</p>
                    <p>Hesabınızı aktifleştirmek için aşağıdaki bağlantıya tıklayın:</p>
                    <p><a href='$activation_link'>$activation_link</a></p>
                    <p>İyi günler dileriz.</p>
                ";

                $mail->send();

                // Başarı mesajı
                $successMessage = "<p>Hesabınız başarıyla oluşturuldu. Aktivasyon e-postası gönderildi. Lütfen e-posta adresinizi kontrol edin.</p>";
            } catch (Exception $e) {
                $successMessage = "<p>E-posta gönderim hatası: {$mail->ErrorInfo}</p>";
            }
        }
    } catch (PDOException $e) {
        die("Hata: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kaydol</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php if ($successMessage) {
        echo $successMessage;
    } ?>

    <form method="POST" action="register.php">
        <input type="email" name="email" placeholder="E-Posta" required>
        <input type="password" name="password" placeholder="Şifre" required>
        <input type="text" name="nickname" placeholder="Rumuz" required>
        <button type="submit" name="submit" class="btn-primary">Kayıt Ol</button>
    </form>
</body>

</html>