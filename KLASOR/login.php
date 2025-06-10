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
  <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
  <div class="container mt-5" style="max-width: 500px;">
    <div class="card p-4 shadow">
      <h4 class="mb-3">Öğrenci Girişi</h4>
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
      <a href="admin_panel_login.php" class="btn btn-outline-secondary w-100 mt-2">👩‍🏫 Öğretmen Girişi</a>
    </div>
  </div>
</body>
</html>
