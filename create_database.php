<?php
$servername = "localhost";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $sql = "CREATE DATABASE IF NOT EXISTS u2262482_alku";
    $conn->exec($sql);
    echo "Veritabanı başarıyla oluşturuldu!<br>";
    
    // Select the database
    $conn->exec("USE u2262482_alku");
    
    // Create tables
    $sql = file_get_contents('create_tables.sql');
    $conn->exec($sql);
    echo "Tablolar başarıyla oluşturuldu!<br>";
    
    // Insert sample data
    include "insert_sample_data.php";
    
} catch(PDOException $e) {
    die("Hata: " . $e->getMessage());
}
?> 