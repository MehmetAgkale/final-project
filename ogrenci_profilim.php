<?php
require_once __DIR__ . '/app_bootstrap.php';

$studentId = requireStudent();
$success = null;
$error = null;

$studentStmt = $db->prepare("
    SELECT k.ad, k.email, o.unvan, o.ad_soyad
    FROM kullanicilar k
    LEFT JOIN ogretmenler o ON o.id = k.danisman_id
    WHERE k.id = ?
");
$studentStmt->execute([$studentId]);
$student = $studentStmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    $passwordStmt = $db->prepare("SELECT sifre FROM kullanicilar WHERE id = ?");
    $passwordStmt->execute([$studentId]);
    $passwordRow = $passwordStmt->fetch(PDO::FETCH_ASSOC);

    if (!$passwordRow || !password_verify($currentPassword, $passwordRow['sifre'])) {
        $error = 'Mevcut şifre yanlış.';
    } elseif ($newPassword === '' || strlen($newPassword) < 6) {
        $error = 'Yeni şifre en az 6 karakter olmalı.';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'Yeni şifre alanları eşleşmiyor.';
    } else {
        $updateStmt = $db->prepare("UPDATE kullanicilar SET sifre = ? WHERE id = ?");
        $updateStmt->execute([password_hash($newPassword, PASSWORD_DEFAULT), $studentId]);
        $success = 'Şifreniz güncellendi.';
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Öğrenci Profili</title>
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
            <p class="text-muted mb-1">Öğrenci Profili</p>
            <h1 class="h3 mb-0"><?php echo h($student['ad'] ?? 'Öğrenci'); ?></h1>
        </div>
        <div class="d-flex gap-2">
            <a href="index.php" class="btn btn-outline-secondary">Ana Sayfa</a>
            <a href="gorusme_ayarla.php" class="btn btn-outline-primary">Rezervasyonlar</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card page-card">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3">Hesap Bilgileri</h2>
                    <p class="mb-2"><strong>Ad Soyad:</strong> <?php echo h($student['ad'] ?? ''); ?></p>
                    <p class="mb-2"><strong>E-posta:</strong> <?php echo h($student['email'] ?? ''); ?></p>
                    <p class="mb-0"><strong>Danışman:</strong> <?php echo h(trim(($student['unvan'] ?? '') . ' ' . ($student['ad_soyad'] ?? 'Atanmadı'))); ?></p>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card page-card">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3">Şifre Güncelle</h2>
                    <?php if ($success): ?><div class="alert alert-success"><?php echo h($success); ?></div><?php endif; ?>
                    <?php if ($error): ?><div class="alert alert-danger"><?php echo h($error); ?></div><?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Mevcut Şifre</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Yeni Şifre</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Yeni Şifre Tekrar</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Şifreyi Güncelle</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
