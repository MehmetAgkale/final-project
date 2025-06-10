<?php
session_start();
include "db.php";

// Check if user is logged in (either as student or teacher)
if (!isset($_SESSION['user_id']) && !isset($_SESSION['ogretmen_id'])) {
    header("Location: login.php");
    exit;
}

// Get user info based on session type
if (isset($_SESSION['user_id'])) {
    // Student session
    $user_id = $_SESSION['user_id'];
    $user_type = 'student';
    $user_query = $db->prepare("SELECT * FROM kullanicilar WHERE id = ?");
} else {
    // Teacher session
    $user_id = $_SESSION['ogretmen_id'];
    $user_type = 'teacher';
    $user_query = $db->prepare("SELECT * FROM ogretmenler WHERE id = ?");
}

$user_query->execute([$user_id]);
$user_data = $user_query->fetch(PDO::FETCH_ASSOC);

if (!$user_data) {
    die("Kullanıcı bulunamadı.");
}

// Get all active announcements
$duyurular = $db->query("
    SELECT d.*, o.ad_soyad as ogretmen_adi 
    FROM duyurular d 
    JOIN ogretmenler o ON d.ogretmen_id = o.id 
    WHERE d.durum = 'aktif' 
    AND (d.son_gecerlilik_tarihi IS NULL OR d.son_gecerlilik_tarihi > NOW())
    ORDER BY d.tarih DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Duyurular</title>
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
        .announcement-teacher {
            color: #007bff;
            font-weight: 500;
        }
        .search-box {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>📢 Duyurular</h2>
            <?php if (isset($_SESSION['ogretmen_id'])): ?>
                <a href="panel.php" class="btn btn-secondary">Panele Dön</a>
            <?php else: ?>
                <a href="index.php" class="btn btn-secondary">Ana Sayfaya Dön</a>
            <?php endif; ?>
        </div>

        <div class="search-box">
            <input type="text" class="form-control" id="searchInput" placeholder="Duyuru ara...">
        </div>

        <?php if (empty($duyurular)): ?>
            <div class="alert alert-info">
                Aktif duyuru bulunmuyor.
            </div>
        <?php else: ?>
            <?php foreach ($duyurular as $duyuru): ?>
                <div class="announcement-card" data-title="<?php echo strtolower(htmlspecialchars($duyuru['baslik'])); ?>" 
                     data-content="<?php echo strtolower(htmlspecialchars($duyuru['icerik'])); ?>"
                     data-teacher="<?php echo strtolower(htmlspecialchars($duyuru['ogretmen_adi'])); ?>">
                    <div class="announcement-header">
                        <h4><?php echo htmlspecialchars($duyuru['baslik']); ?></h4>
                        <span class="announcement-teacher">
                            <?php echo htmlspecialchars($duyuru['ogretmen_adi']); ?>
                        </span>
                    </div>
                    <div class="announcement-content">
                        <?php echo nl2br(htmlspecialchars($duyuru['icerik'])); ?>
                    </div>
                    <div class="announcement-time">
                        Yayınlanma: <?php echo date('d.m.Y H:i', strtotime($duyuru['tarih'])); ?>
                        
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle search filtering
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const announcements = document.querySelectorAll('.announcement-card');
            
            announcements.forEach(announcement => {
                const title = announcement.dataset.title;
                const content = announcement.dataset.content;
                const teacher = announcement.dataset.teacher;
                
                const matches = title.includes(searchTerm) || 
                              content.includes(searchTerm) || 
                              teacher.includes(searchTerm);
                
                announcement.style.display = matches ? '' : 'none';
            });
        });
    </script>
</body>
</html> 