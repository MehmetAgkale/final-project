<?php
include "db.php";
$ogretmenler = $db->query("SELECT id, unvan, ad_soyad FROM ogretmenler ORDER BY unvan, ad_soyad")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Öğrenci Kaydı</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/portal-theme.css">
</head>
<body>
<div class="auth-shell">
    <aside class="auth-side">
        <div class="auth-panel p-4 p-xl-5 w-100">
            <div class="auth-side-copy">
                <div class="portal-badge mb-3">Yeni öğrenci hesabı</div>
                <h1 class="display-5 mb-3">Bitirme projesi sürecine sade ve güçlü bir başlangıç yapın.</h1>
                <p class="lead mb-0 text-white-50">Danışmanınızı seçin, hesabınızı oluşturun ve randevu ile mesaj akışını tek panelden yönetin.</p>
                <div class="auth-bullets">
                    <div class="auth-bullet">
                        <strong>Danışman odaklı başlangıç</strong>
                        <div class="small text-white-50 mt-1">Kayıtta akademisyeninizi seçerek sistemi size uygun şekilde başlatın.</div>
                    </div>
                    <div class="auth-bullet">
                        <strong>Takvim ve mesaj erişimi</strong>
                        <div class="small text-white-50 mt-1">Hesap açar açmaz görüşme ve yazışma ekranlarına erişin.</div>
                    </div>
                    <div class="auth-bullet">
                        <strong>Belgeli süreç takibi</strong>
                        <div class="small text-white-50 mt-1">Raporlar ve sunum dosyaları için dosya ekli iletişim hazır.</div>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <main class="auth-main">
        <div class="portal-card auth-card">
            <div class="portal-brand mb-4">
                <div class="portal-brand-mark">KY</div>
                <div class="portal-brand-text">
                    <span>Öğrenci Kaydı</span>
                    <strong>Yeni Hesap Oluştur</strong>
                </div>
            </div>

            <div id="message" class="mb-3" aria-live="polite"></div>

            <form id="registerForm" method="POST" action="register.php" class="portal-form">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ad</label>
                        <input class="form-control" name="firstname" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Soyad</label>
                        <input class="form-control" name="lastname" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">E-posta</label>
                    <input class="form-control" type="email" name="email" placeholder="ornek@alanya.edu.tr" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Danışman Akademisyen</label>
                    <select class="form-select" name="danisman_id" required>
                        <option value="">Seçiniz</option>
                        <?php foreach ($ogretmenler as $ogretmen): ?>
                            <option value="<?= (int) $ogretmen['id'] ?>">
                                <?= htmlspecialchars($ogretmen['unvan'] . ' ' . $ogretmen['ad_soyad']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label">Şifre</label>
                    <input class="form-control" id="password" name="password" type="password" placeholder="En az 6 karakter" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Kaydı Tamamla</button>
            </form>

            <div class="d-grid gap-2 mt-3">
                <a href="login.php" class="btn btn-outline-primary">Öğrenci Girişine Dön</a>
                <a href="admin_panel_login.php" class="btn btn-outline-secondary">Akademisyen Girişi</a>
            </div>
        </div>
    </main>
</div>

<script>
document.getElementById('registerForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('register.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const messageDiv = document.getElementById('message');
        messageDiv.textContent = data.message;
        messageDiv.className = data.status === 'success' ? 'alert alert-success mb-3' : 'alert alert-danger mb-3';

        if (data.status === 'success') {
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 1800);
        }
    })
    .catch(() => {
        const messageDiv = document.getElementById('message');
        messageDiv.textContent = 'Bir hata oluştu.';
        messageDiv.className = 'alert alert-danger mb-3';
    });
});
</script>
</body>
</html>
