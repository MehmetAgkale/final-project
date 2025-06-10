<?php
session_start();
include "db.php";

if (!isset($_SESSION['ogretmen_id'])) {
    header("Location: login.php");
    exit;
}

// Form gönderildiyse işlem yap
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $gun = $_POST['gun'];
    $baslangic = $_POST['baslangic_saat'];
    $bitis = $_POST['bitis_saat'];
    $ogretmen_id = $_SESSION['ogretmen_id'];

    // Aynı gün için önceden kayıt varsa güncelle, yoksa ekle
    $kontrol = $db->prepare("SELECT id FROM gorusme_saatleri WHERE ogretmen_id = ? AND gun = ?");
    $kontrol->execute([$ogretmen_id, $gun]);

    if ($kontrol->rowCount() > 0) {
        $update = $db->prepare("UPDATE gorusme_saatleri SET baslangic_saat = ?, bitis_saat = ? WHERE ogretmen_id = ? AND gun = ?");
        $update->execute([$baslangic, $bitis, $ogretmen_id, $gun]);
        $mesaj = "✅ Kayıt güncellendi.";
    } else {
        $insert = $db->prepare("INSERT INTO gorusme_saatleri (ogretmen_id, gun, baslangic_saat, bitis_saat) VALUES (?, ?, ?, ?)");
        $insert->execute([$ogretmen_id, $gun, $baslangic, $bitis]);
        $mesaj = "✅ Kayıt başarıyla eklendi.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Görüşme Saatleri Ayarla</title>
</head>
<body>
    <h2>🕒 Görüşme Saatleri Ayarla</h2>

    <?php if (isset($mesaj)) echo "<p>$mesaj</p>"; ?>

    <form method="POST" action="">
        <label>Gün Seçin:</label>
        <select name="gun" required>
            <option value="Pazartesi">Pazartesi</option>
            <option value="Salı">Salı</option>
            <option value="Çarşamba">Çarşamba</option>
            <option value="Perşembe">Perşembe</option>
            <option value="Cuma">Cuma</option>
        </select><br><br>

        <label>Başlangıç Saati:</label>
        <input type="time" name="baslangic_saat" required><br><br>

        <label>Bitiş Saati:</label>
        <input type="time" name="bitis_saat" required><br><br>

        <input type="submit" value="Kaydet">
    </form>
</body>
</html>
