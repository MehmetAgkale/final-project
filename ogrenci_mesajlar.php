<?php
require_once __DIR__ . '/app_bootstrap.php';

$studentId = requireStudent();

$messagesStmt = $db->prepare("
    SELECT m.*, o.unvan, o.ad_soyad
    FROM mesajlar m
    JOIN ogretmenler o ON o.id = m.ogretmen_id
    WHERE m.ogrenci_id = ?
    ORDER BY m.created_at DESC, m.id DESC
");
$messagesStmt->execute([$studentId]);
$messages = $messagesStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Öğrenci Mesajları</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/portal-theme.css" rel="stylesheet">
</head>
<body>
<div class="container portal-shell py-4 py-lg-5">
    <div class="portal-topbar">
        <a class="portal-brand" href="index.php">
            <div class="portal-brand-mark">MS</div>
            <div class="portal-brand-text">
                <span>Öğrenci iletişimi</span>
                <strong>Mesaj Kutusu</strong>
            </div>
        </a>
        <div class="d-flex gap-2 flex-wrap">
            <a href="index.php" class="btn btn-outline-secondary">Ana Sayfa</a>
            <a href="ogrenci_mesaj_gonder.php" class="btn btn-primary">Yeni Mesaj</a>
        </div>
    </div>

    <div class="portal-card p-4">
        <div class="portal-section-title">Akademisyenlerle yazışmalar</div>
        <h1 class="h3 mb-3">Tek kutuda gelen ve giden mesajlar</h1>
        <?php if (!$messages): ?>
            <p class="portal-meta mb-0">Henüz mesaj bulunmuyor.</p>
        <?php else: ?>
            <?php foreach ($messages as $message): ?>
                <?php $isOutgoing = ($message['gonderen_tipi'] ?? '') === 'ogrenci' || ($message['durum'] ?? '') === 'ogrenci'; ?>
                <div class="portal-list-item">
                    <div class="d-flex justify-content-between gap-3 flex-wrap">
                        <div>
                            <strong><?php echo h($message['baslik']); ?></strong>
                            <div class="portal-meta mt-1"><?php echo $isOutgoing ? 'Gönderilen mesaj' : 'Gelen mesaj'; ?> · <?php echo h(trim($message['unvan'] . ' ' . $message['ad_soyad'])); ?></div>
                        </div>
                        <div class="text-end">
                            <span class="portal-chip"><?php echo $isOutgoing ? 'Ben gönderdim' : 'Akademisyen gönderdi'; ?></span>
                            <div class="portal-meta mt-2"><?php echo formatDateTime($message['created_at']); ?></div>
                        </div>
                    </div>
                    <p class="mb-2 mt-3"><?php echo nl2br(h($message['mesaj'])); ?></p>
                    <?php if (!empty($message['dosya_yolu'])): ?>
                        <a href="<?php echo h($message['dosya_yolu']); ?>" class="btn btn-sm btn-outline-secondary" target="_blank">
                            Ek Dosya: <?php echo h($message['dosya_adi'] ?: 'Dosyayı Aç'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
