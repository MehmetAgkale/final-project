<?php
require_once __DIR__ . '/session_bootstrap.php';
require_once __DIR__ . '/db.php';

date_default_timezone_set('Europe/Istanbul');

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function currentStudentId(): ?int
{
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}

function currentTeacherId(): ?int
{
    return isset($_SESSION['ogretmen_id']) ? (int) $_SESSION['ogretmen_id'] : null;
}

function isStudent(): bool
{
    return currentStudentId() !== null;
}

function isTeacher(): bool
{
    return currentTeacherId() !== null;
}

function requireStudent(): int
{
    $studentId = currentStudentId();
    if ($studentId === null) {
        header('Location: login.php');
        exit;
    }

    return $studentId;
}

function requireTeacher(): int
{
    $teacherId = currentTeacherId();
    if ($teacherId === null) {
        header('Location: admin_panel_login.php');
        exit;
    }

    return $teacherId;
}

function appUrl(string $path): string
{
    return $path;
}

function formatDateTime(?string $value): string
{
    if (!$value) {
        return '-';
    }

    $timestamp = strtotime($value);
    if ($timestamp === false) {
        return $value;
    }

    return date('d.m.Y H:i', $timestamp);
}

function formatDateOnly(?string $value): string
{
    if (!$value) {
        return '-';
    }

    $timestamp = strtotime($value);
    if ($timestamp === false) {
        return $value;
    }

    return date('d.m.Y', $timestamp);
}

function ensureAppSchema(PDO $db): void
{
    static $initialized = false;

    if ($initialized) {
        return;
    }

    $db->exec("ALTER TABLE mesajlar ADD COLUMN IF NOT EXISTS dosya_yolu VARCHAR(255) DEFAULT NULL");
    $db->exec("ALTER TABLE mesajlar ADD COLUMN IF NOT EXISTS dosya_adi VARCHAR(255) DEFAULT NULL");
    $db->exec("ALTER TABLE mesajlar ADD COLUMN IF NOT EXISTS gonderen_tipi ENUM('ogrenci','ogretmen') NOT NULL DEFAULT 'ogrenci'");
    $db->exec("ALTER TABLE mesajlar ADD COLUMN IF NOT EXISTS okundu_at DATETIME DEFAULT NULL");
    $db->exec("ALTER TABLE duyurular MODIFY COLUMN durum ENUM('aktif','pasif') NOT NULL DEFAULT 'aktif'");
    $db->exec("ALTER TABLE duyurular MODIFY COLUMN tarih DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP");
    $db->exec("ALTER TABLE rezervasyonlar MODIFY COLUMN durum VARCHAR(255) NOT NULL");
    $db->exec("ALTER TABLE rezervasyonlar MODIFY COLUMN created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP");

    $uploadDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'message_files';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $initialized = true;
}

ensureAppSchema($db);

function storeUploadedMessageFile(array $file): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return [null, null, null];
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        return [null, null, 'Dosya yükleme sırasında bir hata oluştu.'];
    }

    if (($file['size'] ?? 0) > 10 * 1024 * 1024) {
        return [null, null, 'Dosya boyutu 10 MB sınırını aşıyor.'];
    }

    $allowedExtensions = ['pdf', 'doc', 'docx', 'png', 'jpg', 'jpeg', 'zip', 'rar', 'txt'];
    $originalName = basename((string) ($file['name'] ?? ''));
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    if (!in_array($extension, $allowedExtensions, true)) {
        return [null, null, 'Bu dosya uzantısına izin verilmiyor.'];
    }

    $uploadDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'message_files';
    $safeFileName = uniqid('msg_', true) . '.' . $extension;
    $targetPath = $uploadDir . DIRECTORY_SEPARATOR . $safeFileName;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        return [null, null, 'Dosya sunucuya taşınamadı.'];
    }

    return ['uploads/message_files/' . $safeFileName, $originalName, null];
}
?>
