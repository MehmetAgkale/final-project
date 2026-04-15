<?php
require_once __DIR__ . '/app_bootstrap.php';

$teacherId = requireTeacher();
$success = null;
$error = null;

$studentsStmt = $db->prepare("
    SELECT id, ad, email
    FROM kullanicilar
    WHERE danisman_id = ?
    ORDER BY ad ASC
");
$studentsStmt->execute([$teacherId]);
$students = $studentsStmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentIds = array_map('intval', $_POST['student_ids'] ?? []);
    $title = trim($_POST['baslik'] ?? '');
    $message = trim($_POST['mesaj'] ?? '');

    [$filePath, $fileName, $uploadError] = storeUploadedMessageFile($_FILES['dosya'] ?? []);

    if ($uploadError) {
        $error = $uploadError;
    } elseif (!$studentIds || $title === '' || $message === '') {
        $error = 'En az bir öğrenci seçin ve başlık ile mesaj alanlarını doldurun.';
    } else {
        $insertStmt = $db->prepare("
            INSERT INTO mesajlar (ogrenci_id, ogretmen_id, baslik, mesaj, tarih, durum, created_at, gonderen_tipi, dosya_yolu, dosya_adi)
            VALUES (?, ?, ?, ?, CURDATE(), 'akademisyen', NOW(), 'ogretmen', ?, ?)
        ");

        foreach ($studentIds as $studentId) {
            $insertStmt->execute([$studentId, $teacherId, $title, $message, $filePath, $fileName]);
        }

        $success = 'Mesaj gönderildi.';
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Öğrenciye Mesaj Gönder</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/portal-theme.css" rel="stylesheet">
</head>
<body>
<div class="container portal-shell py-4 py-lg-5">
    <div class="portal-topbar">
        <a class="portal-brand" href="ogretmen_mesajlar.php">
            <div class="portal-brand-mark">MG</div>
            <div class="portal-brand-text">
                <span>Akademisyen yazışması</span>
                <strong>Yeni Mesaj</strong>
            </div>
        </a>
        <div class="d-flex gap-2 flex-wrap">
            <a href="ogretmen_mesajlar.php" class="btn btn-outline-secondary">Mesajlara Dön</a>
            <a href="panel.php" class="btn btn-outline-primary">Panele Dön</a>
        </div>
    </div>

    <div class="portal-card p-4">
        <div class="portal-section-title">Danışan iletişimi</div>
        <h1 class="h3 mb-3">Öğrencilere mesaj gönder</h1>
        <?php if ($success): ?><div class="alert alert-success"><?php echo h($success); ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-danger"><?php echo h($error); ?></div><?php endif; ?>

        <?php if (!$students): ?>
            <p class="portal-meta mb-0">Size atanmış öğrenci bulunmuyor.</p>
        <?php else: ?>
            <form method="POST" enctype="multipart/form-data" class="portal-form">
                <div class="mb-3">
                    <label class="form-label">Öğrenciler</label>
                    <div class="portal-list-item" style="max-height: 260px; overflow:auto;">
                        <?php foreach ($students as $student): ?>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="student_ids[]" value="<?php echo (int) $student['id']; ?>" id="student_<?php echo (int) $student['id']; ?>">
                                <label class="form-check-label" for="student_<?php echo (int) $student['id']; ?>">
                                    <?php echo h($student['ad']); ?> <span class="portal-meta">· <?php echo h($student['email']); ?></span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Başlık</label>
                    <input type="text" name="baslik" class="form-control" maxlength="255" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mesaj</label>
                    <textarea name="mesaj" class="form-control" rows="6" placeholder="Toplantı hazırlığı, teslim tarihi veya geri bildirim notlarını yazın." required></textarea>
                </div>
                <div class="mb-4">
                    <label class="form-label">Ek Dosya</label>
                    <input type="file" name="dosya" class="form-control">
                    <div class="form-text">PDF, DOCX, görsel, ZIP ve TXT dosyaları yüklenebilir. En fazla 10 MB.</div>
                </div>
                <button type="submit" class="btn btn-primary">Mesajı Gönder</button>
            </form>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
