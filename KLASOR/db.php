<?php
$host = "localhost";
$dbname = "u2262482_alku";
$username = "u2262482_admin";
$password = "160818Aa..@.";

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantısı başarısız: " . $e->getMessage());
}
?>
