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
$recentAnnouncements = [];

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
        ['label' => 'Danışman', 'value' => trim(($student['unvan'] ?? '') . ' ' . ($student['ad_soyad'] ?? 'Atanmadı'))],
        ['label' => 'Toplam Talep', 'value' => (string) ($countData['toplam'] ?? 0)],
        ['label' => 'Bekleyen Talep', 'value' => (string) ($countData['bekleyen'] ?? 0)],
        ['label' => 'Mesaj', 'value' => (string) $messageCount->fetchColumn()],
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
        ['label' => 'Danışan Öğrenci', 'value' => (string) $studentCount->fetchColumn()],
        ['label' => 'Toplam Talep', 'value' => (string) ($countData['toplam'] ?? 0)],
        ['label' => 'Bekleyen Onay', 'value' => (string) ($countData['bekleyen'] ?? 0)],
        ['label' => 'Mesaj', 'value' => (string) $messageCount->fetchColumn()],
    ];
}

$announcementStmt = $db->query("
    SELECT d.baslik, d.icerik, d.tarih, o.ad_soyad
    FROM duyurular d
    JOIN ogretmenler o ON o.id = d.ogretmen_id
    WHERE d.durum = 'aktif'
    ORDER BY d.tarih DESC
    LIMIT 3
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
    <style>
        body { background: #f4f7fb; color: #1f2937; }
        .hero { background: linear-gradient(135deg, #0f4c81, #1ea7a1); color: #fff; border-radius: 24px; }
        .nav-card, .content-card { border: 0; border-radius: 18px; box-shadow: 0 12px 32px rgba(15, 76, 129, 0.08); }
        .summary-number { font-size: 2rem; font-weight: 700; color: #0f4c81; }
        .quick-link { text-decoration: none; color: inherit; }
        .quick-link .card:hover { transform: translateY(-2px); }
    </style>
</head>
<body>
<div class="container py-4 py-lg-5">
    <div class="hero p-4 p-lg-5 mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
            <div>
                <p class="mb-2 text-white-50"><?php echo $role === 'student' ? 'Öğrenci Paneli' : 'Akademisyen Paneli'; ?></p>
                <h1 class="h2 mb-3"><?php echo h($displayName); ?></h1>
                <p class="mb-0">
                    <?php if ($role === 'student'): ?>
                        Bitirme projesi görüşmelerinizi planlayın, danışman duyurularını takip edin ve dosya ekli mesajlaşın.
                    <?php else: ?>
                        Öğrenci taleplerini yönetin, duyuru paylaşın ve dosya ekli mesajlarla süreci tek yerden yürütün.
                    <?php endif; ?>
                </p>
            </div>
            <div class="d-flex align-items-start align-items-lg-center">
                <a href="logout.php" class="btn btn-light">Çıkış Yap</a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <?php foreach ($summaryCards as $card): ?>
            <div class="col-6 col-lg-3">
                <div class="card nav-card h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-2"><?php echo h($card['label']); ?></div>
                        <div class="summary-number"><?php echo h($card['value']); ?></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="row g-3 mb-4">
                <div class="col-md-6 col-xl-3">
                    <a class="quick-link" href="gorusme_ayarla.php">
                        <div class="card content-card h-100">
                            <div class="card-body">
                                <h2 class="h6">Rezervasyon</h2>
                                <p class="mb-0 text-muted">Bitirme projesi görüşmeleri oluşturun veya yönetin.</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-xl-3">
                    <a class="quick-link" href="duyurular.php">
                        <div class="card content-card h-100">
                            <div class="card-body">
                                <h2 class="h6">Duyurular</h2>
                                <p class="mb-0 text-muted">Akademisyen duyurularını görüntüleyin.</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-xl-3">
                    <a class="quick-link" href="<?php echo $role === 'student' ? 'ogrenci_mesajlar.php' : 'ogretmen_mesajlar.php'; ?>">
                        <div class="card content-card h-100">
                            <div class="card-body">
                                <h2 class="h6">Mesajlarım</h2>
                                <p class="mb-0 text-muted">Dosya ekli mesajlaşma ekranına gidin.</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-xl-3">
                    <a class="quick-link" href="<?php echo $role === 'student' ? 'ogrenci_profilim.php' : 'panel.php'; ?>">
                        <div class="card content-card h-100">
                            <div class="card-body">
                                <h2 class="h6"><?php echo $role === 'student' ? 'Profilim' : 'Yönetim'; ?></h2>
                                <p class="mb-0 text-muted"><?php echo $role === 'student' ? 'Bilgilerinizi görüntüleyin.' : 'Akademisyen yönetim ekranını açın.'; ?></p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="card content-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 mb-0">Güncel Duyurular</h2>
                        <a href="duyurular.php" class="btn btn-sm btn-outline-primary">Tümünü Gör</a>
                    </div>
                    <?php if (!$recentAnnouncements): ?>
                        <p class="text-muted mb-0">Henüz duyuru paylaşılmadı.</p>
                    <?php else: ?>
                        <?php foreach ($recentAnnouncements as $announcement): ?>
                            <div class="border rounded-4 p-3 mb-3">
                                <div class="d-flex justify-content-between gap-3 flex-wrap">
                                    <strong><?php echo h($announcement['baslik']); ?></strong>
                                    <span class="text-muted small"><?php echo h($announcement['ad_soyad']); ?> · <?php echo formatDateTime($announcement['tarih']); ?></span>
                                </div>
                                <p class="mb-0 mt-2 text-muted"><?php echo nl2br(h(mb_strimwidth($announcement['icerik'], 0, 220, '...'))); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card content-card h-100">
                <div class="card-body">
                    <h2 class="h5 mb-3">Hızlı Erişim</h2>
                    <div class="d-grid gap-2">
                        <a href="<?php echo $role === 'student' ? 'gorusme_ayarla.php' : 'ogretmen_rezervasyon_onay.php'; ?>" class="btn btn-primary">
                            <?php echo $role === 'student' ? 'Yeni Randevu Talebi Oluştur' : 'Rezervasyon Taleplerini Aç'; ?>
                        </a>
                        <?php if ($role === 'teacher'): ?>
                            <a href="duyuru_yap.php" class="btn btn-outline-primary">Yeni Duyuru Yayınla</a>
                        <?php endif; ?>
                        <a href="<?php echo $role === 'student' ? 'ogrenci_mesaj_gonder.php' : 'ogretmen_mesaj_gonder.php'; ?>" class="btn btn-outline-secondary">Yeni Mesaj Yaz</a>
                    </div>
                    <hr>
                    <p class="small text-muted mb-0">
                        Menüler artık doğrudan çalışan sayfalara bağlı. Bu ekran ana gezinme noktası olarak kullanılabilir.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
