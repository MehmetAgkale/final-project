<?php
require_once __DIR__ . '/app_bootstrap.php';

$studentId = currentStudentId();
$teacherId = currentTeacherId();

if ($studentId === null && $teacherId === null) {
    header('Location: login.php');
    exit;
}

$role = $studentId !== null ? 'student' : 'teacher';
$displayName = '';
$summaryCards = [];

if ($role === 'student') {
    $studentQuery = $db->prepare("
        SELECT k.ad, o.unvan, o.ad_soyad
        FROM kullanicilar k
        LEFT JOIN ogretmenler o ON o.id = k.danisman_id
        WHERE k.id = ?
    ");
    $studentQuery->execute([$studentId]);
    $student = $studentQuery->fetch(PDO::FETCH_ASSOC);

    $displayName = $student['ad'] ?? 'Öğrenci';

    $counts = $db->prepare("
        SELECT
            SUM(CASE WHEN durum = 'Beklemede' THEN 1 ELSE 0 END) AS bekleyen,
            SUM(CASE WHEN durum = 'Onaylandı' THEN 1 ELSE 0 END) AS onayli,
            COUNT(*) AS toplam
        FROM rezervasyonlar
        WHERE ogrenci_id = ?
    ");
    $counts->execute([$studentId]);
    $countData = $counts->fetch(PDO::FETCH_ASSOC) ?: [];

    $messageCount = $db->prepare("SELECT COUNT(*) FROM mesajlar WHERE ogrenci_id = ?");
    $messageCount->execute([$studentId]);

    $summaryCards = [
        ['label' => 'Danışman', 'value' => trim(($student['unvan'] ?? '') . ' ' . ($student['ad_soyad'] ?? 'Atanmadı')), 'meta' => 'Bitirme projesi danışmanı'],
        ['label' => 'Toplam Talep', 'value' => (string) ($countData['toplam'] ?? 0), 'meta' => 'Oluşturulan görüşme'],
        ['label' => 'Bekleyen', 'value' => (string) ($countData['bekleyen'] ?? 0), 'meta' => 'Yanıt bekleyen talep'],
        ['label' => 'Mesaj', 'value' => (string) $messageCount->fetchColumn(), 'meta' => 'Toplam yazışma'],
    ];
} else {
    $teacherQuery = $db->prepare("SELECT ad_soyad FROM ogretmenler WHERE id = ?");
    $teacherQuery->execute([$teacherId]);
    $teacher = $teacherQuery->fetch(PDO::FETCH_ASSOC);
    $displayName = $teacher['ad_soyad'] ?? 'Akademisyen';

    $counts = $db->prepare("
        SELECT
            SUM(CASE WHEN durum = 'Beklemede' THEN 1 ELSE 0 END) AS bekleyen,
            SUM(CASE WHEN durum = 'Onaylandı' THEN 1 ELSE 0 END) AS onayli,
            COUNT(*) AS toplam
        FROM rezervasyonlar
        WHERE ogretmen_id = ?
    ");
    $counts->execute([$teacherId]);
    $countData = $counts->fetch(PDO::FETCH_ASSOC) ?: [];

    $studentCount = $db->prepare("SELECT COUNT(*) FROM kullanicilar WHERE danisman_id = ?");
    $studentCount->execute([$teacherId]);

    $messageCount = $db->prepare("SELECT COUNT(*) FROM mesajlar WHERE ogretmen_id = ?");
    $messageCount->execute([$teacherId]);

    $summaryCards = [
        ['label' => 'Danışan', 'value' => (string) $studentCount->fetchColumn(), 'meta' => 'Atanmış öğrenci'],
        ['label' => 'Toplam Talep', 'value' => (string) ($countData['toplam'] ?? 0), 'meta' => 'Tüm görüşmeler'],
        ['label' => 'Bekleyen', 'value' => (string) ($countData['bekleyen'] ?? 0), 'meta' => 'Yanıt bekleyenler'],
        ['label' => 'Mesaj', 'value' => (string) $messageCount->fetchColumn(), 'meta' => 'Toplam yazışma'],
    ];
}

$announcementStmt = $db->query("
    SELECT d.baslik, d.icerik, d.tarih, o.ad_soyad
    FROM duyurular d
    JOIN ogretmenler o ON o.id = d.ogretmen_id
    WHERE d.durum = 'aktif'
    ORDER BY d.tarih DESC
    LIMIT 4
");
$recentAnnouncements = $announcementStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bitirme Projesi Portalı</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/portal-theme.css" rel="stylesheet">
</head>
<body>
<div class="container portal-shell py-4 py-lg-5">
    <div class="portal-topbar">
        <a class="portal-brand" href="index.php">
            <div class="portal-brand-mark">BP</div>
            <div class="portal-brand-text">
                <span><?php echo $role === 'student' ? 'Öğrenci Paneli' : 'Akademisyen Paneli'; ?></span>
                <strong>Bitirme Projesi Portalı</strong>
            </div>
        </a>
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?php echo $role === 'student' ? 'gorusme_ayarla.php' : 'ogretmen_rezervasyon_onay.php'; ?>" class="btn btn-outline-primary">Rezervasyon</a>
            <a href="<?php echo $role === 'student' ? 'ogrenci_mesajlar.php' : 'ogretmen_mesajlar.php'; ?>" class="btn btn-outline-secondary">Mesajlarım</a>
            <a href="logout.php" class="btn btn-dark">Çıkış Yap</a>
        </div>
    </div>

    <section class="portal-hero p-4 p-lg-5 mb-4">
        <div class="row g-4 align-items-center">
            <div class="col-lg-8">
                <div class="portal-badge mb-3"><?php echo $role === 'student' ? 'Planla · Takip Et · Paylaş' : 'Yönet · Duyur · Yönlendir'; ?></div>
                <h1 class="display-5 mb-3"><?php echo h($displayName); ?></h1>
                <p class="lead mb-0 text-white-50">
                    <?php if ($role === 'student'): ?>
                        Bitirme projesi görüşmelerinizi planlayın, akademisyen duyurularını takip edin ve belgelerinizi mesaj içinde paylaşın.
                    <?php else: ?>
                        Öğrenci taleplerini tek akışta görün, duyuruları yönetin ve süreç iletişimini daha net hale getirin.
                    <?php endif; ?>
                </p>
            </div>
            <div class="col-lg-4">
                <div class="portal-card portal-card-muted p-4">
                    <div class="portal-section-title">Bugün için önerilen akış</div>
                    <div class="portal-stack">
                        <div class="portal-list-item">
                            <strong><?php echo $role === 'student' ? 'Yeni randevu oluştur' : 'Bekleyen talepleri incele'; ?></strong>
                            <div class="portal-meta mt-1"><?php echo $role === 'student' ? 'Toplantı başlığı ve görüşme notunu net girin.' : 'Öğrenci notlarını okuyup onay veya red verin.'; ?></div>
                        </div>
                        <div class="portal-list-item">
                            <strong><?php echo $role === 'student' ? 'Mesajlarla belge paylaş' : 'Duyuru ile herkesi hizala'; ?></strong>
                            <div class="portal-meta mt-1"><?php echo $role === 'student' ? 'Sunum ve rapor taslaklarını ek dosya ile gönderin.' : 'Takvim veya teslim güncellemelerini merkezden duyurun.'; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="row g-3 mb-4">
        <?php foreach ($summaryCards as $card): ?>
            <div class="col-6 col-xl-3">
                <div class="portal-card portal-stat h-100 p-4">
                    <div class="portal-stat-label mb-2"><?php echo h($card['label']); ?></div>
                    <div class="portal-stat-value"><?php echo h($card['value']); ?></div>
                    <div class="portal-meta mt-2"><?php echo h($card['meta']); ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="row g-3 mb-4">
                <div class="col-md-6 col-xl-3">
                    <a class="portal-link-card" href="gorusme_ayarla.php">
                        <div class="portal-card h-100 p-4">
                            <div class="portal-chip mb-3">01</div>
                            <h2 class="h5 mb-2">Rezervasyon</h2>
                            <p class="portal-meta mb-0">Bitirme projesi toplantılarını planlayın veya talepleri yönetin.</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-xl-3">
                    <a class="portal-link-card" href="duyurular.php">
                        <div class="portal-card h-100 p-4">
                            <div class="portal-chip mb-3">02</div>
                            <h2 class="h5 mb-2">Duyurular</h2>
                            <p class="portal-meta mb-0">Süreç güncellemelerini ve önemli tarihleri tek yerden takip edin.</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-xl-3">
                    <a class="portal-link-card" href="<?php echo $role === 'student' ? 'ogrenci_mesajlar.php' : 'ogretmen_mesajlar.php'; ?>">
                        <div class="portal-card h-100 p-4">
                            <div class="portal-chip mb-3">03</div>
                            <h2 class="h5 mb-2">Mesajlar</h2>
                            <p class="portal-meta mb-0">Dosya ekli yazışmaları tek kutuda yönetin.</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-xl-3">
                    <a class="portal-link-card" href="<?php echo $role === 'student' ? 'ogrenci_profilim.php' : 'panel.php'; ?>">
                        <div class="portal-card h-100 p-4">
                            <div class="portal-chip mb-3">04</div>
                            <h2 class="h5 mb-2"><?php echo $role === 'student' ? 'Profilim' : 'Yönetim'; ?></h2>
                            <p class="portal-meta mb-0"><?php echo $role === 'student' ? 'Şifre ve hesap bilginizi düzenleyin.' : 'Akademisyen görünümüne dönün.'; ?></p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="portal-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <div class="portal-section-title">Son Duyurular</div>
                        <h2 class="h4 mb-0">Süreçten geri kalmayın</h2>
                    </div>
                    <a href="duyurular.php" class="btn btn-sm btn-outline-primary">Tümünü Gör</a>
                </div>
                <?php if (!$recentAnnouncements): ?>
                    <p class="portal-meta mb-0">Henüz duyuru paylaşılmadı.</p>
                <?php else: ?>
                    <?php foreach ($recentAnnouncements as $announcement): ?>
                        <div class="portal-list-item">
                            <div class="d-flex justify-content-between gap-3 flex-wrap">
                                <strong><?php echo h($announcement['baslik']); ?></strong>
                                <span class="portal-meta"><?php echo h($announcement['ad_soyad']); ?> · <?php echo formatDateTime($announcement['tarih']); ?></span>
                            </div>
                            <p class="portal-meta mb-0 mt-2"><?php echo nl2br(h(mb_strimwidth($announcement['icerik'], 0, 220, '...'))); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="portal-card p-4 h-100">
                <div class="portal-section-title">Hızlı Erişim</div>
                <h2 class="h4 mb-3">Bugün ne yapmak istiyorsunuz?</h2>
                <div class="d-grid gap-2">
                    <a href="<?php echo $role === 'student' ? 'gorusme_ayarla.php' : 'ogretmen_rezervasyon_onay.php'; ?>" class="btn btn-primary">
                        <?php echo $role === 'student' ? 'Yeni Randevu Talebi Oluştur' : 'Rezervasyon Taleplerini Aç'; ?>
                    </a>
                    <?php if ($role === 'teacher'): ?>
                        <a href="duyuru_yap.php" class="btn btn-outline-primary">Yeni Duyuru Yayınla</a>
                    <?php endif; ?>
                    <a href="<?php echo $role === 'student' ? 'ogrenci_mesaj_gonder.php' : 'ogretmen_mesaj_gonder.php'; ?>" class="btn btn-outline-secondary">Yeni Mesaj Yaz</a>
                </div>
                <div class="portal-list-item mt-4">
                    <strong>Not</strong>
                    <div class="portal-meta mt-2">Yeni tema, tüm ana akışlarda aynı kart yapısını ve daha okunabilir bir görsel hiyerarşiyi kullanır.</div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
