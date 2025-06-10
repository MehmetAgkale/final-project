<?php
include "db.php";

try {
    $sql = "ALTER TABLE rezervasyonlar ADD COLUMN ogretmen_notu TEXT AFTER durum";
    $db->exec($sql);
    echo "ogretmen_notu column added successfully!";
} catch(PDOException $e) {
    if ($e->getCode() == '42S21') {
        echo "Column already exists.";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
?> 