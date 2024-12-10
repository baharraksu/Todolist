<?php
session_start();

require 'vendor/autoload.php';
include 'db.php';
$successMessage = ""; // Başarı mesajı

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['submit'])) {
    if (isset($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $email = $_POST['email'];

        // Veritabanında e-posta kontrolü
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $reset_code = bin2hex(random_bytes(16));
            $stmt = $pdo->prepare("UPDATE users SET reset_code = ? WHERE email = ?");
            $stmt->execute([$reset_code, $email]);

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
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Şifre Sıfırlama';
                $mail->Body    = "Şifrenizi sıfırlamak için şu bağlantıya tıklayın: 
                                  <a href='http://localhost/TodoList/reset_password.php?code=$reset_code'>Şifreyi Sıfırla</a>";

                $mail->send();
                echo "Şifre sıfırlama bağlantısı e-posta adresinize gönderildi.";
            } catch (Exception $e) {
                echo "E-posta gönderimi sırasında bir hata oluştu: " . $mail->ErrorInfo;
            }
        } else {
            echo "Bu e-posta adresiyle kayıtlı bir kullanıcı bulunamadı.";
        }
    } else {
        echo "Geçerli bir e-posta adresi girin.";
    }
}
?>


<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şifremi Unuttum</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1>Şifremi Unuttum</h1>
        <form method="POST" action="forgot_password.php">
            <input type="email" name="email" placeholder="E-Posta" required>
            <button type="submit" name="submit" class="btn-primary">Şifreyi Sıfırla</button>
        </form>
    </div>
</body>

</html>