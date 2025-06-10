<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ALKÜ REZERVASYON</title>
    <link rel="shortcut icon" href="assets/images/logo/fav.png">
    <link rel="apple-touch-icon" href="assets/images/logo/fav.png">
    <link rel="stylesheet" href="assets/css/line-awesome.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/css/slick.css">
    <link rel="stylesheet" href="assets/css/animate.min.css">
    <link rel="stylesheet" href="assets/css/odometer.css">
    <link rel="stylesheet" href="assets/css/splitting.css">
    <link rel="stylesheet" href="assets/css/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
    <div id="loading">
        <div id="loading-center">
            <div id="loading-center-absolute">
                <span class="loader"></span>
            </div>
        </div>
    </div>
    <div class="sidebar-overlay"></div>
    <div class="header-main-area--three">
        <div class="header--two" id="header">
            <div class="container">
                <div class="header-wrapper d-flex justify-content-between align-items-center flex-row">
                    <i class="fa-sharp fa-solid fa-bars-staggered ham__menu" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample"></i>
                    <div class="header-menu-wrapper align-items-center d-flex">
                        <div class="logo-wrapper">
                            <a href="index.php" class="normal-logo" id="normal-logo"><img src="assets/images/logo/logo.png" alt="..."></a>
                        </div>
                    </div>
                    <div class="menu-list-wrapper">
                        <ul class="main-menu">
                            <li class="links--wrap"><a class='link text--black active' href="index3.html">Anasayfa</a></li>
                            <li class="links--wrap"><a class="link text--black" href="gorusme_ayarla.php">REZERVASYON</a></li>
                            <li class="links--wrap"><a class="link text--black" href="akademik_personeller.html">AKADEMİSYENLER </a></li>
                            <li class="links--wrap"><a class="link text--black" href="duyurular.php">DUYURULAR</a></li>
                            <!-- <li><a class="link text--black" href="#">İLETİŞİM<Main></Main></a></li> -->
                            <?php if (isset($_SESSION['ogretmen_id'])): ?>
                                <li><a class="link text--black" href="ogretmen_mesajlar.php">MESAJLARIM</a></li>
                            <?php elseif (isset($_SESSION['user_id'])): ?>
                                <li><a class="link text--black" href="ogrenci_mesajlar.php">MESAJLARIM</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <ul class="login-lng d-flex align-items-center gap-3">
                        <?php if (isset($_SESSION['user_name'])): ?>
                            <li><a href="ogrenci_profilim.php"><span class="btn btn-outline--dark text--black border--black"><?php echo $_SESSION['user_name']; ?></span></a></li>
                        <?php else: ?>
                            <li><a class="btn btn-outline--dark text--black border--black" href="login.php">Giriş Yap</a></li>
                            <li><a class="btn btn-outline--dark text--black border--black" href="signin.php">Kayıt Ol</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="offcanvas offcanvas-start text-bg-light" tabindex="-1" id="offcanvasExample">
        <div class="offcanvas-header">
            <div class="logo">
                <div class="align-items-center d-flex">
                    <div class="logo-wrapper">
                        <a href="index.php" class="normal-logo" id="offcanvas-logo-normal">
                            <img src="assets/images/logo/logo.png" alt="">
                        </a>
                    </div>
                </div>
            </div>
            <button type="button" class="btn-close btn-close-black" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="user-info">
                <div class="user-thumb">

                </div>

            </div>
            <div class="sidebar-menu position-relative">
                <ul class="sidebar-menu-list">
                    <li class="sidebar-menu-list__item"><a href="index.php" class="sidebar-menu-list__link active"><span class="icon"><i class="fa-solid"></i></span><span class="text">Anasayfa</span></a></li>
                    <li class="sidebar-menu-list__item"><a href="gorusme_ayarla.php" class="sidebar-menu-list__link"><span class="icon"><i class="fa-regular"></i></span><span class="text">Rezervasyon</span></a></li>
                    <li class="sidebar-menu-list__item"><a href="duyurular.php" class="sidebar-menu-list__link"><span class="icon"><i class="fa-solid fa-bell"></i></span><span class="text">Duyurular</span></a></li>
                    <li class="sidebar-menu-list__item has-dropdown"><a href="contact.html" class="sidebar-menu-list__link"><span class="icon"><i class="fa-regular"></i></span><span class="text">İletişim</span></a></li>
                    <?php if (isset($_SESSION['ogretmen_id'])): ?>
                        <li class="sidebar-menu-list__item"><a href="ogretmen_mesajlar.php" class="sidebar-menu-list__link"><span class="icon"><i class="fa-solid"></i></span><span class="text">Mesajlar</span></a></li>
                    <?php elseif (isset($_SESSION['user_id'])): ?>
                        <li class="sidebar-menu-list__item"><a href="ogrenci_mesajlar.php" class="sidebar-menu-list__link"><span class="icon"><i class="fa-solid"></i></span><span class="text">Mesajlar</span></a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
    <section class="hero--three bg--img position-relative" style="background-image: url(assets/images/hero/hero-bgg3.png);">
        <div class="container position-relative">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-md-12 d-flex justify-content-center align-items-center">
                    <div class="hero-content--wrap d-flex flex-column gap--20 justify-content-center align-items-center">
                        <h1 class="text-center hero--title fw--400 wow animate__animated animate__fadeInUp splite-text" data-splitting data-wow-delay="0.2s"> Akademisyenlerinizle Daha Kolay Görüşme Ayarlayın.</h1>
                        <p class="text-center hero--subtitle fs--18 wow animate__animated animate__fadeInUp" data-wow-delay="0.3s">Online rezervasyon sistemi sayesinde akademisyenlerinizle randevu almak çok daha kolay! Takvim kontrolüyle vakit kaybetmeden uygun saatleri görüntüleyin ve tıkla randevunuzu oluşturun.</p>
                        <div class="btn--wrap wow animate__animated animate__fadeInUp" data-wow-delay="0.4s">
                            <button class="btn btn--base"><a href="gorusme_ayarla.php" </a><i class="fs--20 fa-solid fa-book-open"></i>Şimdi Rezervasyon Yap</a></button>
                        </div>
                    </div>
                </div> 
            </div>
        </div>
    </section>
    <!-- <section class="video--two  position-relative wow animate__animated animate__fadeInUp" data-wow-delay="0.5s">
        <div class="container bg--img h-100" style="background-image: url(assets/images/common/video-bg3.png);">
            <div class="popup-video-wrap">
                <div class="waves-block position-relative">
                    <div class="waves wave-1"></div>
                    <div class="waves wave-2"></div>
                    <div class="waves wave-3"></div>
                </div>
                <a class="play-video popup_video" data-fancybox="" href="https://www.youtube.com/watch?v=OEP6f6jRuT4" tabindex="0">
                    <i class="fa fa-play"></i>
                </a>
            </div>
        </div>
    </section> -->
    <section class="about--three py-100 @@bgClass">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-6 d-flex align-items-center d-none d-lg-flex align-items-center">
                    <div class="about-left-content">
                        <div class="about-thumb1">
                            <img src="assets/images/common/about-bg3.png" alt="...">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 d-flex flex-column justify-content-center">
                    <div class="row">
                        <div class="col-xl-10 col-lg-12">
                            <div class="section-content-3">
                                <p class="subtitle wow animate__animated animate__fadeInUp" data-wow-delay="0.2s">ALKÜ HAKKINDA</p>
                                <h6 class="title wow animate__animated animate__fadeInUp splite-text" data-splitting data-wow-delay="0.3s">Alanya Alaaddin Keykubat Üniversitesi (ALKÜ) Hakkında</h6>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="about-content--wrap">
                                <div class="about--content mb-5">
                                    <p class="mb-2">2015 yılında kurulan Alanya Alaaddin Keykubat Üniversitesi (ALKÜ), Akdeniz'in eşsiz güzellikleriyle çevrili Alanya'da yer alan dinamik ve yenilikçi bir devlet üniversitesidir. Geniş akademik yelpazesi, araştırma odaklı yaklaşımı ve modern kampüsü ile öğrencilere kaliteli bir eğitim sunmayı amaçlamaktadır.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <footer class="footer-area bg--black overflow--hidden bg--img" style="background-image: url(assets/images/common/footer-bgg.png);">
        <div class="footer-top">
            <div class="container">
                <div class="pt-100">
                    <div class="row gy-4 justify-content-center">
                        <div class="col-xl-4 col-sm-6">
                            <div class="footer-item">
                                <div class="footer-item--logo"><a href="index.php" class="footer-logo-normal"><img src="assets/images/logo/logo.png" alt=""></a></div>
                                <p class="footer-item--desc">© 2025 Alanya Alaaddin Keykubat Üniversitesi | Tüm Hakları Saklıdır. Eğitimde kalite, bilimde yenilik, gelecekte başarı!</p>
                                <ul class="social-list z--9 position-relative">
                                    <li class="social-list--item"><a href="https://www.facebook.com" class="social-list__link icon-wrapper"><div class="icon"><i class="fab fa-facebook-f"></i></div></a></li>
                                    <li class="social-list--item"><a href="https://www.twitter.com" class="social-list__link icon-wrapper active"><div class="icon"><i class="fa-brands fa-x-twitter"></i></div></a></li>
                                    <li class="social-list--item"><a href="https://www.linkedin.com" class="social-list__link icon-wrapper"><div class="icon"><i class="fab fa-linkedin-in"></i></div></a></li>
                                    <li class="social-list--item"><a href="https://www.pinterest.com" class="social-list__link icon-wrapper"><div class="icon"><i class="fab fa-instagram"></i></div></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-xl-2 col-sm-6">
                            
                        </div>
                        <div class="col-xl-4 col-sm-6">
                            <div class="footer-item">
                                <h5 class="footer-item--title">İletişim</h5>
                                <div class="footer-contact-info mb-3 d-flex justify-content-start align-items-baseline gap-3">
                                    <div class="d-flex justify-content-start align-items-center">
                                        <i class="text--base fa-regular fa-envelope"></i>
                                    </div>
                                    <div class="d-flex align-items-center flex-wrap gap-2">
                                        <p class="fw--400">Eposta Adresi</p>
                                        <p><a href="mailto:alku@alanya.edu.tr">alku@alanya.edu.tr</a></p>
                                    </div>
                                </div>
                                <div class="footer-contact-info mb-3 d-flex justify-content-start align-items-baseline gap-3">
                                    <div class="d-flex justify-content-start align-items-center">
                                        <i class="fa-solid fa-location-dot"></i>
                                    </div>
                                    <div class="d-flex align-items-center flex-wrap gap-2">
                                        <p class="fw--400">Konum</p>
                                        <p>Kestel , Alanya/Antalya</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bottom-footer pt-4 pb-5">
            <div class="container">
                <div class="row text-center gy-2">
                    <div class="col-lg-12">
                        <div class="bottom-footer-text"> &copy; 2025 Alanya Alaaddin Keykubat Üniversitesi </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <div class="scroll-top show">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" style="transition: stroke-dashoffset 10ms linear 0s; stroke-dasharray: 307.919, 307.919; stroke-dashoffset: 197.514;"></path>
        </svg>
    </div>
    <script src="assets/js/jquery-3.7.1.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/slick.min.js"></script>
    <script src="assets/js/odometer.min.js"></script>
    <script src="assets/js/jquery.appear.min.js"></script>
    <script src="assets/js/wow.min.js"></script>
    <script src="assets/js/splitting.min.js"></script>
    <script src="assets/js/jquery.magnific-popup.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        window.addEventListener("load", function () {
            document.getElementById("loading").style.display = "none";
        });
    </script>
</body>
</html>

