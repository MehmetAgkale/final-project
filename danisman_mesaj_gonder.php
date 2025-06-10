<?php
session_start();
include "db.php";

if (!isset($_SESSION['ogretmen_id'])) {
    header("Location: login.php");
    exit;
}

$ogretmen_id = $_SESSION['ogretmen_id'];
$gonderildi = false;

// Form gönderildiyse
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mesaj = $_POST['mesaj'];

    // Danışman öğrencileri al
    $stmt = $db->prepare("SELECT ogrenci_id FROM danismanliklar WHERE ogretmen_id = ?");
    $stmt->execute([$ogretmen_id]);
    $ogrenciler = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Her öğrenciye mesajı ekle
    $mesajEkle = $db->prepare("INSERT INTO mesajlar (gonderen_id, alici_id, mesaj, gonderen_turu, tarih) VALUES (?, ?, ?, 'ogretmen', NOW())");

    foreach ($ogrenciler as $ogrenci) {
        $mesajEkle->execute([$ogretmen_id, $ogrenci['ogrenci_id'], $mesaj]);
    }

    $gonderildi = true;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Danışman Öğrencilere Mesaj</title>
</head>
<body>
    <h2>✉️ Danışman Öğrencilere Toplu Mesaj Gönder</h2>

    <?php if ($gonderildi): ?>
        <p style="color:green;">✅ Mesaj tüm danışman öğrencilerine gönderildi.</p>
    <?php endif; ?>

    <form method="POST">
        <textarea name="mesaj" rows="5" cols="50" placeholder="Mesajınızı yazın..." required></textarea><br><br>
        <button type="submit">Mesajı Gönder</button>
    </form>
</body>
</html>
