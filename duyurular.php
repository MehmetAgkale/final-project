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
    <style>
        body { background: #f4f7fb; }
        .page-card { border: 0; border-radius: 18px; box-shadow: 0 14px 34px rgba(16, 57, 92, 0.08); }
    </style>
</head>
<body>
<div class="container py-4 py-lg-5">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <p class="text-muted mb-1">Portal Duyuruları</p>
            <h1 class="h3 mb-0">Akademisyen Paylaşımları</h1>
        </div>
        <div class="d-flex gap-2">
            <a href="index.php" class="btn btn-outline-secondary">Ana Sayfa</a>
            <?php if (isTeacher()): ?>
                <a href="duyuru_yap.php" class="btn btn-primary">Yeni Duyuru</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="card page-card">
        <div class="card-body p-4">
            <?php if (!$announcements): ?>
                <p class="text-muted mb-0">Henüz aktif duyuru bulunmuyor.</p>
            <?php else: ?>
                <?php foreach ($announcements as $announcement): ?>
                    <div class="border rounded-4 p-3 mb-3">
                        <div class="d-flex justify-content-between gap-3 flex-wrap">
                            <div>
                                <strong><?php echo h($announcement['baslik']); ?></strong>
                                <div class="text-muted small"><?php echo h(trim($announcement['unvan'] . ' ' . $announcement['ad_soyad'])); ?></div>
                            </div>
                            <div class="text-muted small"><?php echo formatDateTime($announcement['tarih']); ?></div>
                        </div>
                        <p class="mb-0 mt-3"><?php echo nl2br(h($announcement['icerik'])); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
