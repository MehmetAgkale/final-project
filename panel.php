<?php
require_once __DIR__ . '/app_bootstrap.php';

$teacherId = requireTeacher();

$teacherStmt = $db->prepare("SELECT ad_soyad, email, unvan FROM ogretmenler WHERE id = ?");
$teacherStmt->execute([$teacherId]);
$teacher = $teacherStmt->fetch(PDO::FETCH_ASSOC);

$pendingStmt = $db->prepare("SELECT COUNT(*) FROM rezervasyonlar WHERE ogretmen_id = ? AND durum = 'Beklemede'");
$pendingStmt->execute([$teacherId]);
$pendingCount = (int) $pendingStmt->fetchColumn();

$studentStmt = $db->prepare("SELECT COUNT(*) FROM kullanicilar WHERE danisman_id = ?");
$studentStmt->execute([$teacherId]);
$studentCount = (int) $studentStmt->fetchColumn();

$announcementStmt = $db->prepare("SELECT COUNT(*) FROM duyurular WHERE ogretmen_id = ? AND durum = 'aktif'");
$announcementStmt->execute([$teacherId]);
$announcementCount = (int) $announcementStmt->fetchColumn();

$messageStmt = $db->prepare("SELECT COUNT(*) FROM mesajlar WHERE ogretmen_id = ?");
$messageStmt->execute([$teacherId]);
$messageCount = (int) $messageStmt->fetchColumn();

$upcomingStmt = $db->prepare("
    SELECT r.id, r.baslik, r.tarih, r.saat, r.durum, k.ad
    FROM rezervasyonlar r
    JOIN kullanicilar k ON k.id = r.ogrenci_id
    WHERE r.ogretmen_id = ?
    ORDER BY r.tarih ASC, r.saat ASC
    LIMIT 5
");
$upcomingStmt->execute([$teacherId]);
$upcomingReservations = $upcomingStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akademisyen Paneli</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f7fb; }
        .panel-shell { max-width: 1180px; }
        .stat-card, .panel-card { border: 0; border-radius: 18px; box-shadow: 0 14px 34px rgba(16, 57, 92, 0.08); }
    </style>
</head>
<body>
<div class="container panel-shell py-4 py-lg-5">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <p class="text-muted mb-1">Akademisyen Yönetimi</p>
            <h1 class="h3 mb-1"><?php echo h(trim(($teacher['unvan'] ?? '') . ' ' . ($teacher['ad_soyad'] ?? ''))); ?></h1>
            <p class="text-muted mb-0"><?php echo h($teacher['email'] ?? ''); ?></p>
        </div>
        <div class="d-flex gap-2">
            <a href="index.php" class="btn btn-outline-secondary">Ana Panel</a>
            <a href="logout.php" class="btn btn-dark">Çıkış Yap</a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3"><div class="card stat-card"><div class="card-body"><div class="text-muted small">Danışan Öğrenci</div><div class="display-6"><?php echo $studentCount; ?></div></div></div></div>
        <div class="col-6 col-lg-3"><div class="card stat-card"><div class="card-body"><div class="text-muted small">Bekleyen Talep</div><div class="display-6"><?php echo $pendingCount; ?></div></div></div></div>
        <div class="col-6 col-lg-3"><div class="card stat-card"><div class="card-body"><div class="text-muted small">Aktif Duyuru</div><div class="display-6"><?php echo $announcementCount; ?></div></div></div></div>
        <div class="col-6 col-lg-3"><div class="card stat-card"><div class="card-body"><div class="text-muted small">Toplam Mesaj</div><div class="display-6"><?php echo $messageCount; ?></div></div></div></div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card panel-card h-100">
                <div class="card-body">
                    <h2 class="h5 mb-3">İşlemler</h2>
                    <div class="d-grid gap-2">
                        <a href="ogretmen_rezervasyon_onay.php" class="btn btn-primary">Rezervasyon Talepleri</a>
                        <a href="duyuru_yap.php" class="btn btn-outline-primary">Duyuru Yönetimi</a>
                        <a href="ogretmen_mesajlar.php" class="btn btn-outline-secondary">Mesaj Kutusu</a>
                        <a href="ogretmen_mesaj_gonder.php" class="btn btn-outline-secondary">Yeni Mesaj</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card panel-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 mb-0">Yaklaşan Görüşmeler</h2>
                        <a href="ogretmen_rezervasyon_onay.php" class="btn btn-sm btn-outline-primary">Tüm Talepler</a>
                    </div>
                    <?php if (!$upcomingReservations): ?>
                        <p class="text-muted mb-0">Henüz görüşme kaydı bulunmuyor.</p>
                    <?php else: ?>
                        <?php foreach ($upcomingReservations as $reservation): ?>
                            <div class="border rounded-4 p-3 mb-3">
                                <div class="d-flex justify-content-between gap-3 flex-wrap">
                                    <div>
                                        <strong><?php echo h($reservation['baslik']); ?></strong>
                                        <div class="text-muted small"><?php echo h($reservation['ad']); ?></div>
                                    </div>
                                    <div class="text-end">
                                        <div><?php echo formatDateOnly($reservation['tarih']); ?> <?php echo h(substr($reservation['saat'], 0, 5)); ?></div>
                                        <span class="badge text-bg-light"><?php echo h($reservation['durum']); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
