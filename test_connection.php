<?php
include "db.php";

try {
    // Test database connection
    echo "Database connection successful!<br>";
    
    // Check if tables exist
    $tables = ['mesajlar', 'ogretmenler', 'kullanicilar'];
    foreach ($tables as $table) {
        $stmt = $db->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "Table '$table' exists<br>";
            
            // Count rows in table
            $count = $db->query("SELECT COUNT(*) FROM $table")->fetchColumn();
            echo "Number of rows in '$table': $count<br>";
            
            // Show table structure
            echo "Structure of '$table':<br>";
            $stmt = $db->query("DESCRIBE $table");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "- {$row['Field']}: {$row['Type']}<br>";
            }
            echo "<br>";
        } else {
            echo "Table '$table' does not exist!<br>";
        }
    }
    
    // Test query for messages
    echo "<br>Testing message query:<br>";
    $stmt = $db->query("SELECT * FROM mesajlar LIMIT 5");
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Found " . count($messages) . " messages<br>";
    foreach ($messages as $msg) {
        echo "Message ID: {$msg['id']}, From: {$msg['ogretmen_id']}, To: {$msg['ogrenci_id']}<br>";
    }
    
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>