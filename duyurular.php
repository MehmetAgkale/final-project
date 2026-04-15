<?php
require_once __DIR__ . '/app_bootstrap.php';

if (!isStudent() && !isTeacher()) {
    header('Location: login.php');
    exit;
}

$announcementStmt = $db->query("
    SELECT d.*, o.unvan, o.ad_soyad
    FROM duyurular d
    JOIN ogretmenler o ON o.id = d.ogretmen_id
    WHERE d.durum = 'aktif'
    ORDER BY d.tarih DESC
");
$announcements = $announcementStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Duyurular</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/portal-theme.css" rel="stylesheet">
</head>
<body>
<div class="container portal-shell py-4 py-lg-5">
    <div class="portal-topbar">
        <a class="portal-brand" href="index.php">
            <div class="portal-brand-mark">DY</div>
            <div class="portal-brand-text">
                <span>Ortak duyuru akışı</span>
                <strong>Duyurular</strong>
            </div>
        </a>
        <div class="d-flex gap-2 flex-wrap">
            <a href="index.php" class="btn btn-outline-secondary">Ana Sayfa</a>
            <?php if (isTeacher()): ?>
                <a href="duyuru_yap.php" class="btn btn-primary">Yeni Duyuru</a>
            <?php endif; ?>
        </div>
    </div>

    <section class="portal-hero p-4 p-lg-5 mb-4">
        <div class="portal-badge mb-3">Süreç duyuruları</div>
        <h1 class="display-5 mb-3">Takvim, teslim ve toplantı güncellemeleri tek akışta.</h1>
        <p class="lead mb-0 text-white-50">Akademisyen paylaşımlarını daha okunabilir bir düzenle görün, önemli başlıkları gözden kaçırmayın.</p>
    </section>

    <div class="portal-card p-4">
        <?php if (!$announcements): ?>
            <p class="portal-meta mb-0">Henüz aktif duyuru bulunmuyor.</p>
        <?php else: ?>
            <?php foreach ($announcements as $announcement): ?>
                <div class="portal-list-item">
                    <div class="d-flex justify-content-between gap-3 flex-wrap">
                        <div>
                            <strong><?php echo h($announcement['baslik']); ?></strong>
                            <div class="portal-meta mt-1"><?php echo h(trim($announcement['unvan'] . ' ' . $announcement['ad_soyad'])); ?></div>
                        </div>
                        <div class="portal-meta"><?php echo formatDateTime($announcement['tarih']); ?></div>
                    </div>
                    <p class="mb-0 mt-3"><?php echo nl2br(h($announcement['icerik'])); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
