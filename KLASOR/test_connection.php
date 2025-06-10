<?php
$servername = "localhost";
$username = "u2262482_admin"; // MySQL kullanıcı adın
$password = "160818Aa.@."; // MySQL şifren
$dbname = "u2262482_alku"; // cPanel’de oluşturulan veritabanı adı

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Bağlantı başarılı!";
} catch (PDOException $e) {
    die("Bağlantı hatası: " . $e->getMessage());
}
?>