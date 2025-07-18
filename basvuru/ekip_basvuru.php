<?php
require_once __DIR__ . '/../config/db.php';

$basvuruBasarili = false;
$hataMesaji = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $isim        = isset($_POST['isim']) ? $_POST['isim'] : '';
    $soyisim     = isset($_POST['soyisim']) ? $_POST['soyisim'] : '';
    $yas         = isset($_POST['yas']) ? $_POST['yas'] : 0;
    $telefon     = isset($_POST['telefon']) ? $_POST['telefon'] : '';
    $mail        = isset($_POST['mail']) ? $_POST['mail'] : '';
    $isim_karti  = isset($_POST['isim_karti']) ? $_POST['isim_karti'] : '';
    $oyuncu_id   = isset($_POST['oyuncu_id']) ? $_POST['oyuncu_id'] : 0;
    $deneyim     = isset($_POST['deneyim']) ? $_POST['deneyim'] : '';

    if ($isim && $soyisim && $yas && $telefon && $mail && $isim_karti && $oyuncu_id && $deneyim) {
        try {
            $stmt = $db->prepare("INSERT INTO ekip_basvuru (isim, soyisim, yas, telefon, mail, isim_karti, oyuncu_id, deneyim)
                                  VALUES (:isim, :soyisim, :yas, :telefon, :mail, :isim_karti, :oyuncu_id, :deneyim)");
            $stmt->execute([
                ':isim' => $isim,
                ':soyisim' => $soyisim,
                ':yas' => $yas,
                ':telefon' => $telefon,
                ':mail' => $mail,
                ':isim_karti' => $isim_karti,
                ':oyuncu_id' => $oyuncu_id,
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
    <link rel="icon" type="image/png" sizes="16x16" href="/new/app/models/favicon/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/new/app/models/favicon/favicon-32x32.png">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Ekip Başvuru - Bi Organizasyon</title>
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

<form action="" method="post" style="max-width: 500px; margin: 80px auto; padding: 20px; background-color: #1c1c1c; border-radius: 8px; color: white; font-family: Arial, sans-serif;">
    <h2 style="text-align: center; margin-top: 80px;">Ekip Başvuru Formu</h2>

    <?php if ($basvuruBasarili): ?>
        <div style="background-color: #28a745; color: white; padding: 10px; text-align: center; border-radius: 5px; margin-bottom: 15px;">
            Başvurunuz başarıyla kaydedildi.
        </div>
    <?php elseif ($hataMesaji): ?>
        <div style="background-color: #dc3545; color: white; padding: 10px; text-align: center; border-radius: 5px; margin-bottom: 15px;">
            <?= htmlspecialchars($hataMesaji) ?>
        </div>
    <?php endif; ?>

    <!-- Form alanları burada... -->
    <div class="form-group">
        <label for="isim">İsim:</label>
        <input type="text" id="isim" name="isim" required>
    </div>

    <div class="form-group">
        <label for="soyisim">Soy İsim:</label>
        <input type="text" id="soyisim" name="soyisim" required>
    </div>

    <div class="form-group">
        <label for="yas">Yaş:</label>
        <input type="number" id="yas" name="yas" required min="10" max="99">
    </div>

    <div class="form-group">
        <label for="telefon">Telefon:</label>
        <input type="tel" id="telefon" name="telefon" required pattern="[0-9]{10,15}" title="Sadece rakam giriniz.">
    </div>

    <div class="form-group">
        <label for="mail">Mail:</label>
        <input type="email" id="mail" name="mail" required>
    </div>

    <div class="form-group">
        <label for="isim_karti">İsim Kartı:</label>
        <select id="isim_karti" name="isim_karti" required>
            <option value="">Seçiniz</option>
            <option value="Yok">Yok</option>
            <option value="Var">Var</option>
        </select>
    </div>

    <div class="form-group">
        <label for="oyuncu_id">Oyuncu ID:</label>
        <input type="number" id="oyuncu_id" name="oyuncu_id" required>
    </div>

    <div class="form-group">
        <label for="deneyim">Turnuva Deneyimi:</label>
        <select id="deneyim" name="deneyim" required>
            <option value="">Seçiniz</option>
            <option value="Kötü">Kötü</option>
            <option value="Orta">Orta</option>
            <option value="İyi">İyi</option>
            <option value="Süper">Süper</option>
        </select>
    </div>

    <div class="button-group">
        <button type="button" id="rulesBtn">Ekip Kuralları</button>
        <button type="submit" id="submitBtn" disabled>Gönder</button>
    </div>
</form>

<div id="rulesModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" id="closeModal">&times;</span>
        <h2>Bi Organizasyon Ekip Alımı - Kurallar Saygı ve Disiplin</h2>
        <p>Takım içinde ve dışındaki oyunculara karşı saygılı olunmalıdır. Küfür, hakaret ve agresif davranışlar kesinlikle yasaktır.</p>
        <p>Komutlara ve takım liderlerine itaat etmek zorunludur.</p>
        <p>Aktiflik: Haftada en az [4 gün] aktif olunması beklenir.</p>
        <p>Oyuncular, maç saatlerine ve antrenmanlara zamanında katılmalıdır.</p>
        <p>İletişim: Takım içi iletişim açık ve net olmalıdır. Discord veya belirlenen sesli iletişim programı kullanılacaktır.</p>
        <p>Mikrofon açık tutulmalı ve gereksiz gürültü yapmaktan kaçınılmalıdır.</p>
        <p>Oyun Tarzı ve Takım Uyumu: Ekip oyununa önem verilmeli, bireysel hareketlerden kaçınılmalıdır.</p>
        <p>Stratejilere ve takım planlarına uyulmalıdır.</p>
        <p>Hile ve Haksız Davranış: Hiçbir şekilde hile, bug kullanımı veya adil olmayan yöntemlere izin verilmez. Bu tür davranışlar tespit edildiğinde derhal ekipten çıkarılır.</p>
        <p>Davranış Kuralları: Ekip üyeleri arasında yaşanacak anlaşmazlıklarda saygılı ve yapıcı çözüm yolları aranmalıdır.</p>
        <p>Ekip kurallarına uymayanlar uyarılır, devamında ekipten çıkarılır.</p>
        <p>Takım İçi Rollerin Belirlenmesi: Rollere saygı gösterilmeli ve görevler eksiksiz yerine getirilmelidir.</p>
        <p>Bi Class Yüksek Yöneticiler: Bi・Tepki , Bi・WolfX2X</p>
        <p>Takım liderinin ve kaptanın kararları kesin ve bağlayıcıdır.</p>
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

// Modal kapandığında gönder butonunu aktif yap
closeModal.onclick = () => {
    modal.style.display = 'none';
    submitBtn.disabled = false;
};

// Modal dışına tıklayınca da kapat ve gönder butonunu aktif yap
window.onclick = (event) => {
    if (event.target == modal) {
        modal.style.display = 'none';
        submitBtn.disabled = false;
    }
};

// Gönder butonuna basıldığında, eğer disabled ise uyar
form.addEventListener('submit', (e) => {
    if (submitBtn.disabled) {
        e.preventDefault();
        alert('Lütfen önce Ekip Kurallarını okuyunuz.');
    }
});
</script>

</body>
</html>
