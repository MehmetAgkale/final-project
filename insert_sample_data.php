<?php
include "db.php";

try {
    echo "<h2>Inserting Sample Data</h2>";
    
    // Insert sample teachers
    $teachers = [
        ['unvan' => 'Dr. Öğr. Üyesi', 'ad_soyad' => 'Kübra Uyar', 'email' => 'kubra.uyar@alanya.edu.tr', 'sifre' => password_hash('123456', PASSWORD_DEFAULT)],
        ['unvan' => 'Doç. Dr.', 'ad_soyad' => 'Ahmet Yılmaz', 'email' => 'ahmet.yilmaz@alanya.edu.tr', 'sifre' => password_hash('123456', PASSWORD_DEFAULT)]
    ];

    echo "<h3>Inserting Teachers:</h3>";
    $stmt = $db->prepare("INSERT INTO ogretmenler (unvan, ad_soyad, email, sifre) VALUES (?, ?, ?, ?)");
    foreach ($teachers as $teacher) {
        $stmt->execute([$teacher['unvan'], $teacher['ad_soyad'], $teacher['email'], $teacher['sifre']]);
        echo "Inserted teacher: {$teacher['ad_soyad']}<br>";
    }

    // Insert sample students
    $students = [
        ['ad' => 'Ali Demir', 'email' => 'ali.demir@alanya.edu.tr', 'sifre' => password_hash('123456', PASSWORD_DEFAULT), 'danisman_id' => 1],
        ['ad' => 'Ayşe Kaya', 'email' => 'ayse.kaya@alanya.edu.tr', 'sifre' => password_hash('123456', PASSWORD_DEFAULT), 'danisman_id' => 1]
    ];

    echo "<h3>Inserting Students:</h3>";
    $stmt = $db->prepare("INSERT INTO kullanicilar (ad, email, sifre, danisman_id) VALUES (?, ?, ?, ?)");
    foreach ($students as $student) {
        $stmt->execute([$student['ad'], $student['email'], $student['sifre'], $student['danisman_id']]);
        echo "Inserted student: {$student['ad']}<br>";
    }

    // Get the IDs of inserted users
    $teacher_ids = $db->query("SELECT id, ad_soyad FROM ogretmenler")->fetchAll(PDO::FETCH_ASSOC);
    $student_ids = $db->query("SELECT id, ad FROM kullanicilar")->fetchAll(PDO::FETCH_ASSOC);

    echo "<h3>Inserted User IDs:</h3>";
    echo "Teachers:<br>";
    print_r($teacher_ids);
    echo "<br>Students:<br>";
    print_r($student_ids);

    // Insert sample messages
    $messages = [
        [
            'ogretmen_id' => $teacher_ids[0]['id'],
            'ogrenci_id' => $student_ids[0]['id'],
            'baslik' => 'Ders Hakkında Soru',
            'mesaj' => 'Merhaba hocam, dersinizle ilgili birkaç sorum var. Müsait olduğunuzda görüşebilir miyiz?',
            'tarih' => date('Y-m-d H:i:s', strtotime('-2 days')),
            'durum' => 'gonderildi'
        ],
        [
            'ogretmen_id' => $teacher_ids[0]['id'],
            'ogrenci_id' => $student_ids[1]['id'],
            'baslik' => 'Ödev Teslimi',
            'mesaj' => 'Hocam, ödevimi teslim ettim. Kontrol edip geri bildirim verebilir misiniz?',
            'tarih' => date('Y-m-d H:i:s', strtotime('-1 day')),
            'durum' => 'okundu'
        ],
        [
            'ogretmen_id' => $teacher_ids[1]['id'],
            'ogrenci_id' => $student_ids[0]['id'],
            'baslik' => 'Proje Danışmanlığı',
            'mesaj' => 'Sayın hocam, projem hakkında danışmanlık almak istiyorum. Uygun bir zaman belirleyebilir miyiz?',
            'tarih' => date('Y-m-d H:i:s'),
            'durum' => 'gonderildi'
        ]
    ];

    echo "<h3>Inserting Messages:</h3>";
    $stmt = $db->prepare("INSERT INTO mesajlar (ogretmen_id, ogrenci_id, baslik, mesaj, tarih, durum) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($messages as $message) {
        $stmt->execute([
            $message['ogretmen_id'],
            $message['ogrenci_id'],
            $message['baslik'],
            $message['mesaj'],
            $message['tarih'],
            $message['durum']
        ]);
        echo "Inserted message from {$teacher_ids[array_search($message['ogretmen_id'], array_column($teacher_ids, 'id'))]['ad_soyad']} to {$student_ids[array_search($message['ogrenci_id'], array_column($student_ids, 'id'))]['ad']}<br>";
    }

    echo "<h3>Verification:</h3>";
    $message_count = $db->query("SELECT COUNT(*) FROM mesajlar")->fetchColumn();
    echo "Total messages in database: $message_count<br>";

    echo "<br>Sample data insertion completed successfully!";
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?> 