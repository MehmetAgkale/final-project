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

// Handle reservation status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rez_id']) && isset($_POST['yeni_durum'])) {
    $rez_id = $_POST['rez_id'];
    $yeni_durum = $_POST['yeni_durum'];
    $not = $_POST['not'] ?? '';

    $update = $db->prepare("UPDATE rezervasyonlar SET durum = ?, ogretmen_notu = ? WHERE id = ? AND ogretmen_id = ?");
    $update->execute([$yeni_durum, $not, $rez_id, $ogretmen_id]);
}

// Get all reservations for this teacher
$rezervasyonlar = $db->prepare("
    SELECT r.*, k.ad as ogrenci_ad 
    FROM rezervasyonlar r 
    JOIN kullanicilar k ON r.ogrenci_id = k.id 
    WHERE r.ogretmen_id = ? 
    ORDER BY r.created_at DESC
");
$rezervasyonlar->execute([$ogretmen_id]);
$rezervasyonlar_listesi = $rezervasyonlar->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Rezervasyon Talepleri - <?= htmlspecialchars($ogretmen_data['ad_soyad']) ?></title>
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
            border-left: 4px solid #3498db;
            transition: box-shadow 0.3s;
        }
        .card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .btn {
            transition: all 0.3s ease;
        }
        .status-badge {
            font-size: 0.9em;
            padding: 0.5em 1em;
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
        <h2>📋 Rezervasyon Talepleri</h2>
        <a href="panel.php" class="btn btn-secondary">Panele Dön</a>
    </div>

    <?php if (empty($rezervasyonlar_listesi)): ?>
        <div class="alert alert-info">
            Henüz rezervasyon talebi bulunmuyor.
        </div>
    <?php else: ?>
        <?php foreach ($rezervasyonlar_listesi as $rez): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="mb-2">📌 <?= htmlspecialchars($rez['baslik']) ?></h5>
                            <p class="mb-1"><strong>👤 Öğrenci:</strong> <?= htmlspecialchars($rez['ogrenci_ad']) ?></p>
                            <p class="mb-1">
                                <strong>📅 Tarih:</strong> <?= date('d.m.Y', strtotime($rez['tarih'])) ?> | 
                                <strong>⏰ Saat:</strong> <?= date('H:i', strtotime($rez['saat'])) ?>
                            </p>
                            <p class="mb-2">
                                <strong>✉️ Mesaj:</strong><br>
                                <?= nl2br(htmlspecialchars($rez['mesaj'])) ?>
                            </p>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-<?= $rez['durum'] === 'Beklemede' ? 'warning text-dark' : ($rez['durum'] === 'Onaylandı' ? 'success' : 'danger') ?> status-badge">
                                <?= $rez['durum'] ?>
                            </span>
                        </div>
                    </div>

                    <?php if ($rez['durum'] === 'Beklemede'): ?>
                        <form method="POST" class="mt-3">
                            <input type="hidden" name="rez_id" value="<?= $rez['id'] ?>">
                            <div class="mb-2">
                                <label class="form-label">Açıklama (isteğe bağlı)</label>
                                <textarea name="not" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="d-flex gap-2">
                                <button name="yeni_durum" value="Onaylandı" class="btn btn-success">Onayla</button>
                                <button name="yeni_durum" value="Reddedildi" class="btn btn-danger">Reddet</button>
                            </div>
                        </form>
                     <?php elseif (!empty($rez['ogretmen_notu'])): ?>
                        <p class="mt-3"><strong>Öğretmen Notu:</strong> <?= nl2br(htmlspecialchars($rez['ogretmen_notu'])) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<footer class="footer text-center bg-light py-3 mt-5">
    <p>&copy; <?= date('Y') ?> Alkü Randevu Sistemi</p>
</footer>
</body>
</html> 