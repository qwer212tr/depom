<?php
session_start();
require_once __DIR__ . '/config/db.php'; // burada $pdo var
$db = $pdo;  // $pdo'yu $db olarak ata, böylece eski kod bozulmaz

$kayitBasarili = false;
$hataMesaji = '';

$isim = isset($_POST['isim']) ? trim($_POST['isim']) : '';
$soyisim = isset($_POST['soyisim']) ? trim($_POST['soyisim']) : '';
$kullanici_adi = isset($_POST['kullanici_adi']) ? trim($_POST['kullanici_adi']) : '';
$telefon = isset($_POST['telefon']) ? trim($_POST['telefon']) : '';
$mail = isset($_POST['mail']) ? trim($_POST['mail']) : '';
$gizli_soru = isset($_POST['gizli_soru']) ? trim($_POST['gizli_soru']) : '';
$gizli_cevap = isset($_POST['gizli_cevap']) ? trim($_POST['gizli_cevap']) : '';
$sifre = isset($_POST['sifre']) ? $_POST['sifre'] : '';
$sifre_tekrar = isset($_POST['sifre_tekrar']) ? $_POST['sifre_tekrar'] : '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $captcha_gelen = isset($_POST['captcha']) ? trim($_POST['captcha']) : '';
    $captcha_sess = isset($_SESSION['captcha_kod']) ? $_SESSION['captcha_kod'] : '';

    if (empty($captcha_gelen) || strcasecmp($captcha_gelen, $captcha_sess) !== 0) {
        $hataMesaji = "CAPTCHA kodu yanlış.";
    } else {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip_adresi = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip_adresi = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } else {
            $ip_adresi = $_SERVER['REMOTE_ADDR'];
        }

        $banKontrol = $db->prepare("SELECT * FROM ip_bans WHERE ip = :ip");
        $banKontrol->execute([':ip' => $ip_adresi]);
        if ($banKontrol->fetchColumn()) {
            $hataMesaji = "Bu IP adresi yasaklanmıştır. Kayıt yapılamaz.";
        } elseif ($sifre !== $sifre_tekrar) {
            $hataMesaji = "Şifreler uyuşmuyor.";
        } else {
            $stmt = $db->prepare("SELECT 1 FROM users WHERE mail = :email OR kullanici_adi = :kullanici_adi");
            $stmt->execute([':email' => $mail, ':kullanici_adi' => $kullanici_adi]);
            if ($stmt->fetchColumn()) {
                $hataMesaji = "Bu email veya kullanıcı adı ile zaten kayıtlı bir kullanıcı var.";
            } else {
                try {
                    $sifreHash = password_hash($sifre, PASSWORD_DEFAULT);
                    $gizli_cevap_hash = password_hash($gizli_cevap, PASSWORD_DEFAULT);
                    $bakiye = 0.00;

                    $stmt = $db->prepare("INSERT INTO users 
                        (isim, soyisim, kullanici_adi, telefon, mail, gizli_soru, gizli_cevap, sifre_hash, ip_adresi, rol, bakiye)
                        VALUES 
                        (:isim, :soyisim, :kullanici_adi, :telefon, :mail, :gizli_soru, :gizli_cevap, :sifre_hash, :ip_adresi, :rol, :bakiye)");

                    $stmt->execute([
                        ':isim' => $isim,
                        ':soyisim' => $soyisim,
                        ':kullanici_adi' => $kullanici_adi,
                        ':telefon' => $telefon,
                        ':mail' => $mail,
                        ':gizli_soru' => $gizli_soru,
                        ':gizli_cevap' => $gizli_cevap_hash,
                        ':sifre_hash' => $sifreHash,
                        ':ip_adresi' => $ip_adresi,
                        ':rol' => 'üye',
                        ':bakiye' => $bakiye
                    ]);

                    $kayitBasarili = true;

                } catch (PDOException $e) {
                    $hataMesaji = "Veritabanı hatası: " . $e->getMessage();
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/png" sizes="16x16" href="/new/app/models/favicon/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/new/app/models/favicon/favicon-32x32.png">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Kayıt Ol - Bi Organizasyon</title>
    <link rel="stylesheet" href="assets/globals.css" />
</head>
<body>

<div class="top-bar">
    <div class="logo"></div>
    <button class="hamburger-button" aria-label="Toggle menu">
      <span class="hamburger-icon"></span>
    </button>
    <nav id="main-navigation" class="main-nav">
        <?php include 'app/controllers/menu.php'; ?>
    </nav>
</div>

<form action="" method="post" style="max-width: 500px; margin: 80px auto; padding: 20px; background-color: #1c1c1c; border-radius: 8px; color: white; font-family: Arial, sans-serif;">
    <h2 style="text-align: center; margin-top: 80px;">Kayıt Ol</h2>

    <?php if ($kayitBasarili): ?>
        <div style="background-color: #28a745; color: white; padding: 10px; text-align: center; border-radius: 5px; margin-bottom: 15px;">
            Kayıt işleminiz başarıyla tamamlandı. Giriş yapabilirsiniz.
        </div>
    <?php elseif ($hataMesaji): ?>
        <div style="background-color: #dc3545; color: white; padding: 10px; text-align: center; border-radius: 5px; margin-bottom: 15px;">
            <?= htmlspecialchars($hataMesaji) ?>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <label for="isim">İsim:</label>
        <input type="text" id="isim" name="isim" value="<?= htmlspecialchars($isim) ?>" required>
    </div>

    <div class="form-group">
        <label for="soyisim">Soy İsim:</label>
        <input type="text" id="soyisim" name="soyisim" value="<?= htmlspecialchars($soyisim) ?>" required>
    </div>

    <div class="form-group">
        <label for="kullanici_adi">Kullanıcı Adı:</label>
        <input type="text" id="kullanici_adi" name="kullanici_adi" value="<?= htmlspecialchars($kullanici_adi) ?>" required>
    </div>

    <div class="form-group">
        <label for="telefon">Telefon:</label>
        <input type="tel" id="telefon" name="telefon" value="<?= htmlspecialchars($telefon) ?>" required pattern="[0-9]{10,15}" title="Sadece rakam giriniz.">
    </div>

    <div class="form-group">
        <label for="mail">E-posta:</label>
        <input type="email" id="mail" name="mail" value="<?= htmlspecialchars($mail) ?>" required>
    </div>

    <div class="form-group">
        <label for="gizli_soru">Gizli Soru:</label>
        <select id="gizli_soru" name="gizli_soru" required>
            <option value="">Seçiniz</option>
            <option value="annenizin_kizlik_soyadi" <?= ($gizli_soru === 'annenizin_kizlik_soyadi') ? 'selected' : '' ?>>Annenizin kızlık soyadı nedir?</option>
            <option value="ilk_evcil_hayvaniniz" <?= ($gizli_soru === 'ilk_evcil_hayvaniniz') ? 'selected' : '' ?>>İlk evcil hayvanınızın adı nedir?</option>
            <option value="ilk_araciniz_modeli" <?= ($gizli_soru === 'ilk_araciniz_modeli') ? 'selected' : '' ?>>İlk aracınızın modeli nedir?</option>
            <option value="dogdugunuz_sehir" <?= ($gizli_soru === 'dogdugunuz_sehir') ? 'selected' : '' ?>>Hangi şehirde doğdunuz?</option>
            <option value="babanizin_ortanca_ismi" <?= ($gizli_soru === 'babanizin_ortanca_ismi') ? 'selected' : '' ?>>Babanızın ortanca ismi nedir?</option>
            <option value="cocukluk_lakabiniz" <?= ($gizli_soru === 'cocukluk_lakabiniz') ? 'selected' : '' ?>>Çocukluk lakabınız nedir?</option>
        </select>
    </div>

    <div class="form-group">
        <label for="gizli_cevap">Gizli Cevap:</label>
        <input type="text" id="gizli_cevap" name="gizli_cevap" value="<?= htmlspecialchars($gizli_cevap) ?>" required>
    </div>

    <div class="form-group">
        <label for="sifre">Şifre:</label>
        <input type="password" id="sifre" name="sifre" required>
    </div>

    <div class="form-group">
        <label for="sifre_tekrar">Şifre Tekrar:</label>
        <input type="password" id="sifre_tekrar" name="sifre_tekrar" required>
    </div>
    <div class="form-group">
        <label for="captcha">Aşağıdaki kodu giriniz:</label>
        <img src="app/controllers/captcha.php" alt="CAPTCHA" style="margin: 6px 0; border: 1px solid #555;"><br>
        <input type="text" id="captcha" name="captcha" required autocomplete="off"
               style="width: 100%; padding: 8px; border-radius: 16px; background-color: #2a2a2a; color: white;">
    </div>
    <div class="button-group" style="margin-top: 15px;">
        <button type="button" id="rulesBtn" style="color: white;">Kayıt Kuralları</button>
        <button type="submit" id="submitBtn" style="color: white;" disabled>Kaydol</button>
    </div>
</form>

<div id="rulesModal" class="modal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; overflow:auto; background-color: rgba(0,0,0,0.7);">
    <div class="modal-content" style="background:#1c1c1c; margin:10% auto; padding:20px; border-radius:8px; max-width:500px; color:#000; position:relative;">
        <span class="close-btn" id="closeModal" style="position:absolute; top:10px; right:20px; font-size:28px; cursor:pointer;">&times;</span>
        <h3>1. Genel Hükümler</h3>
        <p>Bu web sitesi, kullanıcıların kayıt olmalarını ve sisteme giriş yapmalarını sağlar. Kullanıcılar, sisteme üye olmadan önce aşağıdaki kullanım koşullarını dikkatlice okumalıdır.</p>

        <h3>2. Üyelik</h3>
        <p>Üyelik işlemi, kullanıcı adı, e-posta adresi ve şifre belirleme işlemi ile yapılır. Üye, kişisel bilgilerini doğru ve güncel tutmakla yükümlüdür.</p>

        <h3>3. Şifre Güvenliği</h3>
        <p>Kullanıcı, şifresini üçüncü şahıslarla paylaşmamalı ve güvenliğini sağlamak için gerekli önlemleri almalıdır.</p>

        <h3>4. İletişim</h3>
        <p>Üye, kaydolduktan sonra web sitesi yönetimiyle iletişime geçebilir ve gerektiğinde şifre sıfırlama işlemleri gibi destek alabilir.</p>

        <button id="acceptRules" style="margin-top:15px; padding:10px 15px; cursor:pointer;">Kuralları Kabul Et</button>
    </div>
</div>

<?php include 'app/controllers/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const hamburgerButton = document.querySelector('.hamburger-button');
    const mainNav = document.getElementById('main-navigation');
    const dropdowns = document.querySelectorAll('.main-nav .dropdown > a');
    const submitBtn = document.getElementById('submitBtn');

    hamburgerButton.addEventListener('click', function() {
        mainNav.classList.toggle('active');
        hamburgerButton.classList.toggle('active');
    });

    dropdowns.forEach(dropdown => {
        dropdown.addEventListener('click', function(event) {
            if (window.innerWidth <= 768) {
                event.preventDefault();
                const parentLi = this.closest('.dropdown');
                parentLi.classList.toggle('active');
            }
        });
    });

    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            mainNav.classList.remove('active');
            hamburgerButton.classList.remove('active');
            document.querySelectorAll('.dropdown').forEach(d => d.classList.remove('active'));
        }
    });

    document.getElementById('rulesBtn').addEventListener('click', function() {
        document.getElementById('rulesModal').style.display = 'block';
    });

    document.getElementById('closeModal').addEventListener('click', function() {
        document.getElementById('rulesModal').style.display = 'none';
    });

    document.getElementById('acceptRules').addEventListener('click', function() {
        submitBtn.disabled = false;
        document.getElementById('rulesModal').style.display = 'none';
    });

    // Başlangıçta kayıt butonunu devre dışı bırak
    submitBtn.disabled = true;
});
</script>

</body>
</html>
