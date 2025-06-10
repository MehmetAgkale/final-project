<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$ogrenci_id = $_SESSION['user_id'];

// Get student info
$student = $db->prepare("SELECT * FROM kullanicilar WHERE id = ?");
$student->execute([$ogrenci_id]);
$student_data = $student->fetch(PDO::FETCH_ASSOC);

if (!$student_data) {
    die("Öğrenci bulunamadı.");
}

// Get all teachers
$teachers = $db->query("SELECT id, ad_soyad, email FROM ogretmenler")->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $baslik = trim($_POST['baslik'] ?? "");
    $mesaj = trim($_POST['mesaj'] ?? "");
    $hedef_ogretmenler = $_POST['hedef_ogretmenler'] ?? [];

    if (!$baslik || !$mesaj || empty($hedef_ogretmenler)) {
        $error = "Lütfen tüm alanları doldurun ve en az bir akademisyen seçin.";
    } else {
        try {
            if (in_array("all", $hedef_ogretmenler)) {
                // Send to all teachers
                $stmt = $db->prepare("INSERT INTO mesajlar (ogrenci_id, ogretmen_id, baslik, mesaj, tarih, durum, created_at) VALUES (?, ?, ?, ?, NOW(), 'ogrenci', NOW())");
                foreach ($teachers as $ogretmen) {
                    $stmt->execute([$ogrenci_id, $ogretmen['id'], $baslik, $mesaj]);
                }
            } else {
                // Send to selected teachers
                $stmt = $db->prepare("INSERT INTO mesajlar (ogrenci_id, ogretmen_id, baslik, mesaj, tarih, durum, created_at) VALUES (?, ?, ?, ?, NOW(), 'ogrenci', NOW())");
                foreach ($hedef_ogretmenler as $ogretmen_id) {
                    $stmt->execute([$ogrenci_id, $ogretmen_id, $baslik, $mesaj]);
                }
            }
            $success = "Mesaj(lar) başarıyla gönderildi.";
        } catch (PDOException $e) {
            $error = "Mesaj gönderilirken bir hata oluştu: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Mesaj Gönder</title>
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
        .teacher-list {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            background: white;
        }
        .teacher-item {
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        .teacher-item:last-child {
            border-bottom: none;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>📤 Akademisyenlere Mesaj Gönder</h2>
            <a href="index.php" class="btn btn-primary">Anasayfa</a>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Akademisyen(ler)</label>
                <div class="mb-2">
                    <input type="text" class="form-control" id="teacherSearch" placeholder="akademisyen ara...">
                </div>
                <div class="teacher-list">
                    <div class="teacher-item">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="all" name="hedef_ogretmenler[]" value="all">
                            <label class="form-check-label" for="all">Tüm Akademisyenler</label>
                        </div>
                    </div>
                    <?php foreach ($teachers as $teacher): ?>
                        <div class="teacher-item" data-name="<?php echo strtolower(htmlspecialchars($teacher['ad_soyad'])); ?>" data-email="<?php echo strtolower(htmlspecialchars($teacher['email'])); ?>">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="ogretmen_<?php echo $teacher['id']; ?>" 
                                       name="hedef_ogretmenler[]" value="<?php echo $teacher['id']; ?>">
                                <label class="form-check-label" for="ogretmen_<?php echo $teacher['id']; ?>">
                                    <?php echo htmlspecialchars($teacher['ad_soyad']); ?>
                                    <small class="text-muted">(<?php echo htmlspecialchars($teacher['email']); ?>)</small>
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="baslik">Başlık</label>
                <input type="text" class="form-control" id="baslik" name="baslik" required>
            </div>

            <div class="form-group">
                <label for="mesaj">Mesaj</label>
                <textarea class="form-control" id="mesaj" name="mesaj" rows="5" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Mesajı Gönder</button>
            <a href="ogrenci_mesajlar.php" class="btn btn-secondary">Mesajlara Dön</a>
        </form>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle "Select All" checkbox
        document.getElementById('all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="hedef_ogretmenler[]"]:not(#all)');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Handle individual checkboxes
        const individualCheckboxes = document.querySelectorAll('input[name="hedef_ogretmenler[]"]:not(#all)');
        individualCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const allCheckbox = document.getElementById('all');
                const allChecked = Array.from(individualCheckboxes).every(cb => cb.checked);
                allCheckbox.checked = allChecked;
            });
        });

        // Handle search filtering
        document.getElementById('teacherSearch').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const teacherItems = document.querySelectorAll('.teacher-item:not(:first-child)');
            
            teacherItems.forEach(item => {
                const name = item.dataset.name;
                const email = item.dataset.email;
                const matches = name.includes(searchTerm) || email.includes(searchTerm);
                item.style.display = matches ? '' : 'none';
            });
        });
    </script>
</body>
</html> 