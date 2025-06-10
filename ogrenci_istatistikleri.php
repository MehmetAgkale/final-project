<?php
session_start();
include "db.php";

if (!isset($_SESSION['ogretmen_id'])) {
    header("Location: login.php");
    exit;
}

$ogretmen_id = $_SESSION['ogretmen_id'];

// Get teacher info
$ogretmen = $db->prepare("SELECT * FROM ogretmenler WHERE id = ?");
$ogretmen->execute([$ogretmen_id]);
$ogretmen_data = $ogretmen->fetch(PDO::FETCH_ASSOC);

if (!$ogretmen_data) {
    die("Öğretmen bulunamadı.");
}

// Get all students with their statistics
$ogrenciler = $db->prepare("
    SELECT 
        k.id,
        k.ad,
        COUNT(r.id) as toplam_gorusme,
        SUM(CASE WHEN r.katildi_mi = 1 THEN 1 ELSE 0 END) as katildi,
        SUM(CASE WHEN r.katildi_mi = 0 THEN 1 ELSE 0 END) as katilmadi,
        SUM(CASE WHEN r.durum = 'Reddedildi' THEN 1 ELSE 0 END) as reddedildi,
        SUM(CASE WHEN r.durum = 'Beklemede' THEN 1 ELSE 0 END) as beklemede
    FROM kullanicilar k
    LEFT JOIN rezervasyonlar r ON k.id = r.ogrenci_id AND r.ogretmen_id = ?
    WHERE k.danisman_id = ?
    GROUP BY k.id, k.ad
    ORDER BY k.ad
");
$ogrenciler->execute([$ogretmen_id, $ogretmen_id]);
$ogrenci_listesi = $ogrenciler->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Öğrenci İstatistikleri - <?= htmlspecialchars($ogretmen_data['ad_soyad']) ?></title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .navbar .logo img {
            height: 45px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .progress {
            height: 20px;
            border-radius: 10px;
        }
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .stats-number {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }
        .stats-label {
            color: #7f8c8d;
            font-size: 14px;
        }
    </style>
</head>
<body>
<header class="navbar navbar-expand-lg p-3">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div class="logo">
            <a href="panel.php"><img src="assets/images/logo/logo.png" alt="ALKÜ Logo"></a>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="fw-semibold text-dark">👨‍🏫 <?= htmlspecialchars($ogretmen_data['ad_soyad']) ?></span>
            <a href="logout.php" class="btn btn-outline-secondary btn-sm">Çıkış Yap</a>
        </div>
    </div>
</header>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>📊 Öğrenci İstatistikleri</h2>
        <a href="panel.php" class="btn btn-secondary">Panele Dön</a>
    </div>

    <?php if (empty($ogrenci_listesi)): ?>
        <div class="alert alert-info">
            Henüz danışmanlık yaptığınız öğrenci bulunmuyor.
        </div>
    <?php else: ?>
        <?php foreach ($ogrenci_listesi as $ogrenci): ?>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        👤 <?= htmlspecialchars($ogrenci['ad']) ?>
                    </h5>
                    
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-number"><?= $ogrenci['toplam_gorusme'] ?></div>
                                <div class="stats-label">Toplam Görüşme</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-number"><?= $ogrenci['katildi'] ?></div>
                                <div class="stats-label">Katıldı</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-number"><?= $ogrenci['katilmadi'] ?></div>
                                <div class="stats-label">Katılmadı</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-number"><?= $ogrenci['reddedildi'] ?></div>
                                <div class="stats-label">Reddedildi</div>
                            </div>
                        </div>
                    </div>

                    <?php if ($ogrenci['toplam_gorusme'] > 0): ?>
                        <div class="mb-3">
                            <label class="form-label">Katılım Oranı</label>
                            <?php 
                            $katilim_orani = $ogrenci['toplam_gorusme'] > 0 
                                ? round(($ogrenci['katildi'] / ($ogrenci['katildi'] + $ogrenci['katilmadi'])) * 100) 
                                : 0;
                            ?>
                            <div class="progress">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: <?= $katilim_orani ?>%" 
                                     aria-valuenow="<?= $katilim_orani ?>" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    <?= $katilim_orani ?>%
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($ogrenci['beklemede'] > 0): ?>
                        <div class="alert alert-warning">
                            ⏳ <?= $ogrenci['beklemede'] ?> bekleyen görüşme talebi var
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<footer class="footer text-center bg-light py-3 mt-5">
    <p>&copy; <?= date('Y') ?> Alkü Randevu Sistemi</p>
</footer>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html> 