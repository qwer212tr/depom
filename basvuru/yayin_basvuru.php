<?php
require_once __DIR__ . '/../config/db.php';

$basvuruBasarili = false;
$hataMesaji = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $isim         = isset($_POST['isim']) ? $_POST['isim'] : '';
    $soyisim      = isset($_POST['soyisim']) ? $_POST['soyisim'] : '';
    $yas          = isset($_POST['yas']) ? $_POST['yas'] : 0;
    $telefon      = isset($_POST['telefon']) ? $_POST['telefon'] : '';
    $mail         = isset($_POST['mail']) ? $_POST['mail'] : '';
    $yayinci_kadi = isset($_POST['yayinci_kadi']) ? $_POST['yayinci_kadi'] : '';
    $platform     = isset($_POST['platform']) ? $_POST['platform'] : '';
    $deneyim      = isset($_POST['deneyim']) ? $_POST['deneyim'] : '';

    if ($isim && $soyisim && $yas && $telefon && $mail && $yayinci_kadi && $platform && $deneyim) {
        try {
            $stmt = $db->prepare("INSERT INTO yayin_basvuru (isim, soyisim, yas, telefon, mail, yayinci_kadi, platform, deneyim)
                                  VALUES (:isim, :soyisim, :yas, :telefon, :mail, :yayinci_kadi, :platform, :deneyim)");
            $stmt->execute([
                ':isim' => $isim,
                ':soyisim' => $soyisim,
                ':yas' => $yas,
                ':telefon' => $telefon,
                ':mail' => $mail,
                ':yayinci_kadi' => $yayinci_kadi,
                ':platform' => $platform,
                ':deneyim' => $deneyim
            ]);
            $basvuruBasarili = true;
        } catch (PDOException $e) {
            $hataMesaji = "Veritabanı hatası: " . $e->getMessage();
        }
    } else {
        $hataMesaji = "Lütfen tüm alanları doldurun.";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <!-- ✅ Favicon dosyaları doğru klasöre göre güncellendi -->
    <link rel="icon" type="image/png" sizes="16x16" href="/new/app/models/favicon/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/new/app/models/favicon/favicon-32x32.png">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Yayıncı Başvuru - Bi Organizasyon</title>
    <link rel="stylesheet" href="../assets/globals.css" />
</head>
<body>
<!-- Top Bar -->
<div class="top-bar">
    <div class="logo"></div>
<button class="hamburger-button" aria-label="Toggle menu">
  <span class="hamburger-icon"></span>
</button>
    <nav id="main-navigation" class="main-nav">
        <?php include '../app/controllers/menu.php'; ?>
		<?php include '../app/controllers/header.php'; ?>
    </nav>
</div>
<form action="" method="post" novalidate>
    <h2>Yayıncı Başvuru Formu</h2>

    <?php if ($basvuruBasarili): ?>
        <div style="background-color: #28a745; color: white; padding: 10px; text-align: center; border-radius: 5px; margin-bottom: 15px;">
            Başvurunuz başarıyla kaydedildi.
        </div>
    <?php elseif ($hataMesaji): ?>
        <div style="background-color: #dc3545; color: white; padding: 10px; text-align: center; border-radius: 5px; margin-bottom: 15px;">
            <?= htmlspecialchars($hataMesaji) ?>
        </div>
    <?php endif; ?>

    <label for="isim">İsim:</label>
    <input type="text" id="isim" name="isim" required>

    <label for="soyisim">Soy İsim:</label>
    <input type="text" id="soyisim" name="soyisim" required>

    <label for="yas">Yaş:</label>
    <input type="number" id="yas" name="yas" required min="10" max="99">

    <label for="telefon">Telefon:</label>
    <input type="tel" id="telefon" name="telefon" required pattern="[0-9]{10,15}" title="Sadece rakam giriniz.">

    <label for="mail">E-posta:</label>
    <input type="email" id="mail" name="mail" required>

    <label for="yayinci_kadi">Yayıncı Kullanıcı Adı:</label>
    <input type="text" id="yayinci_kadi" name="yayinci_kadi" required>

    <label for="platform">Yayın Platformu:</label>
    <select id="platform" name="platform" required>
        <option value="" disabled selected>Seçiniz</option>
        <option value="twitch">Twitch</option>
        <option value="youtube">Youtube</option>
        <option value="kick">Kick</option>
        <option value="tiktok">TikTok</option>
        <option value="facebook">Facebook</option>
        <option value="diger">Diğer</option>
    </select>

    <label for="deneyim">Yayın Deneyimi:</label>
    <select id="deneyim" name="deneyim" required>
        <option value="" disabled selected>Seçiniz</option>
        <option value="yeni">Yeni Başladım</option>
        <option value="orta">Orta Seviye</option>
        <option value="deneyimli">Deneyimli</option>
        <option value="profesyonel">Profesyonel</option>
    </select>

    <button type="button" id="rulesBtn">Yayıncı Kuralları</button>
    <button type="submit" id="submitBtn" disabled>Başvuruyu Gönder</button>
</form>

<div id="rulesModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" id="closeModal">&times;</span>
        <h2>Bi Organizasyon Yayıncı Sözleşmesi ve Kurallar</h2>
        <p>Saygılı ve olumlu iletişim kurulmalıdır.</p>
        <p>Yayınlarda küfür, hakaret ve uygunsuz davranış kesinlikle yasaktır.</p>
        <p>Bi Organizasyon etkinliklerine ve kurallarına uyulması zorunludur.</p>
        <p>Yayın saatlerine ve etkinlik programlarına düzenli olarak katılım sağlanmalıdır.</p>
        <p>Reklam ve tanıtımlar organizasyonun onayı olmadan yapılamaz.</p>
        <p>Hile, dolandırıcılık veya haksız rekabet kabul edilmez.</p>
        <p>Takım içi ve organizasyon içi iletişim Discord üzerinden sağlanacaktır.</p>
        <p>Kurallara uymayan yayıncılar uyarılır, devamında ekipten çıkarılır.</p>
        <p>Organizasyon ve takım yönetiminin kararları kesindir.</p>
        <p>Herkese eşit davranılmalı, dostane ve profesyonel olunmalıdır.</p>
    </div>
</div>
<?php include '../app/controllers/footer.php'; ?>

<script>
const rulesBtn = document.getElementById('rulesBtn');
const modal = document.getElementById('rulesModal');
const closeModal = document.getElementById('closeModal');
const submitBtn = document.getElementById('submitBtn');
const form = document.querySelector('form');
    document.addEventListener('DOMContentLoaded', function() {
        const hamburgerButton = document.querySelector('.hamburger-button');
        const mainNav = document.getElementById('main-navigation');
        const dropdowns = document.querySelectorAll('.main-nav .dropdown > a'); // Only main dropdown links

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
rulesBtn.onclick = () => {
    modal.style.display = 'block';
};

closeModal.onclick = () => {
    modal.style.display = 'none';
    submitBtn.disabled = false;
};

window.onclick = (event) => {
    if (event.target == modal) {
        modal.style.display = 'none';
        submitBtn.disabled = false;
    }
};

form.addEventListener('submit', (e) => {
    if (submitBtn.disabled) {
        e.preventDefault();
        alert('Lütfen önce Yayıncı Kurallarını okuyunuz.');
    }
});
</script>

</body>
</html>
