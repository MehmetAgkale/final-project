<?php
require_once __DIR__ . '/app_bootstrap.php';

$teacherId = requireTeacher();
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservationId = (int) ($_POST['reservation_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    $note = trim($_POST['note'] ?? '');

    if ($reservationId > 0 && in_array($action, ['Onaylandı', 'Reddedildi'], true)) {
        $updateStmt = $db->prepare("
            UPDATE rezervasyonlar
            SET durum = ?, ogretmen_notu = ?, katildi_mi = CASE WHEN ? = 'Reddedildi' THEN NULL ELSE katildi_mi END
            WHERE id = ? AND ogretmen_id = ?
        ");
        $updateStmt->execute([$action, $note, $action, $reservationId, $teacherId]);
        $success = 'Rezervasyon durumu güncellendi.';
    }
}

$reservationsStmt = $db->prepare("
    SELECT r.*, k.ad, k.email
    FROM rezervasyonlar r
    JOIN kullanicilar k ON k.id = r.ogrenci_id
    WHERE r.ogretmen_id = ?
    ORDER BY
        CASE r.durum
            WHEN 'Beklemede' THEN 0
            WHEN 'Onaylandı' THEN 1
            ELSE 2
        END,
        r.tarih ASC,
        r.saat ASC
");
$reservationsStmt->execute([$teacherId]);
$reservations = $reservationsStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rezervasyon Talepleri</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/portal-theme.css" rel="stylesheet">
</head>
<body>
<div class="container portal-shell py-4 py-lg-5">
    <div class="portal-topbar">
        <a class="portal-brand" href="panel.php">
            <div class="portal-brand-mark">RV</div>
            <div class="portal-brand-text">
                <span>Akademisyen rezervasyon yönetimi</span>
                <strong>Bitirme Projesi Talepleri</strong>
            </div>
        </a>
        <div class="d-flex gap-2 flex-wrap">
            <a href="panel.php" class="btn btn-outline-secondary">Panele Dön</a>
            <a href="ogretmen_mesajlar.php" class="btn btn-outline-primary">Mesajlar</a>
        </div>
    </div>

    <div class="portal-card p-4">
        <div class="portal-section-title">Karar masası</div>
        <h1 class="h3 mb-3">Öğrenci taleplerini yönetin</h1>
        <?php if ($success): ?><div class="alert alert-success"><?php echo h($success); ?></div><?php endif; ?>
        <?php if (!$reservations): ?>
            <p class="portal-meta mb-0">Henüz öğrenci talebi bulunmuyor.</p>
        <?php else: ?>
            <?php foreach ($reservations as $reservation): ?>
                <div class="portal-list-item">
                    <div class="d-flex justify-content-between gap-3 flex-wrap mb-2">
                        <div>
                            <strong><?php echo h($reservation['baslik']); ?></strong>
                            <div class="portal-meta mt-1"><?php echo h($reservation['ad']); ?> · <?php echo h($reservation['email']); ?></div>
                        </div>
                        <span class="portal-chip"><?php echo h($reservation['durum']); ?></span>
                    </div>
                    <div class="portal-meta mb-2"><?php echo formatDateOnly($reservation['tarih']); ?> · <?php echo h(substr($reservation['saat'], 0, 5)); ?></div>
                    <p class="mb-2"><?php echo nl2br(h($reservation['mesaj'])); ?></p>
                    <?php if ($reservation['durum'] === 'Beklemede'): ?>
                        <form method="POST" class="portal-form mt-3">
                            <input type="hidden" name="reservation_id" value="<?php echo (int) $reservation['id']; ?>">
                            <div class="mb-3">
                                <label class="form-label">Akademisyen Notu</label>
                                <textarea name="note" class="form-control" rows="3" placeholder="Onay veya red açıklaması ekleyebilirsiniz."></textarea>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" name="action" value="Onaylandı" class="btn btn-primary">Onayla</button>
                                <button type="submit" name="action" value="Reddedildi" class="btn btn-outline-secondary">Reddet</button>
                            </div>
                        </form>
                    <?php elseif (!empty($reservation['ogretmen_notu'])): ?>
                        <div class="alert alert-info mt-3 mb-0">
                            <strong>Not:</strong><br>
                            <?php echo nl2br(h($reservation['ogretmen_notu'])); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
