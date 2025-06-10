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

// Get all students
$students = $db->query("SELECT id, ad, email FROM kullanicilar")->fetchAll(PDO::FETCH_ASSOC);

// Get students where this teacher is their advisor
$advisees = $db->prepare("SELECT id, ad, email FROM kullanicilar WHERE danisman_id = ?");
$advisees->execute([$ogretmen_id]);
$advisee_list = $advisees->fetchAll(PDO::FETCH_ASSOC);

// Pass data to JavaScript for debugging
echo "<script>
    console.log('Teacher ID:', " . json_encode($ogretmen_id) . ");
    console.log('Number of advisees:', " . json_encode(count($advisee_list)) . ");
    console.log('Advisees data:', " . json_encode($advisee_list) . ");
</script>";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $baslik = trim($_POST['baslik'] ?? "");
    $mesaj = trim($_POST['mesaj'] ?? "");
    $hedef_ogrenciler = $_POST['hedef_ogrenciler'] ?? [];

    // Pass form data to JavaScript for debugging
    echo "<script>
        console.log('Form submitted with data:');
        console.log('Baslik:', " . json_encode($baslik) . ");
        console.log('Mesaj:', " . json_encode($mesaj) . ");
        console.log('Selected students:', " . json_encode($hedef_ogrenciler) . ");
    </script>";

    if (!$baslik || !$mesaj || empty($hedef_ogrenciler)) {
        $error = "Lütfen tüm alanları doldurun ve en az bir öğrenci seçin.";
    } else {
        try {
            if (in_array("all", $hedef_ogrenciler)) {
                // Send to all students
                echo "<script>console.log('Sending to all students');</script>";
                $stmt = $db->prepare("INSERT INTO mesajlar (ogretmen_id, ogrenci_id, baslik, mesaj, tarih, durum, created_at) VALUES (?, ?, ?, ?, NOW(), 'akademisyen', NOW())");
                foreach ($students as $ogrenci) {
                    $stmt->execute([$ogretmen_id, $ogrenci['id'], $baslik, $mesaj]);
                }
            } elseif (in_array("advisees", $hedef_ogrenciler)) {
                // Send to advisees only
                echo "<script>
                    console.log('Sending to advisees only');
                    console.log('Number of advisees to receive message:', " . json_encode(count($advisee_list)) . ");
                </script>";
                
                if (empty($advisee_list)) {
                    $error = "Danışmanı olduğunuz öğrenci bulunmamaktadır.";
                } else {
                    $stmt = $db->prepare("INSERT INTO mesajlar (ogretmen_id, ogrenci_id, baslik, mesaj, tarih, durum, created_at) VALUES (?, ?, ?, ?, NOW(), 'akademisyen', NOW())");
                    
                    // Get selected student IDs from the form
                    $selected_student_ids = array_filter($hedef_ogrenciler, function($id) {
                        return $id !== 'advisees' && $id !== 'all';
                    });
                    
                    // Only send to advisees that are also selected
                    foreach ($advisee_list as $ogrenci) {
                        if (in_array($ogrenci['id'], $selected_student_ids)) {
                            echo "<script>console.log('Sending message to selected advisee:', " . json_encode($ogrenci['ad']) . ", '(ID:', " . json_encode($ogrenci['id']) . "))</script>";
                            $stmt->execute([$ogretmen_id, $ogrenci['id'], $baslik, $mesaj]);
                        }
                    }
                    $success = "Mesaj(lar) seçili danışmanlık öğrencilerine başarıyla gönderildi.";
                }
            } else {
                // Send to selected students
                echo "<script>console.log('Sending to selected students');</script>";
                $stmt = $db->prepare("INSERT INTO mesajlar (ogretmen_id, ogrenci_id, baslik, mesaj, tarih, durum, created_at) VALUES (?, ?, ?, ?, NOW(), 'akademisyen', NOW())");
                foreach ($hedef_ogrenciler as $ogrenci_id) {
                    $stmt->execute([$ogretmen_id, $ogrenci_id, $baslik, $mesaj]);
                }
                $success = "Mesaj(lar) başarıyla gönderildi.";
            }
        } catch (PDOException $e) {
            echo "<script>console.error('Database error:', " . json_encode($e->getMessage()) . ");</script>";
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
        .student-list {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            background: white;
        }
        .student-item {
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        .student-item:last-child {
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
            <h2>📤 Öğrencilere Mesaj Gönder</h2>
            <div>
                <a href="panel.php" class="btn btn-primary">Anasayfa</a>
            </div>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Öğrenci(ler)</label>
                <div class="mb-2">
                    <input type="text" class="form-control" id="studentSearch" placeholder="Öğrenci ara...">
                </div>
                <div class="student-list">
                    <div class="student-item">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="all" name="hedef_ogrenciler[]" value="all">
                            <label class="form-check-label" for="all">Tüm Öğrenciler</label>
                        </div>
                    </div>
                    <div class="student-item">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="advisees" name="hedef_ogrenciler[]" value="advisees">
                            <label class="form-check-label" for="advisees">Danışmanı Olduğum Öğrenciler</label>
                        </div>
                    </div>
                    <?php foreach ($students as $student): ?>
                        <div class="student-item" data-name="<?php echo strtolower(htmlspecialchars($student['ad'])); ?>" data-email="<?php echo strtolower(htmlspecialchars($student['email'])); ?>">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="ogrenci_<?php echo $student['id']; ?>" 
                                       name="hedef_ogrenciler[]" value="<?php echo $student['id']; ?>">
                                <label class="form-check-label" for="ogrenci_<?php echo $student['id']; ?>">
                                    <?php echo htmlspecialchars($student['ad']); ?>
                                    <small class="text-muted">(<?php echo htmlspecialchars($student['email']); ?>)</small>
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
            <a href="ogretmen_mesajlar.php" class="btn btn-secondary">Mesajlara Dön</a>
        </form>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // Get advisee IDs from PHP
        const adviseeIds = <?php echo json_encode(array_column($advisee_list, 'id')); ?>;
        console.log('Advisee IDs:', adviseeIds);

        // Handle "Select All" checkbox
        document.getElementById('all').addEventListener('change', function() {
            console.log('All students checkbox changed:', this.checked);
            const checkboxes = document.querySelectorAll('input[name="hedef_ogrenciler[]"]:not(#all):not(#advisees)');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            // Uncheck advisees if "All" is checked
            if (this.checked) {
                document.getElementById('advisees').checked = false;
            }
        });

        // Handle advisees checkbox
        document.getElementById('advisees').addEventListener('change', function() {
            console.log('Advisees checkbox changed:', this.checked);
            // Uncheck "All" if advisees is checked
            if (this.checked) {
                document.getElementById('all').checked = false;
                // Check only advisee checkboxes
                const individualCheckboxes = document.querySelectorAll('input[name="hedef_ogrenciler[]"]:not(#all):not(#advisees)');
                individualCheckboxes.forEach(checkbox => {
                    const studentId = parseInt(checkbox.value);
                    checkbox.checked = adviseeIds.includes(studentId);
                });
            } else {
                // Uncheck all individual checkboxes
                const individualCheckboxes = document.querySelectorAll('input[name="hedef_ogrenciler[]"]:not(#all):not(#advisees)');
                individualCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
            }
        });

        // Handle individual checkboxes
        const individualCheckboxes = document.querySelectorAll('input[name="hedef_ogrenciler[]"]:not(#all):not(#advisees)');
        individualCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                console.log('Individual checkbox changed:', this.id, this.checked);
                // Uncheck "All" and "Advisees" if any individual checkbox is checked
                if (this.checked) {
                    document.getElementById('all').checked = false;
                    document.getElementById('advisees').checked = false;
                }
                const allChecked = Array.from(individualCheckboxes).every(cb => cb.checked);
                document.getElementById('all').checked = allChecked;
            });
        });

        // Handle form submission
        document.querySelector('form').addEventListener('submit', function(e) {
            const selectedStudents = Array.from(document.querySelectorAll('input[name="hedef_ogrenciler[]"]:checked')).map(cb => cb.value);
            console.log('Form submitted with selected students:', selectedStudents);
            
            if (selectedStudents.includes('advisees')) {
                console.log('Sending to advisees only');
            } else if (selectedStudents.includes('all')) {
                console.log('Sending to all students');
            } else {
                console.log('Sending to selected individual students');
            }
        });

        // Handle search filtering
        document.getElementById('studentSearch').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            console.log('Searching for:', searchTerm);
            const studentItems = document.querySelectorAll('.student-item:not(:first-child):not(:nth-child(2))');
            
            studentItems.forEach(item => {
                const name = item.dataset.name;
                const email = item.dataset.email;
                const matches = name.includes(searchTerm) || email.includes(searchTerm);
                item.style.display = matches ? '' : 'none';
                console.log('Student item:', name, email, 'matches:', matches);
            });
        });
    </script>
</body>
</html> 