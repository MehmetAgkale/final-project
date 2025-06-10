<?php
session_start();
include "db.php";

if (!isset($_SESSION['ogrenci_id'])) {
    header("Location: login.php");
    exit;
}

$ogrenci_id = $_SESSION['ogrenci_id'];
$gonderildi = false;

// Danışman öğretmeni bul
$stmt = $db->prepare("SELECT ogretmen_id FROM danismanliklar WHERE ogrenci_id = ?");
$stmt->execute([$ogrenci_id]);
$danisman = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$danisman) {
    die("Danışman öğretmeniniz bulunamadı.");
}

$ogretmen_id = $danisman['ogretmen_id'];

// Form gönderildiyse
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mesaj = $_POST['mesaj'];

    $insert = $db->prepare("INSERT INTO mesajlar (gonderen_id, alici_id, mesaj, gonderen_turu, tarih) VALUES (?, ?, ?, 'ogrenci', NOW())");
    $insert->execute([$ogrenci_id, $ogretmen_id, $mesaj]);

    $gonderildi = true;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Mesaj Gönder</title>
</head>
<body>
    <h2>📤 Danışman Hocana Mesaj Gönder</h2>

    <?php if ($gonderildi): ?>
        <p style="color:green;">✅ Mesaj gönderildi.</p>
    <?php endif; ?>

    <form method="POST">
        <textarea name="mesaj" rows="5" cols="50" placeholder="Mesajınızı yazın..." required></textarea><br><br>
        <button type="submit">Gönder</button>
    </form>
</body>
</html>
