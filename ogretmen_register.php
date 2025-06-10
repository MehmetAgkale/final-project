<?php
session_start();
include "db.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $unvan = trim($_POST["unvan"]);
    $ad_soyad = trim($_POST["ad_soyad"]);
    $email = trim($_POST["email"]);
    $sifre = $_POST["sifre"];
    $sifre_tekrar = $_POST["sifre_tekrar"];

    // Validation
    if (empty($unvan) || empty($ad_soyad) || empty($email) || empty($sifre)) {
        $error = "Tüm alanları doldurunuz.";
    } elseif ($sifre !== $sifre_tekrar) {
        $error = "Şifreler eşleşmiyor.";
    } elseif (strlen($sifre) < 6) {
        $error = "Şifre en az 6 karakter olmalıdır.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Geçerli bir e-posta adresi giriniz.";
    } elseif (!str_ends_with($email, '@alanya.edu.tr')) {
        $error = "E-posta adresi @alanya.edu.tr ile bitmelidir.";
    } else {
        try {
            // Check if email already exists
            $stmt = $db->prepare("SELECT id FROM ogretmenler WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                $error = "Bu e-posta adresi zaten kayıtlı.";
            } else {
                // Insert new teacher
                $hashed_password = password_hash($sifre, PASSWORD_DEFAULT);
                $stmt = $db->prepare("INSERT INTO ogretmenler (unvan, ad_soyad, email, sifre) VALUES (?, ?, ?, ?)");
                
                if ($stmt->execute([$unvan, $ad_soyad, $email, $hashed_password])) {
                    $success = "Kayıt başarıyla tamamlandı. Giriş yapabilirsiniz.";
                    // Clear form data
                    $_POST = array();
                } else {
                    $error = "Kayıt sırasında bir hata oluştu.";
                }
            }
        } catch (PDOException $e) {
            $error = "Veritabanı hatası: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Akademisyen Kayıt - ALKÜ</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <h3 class="text-center mb-4">Akademisyen Kayıt</h3>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">Unvan</label>
                                <select name="unvan" class="form-select" required>
                                    <option value="">Seçiniz</option>
                                    <option value="Prof. Dr.">Prof. Dr.</option>
                                    <option value="Doç. Dr.">Doç. Dr.</option>
                                    <option value="Dr. Öğr. Üyesi">Dr. Öğr. Üyesi</option>
                                    <option value="Öğr. Gör. Dr.">Öğr. Gör. Dr.</option>
                                    <option value="Öğr. Gör.">Öğr. Gör.</option>
                                    <option value="Arş. Gör. Dr.">Arş. Gör. Dr.</option>
                                    <option value="Arş. Gör.">Arş. Gör.</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Ad Soyad</label>
                                <input type="text" name="ad_soyad" class="form-control" required value="<?php echo isset($_POST['ad_soyad']) ? htmlspecialchars($_POST['ad_soyad']) : ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">E-posta</label>
                                <input type="email" name="email" class="form-control" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                <small class="text-muted">@alanya.edu.tr uzantılı e-posta adresinizi kullanın</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Şifre</label>
                                <input type="password" name="sifre" class="form-control" required>
                                <small class="text-muted">En az 6 karakter</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Şifre Tekrar</label>
                                <input type="password" name="sifre_tekrar" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Kayıt Ol</button>
                        </form>

                        <div class="text-center mt-3">
                            <p>Zaten hesabınız var mı? <a href="admin_panel_login.php">Giriş Yap</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html> 