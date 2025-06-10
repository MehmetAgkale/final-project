<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

$success = $error = "";

// Öğretmenleri çek
$ogretmenler = $db->query("SELECT id, unvan, ad_soyad FROM ogretmenler")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $teacher_id = $_POST['teacher'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $title = $_POST['title'];
    $message = $_POST['message'];

    $check = $db->prepare("SELECT COUNT(*) FROM rezervasyonlar WHERE ogretmen_id = ? AND tarih = ? AND saat = ? AND durum != 'Reddedildi'");
    $check->execute([$teacher_id, $date, $time]);

    if ($check->fetchColumn() > 0) {
        $error = "Seçilen tarih ve saat zaten dolu.";
    } else {
        $insert = $db->prepare("INSERT INTO rezervasyonlar (ogrenci_id, ogretmen_id, tarih, saat, baslik, mesaj, durum, created_at) VALUES (?, ?, ?, ?, ?, ?, 'Beklemede', NOW())");
        $insert->execute([$user_id, $teacher_id, $date, $time, $title, $message]);
        $success = "Rezervasyon talebiniz oluşturuldu.";
    }
}

$rezervasyonlar = $db->prepare("SELECT r.*, o.unvan, o.ad_soyad FROM rezervasyonlar r JOIN ogretmenler o ON r.ogretmen_id = o.id WHERE r.ogrenci_id = ? ORDER BY r.created_at DESC");
$rezervasyonlar->execute([$user_id]);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Görüşme Ayarla</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
</head>
<body>
<header class="d-flex justify-content-between align-items-center p-3 bg-white shadow-sm">
    <div class="logo">
        <a href="index.php"><img src="assets/images/logo/logo.png" alt="ALKÜ Logo" style="height: 40px;"></a>
    </div>
    <div class="d-flex align-items-center gap-3">
        <nav class="nav">
            <a href="index.php" class="mx-2">Anasayfa</a>
            <a href="ogrenci_rezervasyon.php" class="mx-2 text-primary">Rezervasyon</a>
            <a href="ogrenci_mesajlar.php" class="mx-2">Mesajlarım</a>
            <a href="ogrenci_profilim.php" class="mx-2">Profilim</a>
            <a href="logout.php" class="mx-2">Çıkış</a>
        </nav>
        <a href="index.php" class="btn btn-primary">Anasayfa</a>
    </div>
</header>

<div class="container py-5">
    <div class="mx-auto" style="max-width: 700px;">
        <h2 class="mb-4">📅 Rezervasyon Oluştur</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"> <?= $error ?> </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"> <?= $success ?> </div>
        <?php endif; ?>

        <form method="POST" class="shadow-sm p-4 bg-white rounded">
            <div class="mb-3">
                <label class="form-label">Öğretmen Seç</label>
                <select name="teacher" class="form-select" required>
                    <option value="">-- Seçiniz --</option>
                    <?php foreach ($ogretmenler as $ogretmen): ?>
                        <option value="<?= $ogretmen['id'] ?>">
                            <?= $ogretmen['unvan'] . ' ' . $ogretmen['ad_soyad'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tarih</label>
                    <input type="date" name="date" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Saat</label>
                    <select name="time" class="form-select" required>
                        <?php
                        for ($h = 9; $h <= 17; $h++) {
                            foreach (["00", "30"] as $m) {
                                $time = sprintf("%02d:%s", $h, $m);
                                echo "<option value='$time'>$time</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Görüşme Başlığı</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mesajınız</label>
                <textarea name="message" class="form-control" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100">Rezervasyon Oluştur</button>
        </form>

        <hr class="my-5">

        <h4 class="mb-4">📋 Önceki Rezervasyonlarım</h4>
        <?php if ($rezervasyonlar->rowCount() == 0): ?>
            <div class="alert alert-info">Henüz rezervasyon oluşturmadınız.</div>
        <?php else: ?>
            <?php foreach ($rezervasyonlar as $rez): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5><?= htmlspecialchars($rez['baslik']) ?></h5>
                        <p><strong>👨‍🏫 Öğretmen:</strong> <?= htmlspecialchars($rez['unvan'] . ' ' . $rez['ad_soyad']) ?></p>
                        <p><strong>📅 Tarih:</strong> <?= $rez['tarih'] ?> | <strong>⏰ Saat:</strong> <?= $rez['saat'] ?></p>
                        <p><strong>✉️ Mesaj:</strong> <?= nl2br(htmlspecialchars($rez['mesaj'])) ?></p>
                        <p><strong>📌 Durum:</strong> 
                            <?php
                                $durum = $rez['durum'];
                                if ($durum == 'Beklemede') {
                                    echo "<span class='badge bg-warning text-dark'>Beklemede</span>";
                                } elseif ($durum == 'Onaylandı') {
                                    echo "<span class='badge bg-success'>Onaylandı</span>";
                                } elseif ($durum == 'Reddedildi') {
                                    echo "<span class='badge bg-danger'>Reddedildi</span>";
                                } else {
                                    echo "<span class='badge bg-secondary'>$durum</span>";
                                }
                            ?>
                        </p>
                        <?php if (!empty($rez['ogretmen_notu'])): ?>
                            <div class="alert alert-info mt-2">
                                <strong>👨‍🏫 Öğretmen Notu:</strong><br>
                                <?= nl2br(htmlspecialchars($rez['ogretmen_notu'])) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<footer class="footer text-center bg-light py-3 mt-5">
    <p>&copy; <?= date('Y') ?> Alkü Randevu Sistemi. Tüm hakları saklıdır.</p>
</footer>
</body>
</html>
