<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstname = trim($_POST["firstname"]);
    $lastname = trim($_POST["lastname"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $danisman_id = $_POST["danisman_id"];

    // Validation
    if (empty($firstname) || empty($lastname) || empty($email) || empty($password) || empty($danisman_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Tüm alanları doldurunuz.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Geçerli bir e-posta adresi giriniz.']);
        exit;
    }

    try {
        // Check if email already exists
        $stmt = $db->prepare("SELECT id FROM kullanicilar WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Bu e-posta adresi zaten kayıtlı.']);
            exit;
        }

        // Insert new user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO kullanicilar (ad, email, sifre, danisman_id) VALUES (?, ?, ?, ?)");
        
        if ($stmt->execute([$firstname . ' ' . $lastname, $email, $hashed_password, $danisman_id])) {
            echo json_encode(['status' => 'success', 'message' => 'Kayıt başarıyla tamamlandı. Giriş yapabilirsiniz.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Kayıt sırasında bir hata oluştu.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz istek!']);
}
?> 