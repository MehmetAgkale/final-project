<?php
include "db.php";

try {
    $sql = "ALTER TABLE kullanicilar ADD COLUMN danisman_id INT, ADD FOREIGN KEY (danisman_id) REFERENCES ogretmenler(id)";
    $db->exec($sql);
    echo "danisman_id column added successfully!";
} catch(PDOException $e) {
    if ($e->getCode() == '42S21') {
        echo "Column already exists.";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
?> 