<?php

// Oturum başlatma (eğer henüz başlatılmadıysa)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Giriş kontrolü
if (!isset($_SESSION['kullanici_id'])) {
    header('Location: /login.php');
    exit;
}


// Veritabanı bağlantısı dosyasını dahil et
// Bu yolun sunucunuzdaki 'config/db.php' dosyasına göre doğru olduğundan emin olun.
require_once __DIR__ . '/../../config/db.php'; // PDO bağlantısı $pdo olarak geliyor

$errors = [];
$kayitBasarili = false;
$ip_adresi = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'BILINMEYEN_IP';

// IP ile kayıt kontrolü - Form gönderilmeden önce bile yapılabilir
// Eğer aynı IP'den birden fazla kaydı engellemek istiyorsanız bu kontrolü en başta yapıyoruz.
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM pubg_turnuva_kayit WHERE ip_adresi = ?");
    $stmt->execute([$ip_adresi]);
    $ipKayitSayisi = $stmt->fetchColumn();

    if ($ipKayitSayisi > 0) {
        $errors['ip'] = 'Bu IP adresiyle zaten bir kayıt yapılmıştır. Tekrar kayıt olamazsınız.';
    }
} catch (PDOException $e) {
    // Hata günlüğüne yazmak iyi bir uygulamadır, kullanıcıya göstermek yerine.
    // error_log("IP kontrolü sırasında veritabanı hatası: " . $e->getMessage());
    $errors['db'] = 'Sistem hatası oluştu. Lütfen daha sonra tekrar deneyin.';
}

// Form gönderildiyse ve IP ile ilgili bir hata yoksa formu işle
if ($_SERVER["REQUEST_METHOD"] === "POST" && empty($errors['ip'])) {
    // Gerekli alanların listesi
    $required_fields = ['oyuncu1', 'oyuncu2', 'oyuncu3', 'oyuncu4', 'ekip_tag', 'kaptan_tel', 'kaptan_mail'];

    foreach ($required_fields as $field) {
        // $_POST[$field] var mı diye kontrol et, yoksa boş string kullan ve boşlukları temizle
        $value = isset($_POST[$field]) ? trim($_POST[$field]) : '';
        if (empty($value)) {
            $errors[$field] = 'Bu alan zorunludur.';
        }
        // HTML özel karakterlerinden koru ve tekrar $_POST'a ata (temizlenmiş ve güvenli veri için)
        $_POST[$field] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    // Ekstra doğrulama kuralları
    if (isset($_POST['kaptan_mail']) && !filter_var($_POST['kaptan_mail'], FILTER_VALIDATE_EMAIL)) {
        $errors['kaptan_mail'] = 'Geçerli bir e-posta adresi giriniz.';
    }

    // Telefon numarası doğrulaması (daha esnek bir regex, sadece rakamlar, boşluk, -, +, () içerebilir)
    if (isset($_POST['kaptan_tel']) && !preg_match('/^\+?[0-9\s\-\(\)]+$/', $_POST['kaptan_tel'])) {
        $errors['kaptan_tel'] = 'Geçerli bir telefon numarası giriniz (sadece rakamlar, boşluk, -, +, () içerebilir).';
    }

    // Yedek oyuncu alanını temizle (zorunlu değil)
    $_POST['yedek'] = isset($_POST['yedek']) ? htmlspecialchars(trim($_POST['yedek']), ENT_QUOTES, 'UTF-8') : null;

    // Herhangi bir doğrulama hatası yoksa veritabanına kaydet
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO pubg_turnuva_kayit
                (oyuncu1, oyuncu2, oyuncu3, oyuncu4, yedek, ekip_tag, kaptan_tel, kaptan_mail, ip_adresi)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $success = $stmt->execute([
                $_POST['oyuncu1'],
                $_POST['oyuncu2'],
                $_POST['oyuncu3'],
                $_POST['oyuncu4'],
                $_POST['yedek'],
                $_POST['ekip_tag'],
                $_POST['kaptan_tel'],
                $_POST['kaptan_mail'],
                $ip_adresi
            ]);

            if ($success) {
                $kayitBasarili = true;
                // Formu tekrar göndermeyi önlemek için POST verilerini temizle
                $_POST = [];
            } else {
                $errors['db'] = 'Kayıt sırasında bir veritabanı hatası oluştu. Lütfen tekrar deneyin.';
            }
        } catch (PDOException $e) {
            // error_log("Veritabanı kayıt hatası: " . $e->getMessage());
            $errors['db'] = 'Kayıt sırasında beklenmeyen bir hata oluştu. Lütfen site yöneticisi ile iletişime geçin.';
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
    <title>Pubg Mobil Turnuva Kayıt Formu - Bi Organizasyon</title>
    <link rel="stylesheet" href="../../assets/globals.css" />
    <style>
        .form-message-container {
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .required {
            color: #ff4d4d; /* Kırmızı renk ile zorunlu alan işaretçisi */
            font-weight: bold;
        }
        /* Hata mesajlarının görünürlüğü için stil */
        .error-message {
            color: #e74c3c; /* Kırmızı hata rengi */
            font-size: 0.9em;
            margin-top: 5px;
            margin-bottom: 10px;
            display: block; /* Her hata mesajının ayrı satırda olmasını sağlar */
        }
    </style>
</head>
<body>

<!-- Top Bar -->
<div class="top-bar">
    <div class="logo"></div>
<button class="hamburger-button" aria-label="Toggle menu">
  <span class="hamburger-icon"></span>
</button>
    <nav id="main-navigation" class="main-nav">
        <?php include '../../app/controllers/menu.php'; ?>
		<?php include '../../app/controllers/header.php'; ?>
    </nav>
</div>

<form method="post" action="">
    <h1>Pubg Mobil Turnuva Kayıt Formu</h1>

    <div class="form-message-container">
        <?php if ($kayitBasarili): ?>
            <p class="success-message">Kayıt işleminiz başarıyla tamamlandı! Oda kodu ve şifre, mail adresinize gönderilecektir. Lütfen spam klasörünüzü de kontrol etmeyi unutmayın.</p>
        <?php else: // Kayıt başarılı değilse (hata varsa veya ilk yüklenme ise) ?>
            <?php if (isset($errors['ip'])): ?>
                <p class="error-message"><?php echo htmlspecialchars($errors['ip']); ?></p>
            <?php elseif (isset($errors['db'])): ?>
                <p class="error-message"><?php echo htmlspecialchars($errors['db']); ?></p>
            <?php elseif (!empty($errors)): ?>
                <p class="error-message">Kayıt işlemi sırasında hatalar oluştu. Lütfen formu kontrol edin.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <label for="oyuncu1">Oyuncu 1 Tag <span class="required">*</span></label>
    <input type="text" id="oyuncu1" name="oyuncu1" value="<?php echo htmlspecialchars(isset($_POST['oyuncu1']) ? $_POST['oyuncu1'] : '', ENT_QUOTES, 'UTF-8'); ?>" required />
    <?php if (isset($errors['oyuncu1'])): ?><span class="error-message"><?php echo htmlspecialchars($errors['oyuncu1']); ?></span><?php endif; ?>

    <label for="oyuncu2">Oyuncu 2 Tag <span class="required">*</span></label>
    <input type="text" id="oyuncu2" name="oyuncu2" value="<?php echo htmlspecialchars(isset($_POST['oyuncu2']) ? $_POST['oyuncu2'] : '', ENT_QUOTES, 'UTF-8'); ?>" required />
    <?php if (isset($errors['oyuncu2'])): ?><span class="error-message"><?php echo htmlspecialchars($errors['oyuncu2']); ?></span><?php endif; ?>

    <label for="oyuncu3">Oyuncu 3 Tag <span class="required">*</span></label>
    <input type="text" id="oyuncu3" name="oyuncu3" value="<?php echo htmlspecialchars(isset($_POST['oyuncu3']) ? $_POST['oyuncu3'] : '', ENT_QUOTES, 'UTF-8'); ?>" required />
    <?php if (isset($errors['oyuncu3'])): ?><span class="error-message"><?php echo htmlspecialchars($errors['oyuncu3']); ?></span><?php endif; ?>

    <label for="oyuncu4">Oyuncu 4 Tag <span class="required">*</span></label>
    <input type="text" id="oyuncu4" name="oyuncu4" value="<?php echo htmlspecialchars(isset($_POST['oyuncu4']) ? $_POST['oyuncu4'] : '', ENT_QUOTES, 'UTF-8'); ?>" required />
    <?php if (isset($errors['oyuncu4'])): ?><span class="error-message"><?php echo htmlspecialchars($errors['oyuncu4']); ?></span><?php endif; ?>

    <label for="yedek">Yedek Oyuncu Tag (İsteğe Bağlı)</label>
    <input type="text" id="yedek" name="yedek" value="<?php echo htmlspecialchars(isset($_POST['yedek']) ? $_POST['yedek'] : '', ENT_QUOTES, 'UTF-8'); ?>" />
    <?php /* Yedek oyuncu için hata mesajı göstermeye gerek yok çünkü zorunlu değil */ ?>

    <label for="ekip_tag">Ekip Tagı <span class="required">*</span></label>
    <input type="text" id="ekip_tag" name="ekip_tag" value="<?php echo htmlspecialchars(isset($_POST['ekip_tag']) ? $_POST['ekip_tag'] : '', ENT_QUOTES, 'UTF-8'); ?>" required />
    <?php if (isset($errors['ekip_tag'])): ?><span class="error-message"><?php echo htmlspecialchars($errors['ekip_tag']); ?></span><?php endif; ?>

    <label for="kaptan_tel">Kaptan Telefon Numarası <span class="required">*</span></label>
    <input type="tel" id="kaptan_tel" name="kaptan_tel" value="<?php echo htmlspecialchars(isset($_POST['kaptan_tel']) ? $_POST['kaptan_tel'] : '', ENT_QUOTES, 'UTF-8'); ?>" required pattern="^\+?[0-9\s\-\(\)]+$" title="Geçerli bir telefon numarası giriniz." />
    <?php if (isset($errors['kaptan_tel'])): ?><span class="error-message"><?php echo htmlspecialchars($errors['kaptan_tel']); ?></span><?php endif; ?>

    <label for="kaptan_mail">Kaptan Mail Adresi <span class="required">*</span></label>
    <input type="email" id="kaptan_mail" name="kaptan_mail" value="<?php echo htmlspecialchars(isset($_POST['kaptan_mail']) ? $_POST['kaptan_mail'] : '', ENT_QUOTES, 'UTF-8'); ?>" required />
    <?php if (isset($errors['kaptan_mail'])): ?><span class="error-message"><?php echo htmlspecialchars($errors['kaptan_mail']); ?></span><?php endif; ?>

    <div class="button-group">
        <button type="button" id="rulesBtn">Kayıt ve Turnuva Kuralları</button>
        <button type="submit" id="submitBtn" disabled>Kaydı Gönder</button>
    </div>
</form>

<?php
// Footer kontrolcüsünü dahil et (dosya yolu doğru olduğundan emin olun)
// Eğer footer HTML'i direkt buraya yapıştırılabilirse, o da yapılabilir.
include '../../app/controllers/footer.php';
?>

<div id="rulesModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" id="closeModal">&times;</span>
        <h2>Kayıt ve Turnuva Kuralları</h2>
        <p>Turnuvaya katılım için aşağıdaki kuralları okuyup onaylamanız gerekmektedir:</p>
        <pre>
Kayıt Kuralları
Her oyuncu yalnızca bir kez kayıt olabilecektir. Çift kayıt yapan oyuncular diskalifiye edilecektir.
Maç esnasında en az bir kişi kayıt almak zorundadır. Kayıt alınmayan maçlar geçersiz sayılacaktır.
Kayıt yaptıktan sonra turnuvaya katılmayan takımlar elenecektir ve 1 aylık zaman aşımı uygulanacaktır.
Turnuva katılımı için belirlenen kurallara uymayan oyuncular diskalifiye edilecektir.
Turnuva tarih ve saatine dikkat edilmelidir. Geciken oyuncular maça alınmayacaktır.
Turnuva sırasında fair play kurallarına uyulmalıdır. Hile yapmak kesinlikle yasaktır.
Turnuva organizatörlerinin belirlediği kurallara uyulması zorunludur. Aksi takdirde oyuncular turnuvadan elenecektir.
Turnuva sırasında her oyuncu kendi sorumluluğundadır ve kişisel bilgilerini gizli tutmalıdır.
En az 4 tag kullanmak zorunludur.
Dışardan kayıt haricinde oyuncu sokmak yasak.
Kayıt olan her takım, belirtilen tüm kuralları kabul etmiş sayılır.

Turnuva Kuralları
İşaret fişeği kullanmak yasaktır.
En az 3 tag kullanmak zorunludur.
2 oyunculu takımlar ve tagsız oyuncular odadan atılacaktır.
Acil tahliye kullanmak yasaktır.
Takım içerisinden en az bir kişinin maç boyunca kayıt alması zorunludur.
Oda kodu sorumluluğu oyunculara aittir.
Oda kurulduktan 5 dakika sonra maç başlayacaktır.
Maç sırasında herhangi bir hile veya exploit kullanmak yasaktır.
Oyuncular, maç öncesinde ve sırasında birbirine küfürlü, hakaret içeren dil kullanamaz.
Herhangi bir takım dışı oyuncunun oyun alanına müdahale etmesi yasaktır.
Maç sırasında sunucuya müdahale etmek veya teknik sorunları kasıtlı olarak oluşturmak yasaktır.
Oyun başladıktan sonra, oyuncular oyun alanını terk edemez.
Oyun sırasında takımlar arasındaki iletişim yalnızca takım içi olacaktır; diğer takımların bilgilerine izinsiz erişim yasaktır.
Takımlar, belirlenen süre içerisinde oyuncu değişikliği yapamaz.
Kayıt olan takımlar katılmak zorundadır aksi taktirde hesabınıza erişim engeli koyulabilir.
Kayıt olan her takım oyuncusu, belirtilen tüm kuralları kabul etmiş sayılır.
</pre>
        <div class="button-group">
             <button type="button" id="agreeRulesBtn" class="btn-primary">Kuralları Okudum ve Onaylıyorum</button>
        </div>
    </div>
</div>

<script>
	document.addEventListener('DOMContentLoaded', function() {
    const rulesBtn = document.getElementById('rulesBtn');
    const modal = document.getElementById('rulesModal');
    const closeModalBtn = document.getElementById('closeModal');
    const submitBtn = document.getElementById('submitBtn');
    const agreeRulesBtn = document.getElementById('agreeRulesBtn');
    const hamburgerButton = document.querySelector('.hamburger-button');
    const mainNav = document.getElementById('main-navigation');
    const dropdowns = document.querySelectorAll('.main-nav .dropdown > a'); // Only main dropdown links

    // Kurallar butonuna tıklandığında modalı aç
    rulesBtn.onclick = function() {
        modal.style.display = 'flex'; // Flex yaparak içeriği ortalayabiliriz
    };

    // Kapat butonuna tıklandığında modalı kapat
    closeModalBtn.onclick = function() {
        modal.style.display = 'none';
    };

    // "Kuralları Okudum ve Onaylıyorum" butonuna tıklandığında
    agreeRulesBtn.onclick = function() {
        modal.style.display = 'none'; // Modalı kapat
        submitBtn.disabled = false; // Gönder butonunu aktif et
        submitBtn.style.backgroundColor = '#f1c40f'; // Buton rengini aktif hale getir (opsiyonel, globals.css'inizde stil varsa gerek yok)
        submitBtn.style.cursor = 'pointer'; // Fare işaretçisini değiştir (opsiyonel)
        rulesBtn.disabled = true; // Kurallar butonunu pasif et, tekrar açılmasın
        rulesBtn.style.opacity = '0.7';
        rulesBtn.style.cursor = 'not-allowed';
    };

    // Modal dışında tıklayınca kapat (ve gönder butonunu aktif etme)
    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    };
        // Hamburger menu toggle
        hamburgerButton.addEventListener('click', function() {
            mainNav.classList.toggle('active');
            hamburgerButton.classList.toggle('active');
        });

        // Mobile dropdown toggle
        dropdowns.forEach(dropdown => {
            dropdown.addEventListener('click', function(event) {
                // Toggle dropdown only in mobile view
                if (window.innerWidth <= 768) {
                    event.preventDefault(); // Prevent default link behavior
                    const parentLi = this.closest('.dropdown');
                    parentLi.classList.toggle('active'); // Toggle 'active' class
                }
            });
        });

        // Reset menu state on window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                mainNav.classList.remove('active');
                hamburgerButton.classList.remove('active');
                // Hide all dropdowns on desktop
                document.querySelectorAll('.dropdown').forEach(d => d.classList.remove('active'));
            }
        });
    });
</script>

</body>
</html>