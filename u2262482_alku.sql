-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 10 Haz 2025, 21:23:41
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `u2262482_alku`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `duyurular`
--

CREATE TABLE `duyurular` (
  `id` int(11) NOT NULL,
  `ogretmen_id` int(11) NOT NULL,
  `baslik` varchar(255) NOT NULL,
  `icerik` text NOT NULL,
  `tarih` datetime NOT NULL,
  `son_gecerlilik_tarihi` datetime DEFAULT NULL,
  `durum` enum('aktif','pasif','','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `duyurular`
--

INSERT INTO `duyurular` (`id`, `ogretmen_id`, `baslik`, `icerik`, `tarih`, `son_gecerlilik_tarihi`, `durum`) VALUES
(5, 1, 'DUYUR 5', 'ASD', '2025-06-10 21:26:31', NULL, 'aktif'),
(6, 1, 'DUYURU 6', 'TEST', '2025-06-10 21:27:47', NULL, 'aktif'),
(7, 1, 'DUYURU 7', 'DUYURU', '2025-06-10 21:27:53', NULL, 'aktif'),
(8, 3, 'Ahmet DUYURU', 'duyurdum', '2025-06-10 21:29:17', NULL, 'aktif'),
(9, 3, 'Ahmet duyuru 2', 'test', '2025-06-10 21:30:34', NULL, 'aktif');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kullanicilar`
--

CREATE TABLE `kullanicilar` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `sifre` varchar(255) NOT NULL,
  `ad` varchar(255) NOT NULL,
  `danisman_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `kullanicilar`
--

INSERT INTO `kullanicilar` (`id`, `email`, `sifre`, `ad`, `danisman_id`) VALUES
(1, '180254038@ogr.alanya.edu.tr', '$2y$10$XZ7HUyxWbEz0rhtO3yhgteS0JyZM3Vbz4ssFRkG8Rbz12COnCleRm', 'Mehmet Ağkale', 1),
(2, 'test@ogr.alanya.edu.tr', '$2y$10$XLjX4kfzqxW7kTio4xyhge2Fy0GrhR9X1ERMwgObA.PQ5Y4vQiRS6', 'test test', 3),
(3, 'ali.demir@alanya.edu.tr', '$2y$10$PGl2BLlTuxZKMgxses.UPOYVbQ0tPxl4qDhLAo.EIbccaOvDVwU7i', 'Ali Demir', 1),
(4, 'ayse.kaya@alanya.edu.tr', '$2y$10$0ixyUlB6breC1PBZ.cMgR.KuncR6KZlidYePbgWT8coc.KtsjenC2', 'Ayşe Kaya', 3),
(5, 'test2@ogr.alanya.edu.tr', '$2y$10$yV0sYK2NoHTzZKz2FS/AlucjzhpjY4ZiKvVDgQR6YTx13TttQCv6y', 'Test 2', 1),
(6, 'test3@ogr.alanya.edu.tr', '$2y$10$p8x0pYtODiKGmybt6w34IeRLx9pW5zp54xfxeL8s8Lt9l196TIBx.', 'test 3', 3);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `mesajlar`
--

CREATE TABLE `mesajlar` (
  `id` int(11) NOT NULL,
  `ogretmen_id` int(11) NOT NULL,
  `ogrenci_id` int(11) NOT NULL,
  `baslik` varchar(255) NOT NULL,
  `mesaj` text NOT NULL,
  `tarih` date NOT NULL,
  `durum` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `mesajlar`
--

INSERT INTO `mesajlar` (`id`, `ogretmen_id`, `ogrenci_id`, `baslik`, `mesaj`, `tarih`, `durum`, `created_at`) VALUES
(28, 1, 1, 'Danışıyorum 1', 'asd', '2025-06-10', 'akademisyen', '2025-06-10 20:48:31'),
(29, 1, 3, 'Danışıyorum 1', 'asd', '2025-06-10', 'akademisyen', '2025-06-10 20:48:31'),
(30, 1, 1, 'Danışılmam lazım 1', '1', '2025-06-10', 'ogrenci', '2025-06-10 20:49:35'),
(31, 1, 1, 'Danışıyorum 2', 'dasd', '2025-06-10', 'akademisyen', '2025-06-10 20:52:48'),
(32, 1, 3, 'Danışıyorum 2', 'dasd', '2025-06-10', 'akademisyen', '2025-06-10 20:52:48'),
(33, 3, 2, 'Ben geldim', 'merhaba', '2025-06-10', 'akademisyen', '2025-06-10 22:22:01'),
(34, 3, 4, 'Ben geldim', 'merhaba', '2025-06-10', 'akademisyen', '2025-06-10 22:22:01'),
(35, 3, 6, 'Ben geldim', 'merhaba', '2025-06-10', 'akademisyen', '2025-06-10 22:22:01');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `ogretmenler`
--

CREATE TABLE `ogretmenler` (
  `id` int(11) NOT NULL,
  `unvan` varchar(255) NOT NULL,
  `ad_soyad` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `sifre` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `ogretmenler`
--

INSERT INTO `ogretmenler` (`id`, `unvan`, `ad_soyad`, `email`, `sifre`, `created_at`) VALUES
(1, 'Dr. Öğr. Üyesi', 'Kübra Uyar', 'kubra.uyar@alanya.edu.tr', '$2y$10$8ZjCIr0rlWUhHNILcAaNU.EOOUcK/ZQ1vIVzcDmJjEKDhcxCIjRN.', '0000-00-00 00:00:00'),
(3, 'Doç. Dr.', 'Ahmet Yılmaz', 'ahmet.yilmaz@alanya.edu.tr', '$2y$10$UceXGOUkMKVIEE5NgMCaLeZvU0GkESeSONdiRXDyk2p8YB/c0jZkW', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `rezervasyonlar`
--

CREATE TABLE `rezervasyonlar` (
  `id` int(11) NOT NULL,
  `ogrenci_id` int(11) NOT NULL,
  `ogretmen_id` int(11) NOT NULL,
  `tarih` date NOT NULL,
  `saat` time NOT NULL,
  `baslik` varchar(255) NOT NULL,
  `mesaj` varchar(255) NOT NULL,
  `durum` varchar(255) NOT NULL,
  `ogretmen_notu` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `katildi_mi` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `rezervasyonlar`
--

INSERT INTO `rezervasyonlar` (`id`, `ogrenci_id`, `ogretmen_id`, `tarih`, `saat`, `baslik`, `mesaj`, `durum`, `ogretmen_notu`, `created_at`, `katildi_mi`) VALUES
(10, 1, 1, '2025-06-11', '11:30:00', 'Test 1', 'test', 'Onaylandı', 'kabul edildi. proje', '2025-06-10 22:06:59', 0),
(11, 1, 1, '2025-06-11', '12:00:00', 'test 2', 'test', 'Reddedildi', 'red', '2025-06-10 22:08:27', NULL),
(12, 1, 1, '2025-12-15', '14:00:00', 'test 3', 'test', 'Reddedildi', 'saati beğenmedim', '2025-06-10 22:13:01', NULL),
(13, 1, 1, '2025-02-14', '15:30:00', 'test 4', 'test', 'Onaylandı', 'saat fena değil', '2025-06-10 22:13:14', 1),
(14, 1, 1, '2025-05-04', '11:00:00', 'test 5 ', 'test', 'Onaylandı', 'test', '2025-06-10 22:13:28', 1),
(15, 1, 1, '2025-02-14', '12:30:00', 'test 6', 'test', 'Onaylandı', 'test', '2025-06-10 22:13:46', 0);

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `duyurular`
--
ALTER TABLE `duyurular`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `kullanicilar`
--
ALTER TABLE `kullanicilar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `danisman_id` (`danisman_id`);

--
-- Tablo için indeksler `mesajlar`
--
ALTER TABLE `mesajlar`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `ogretmenler`
--
ALTER TABLE `ogretmenler`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `rezervasyonlar`
--
ALTER TABLE `rezervasyonlar`
  ADD PRIMARY KEY (`id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `duyurular`
--
ALTER TABLE `duyurular`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Tablo için AUTO_INCREMENT değeri `kullanicilar`
--
ALTER TABLE `kullanicilar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Tablo için AUTO_INCREMENT değeri `mesajlar`
--
ALTER TABLE `mesajlar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Tablo için AUTO_INCREMENT değeri `ogretmenler`
--
ALTER TABLE `ogretmenler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `rezervasyonlar`
--
ALTER TABLE `rezervasyonlar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `kullanicilar`
--
ALTER TABLE `kullanicilar`
  ADD CONSTRAINT `kullanicilar_ibfk_1` FOREIGN KEY (`danisman_id`) REFERENCES `ogretmenler` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
