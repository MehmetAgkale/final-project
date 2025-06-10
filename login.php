<?php
session_start();
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
  <title>ALKÜ Randevu - Öğrenci Giriş</title>
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <style>
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
      background-image: url('https://plus.unsplash.com/premium_photo-1683887034473-74e486cdb7a1?q=80&w=2069&auto=format&fit=crop');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      font-family: 'Segoe UI', sans-serif;
    }

    .overlay {
      height: 100%;
      width: 100%;
      background: rgba(0, 0, 0, 0.4); /* koyu şeffaf overlay */
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }

    .login-container {
      background: white;
      max-width: 400px;
      width: 100%;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
    }

    .login-title {
      font-size: 24px;
      font-weight: 600;
      text-align: center;
      margin-bottom: 20px;
      color: #3f51b5;
    }

    a {
      text-decoration: none;
      color: #3f51b5;
    }

    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="overlay">
    <div class="login-container">
      <div class="login-title">Öğrenci Girişi</div>
      <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
      <form method="POST">
        <div class="mb-3">
          <label>E-posta</label>
          <input type="email" name="email" class="form-control" required />
        </div>
        <div class="mb-3">
          <label>Şifre</label>
          <input type="password" name="password" class="form-control" required />
        </div>
        <button type="submit" class="btn btn-primary w-100">Giriş Yap</button>
      </form>
      <hr>
      <div class="text-center mb-3">
        <a href="signin.php" class="btn btn-outline-primary w-100">Yeni Hesap Oluştur</a>
      </div>
      <a href="admin_panel_login.php" class="btn btn-outline-secondary w-100">👩‍🏫 Akademisyen Girişi</a>
    </div>
  </div>
</body>
</html>
