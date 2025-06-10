<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

$error = $success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    $stmt = $db->prepare("SELECT sifre FROM kullanicilar WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($current, $user['sifre'])) {
        if ($new === $confirm) {
            $hashed = password_hash($new, PASSWORD_DEFAULT);
            $update = $db->prepare("UPDATE kullanicilar SET sifre = ? WHERE id = ?");
            $update->execute([$hashed, $user_id]);
            $success = "Şifre başarıyla güncellendi.";
        } else {
            $error = "Yeni şifreler eşleşmiyor.";
        }
    } else {
        $error = "Mevcut şifre yanlış.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Profilim</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
</head>
<body>
<header class="d-flex justify-content-between align-items-center p-3 bg-white shadow-sm">
    <div class="logo">
        <a href="index.php"><img src="assets/images/logo/logo.png" alt="ALKÜ Logo" style="height: 40px;"></a>
    </div>
    <nav class="nav">
        <a href="index.php" class="mx-2">Anasayfa</a>
        <a href="ogrenci_rezervasyon.php" class="mx-2">Rezervasyon</a>
        <a href="ogrenci_mesajlar.php" class="mx-2">Mesajlarım</a>
        <a href="ogrenci_profilim.php" class="mx-2 text-primary">Profilim</a>
        <a href="logout.php" class="mx-2">Çıkış</a>
    </nav>
</header>

<div class="container py-5">
    <div class="mx-auto" style="max-width: 600px;">
        <h2 class="mb-4">👤 Şifre Değiştir</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"> <?= $error ?> </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"> <?= $success ?> </div>
        <?php endif; ?>

        <form method="POST" class="shadow-sm p-4 bg-white rounded">
            <div class="mb-3">
                <label class="form-label">Adınız</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($user_name) ?>" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Mevcut Şifre</label>
                <input type="password" class="form-control" name="current_password" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Yeni Şifre</label>
                <input type="password" class="form-control" name="new_password" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Yeni Şifre (Tekrar)</label>
                <input type="password" class="form-control" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Şifreyi Güncelle</button>
        </form>
    </div>
</div>

<footer class="footer text-center bg-light py-3 mt-5">
    <p>&copy; <?= date('Y') ?> Alkü Randevu Sistemi. Tüm hakları saklıdır.</p>
</footer>

</body>
</html>
