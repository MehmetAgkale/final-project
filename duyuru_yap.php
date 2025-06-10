<?php
session_start();
include "db.php";

if (!isset($_SESSION['ogretmen_id'])) {
    header("Location: login.php");
    exit;
}

$ogretmen_id = $_SESSION['ogretmen_id'];

// Get teacher info
$teacher = $db->prepare("SELECT * FROM ogretmenler WHERE id = ?");
$teacher->execute([$ogretmen_id]);
$teacher_data = $teacher->fetch(PDO::FETCH_ASSOC);

if (!$teacher_data) {
    die("Öğretmen bulunamadı.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $baslik = trim($_POST['baslik'] ?? "");
    $icerik = trim($_POST['icerik'] ?? "");
    $son_gecerlilik_tarihi = !empty($_POST['son_gecerlilik_tarihi']) ? $_POST['son_gecerlilik_tarihi'] : null;

    if (!$baslik || !$icerik) {
        $error = "Lütfen başlık ve içerik alanlarını doldurun.";
    } else {
        try {
            $stmt = $db->prepare("INSERT INTO duyurular (ogretmen_id, baslik, icerik, son_gecerlilik_tarihi, tarih) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$ogretmen_id, $baslik, $icerik, $son_gecerlilik_tarihi]);
            $success = "Duyuru başarıyla oluşturuldu.";
        } catch (PDOException $e) {
            $error = "Duyuru oluşturulurken bir hata oluştu: " . $e->getMessage();
        }
    }
}

// Get teacher's announcements
$duyurular = $db->prepare("
    SELECT d.*, o.ad_soyad as ogretmen_adi 
    FROM duyurular d 
    JOIN ogretmenler o ON d.ogretmen_id = o.id 
    WHERE d.ogretmen_id = ? 
    ORDER BY d.tarih DESC
");
$duyurular->execute([$ogretmen_id]);
$duyurular_listesi = $duyurular->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Duyuru Yap</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .announcement-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 15px;
            padding: 15px;
        }
        .announcement-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .announcement-content {
            color: #333;
            line-height: 1.5;
        }
        .announcement-time {
            color: #6c757d;
            font-size: 0.9em;
        }
        .status-badge {
            font-size: 0.8em;
            padding: 3px 8px;
            border-radius: 12px;
        }
        /* .status-aktif {
            background-color: #e8f5e9;
            color: #2e7d32;
        } */
        .status-pasif {
            background-color: #ffebee;
            color: #c62828;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4">📢 Duyuru Yap</h2>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" class="mb-5">
            <div class="form-group">
                <label for="baslik">Başlık</label>
                <input type="text" class="form-control" id="baslik" name="baslik" required>
            </div>

            <div class="form-group">
                <label for="icerik">İçerik</label>
                <textarea class="form-control" id="icerik" name="icerik" rows="5" required></textarea>
            </div>

            <!-- <div class="form-group">
                <label for="son_gecerlilik_tarihi">Son Geçerlilik Tarihi (Opsiyonel)</label>
                <input type="datetime-local" class="form-control" id="son_gecerlilik_tarihi" name="son_gecerlilik_tarihi">
            </div> -->

            <button type="submit" class="btn btn-primary">Duyuru Yayınla</button>
            <a href="panel.php" class="btn btn-secondary">Panele Dön</a>
        </form>

        <h3 class="mb-4">Duyurularım</h3>
        <?php if (empty($duyurular_listesi)): ?>
            <div class="alert alert-info">
                Henüz hiç duyuru yapmadınız.
            </div>
        <?php else: ?>
            <?php foreach ($duyurular_listesi as $duyuru): ?>
                <div class="announcement-card">
                    <div class="announcement-header">
                        <h4><?php echo htmlspecialchars($duyuru['baslik']); ?></h4>
                    
                    </div>
                    <div class="announcement-content">
                        <?php echo nl2br(htmlspecialchars($duyuru['icerik'])); ?>
                    </div>
                    <div class="announcement-time">
                        Yayınlanma: <?php echo date('d.m.Y H:i', strtotime($duyuru['tarih'])); ?>
                        <?php if ($duyuru['son_gecerlilik_tarihi']): ?>
                            <br>Son Geçerlilik: <?php echo date('d.m.Y H:i', strtotime($duyuru['son_gecerlilik_tarihi'])); ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html> 