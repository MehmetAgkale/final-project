<?php
require_once __DIR__ . '/app_bootstrap.php';

$teacherId = requireTeacher();
$success = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['baslik'] ?? '');
    $content = trim($_POST['icerik'] ?? '');
    $status = ($_POST['durum'] ?? 'aktif') === 'pasif' ? 'pasif' : 'aktif';

    if ($title === '' || $content === '') {
        $error = 'Başlık ve içerik alanlarını doldurun.';
    } else {
        $insertStmt = $db->prepare("
            INSERT INTO duyurular (ogretmen_id, baslik, icerik, tarih, durum)
            VALUES (?, ?, ?, NOW(), ?)
        ");
        $insertStmt->execute([$teacherId, $title, $content, $status]);
        $success = 'Duyuru kaydedildi.';
    }
}

$myAnnouncementsStmt = $db->prepare("
    SELECT *
    FROM duyurular
    WHERE ogretmen_id = ?
    ORDER BY tarih DESC
");
$myAnnouncementsStmt->execute([$teacherId]);
$myAnnouncements = $myAnnouncementsStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Duyuru Yönetimi</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/portal-theme.css" rel="stylesheet">
</head>
<body>
<div class="container portal-shell py-4 py-lg-5">
    <div class="portal-topbar">
        <a class="portal-brand" href="panel.php">
            <div class="portal-brand-mark">DY</div>
            <div class="portal-brand-text">
                <span>Akademisyen duyuru yönetimi</span>
                <strong>Duyuru Tasarımı</strong>
            </div>
        </a>
        <div class="d-flex gap-2 flex-wrap">
            <a href="panel.php" class="btn btn-outline-secondary">Panele Dön</a>
            <a href="duyurular.php" class="btn btn-outline-primary">Genel Akış</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="portal-card p-4 h-100">
                <div class="portal-section-title">Yeni yayın</div>
                <h1 class="h3 mb-3">Duyuru Oluştur</h1>
                <?php if ($success): ?><div class="alert alert-success"><?php echo h($success); ?></div><?php endif; ?>
                <?php if ($error): ?><div class="alert alert-danger"><?php echo h($error); ?></div><?php endif; ?>
                <form method="POST" class="portal-form">
                    <div class="mb-3">
                        <label class="form-label">Başlık</label>
                        <input type="text" class="form-control" name="baslik" maxlength="255" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">İçerik</label>
                        <textarea class="form-control" name="icerik" rows="7" placeholder="Takvim, toplantı ya da teslim süreciyle ilgili net bir duyuru yazın." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Durum</label>
                        <select name="durum" class="form-select">
                            <option value="aktif">Aktif</option>
                            <option value="pasif">Taslak / Pasif</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Duyuruyu Kaydet</button>
                </form>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="portal-card p-4">
                <div class="portal-section-title">Arşiv</div>
                <h2 class="h3 mb-3">Yayınladığım duyurular</h2>
                <?php if (!$myAnnouncements): ?>
                    <p class="portal-meta mb-0">Henüz duyuru oluşturmadınız.</p>
                <?php else: ?>
                    <?php foreach ($myAnnouncements as $announcement): ?>
                        <div class="portal-list-item">
                            <div class="d-flex justify-content-between gap-3 flex-wrap">
                                <strong><?php echo h($announcement['baslik']); ?></strong>
                                <span class="portal-chip"><?php echo h($announcement['durum']); ?></span>
                            </div>
                            <div class="portal-meta mt-2"><?php echo formatDateTime($announcement['tarih']); ?></div>
                            <p class="mb-0 mt-3"><?php echo nl2br(h($announcement['icerik'])); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
