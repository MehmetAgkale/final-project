<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ogrenci_id = $_SESSION['user_id'];
    $ogretmen_id = $_POST['ogretmen_id'] ?? '';
    $tarih = $_POST['tarih'] ?? '';
    $saat = $_POST['saat'] ?? '';
    $mesaj = $_POST['mesaj'] ?? '';

    if (!$ogretmen_id || !$tarih || !$saat) {
        $_SESSION['rezervasyon_hata'] = "Lütfen tüm alanları doldurun.";
        header("Location: ogrenci_rezervasyon.php");
        exit;
    }

    // Çakışma kontrolü
    $check = $db->prepare("SELECT COUNT(*) FROM rezervasyonlar WHERE ogretmen_id = ? AND tarih = ? AND saat = ?");
    $check->execute([$ogretmen_id, $tarih, $saat]);
    $count = $check->fetchColumn();

    if ($count > 0) {
        $_SESSION['rezervasyon_hata'] = "Bu tarih ve saat için zaten bir rezervasyon mevcut.";
        header("Location: ogrenci_rezervasyon.php");
        exit;
    }

    // Kayıt
    $stmt = $db->prepare("INSERT INTO rezervasyonlar (ogrenci_id, ogretmen_id, tarih, saat, mesaj, durum) VALUES (?, ?, ?, ?, ?, 'Beklemede')");
    $result = $stmt->execute([$ogrenci_id, $ogretmen_id, $tarih, $saat, $mesaj]);

    if ($result) {
        $_SESSION['rezervasyon_basarili'] = "Rezervasyon başarıyla oluşturuldu.";
    } else {
        $_SESSION['rezervasyon_hata'] = "Bir hata oluştu. Lütfen tekrar deneyin.";
    }
    header("Location: ogrenci_rezervasyon.php");
    exit;
} else {
    header("Location: ogrenci_rezervasyon.php");
    exit;
}
