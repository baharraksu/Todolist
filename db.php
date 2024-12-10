<?php
$host = 'localhost';
$port = '3306'; // Port numarası 3307 olarak belirtildi
$dbname = 'todo'; // Veritabanı adı
$username = 'root'; // Kullanıcı adı
$password = '1qaz'; // Şifre

try {
    // Port numarasını doğru şekilde ekliyoruz
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Hata yönetimi
} catch (PDOException $e) {
    die("Bağlantı hatası: " . $e->getMessage());
}
