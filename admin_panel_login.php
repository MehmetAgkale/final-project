<?php
require_once __DIR__ . '/session_bootstrap.php';
include "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $db->prepare("SELECT * FROM ogretmenler WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin["sifre"])) {
        $_SESSION["ogretmen_id"] = $admin["id"];
        $_SESSION["ogretmen_name"] = $admin["ad_soyad"];
        header("Location: panel.php");
        exit;
    } else {
        $error = "E-posta veya şifre hatalı.";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Akademisyen Girişi</title>
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/portal-theme.css">
</head>
<body>
  <div class="auth-shell">
    <aside class="auth-side">
      <div class="auth-panel p-4 p-xl-5 w-100">
        <div class="auth-side-copy">
          <div class="portal-badge mb-3">Akademisyen Yönetimi</div>
          <h1 class="display-5 mb-3">Danışmanlık sürecini, duyuruları ve öğrenci iletişimini sakin bir panelden yönetin.</h1>
          <p class="lead mb-0 text-white-50">Takvim, mesajlar ve bitirme projesi talepleri aynı akış içinde daha düzenli ilerlesin.</p>
          <div class="auth-bullets">
            <div class="auth-bullet">
              <strong>Talepleri sırala</strong>
              <div class="small text-white-50 mt-1">Bekleyen görüşme taleplerini öncelik sırasına göre görün.</div>
            </div>
            <div class="auth-bullet">
              <strong>Duyuru yayınla</strong>
              <div class="small text-white-50 mt-1">Ders dışı saatleri, teslim planını ve süreç değişikliklerini paylaşın.</div>
            </div>
            <div class="auth-bullet">
              <strong>Belgeli mesajlaşma</strong>
              <div class="small text-white-50 mt-1">Sunum, rapor ve düzeltme dosyalarını yazışmalar içinde yönetin.</div>
            </div>
          </div>
        </div>
      </div>
    </aside>

    <main class="auth-main">
      <div class="portal-card auth-card">
        <div class="portal-brand mb-4">
          <div class="portal-brand-mark">AK</div>
          <div class="portal-brand-text">
            <span>Akademisyen Erişimi</span>
            <strong>Danışman Paneli Girişi</strong>
          </div>
        </div>

        <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

        <form method="POST" class="portal-form">
          <div class="mb-3">
            <label class="form-label">E-posta</label>
            <input type="email" class="form-control" name="email" placeholder="ornek@alanya.edu.tr" required>
          </div>
          <div class="mb-4">
            <label class="form-label">Şifre</label>
            <input type="password" class="form-control" name="password" placeholder="••••••••" required>
          </div>
          <button type="submit" class="btn btn-primary w-100">Panele Gir</button>
        </form>

        <div class="d-grid gap-2 mt-3">
          <a href="ogretmen_register.php" class="btn btn-outline-primary">Akademisyen Kaydı Oluştur</a>
          <a href="login.php" class="btn btn-outline-secondary">Öğrenci Girişine Dön</a>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
