<?php
require_once __DIR__ . '/app_bootstrap.php';

$studentId = requireStudent();
$success = null;
$error = null;

$teachers = $db->query("
    SELECT id, unvan, ad_soyad, email
    FROM ogretmenler
    ORDER BY ad_soyad ASC
")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $teacherId = (int) ($_POST['teacher_id'] ?? 0);
    $title = trim($_POST['baslik'] ?? '');
    $message = trim($_POST['mesaj'] ?? '');

    [$filePath, $fileName, $uploadError] = storeUploadedMessageFile($_FILES['dosya'] ?? []);

    if ($uploadError) {
        $error = $uploadError;
    } elseif (!$teacherId || $title === '' || $message === '') {
        $error = 'Akademisyen, başlık ve mesaj alanlarını doldurun.';
    } else {
        $insertStmt = $db->prepare("
            INSERT INTO mesajlar (ogrenci_id, ogretmen_id, baslik, mesaj, tarih, durum, created_at, gonderen_tipi, dosya_yolu, dosya_adi)
            VALUES (?, ?, ?, ?, CURDATE(), 'ogrenci', NOW(), 'ogrenci', ?, ?)
        ");
        $insertStmt->execute([$studentId, $teacherId, $title, $message, $filePath, $fileName]);
        $success = 'Mesaj gönderildi.';
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akademisyene Mesaj Gönder</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/portal-theme.css" rel="stylesheet">
</head>
<body>
<div class="container portal-shell py-4 py-lg-5">
    <div class="portal-topbar">
        <a class="portal-brand" href="ogrenci_mesajlar.php">
            <div class="portal-brand-mark">MG</div>
            <div class="portal-brand-text">
                <span>Öğrenci yazışması</span>
                <strong>Yeni Mesaj</strong>
            </div>
        </a>
        <div class="d-flex gap-2 flex-wrap">
            <a href="ogrenci_mesajlar.php" class="btn btn-outline-secondary">Mesajlara Dön</a>
            <a href="index.php" class="btn btn-outline-primary">Ana Sayfa</a>
        </div>
    </div>

    <div class="portal-card p-4">
        <div class="portal-section-title">Dosya ekli iletişim</div>
        <h1 class="h3 mb-3">Akademisyene mesaj yaz</h1>
        <?php if ($success): ?><div class="alert alert-success"><?php echo h($success); ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-danger"><?php echo h($error); ?></div><?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="portal-form">
            <div class="mb-3">
                <label class="form-label">Akademisyen</label>
                <select name="teacher_id" class="form-select" required>
                    <option value="">Seçiniz</option>
                    <?php foreach ($teachers as $teacher): ?>
                        <option value="<?php echo (int) $teacher['id']; ?>">
                            <?php echo h(trim($teacher['unvan'] . ' ' . $teacher['ad_soyad'] . ' · ' . $teacher['email'])); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Başlık</label>
                <input type="text" name="baslik" class="form-control" maxlength="255" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mesaj</label>
                <textarea name="mesaj" class="form-control" rows="6" placeholder="Bitirme projenizle ilgili net soruları ve belge durumunu yazın." required></textarea>
            </div>
            <div class="mb-4">
                <label class="form-label">Ek Dosya</label>
                <input type="file" name="dosya" class="form-control">
                <div class="form-text">PDF, DOCX, görsel, ZIP ve TXT dosyaları yüklenebilir. En fazla 10 MB.</div>
            </div>
            <button type="submit" class="btn btn-primary">Mesajı Gönder</button>
        </form>
    </div>
</div>
</body>
</html>
