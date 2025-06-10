<?php
session_start();
include "db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_panel_login.php");
    exit;
}

// Öğrenci listesini çek (sadece "ad" sütunu var)
$ogrenciler = $db->query("SELECT id, ad, email FROM kullanicilar")->fetchAll(PDO::FETCH_ASSOC);

// Mesaj gönderme işlemi
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ogretmen_id = $_SESSION['admin_id'];
    $baslik = trim($_POST['baslik'] ?? "");
    $mesaj = trim($_POST['mesaj'] ?? "");
    $hedef_ogrenciler = $_POST['hedef_ogrenciler'] ?? [];

    if (!$baslik || !$mesaj || empty($hedef_ogrenciler)) {
        $_SESSION['mesaj_hata'] = "Lütfen tüm alanları doldurun ve en az bir öğrenci seçin.";
    } else {
        if (in_array("all", $hedef_ogrenciler)) {
            $query = $db->query("SELECT id FROM kullanicilar");
            $ogrenciler = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($ogrenciler as $ogrenci) {
                $stmt = $db->prepare("INSERT INTO mesajlar (ogretmen_id, ogrenci_id, baslik, mesaj, tarih, durum) VALUES (?, ?, ?, ?, NOW(), ?)");
                $stmt->execute([$ogretmen_id, $ogrenci['id'], $baslik, $mesaj, 'gonderildi']);
            }
        } else {
            foreach ($hedef_ogrenciler as $ogrenci_id) {
                $stmt = $db->prepare("INSERT INTO mesajlar (ogretmen_id, ogrenci_id, baslik, mesaj, tarih, durum) VALUES (?, ?, ?, ?, NOW(), ?)");
                $stmt->execute([$ogretmen_id, $ogrenci_id, $baslik, $mesaj, 'gonderildi']);
            }
        }
        $_SESSION['mesaj_basarili'] = "Mesaj(lar) başarıyla gönderildi.";
    }
    header("Location: messages.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesaj Gönder</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #dbeafe, #e0f2fe);
            font-family: 'Poppins', sans-serif;
        }
        .container {
            max-width: 800px;
            margin: 80px auto;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-in-out;
        }
        h3 {
            font-weight: 600;
            margin-bottom: 30px;
            color: #1d4ed8;
        }
        label {
            font-weight: 500;
            color: #1e293b;
        }
        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            transition: border-color 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
        }
        .btn-primary {
            background-color: #2563eb;
            border: none;
            border-radius: 10px;
            padding: 10px 25px;
            font-weight: 500;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .btn-primary:hover {
            background-color: #1d4ed8;
            transform: translateY(-2px);
        }
        .alert {
            border-radius: 10px;
            padding: 15px;
        }
        .checkbox-list {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        .checkbox-item {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            background-color: #f1f5f9;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            user-select: none;
            transition: all 0.2s;
        }
        .checkbox-item:hover {
            background-color: #e2e8f0;
        }
        .checkbox-item input[type="checkbox"] {
            margin-right: 8px;
        }
        .checkbox-item.checked {
            background-color: #2563eb;
            color: white;
            font-weight: 600;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="container">
        <h3>📬 Mesaj Gönder</h3>
        <?php if (!empty($_SESSION['mesaj_hata'])) { echo '<div class="alert alert-danger">' . $_SESSION['mesaj_hata'] . '</div>'; unset($_SESSION['mesaj_hata']); } ?>
        <?php if (!empty($_SESSION['mesaj_basarili'])) { echo '<div class="alert alert-success">' . $_SESSION['mesaj_basarili'] . '</div>'; unset($_SESSION['mesaj_basarili']); } ?>
        <form method="POST">
            <div class="mb-4">
                <label class="form-label">Öğrenci(ler)</label>
                <div class="checkbox-list">
                    <div class="checkbox-item">
                        <input type="checkbox" id="all" name="hedef_ogrenciler[]" value="all">
                        <label for="all">Tüm Öğrenciler</label>
                    </div>
                    <?php foreach ($ogrenciler as $ogrenci): ?>
                        <div class="checkbox-item">
                            <input type="checkbox" id="ogrenci_<?= $ogrenci['id'] ?>" name="hedef_ogrenciler[]" value="<?= $ogrenci['id'] ?>">
                            <label for="ogrenci_<?= $ogrenci['id'] ?>">
                                <?= htmlspecialchars($ogrenci['ad']) ?><br>
                                <small><?= htmlspecialchars($ogrenci['email']) ?></small>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="mb-3">
                <label for="baslik" class="form-label">Başlık</label>
                <input type="text" name="baslik" id="baslik" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="mesaj" class="form-label">Mesaj</label>
                <textarea name="mesaj" id="mesaj" rows="6" class="form-control" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Gönder</button>
        </form>
    </div>
</body>
</html>
