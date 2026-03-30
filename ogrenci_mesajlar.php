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
    <style>
        body { background: #f5f7fb; }
        .page-card { border: 0; border-radius: 18px; box-shadow: 0 14px 34px rgba(16, 57, 92, 0.08); }
    </style>
</head>
<body>
<div class="container py-4 py-lg-5">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <p class="text-muted mb-1">Öğrenci Mesaj Kutusu</p>
            <h1 class="h3 mb-0">Akademisyenlerle Yazışmalar</h1>
        </div>
        <div class="d-flex gap-2">
            <a href="index.php" class="btn btn-outline-secondary">Ana Sayfa</a>
            <a href="ogrenci_mesaj_gonder.php" class="btn btn-primary">Yeni Mesaj</a>
        </div>
    </div>

    <div class="card page-card">
        <div class="card-body p-4">
            <?php if (!$messages): ?>
                <p class="text-muted mb-0">Henüz mesaj bulunmuyor.</p>
            <?php else: ?>
                <?php foreach ($messages as $message): ?>
                    <?php $isOutgoing = ($message['gonderen_tipi'] ?? '') === 'ogrenci' || ($message['durum'] ?? '') === 'ogrenci'; ?>
                    <div class="border rounded-4 p-3 mb-3">
                        <div class="d-flex justify-content-between gap-3 flex-wrap">
                            <div>
                                <strong><?php echo h($message['baslik']); ?></strong>
                                <div class="text-muted small">
                                    <?php echo $isOutgoing ? 'Gönderilen' : 'Gelen'; ?> mesaj ·
                                    <?php echo h(trim($message['unvan'] . ' ' . $message['ad_soyad'])); ?>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge <?php echo $isOutgoing ? 'text-bg-primary' : 'text-bg-light'; ?>">
                                    <?php echo $isOutgoing ? 'Ben gönderdim' : 'Akademisyen gönderdi'; ?>
                                </span>
                                <div class="text-muted small mt-1"><?php echo formatDateTime($message['created_at']); ?></div>
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
</div>
</body>
</html>
