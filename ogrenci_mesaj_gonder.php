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
    <style>
        body { background: #f5f7fb; }
        .page-card { border: 0; border-radius: 18px; box-shadow: 0 14px 34px rgba(16, 57, 92, 0.08); }
    </style>
</head>
<body>
<div class="container py-4 py-lg-5">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <p class="text-muted mb-1">Öğrenci Mesaj Gönderimi</p>
            <h1 class="h3 mb-0">Akademisyene Mesaj Yaz</h1>
        </div>
        <div class="d-flex gap-2">
            <a href="ogrenci_mesajlar.php" class="btn btn-outline-secondary">Mesajlara Dön</a>
            <a href="index.php" class="btn btn-outline-primary">Ana Sayfa</a>
        </div>
    </div>

    <div class="card page-card">
        <div class="card-body p-4">
            <?php if ($success): ?><div class="alert alert-success"><?php echo h($success); ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-danger"><?php echo h($error); ?></div><?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
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
                    <textarea name="mesaj" class="form-control" rows="6" placeholder="Bitirme projenizle ilgili soruları ve beklentileri yazın." required></textarea>
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
</div>
</body>
</html>
