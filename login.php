<?php
require_once __DIR__ . '/session_bootstrap.php';
include "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $stmt = $db->prepare("SELECT * FROM kullanicilar WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user["sifre"])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["user_name"] = $user["ad"];
        header("Location: index.php");
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
  <title>Öğrenci Girişi</title>
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/portal-theme.css">
</head>
<body>
  <div class="auth-shell">
    <aside class="auth-side">
      <div class="auth-panel p-4 p-xl-5 w-100">
        <div class="auth-side-copy">
          <div class="portal-badge mb-3">Bitirme Projesi Portalı</div>
          <h1 class="display-5 mb-3">Danışman görüşmelerini, duyuruları ve proje iletişimini tek merkezden yönetin.</h1>
          <p class="lead mb-0 text-white-50">Öğrenciler için planlı, akademisyenler için izlenebilir ve her iki taraf için daha sade bir süreç.</p>
          <div class="auth-bullets">
            <div class="auth-bullet">
              <strong>Randevu akışı</strong>
              <div class="small text-white-50 mt-1">Bitirme projesi toplantılarını uygun saatlerde oluşturun ve takip edin.</div>
            </div>
            <div class="auth-bullet">
              <strong>Duyuru merkezi</strong>
              <div class="small text-white-50 mt-1">Teslim tarihi, toplantı günü ve süreç güncellemelerini kaçırmayın.</div>
            </div>
            <div class="auth-bullet">
              <strong>Dosya ekli mesajlaşma</strong>
              <div class="small text-white-50 mt-1">Rapor, sunum ve proje belgelerini yazışma içinde paylaşın.</div>
            </div>
          </div>
        </div>
      </div>
    </aside>

    <main class="auth-main">
      <div class="portal-card auth-card">
        <div class="portal-brand mb-4">
          <div class="portal-brand-mark">BP</div>
          <div class="portal-brand-text">
            <span>Öğrenci Erişimi</span>
            <strong>Bitirme Projesi Girişi</strong>
          </div>
        </div>

        <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

        <form method="POST" class="portal-form">
          <div class="mb-3">
            <label class="form-label">E-posta</label>
            <input type="email" name="email" class="form-control" placeholder="ornek@alanya.edu.tr" required />
          </div>
          <div class="mb-4">
            <label class="form-label">Şifre</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required />
          </div>
          <button type="submit" class="btn btn-primary w-100">Giriş Yap</button>
        </form>

        <div class="d-grid gap-2 mt-3">
          <a href="signin.php" class="btn btn-outline-primary">Yeni Hesap Oluştur</a>
          <a href="admin_panel_login.php" class="btn btn-outline-secondary">Akademisyen Girişi</a>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
