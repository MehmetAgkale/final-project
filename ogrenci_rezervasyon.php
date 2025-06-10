<?php
session_start();
include "db.php";

if (!isset($_SESSION['ogrenci_id'])) {
    header("Location: login.php");
    exit;
}

$ogrenci_id = $_SESSION['ogrenci_id'];

// Öğrencinin danışman hocasını bul
$stmt = $db->prepare("SELECT ogretmen_id FROM danismanliklar WHERE ogrenci_id = ?");
$stmt->execute([$ogrenci_id]);
$danisman = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$danisman) {
    die("Danışman hocanız tanımlı değil.");
}

$ogretmen_id = $danisman['ogretmen_id'];

// Günlük saat aralıklarını çek
$saatler = $db->prepare("SELECT * FROM gorusme_saatleri WHERE ogretmen_id = ?");
$saatler->execute([$ogretmen_id]);
$saat_araliklari = $saatler->fetchAll(PDO::FETCH_ASSOC);

// Randevu alındı mı kontrol
$basari = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $gun = $_POST['gun'];
    $saat = $_POST['saat'];

    // Çift rezervasyon kontrolü (aynı öğrenci aynı gün/saat almamalı)
    $kontrol = $db->prepare("SELECT * FROM rezervasyonlar WHERE ogrenci_id = ? AND gun = ? AND saat = ?");
    $kontrol->execute([$ogrenci_id, $gun, $saat]);

    if ($kontrol->rowCount() == 0) {
        $ekle = $db->prepare("INSERT INTO rezervasyonlar (ogrenci_id, ogretmen_id, gun, saat, durum) VALUES (?, ?, ?, ?, 'bekliyor')");
        $ekle->execute([$ogrenci_id, $ogretmen_id, $gun, $saat]);
        $basari = true;
    } else {
        $mesaj = "❌ Bu saate zaten rezervasyonunuz var.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Görüşme Randevusu Al</title>
</head>
<body>
    <h2>📆 Görüşme Randevusu Al</h2>

    <?php if ($basari): ?>
        <p style="color:green;">✅ Randevunuz başarıyla oluşturuldu.</p>
    <?php elseif (isset($mesaj)): ?>
        <p style="color:red;"><?php echo $mesaj; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Gün Seçin:</label>
        <select name="gun" required>
            <?php foreach ($saat_araliklari as $satir): ?>
                <option value="<?= $satir['gun'] ?>"><?= $satir['gun'] ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Saat Seçin:</label>
        <select name="saat" required>
            <?php
            // Her aralığı tek tek 30 dakikalık dilimlere böl
            foreach ($saat_araliklari as $aralik) {
                $start = strtotime($aralik['baslangic_saat']);
                $end = strtotime($aralik['bitis_saat']);

                while ($start < $end) {
                    $slot = date('H:i', $start);
                    echo "<option value='$slot'>$slot</option>";
                    $start = strtotime("+30 minutes", $start);
                }
            }
            ?>
        </select><br><br>

        <input type="submit" value="Randevu Al">
    </form>
</body>
</html>
