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
    <style>
        body { background: #f5f7fb; }
        .page-card { border: 0; border-radius: 18px; box-shadow: 0 14px 34px rgba(16, 57, 92, 0.08); }
    </style>
</head>
<body>
<div class="container py-4 py-lg-5">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <p class="text-muted mb-1">Akademisyen Duyuru Ekranı</p>
            <h1 class="h3 mb-0">Bitirme Projesi Duyurusu Paylaş</h1>
        </div>
        <div class="d-flex gap-2">
            <a href="panel.php" class="btn btn-outline-secondary">Panele Dön</a>
            <a href="duyurular.php" class="btn btn-outline-primary">Genel Duyurular</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card page-card">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3">Yeni Duyuru</h2>
                    <?php if ($success): ?><div class="alert alert-success"><?php echo h($success); ?></div><?php endif; ?>
                    <?php if ($error): ?><div class="alert alert-danger"><?php echo h($error); ?></div><?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Başlık</label>
                            <input type="text" class="form-control" name="baslik" maxlength="255" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">İçerik</label>
                            <textarea class="form-control" name="icerik" rows="6" placeholder="Takvim değişikliği, teslim duyurusu veya proje toplantısı çağrısı paylaşabilirsiniz." required></textarea>
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
        </div>

        <div class="col-lg-7">
            <div class="card page-card">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3">Yayınladığım Duyurular</h2>
                    <?php if (!$myAnnouncements): ?>
                        <p class="text-muted mb-0">Henüz duyuru oluşturmadınız.</p>
                    <?php else: ?>
                        <?php foreach ($myAnnouncements as $announcement): ?>
                            <div class="border rounded-4 p-3 mb-3">
                                <div class="d-flex justify-content-between gap-3 flex-wrap">
                                    <strong><?php echo h($announcement['baslik']); ?></strong>
                                    <span class="badge text-bg-light"><?php echo h($announcement['durum']); ?></span>
                                </div>
                                <div class="text-muted small mt-1"><?php echo formatDateTime($announcement['tarih']); ?></div>
                                <p class="mb-0 mt-3"><?php echo nl2br(h($announcement['icerik'])); ?></p>
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
