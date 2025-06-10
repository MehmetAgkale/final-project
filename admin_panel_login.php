<?php
session_start();
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
  <title>Öğretmen Paneli Giriş</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <style>
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
      background-image: url('https://images.unsplash.com/photo-1588072432836-e10032774350?auto=format&fit=crop&w=1950&q=80');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      font-family: 'Segoe UI', sans-serif;
      background: "transparent";
    }
    .overlay {
      height: 100%;
      width: 100%;
      background: rgba(255, 255, 255, 0.85);
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .login-container {
      background: white;
      max-width: 400px;
      width: 100%;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
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
    <div class="login-title">
      <i class="fas fa-chalkboard-teacher"></i> Öğretmen Paneli
    </div>
    <?php if (!empty($error)) echo '<div class="alert alert-danger">'.$error.'</div>'; ?>
    <form method="POST">
      <div class="mb-3">
        <label for="email" class="form-label">E-Posta</label>
        <div class="input-group">
          <span class="input-group-text"><i class="fas fa-envelope"></i></span>
          <input type="email" class="form-control" name="email" id="email" placeholder="ornek@alanya.edu.tr" required>
        </div>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Şifre</label>
        <div class="input-group">
          <span class="input-group-text"><i class="fas fa-lock"></i></span>
          <input type="password" class="form-control" name="password" id="password" placeholder="••••••••" required>
        </div>
      </div>
      <div class="d-grid">
        <button type="submit" class="btn btn-primary w-100">Giriş Yap</button>
      </div>
      <hr>
      <div class="text-center">
        <p>Hesabınız yok mu? <a href="ogretmen_register.php">Akademisyen Kaydı Oluştur</a></p>
      </div>
    </form>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
