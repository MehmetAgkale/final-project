-- Create ogretmenler table
CREATE TABLE IF NOT EXISTS ogretmenler (
    id INT AUTO_INCREMENT PRIMARY KEY,
    unvan VARCHAR(255) NOT NULL,
    ad_soyad VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    sifre VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create kullanicilar table
CREATE TABLE IF NOT EXISTS kullanicilar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    sifre VARCHAR(255) NOT NULL,
    ad VARCHAR(255) NOT NULL,
    danisman_id INT DEFAULT NULL,
    FOREIGN KEY (danisman_id) REFERENCES ogretmenler(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- Create rezervasyonlar table
CREATE TABLE IF NOT EXISTS rezervasyonlar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ogrenci_id INT NOT NULL,
    ogretmen_id INT NOT NULL,
    tarih DATE NOT NULL,
    saat TIME NOT NULL,
    baslik VARCHAR(255) NOT NULL,
    mesaj VARCHAR(255) NOT NULL,
    durum VARCHAR(255) NOT NULL,
    ogretmen_notu TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL,
    katildi_mi TINYINT(1) DEFAULT 1,
    FOREIGN KEY (ogrenci_id) REFERENCES kullanicilar(id),
    FOREIGN KEY (ogretmen_id) REFERENCES ogretmenler(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create mesajlar table
CREATE TABLE IF NOT EXISTS mesajlar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ogretmen_id INT NOT NULL,
    ogrenci_id INT NOT NULL,
    baslik VARCHAR(255) NOT NULL,
    mesaj TEXT NOT NULL,
    tarih DATE NOT NULL,
    durum VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (ogretmen_id) REFERENCES ogretmenler(id),
    FOREIGN KEY (ogrenci_id) REFERENCES kullanicilar(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create duyurular table
CREATE TABLE IF NOT EXISTS duyurular (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ogretmen_id INT NOT NULL,
    baslik VARCHAR(255) NOT NULL,
    icerik TEXT NOT NULL,
    tarih DATETIME NOT NULL,
    son_gecerlilik_tarihi DATETIME DEFAULT NULL,
    durum ENUM('aktif','pasif','','') NOT NULL,
    FOREIGN KEY (ogretmen_id) REFERENCES ogretmenler(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create gorusme_saatleri table
CREATE TABLE IF NOT EXISTS gorusme_saatleri (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ogretmen_id INT NOT NULL,
    gun VARCHAR(20) NOT NULL,
    baslangic_saat TIME NOT NULL,
    bitis_saat TIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ogretmen_id) REFERENCES ogretmenler(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci; 