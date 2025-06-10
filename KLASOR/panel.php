<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_panel_login.php");
    exit;
}
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
        <a href="#"><i class="fas fa-book"></i> Görüşme saatlerim </a>
        <a href="#"><i class="fas fa-upload"></i> Duyuru Yap</a>
        <a href="#"><i class="fas fa-envelope"></i> Gelen Mesajlar</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Çıkış</a>
    </div>
    <nav class="navbar">
        <span class="h5">👩‍🏫 Hoş geldiniz, <strong><?php echo $_SESSION['admin_name']; ?></strong></span>
    </nav>
    <div class="content animate__animated animate__fadeIn">
        <h2 class="mb-4">Kontrol Paneli</h2>
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card shadow p-3">
                    <h5><i class="fas fa-book text-primary"></i> Beklenen Görüşmelerim</h5>
                    <p>3 beklenen görüşme</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow p-3">
                    <h5><i class="fas fa-envelope text-warning"></i> Mesajlar</h5>
                    <p>1 yeni mesaj</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
