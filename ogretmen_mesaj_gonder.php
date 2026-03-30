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
    <style>
        body { background: #f5f7fb; }
        .page-card { border: 0; border-radius: 18px; box-shadow: 0 14px 34px rgba(16, 57, 92, 0.08); }
    </style>
</head>
<body>
<div class="container py-4 py-lg-5">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <p class="text-muted mb-1">Akademisyen Mesaj Gönderimi</p>
            <h1 class="h3 mb-0">Danışan Öğrencilere Mesaj Yaz</h1>
        </div>
        <div class="d-flex gap-2">
            <a href="ogretmen_mesajlar.php" class="btn btn-outline-secondary">Mesajlara Dön</a>
            <a href="panel.php" class="btn btn-outline-primary">Panele Dön</a>
        </div>
    </div>

    <div class="card page-card">
        <div class="card-body p-4">
            <?php if ($success): ?><div class="alert alert-success"><?php echo h($success); ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-danger"><?php echo h($error); ?></div><?php endif; ?>

            <?php if (!$students): ?>
                <p class="text-muted mb-0">Size atanmış öğrenci bulunmuyor.</p>
            <?php else: ?>
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Öğrenciler</label>
                        <div class="border rounded-4 p-3" style="max-height: 260px; overflow:auto;">
                            <?php foreach ($students as $student): ?>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="student_ids[]" value="<?php echo (int) $student['id']; ?>" id="student_<?php echo (int) $student['id']; ?>">
                                    <label class="form-check-label" for="student_<?php echo (int) $student['id']; ?>">
                                        <?php echo h($student['ad']); ?> <span class="text-muted">· <?php echo h($student['email']); ?></span>
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
                        <textarea name="mesaj" class="form-control" rows="6" placeholder="Toplantı hazırlığı, teslim tarihleri veya geri bildirim notlarını paylaşabilirsiniz." required></textarea>
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
</div>
</body>
</html>
