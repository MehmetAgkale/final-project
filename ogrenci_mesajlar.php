<?php
session_start();
include "db.php";

// Debug session
echo "<!-- Debug: Session contents: " . print_r($_SESSION, true) . " -->";

if (!isset($_SESSION['user_id'])) {
    echo "<!-- Debug: No user_id in session -->";
    header("Location: login.php");
    exit;
}

$ogrenci_id = $_SESSION['user_id'];
echo "<!-- Debug: Student ID = " . $ogrenci_id . " -->";

// Get student info
$student = $db->prepare("SELECT * FROM kullanicilar WHERE id = ?");
$student->execute([$ogrenci_id]);
$student_data = $student->fetch(PDO::FETCH_ASSOC);

if (!$student_data) {
    echo "<!-- Debug: Student not found in kullanicilar table -->";
    die("Öğrenci bulunamadı.");
}

// Let's check the mesajlar table structure
$table_info = $db->query("DESCRIBE mesajlar")->fetchAll(PDO::FETCH_ASSOC);
echo "<!-- Debug: Mesajlar table structure: " . print_r($table_info, true) . " -->";

// Öğrenciye gelen tüm mesajları çek
$query = "
    SELECT m.*, o.ad_soyad AS ogretmen_adi
    FROM mesajlar m
    LEFT JOIN ogretmenler o ON m.ogretmen_id = o.id
    WHERE m.ogrenci_id = ?
    ORDER BY m.created_at DESC
";
echo "<!-- Debug: Query = " . $query . " -->";
echo "<!-- Debug: Parameters = [" . $ogrenci_id . "] -->";

try {
    $stmt = $db->prepare($query);
    if (!$stmt) {
        throw new PDOException("Query preparation failed: " . print_r($db->errorInfo(), true));
    }
    
    $result = $stmt->execute([$ogrenci_id]);
    if (!$result) {
        throw new PDOException("Query execution failed: " . print_r($stmt->errorInfo(), true));
    }
    
    $mesajlar = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<!-- Debug: Query executed successfully -->";
    echo "<!-- Debug: Number of messages = " . count($mesajlar) . " -->";
    echo "<!-- Debug: Messages data: " . print_r($mesajlar, true) . " -->";
    
} catch (PDOException $e) {
    echo "<!-- Debug: Error occurred: " . $e->getMessage() . " -->";
    $mesajlar = [];
}

// Debug: Check if there are any messages at all
try {
    $all_messages = $db->query("SELECT COUNT(*) FROM mesajlar")->fetchColumn();
    echo "<!-- Debug: Total messages in database = " . $all_messages . " -->";
    
    // Check messages for this specific user
    $user_messages = $db->prepare("SELECT COUNT(*) FROM mesajlar WHERE ogrenci_id = ?");
    $user_messages->execute([$ogrenci_id]);
    $count = $user_messages->fetchColumn();
    echo "<!-- Debug: Messages for this user = " . $count . " -->";
    
    // Let's check the actual data
    $debug_query = $db->prepare("
        SELECT m.*, k.ad as ogrenci_adi, o.ad_soyad as ogretmen_adi 
        FROM mesajlar m 
        LEFT JOIN kullanicilar k ON m.ogrenci_id = k.id 
        LEFT JOIN ogretmenler o ON m.ogretmen_id = o.id
        WHERE m.ogrenci_id = ?
    ");
    $debug_query->execute([$ogrenci_id]);
    $debug_messages = $debug_query->fetchAll(PDO::FETCH_ASSOC);
    echo "<!-- Debug: Raw message data = " . print_r($debug_messages, true) . " -->";
} catch (PDOException $e) {
    echo "<!-- Debug: Error in debug queries: " . $e->getMessage() . " -->";
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Mesajlarım</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
            background-color: #f8f9fa;
        }
        .message-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 15px;
            padding: 15px;
        }
        .message-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .message-content {
            color: #333;
            line-height: 1.5;
        }
        .message-time {
            color: #6c757d;
            font-size: 0.9em;
        }
        .status-badge {
            font-size: 0.8em;
            padding: 3px 8px;
            border-radius: 12px;
        }
        .status-gonderildi {
            background-color: #e3f2fd;
            color: #1976d2;
        }
        .status-okundu {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        .status-akademisyen {
            background-color: #e8eaf6;
            font-weight: bold;
            color:rgb(17, 20, 161);
        }
        .status-ogrenci, .status-okundu {
            background-color: #e8eaf6;
            font-weight: bold;
            color: #c2185b;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>📬 Akademisyenlerden Gelen Mesajlar</h2>
            <div>
                <a href="index.php" class="btn btn-primary">Anasayfa</a>
                <a href="ogrenci_mesaj_gonder.php" class="btn btn-primary">Yeni Mesaj Gönder</a>
            </div>
        </div>

        <?php if (empty($mesajlar)): ?>
            <div class="alert alert-info">
                Henüz hiç mesaj bulunmuyor.
            </div>
        <?php else: ?>
            <?php foreach ($mesajlar as $mesaj): ?>
                <div class="message-card">
                    <div class="message-header">
                        <strong><?php echo htmlspecialchars($mesaj['ogretmen_adi']); ?></strong>
                        <span class="status-badge status-<?php echo $mesaj['durum']; ?>">
                            <?php echo $mesaj['durum'] === 'akademisyen' ? 'Gelen' : 'Giden'; ?>
                        </span>
                    </div>
                    <div class="message-content">
                        <h5><?php echo htmlspecialchars($mesaj['baslik']); ?></h5>
                        <p><?php echo nl2br(htmlspecialchars($mesaj['mesaj'])); ?></p>
                    </div>
                    <div class="message-time">
                        <?php echo date('d.m.Y H:i', strtotime($mesaj['created_at'])); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // JavaScript debugging
        console.log('Debug Information:');
        console.log('Session:', <?php echo json_encode($_SESSION); ?>);
        console.log('User ID:', <?php echo json_encode($ogrenci_id); ?>);
        console.log('User Data:', <?php echo json_encode($student_data); ?>);
        console.log('Messages:', <?php echo json_encode($mesajlar); ?>);
        console.log('Table Structure:', <?php echo json_encode($table_info); ?>);
    </script>
</body>
</html>
