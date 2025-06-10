-- Create kullanicilar table
CREATE TABLE IF NOT EXISTS kullanicilar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ad VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    sifre VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create ogretmenler table
DROP TABLE IF EXISTS ogretmenler;
CREATE TABLE ogretmenler (
    id INT NOT NULL AUTO_INCREMENT,
    unvan VARCHAR(50) NOT NULL,
    ad_soyad VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    sifre VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create rezervasyonlar table
CREATE TABLE IF NOT EXISTS rezervasyonlar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ogrenci_id INT NOT NULL,
    ogretmen_id INT NOT NULL,
    tarih DATE NOT NULL,
    saat TIME NOT NULL,
    mesaj TEXT,
    durum ENUM('Beklemede', 'Onaylandı', 'Reddedildi') DEFAULT 'Beklemede',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ogrenci_id) REFERENCES kullanicilar(id),
    FOREIGN KEY (ogretmen_id) REFERENCES ogretmenler(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create mesajlar table
CREATE TABLE IF NOT EXISTS mesajlar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ogretmen_id INT NOT NULL,
    ogrenci_id INT NOT NULL,
    baslik VARCHAR(255) NOT NULL,
    mesaj TEXT NOT NULL,
    tarih DATETIME NOT NULL,
    durum ENUM('gonderildi', 'okundu') DEFAULT 'gonderildi',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ogretmen_id) REFERENCES ogretmenler(id),
    FOREIGN KEY (ogrenci_id) REFERENCES kullanicilar(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create gorusme_saatleri table
CREATE TABLE IF NOT EXISTS gorusme_saatleri (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ogretmen_id INT NOT NULL,
    gun VARCHAR(20) NOT NULL,
    baslangic_saat TIME NOT NULL,
    bitis_saat TIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ogretmen_id) REFERENCES ogretmenler(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 