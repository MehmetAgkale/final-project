<?php
session_start();
include "db.php";

if (!isset($_SESSION['ogretmen_id'])) {
    header("Location: login.php");
    exit;
}

$ogretmen_id = $_SESSION['ogretmen_id'];

// Get pending meetings count
$meetings_query = $db->prepare("SELECT COUNT(*) FROM rezervasyonlar WHERE ogretmen_id = ? AND durum = 'beklemede'");
$meetings_query->execute([$ogretmen_id]);
$pending_meetings = $meetings_query->fetchColumn();

// Get unread messages count
$messages_query = $db->prepare("SELECT COUNT(*) FROM mesajlar WHERE ogretmen_id = ? AND durum = 'ogrenci'");
$messages_query->execute([$ogretmen_id]);
$new_messages = $messages_query->fetchColumn();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Öğretmen Paneli</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/fontawesome-all.min.css" rel="stylesheet">
    <link href="assets/css/animate.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            height: 100vh;
            background-color: #343a40;
            padding-top: 20px;
            position: fixed;
            width: 250px;
        }
        .sidebar a {
            color: white;
            padding: 15px;
            display: block;
            text-decoration: none;
            transition: 0.3s;
        }
        .sidebar a:hover {
            background-color: #495057;
            padding-left: 25px;
        }
        .content {
            margin-left: 260px;
            padding: 30px;
        }
        .card:hover {
            transform: scale(1.03);
            transition: 0.3s;
        }
        .navbar {
            margin-left: 250px;
            background-color: #fff;
            padding: 10px 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="sidebar animate__animated animate__fadeInLeft">
        <a href="#"><i class="fas fa-home"></i> Ana Sayfa</a>
        <a href="ogretmen_rezervasyon_onay.php"><i class="fas fa-calendar-check"></i> Rezervasyon Talepleri</a>
        <a href="duyuru_yap.php"><i class="fas fa-bullhorn"></i> Duyuru Yap</a>
        <a href="duyurular.php"><i class="fas fa-bell"></i> Duyurular</a>
        <a href="ogretmen_mesajlar.php"><i class="fas fa-envelope"></i> Mesajlar</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Çıkış</a>
    </div>
    <nav class="navbar">
        <span class="h5">👩‍🏫 Hoş geldiniz, <strong><?php echo $_SESSION['ogretmen_name']; ?></strong></span>
    </nav>
    <div class="content animate__animated animate__fadeIn">
        <h2 class="mb-4">Kontrol Paneli</h2>
        <div class="row">
            <div class="col-md-4 mb-3">
                <a href="ogretmen_rezervasyon_onay.php" class="text-decoration-none">
                    <div class="card shadow p-3">
                        <h5><i class="fas fa-book text-primary"></i> Beklenen Görüşmelerim</h5>
                        <p><?php echo $pending_meetings; ?> beklenen görüşme</p>
                    </div>
                </a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="ogretmen_mesajlar.php" class="text-decoration-none">
                    <div class="card shadow p-3">
                        <h5><i class="fas fa-envelope text-warning"></i> Mesajlar</h5>
                        <p><?php echo $new_messages; ?> yeni mesaj</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
