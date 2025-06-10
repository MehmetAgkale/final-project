<?php
include "db.php";

echo "<h2>Database Check</h2>";

// 1. Check current session
echo "<h3>Session Data:</h3>";
session_start();
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// 2. Check users table
echo "<h3>Users in Database:</h3>";
$users = $db->query("SELECT id, ad, email FROM kullanicilar")->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($users);
echo "</pre>";

// 3. Check messages table
echo "<h3>All Messages in Database:</h3>";
$messages = $db->query("
    SELECT m.*, k.ad as ogrenci_adi, o.ad_soyad as ogretmen_adi 
    FROM mesajlar m 
    LEFT JOIN kullanicilar k ON m.ogrenci_id = k.id 
    LEFT JOIN ogretmenler o ON m.ogretmen_id = o.id
")->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($messages);
echo "</pre>";

// 4. If we have a user_id in session, check their messages specifically
if (isset($_SESSION['user_id'])) {
    echo "<h3>Messages for Current User (ID: {$_SESSION['user_id']}):</h3>";
    $stmt = $db->prepare("
        SELECT m.*, k.ad as ogrenci_adi, o.ad_soyad as ogretmen_adi 
        FROM mesajlar m 
        LEFT JOIN kullanicilar k ON m.ogrenci_id = k.id 
        LEFT JOIN ogretmenler o ON m.ogretmen_id = o.id
        WHERE m.ogrenci_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $user_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($user_messages);
    echo "</pre>";
}

// 5. Check if sample data was inserted correctly
echo "<h3>Sample Data Check:</h3>";
$sample_data = $db->query("
    SELECT 'kullanicilar' as table_name, COUNT(*) as count FROM kullanicilar
    UNION ALL
    SELECT 'ogretmenler', COUNT(*) FROM ogretmenler
    UNION ALL
    SELECT 'mesajlar', COUNT(*) FROM mesajlar
")->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($sample_data);
echo "</pre>";
?> 