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
    <link href="assets/css/portal-theme.css" rel="stylesheet">
</head>
<body>
<div class="container panel-shell py-4 py-lg-5">
    <div class="portal-topbar">
        <a class="portal-brand" href="panel.php">
            <div class="portal-brand-mark">AK</div>
            <div class="portal-brand-text">
                <span>Akademisyen Yönetimi</span>
                <strong><?php echo h(trim(($teacher['unvan'] ?? '') . ' ' . ($teacher['ad_soyad'] ?? ''))); ?></strong>
            </div>
        </a>
        <div class="d-flex gap-2 flex-wrap">
            <a href="index.php" class="btn btn-outline-secondary">Ana Panel</a>
            <a href="logout.php" class="btn btn-dark">Çıkış Yap</a>
        </div>
    </div>

    <section class="portal-hero p-4 p-lg-5 mb-4">
        <div class="row g-4 align-items-center">
            <div class="col-lg-8">
                <div class="portal-badge mb-3">Akademisyen görünümü</div>
                <h1 class="display-5 mb-3">Danışmanlık sürecini daha net yönetin.</h1>
                <p class="lead mb-0 text-white-50">Görüşme talepleri, duyurular ve öğrenci mesajları aynı arayüzde daha okunabilir bir düzenle sunuluyor.</p>
            </div>
            <div class="col-lg-4">
                <div class="portal-card portal-card-muted p-4">
                    <div class="portal-section-title">Bugünün odakları</div>
                    <div class="portal-stack">
                        <div class="portal-list-item">
                            <strong>Bekleyen talepleri gözden geçirin</strong>
                            <div class="portal-meta mt-1"><?php echo $pendingCount; ?> görüşme talebi karar bekliyor.</div>
                        </div>
                        <div class="portal-list-item">
                            <strong>Duyuruları güncelleyin</strong>
                            <div class="portal-meta mt-1">Teslim tarihleri ve toplantı değişikliklerini tek noktadan paylaşın.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3"><div class="portal-card portal-stat h-100 p-4"><div class="portal-stat-label">Danışan</div><div class="portal-stat-value"><?php echo $studentCount; ?></div><div class="portal-meta mt-2">Atanmış öğrenci</div></div></div>
        <div class="col-6 col-xl-3"><div class="portal-card portal-stat h-100 p-4"><div class="portal-stat-label">Bekleyen</div><div class="portal-stat-value"><?php echo $pendingCount; ?></div><div class="portal-meta mt-2">Karar bekleyen talep</div></div></div>
        <div class="col-6 col-xl-3"><div class="portal-card portal-stat h-100 p-4"><div class="portal-stat-label">Duyuru</div><div class="portal-stat-value"><?php echo $announcementCount; ?></div><div class="portal-meta mt-2">Aktif paylaşım</div></div></div>
        <div class="col-6 col-xl-3"><div class="portal-card portal-stat h-100 p-4"><div class="portal-stat-label">Mesaj</div><div class="portal-stat-value"><?php echo $messageCount; ?></div><div class="portal-meta mt-2">Toplam yazışma</div></div></div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="portal-card p-4 h-100">
                <div class="portal-section-title">İşlemler</div>
                <h2 class="h4 mb-3">Hızlı yönetim</h2>
                <div class="d-grid gap-2">
                    <a href="ogretmen_rezervasyon_onay.php" class="btn btn-primary">Rezervasyon Talepleri</a>
                    <a href="duyuru_yap.php" class="btn btn-outline-primary">Duyuru Yönetimi</a>
                    <a href="ogretmen_mesajlar.php" class="btn btn-outline-secondary">Mesaj Kutusu</a>
                    <a href="ogretmen_mesaj_gonder.php" class="btn btn-outline-secondary">Yeni Mesaj</a>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="portal-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <div class="portal-section-title">Yaklaşan görüşmeler</div>
                        <h2 class="h4 mb-0">Takvim görünümü</h2>
                    </div>
                    <a href="ogretmen_rezervasyon_onay.php" class="btn btn-sm btn-outline-primary">Tüm Talepler</a>
                </div>
                <?php if (!$upcomingReservations): ?>
                    <p class="portal-meta mb-0">Henüz görüşme kaydı bulunmuyor.</p>
                <?php else: ?>
                    <?php foreach ($upcomingReservations as $reservation): ?>
                        <div class="portal-list-item">
                            <div class="d-flex justify-content-between gap-3 flex-wrap">
                                <div>
                                    <strong><?php echo h($reservation['baslik']); ?></strong>
                                    <div class="portal-meta mt-1"><?php echo h($reservation['ad']); ?></div>
                                </div>
                                <div class="text-end">
                                    <div><?php echo formatDateOnly($reservation['tarih']); ?> <?php echo h(substr($reservation['saat'], 0, 5)); ?></div>
                                    <span class="portal-chip mt-2"><?php echo h($reservation['durum']); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
