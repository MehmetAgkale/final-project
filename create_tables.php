<?php
include "db.php";

try {
    // Read the SQL file
    $sql = file_get_contents('create_tables.sql');
    
    // Execute the SQL statements
    $db->exec($sql);
    
    echo "Tablolar başarıyla oluşturuldu!";
} catch(PDOException $e) {
    die("Hata: " . $e->getMessage());
}
?> 