<?php
require_once __DIR__ . '/app_bootstrap.php';

if (isTeacher()) {
    header('Location: ogretmen_rezervasyon_onay.php');
    exit;
}

$studentId = requireStudent();
$success = null;
$error = null;

$studentStmt = $db->prepare("
    SELECT k.ad, k.email, k.danisman_id, o.unvan, o.ad_soyad
    FROM kullanicilar k
    LEFT JOIN ogretmenler o ON o.id = k.danisman_id
    WHERE k.id = ?
");
$studentStmt->execute([$studentId]);
$student = $studentStmt->fetch(PDO::FETCH_ASSOC);

$teachers = $db->query("
    SELECT id, unvan, ad_soyad, email
    FROM ogretmenler
    ORDER BY ad_soyad ASC
")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $teacherId = (int) ($_POST['teacher_id'] ?? 0);
    $date = trim($_POST['date'] ?? '');
    $time = trim($_POST['time'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$teacherId || !$date || !$time || !$title || !$message) {
        $error = 'Lütfen tüm alanları doldurun.';
    } elseif (strtotime($date . ' ' . $time) === false || strtotime($date . ' ' . $time) < time()) {
        $error = 'Geçmiş bir tarih için rezervasyon oluşturamazsınız.';
    } else {
        $conflictStmt = $db->prepare("
            SELECT COUNT(*)
            FROM rezervasyonlar
            WHERE ogretmen_id = ? AND tarih = ? AND saat = ? AND durum IN ('Beklemede', 'Onaylandı')
        ");
        $conflictStmt->execute([$teacherId, $date, $time]);

        if ((int) $conflictStmt->fetchColumn() > 0) {
            $error = 'Seçtiğiniz saat dolu. Lütfen başka bir saat deneyin.';
        } else {
            $insertStmt = $db->prepare("
                INSERT INTO rezervasyonlar (ogrenci_id, ogretmen_id, tarih, saat, baslik, mesaj, durum, created_at)
                VALUES (?, ?, ?, ?, ?, ?, 'Beklemede', NOW())
            ");
            $insertStmt->execute([$studentId, $teacherId, $date, $time, $title, $message]);
            $success = 'Bitirme projesi görüşme talebiniz oluşturuldu.';
        }
    }
}

$reservationsStmt = $db->prepare("
    SELECT r.*, o.unvan, o.ad_soyad, o.email
    FROM rezervasyonlar r
    JOIN ogretmenler o ON o.id = r.ogretmen_id
    WHERE r.ogrenci_id = ?
    ORDER BY r.tarih DESC, r.saat DESC
");
$reservationsStmt->execute([$studentId]);
$reservations = $reservationsStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bitirme Projesi Randevuları</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/portal-theme.css" rel="stylesheet">
</head>
<body>
<div class="container portal-shell py-4 py-lg-5">
    <div class="portal-topbar">
        <a class="portal-brand" href="index.php">
            <div class="portal-brand-mark">RV</div>
            <div class="portal-brand-text">
                <span>Öğrenci Rezervasyon Ekranı</span>
                <strong>Bitirme Projesi Görüşmeleri</strong>
            </div>
        </a>
        <div class="d-flex gap-2 flex-wrap">
            <a href="index.php" class="btn btn-outline-secondary">Ana Sayfa</a>
            <a href="ogrenci_mesajlar.php" class="btn btn-outline-primary">Mesajlarım</a>
        </div>
    </div>

    <section class="portal-hero p-4 p-lg-5 mb-4">
        <div class="row g-4 align-items-center">
            <div class="col-lg-8">
                <div class="portal-badge mb-3">Randevu planlama</div>
                <h1 class="display-5 mb-3">Bitirme projesi görüşmesini net bir akışla oluşturun.</h1>
                <p class="lead mb-0 text-white-50">Danışmanınızı seçin, tarih ve saat belirleyin, toplantı gündemini tek seferde iletin.</p>
            </div>
            <div class="col-lg-4">
                <div class="portal-card portal-card-muted p-4">
                    <div class="portal-section-title">Danışman</div>
                    <h2 class="h4 mb-1"><?php echo h(trim(($student['unvan'] ?? '') . ' ' . ($student['ad_soyad'] ?? 'Henüz atanmadı'))); ?></h2>
                    <div class="portal-meta"><?php echo h($student['email'] ?? ''); ?></div>
                </div>
            </div>
        </div>
    </section>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="portal-card p-4 h-100">
                <div class="portal-section-title">Yeni talep</div>
                <h2 class="h4 mb-3">Görüşme formu</h2>
                <?php if ($success): ?><div class="alert alert-success"><?php echo h($success); ?></div><?php endif; ?>
                <?php if ($error): ?><div class="alert alert-danger"><?php echo h($error); ?></div><?php endif; ?>
                <form method="POST" class="portal-form">
                    <div class="mb-3">
                        <label class="form-label">Akademisyen</label>
                        <select name="teacher_id" class="form-select" required>
                            <option value="">Seçiniz</option>
                            <?php foreach ($teachers as $teacher): ?>
                                <option value="<?php echo (int) $teacher['id']; ?>" <?php echo ((int) ($student['danisman_id'] ?? 0) === (int) $teacher['id']) ? 'selected' : ''; ?>>
                                    <?php echo h(trim($teacher['unvan'] . ' ' . $teacher['ad_soyad'])); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tarih</label>
                            <input type="date" name="date" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Saat</label>
                            <select name="time" class="form-select" required>
                                <option value="">Seçiniz</option>
                                <?php for ($hour = 9; $hour <= 17; $hour++): ?>
                                    <?php foreach (['00', '30'] as $minute): ?>
                                        <?php $slot = sprintf('%02d:%s:00', $hour, $minute); ?>
                                        <option value="<?php echo $slot; ?>"><?php echo substr($slot, 0, 5); ?></option>
                                    <?php endforeach; ?>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Başlık</label>
                        <input type="text" name="title" class="form-control" maxlength="255" placeholder="Örn. sunum öncesi değerlendirme" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Görüşme notu</label>
                        <textarea name="message" class="form-control" rows="5" placeholder="Toplantıda ele alınacak başlıkları, belge durumunu ve beklentinizi yazın." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Talep Oluştur</button>
                </form>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="portal-card p-4">
                <div class="portal-section-title">Rezervasyon geçmişi</div>
                <h2 class="h4 mb-3">Önceki taleplerim</h2>
                <?php if (!$reservations): ?>
                    <p class="portal-meta mb-0">Henüz rezervasyon talebi oluşturmadınız.</p>
                <?php else: ?>
                    <?php foreach ($reservations as $reservation): ?>
                        <div class="portal-list-item">
                            <div class="d-flex justify-content-between gap-3 flex-wrap">
                                <div>
                                    <strong><?php echo h($reservation['baslik']); ?></strong>
                                    <div class="portal-meta mt-1"><?php echo h(trim($reservation['unvan'] . ' ' . $reservation['ad_soyad'])); ?></div>
                                </div>
                                <span class="portal-chip"><?php echo h($reservation['durum']); ?></span>
                            </div>
                            <div class="portal-meta mt-2"><?php echo formatDateOnly($reservation['tarih']); ?> · <?php echo h(substr($reservation['saat'], 0, 5)); ?></div>
                            <p class="mb-0 mt-3"><?php echo nl2br(h($reservation['mesaj'])); ?></p>
                            <?php if (!empty($reservation['ogretmen_notu'])): ?>
                                <div class="alert alert-info mt-3 mb-0">
                                    <strong>Akademisyen Notu:</strong><br>
                                    <?php echo nl2br(h($reservation['ogretmen_notu'])); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
