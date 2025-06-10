ALTER TABLE kullanicilar
ADD COLUMN danisman_id INT,
ADD FOREIGN KEY (danisman_id) REFERENCES ogretmenler(id); 