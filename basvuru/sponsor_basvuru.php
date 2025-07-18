<?php
require_once __DIR__ . '/../config/db.php';

$basvuruBasarili = false;
$hataMesaji = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firma_adi       = isset($_POST['firma_adi']) ? $_POST['firma_adi'] : '';
    $yetkili_adi     = isset($_POST['yetkili_adi']) ? $_POST['yetkili_adi'] : '';
    $mail            = isset($_POST['mail']) ? $_POST['mail'] : '';
    $telefon         = isset($_POST['telefon']) ? $_POST['telefon'] : '';
    $web_sitemiz_var = isset($_POST['web_sitemiz_var']) ? 1 : 0;
    $site_adresi     = isset($_POST['site_adresi']) ? $_POST['site_adresi'] : '';
    $konu            = isset($_POST['konu']) ? $_POST['konu'] : '';
    $mesaj           = isset($_POST['mesaj']) ? $_POST['mesaj'] : '';

    // Zorunlu alan kontrolleri
    if ($firma_adi && $yetkili_adi && $mail && $telefon && $konu) {
        if ($web_sitemiz_var && !$site_adresi) {
            $hataMesaji = "Site adresi boş bırakılamaz.";
        } else {
            try {
                $stmt = $db->prepare("INSERT INTO sponsor_basvuru (firma_adi, yetkili_adi, mail, telefon, web_sitemiz_var, site_adresi, konu, mesaj) 
                                      VALUES (:firma_adi, :yetkili_adi, :mail, :telefon, :web_sitemiz_var, :site_adresi, :konu, :mesaj)");
                $stmt->execute([
                    ':firma_adi' => $firma_adi,
                    ':yetkili_adi' => $yetkili_adi,
                    ':mail' => $mail,
                    ':telefon' => $telefon,
                    ':web_sitemiz_var' => $web_sitemiz_var,
                    ':site_adresi' => $site_adresi,
                    ':konu' => $konu,
                    ':mesaj' => $mesaj
                ]);
                $basvuruBasarili = true;
            } catch (PDOException $e) {
                $hataMesaji = "Veritabanı hatası: " . $e->getMessage();
            }
        }
    } else {
        $hataMesaji = "Lütfen zorunlu alanları doldurun.";
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
    <title>Sponsor Başvuru - Bi Organizasyon</title>
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
    <h2>Sponsor Başvuru</h2>

    <?php if ($basvuruBasarili): ?>
        <div class="success-message">
            Başvurunuz başarıyla kaydedildi.
        </div>
    <?php elseif ($hataMesaji): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($hataMesaji); ?>
        </div>
    <?php endif; ?>

    <label for="firma_adi">Firma Adı <sup style="color:#f1c40f;">*</sup></label>
    <input type="text" id="firma_adi" name="firma_adi" required value="<?php echo isset($_POST['firma_adi']) ? htmlspecialchars($_POST['firma_adi']) : ''; ?>">

    <label for="yetkili_adi">Yetkili Adı <sup style="color:#f1c40f;">*</sup></label>
    <input type="text" id="yetkili_adi" name="yetkili_adi" required value="<?php echo isset($_POST['yetkili_adi']) ? htmlspecialchars($_POST['yetkili_adi']) : ''; ?>">

    <label for="mail">Mail <sup style="color:#f1c40f;">*</sup></label>
    <input type="email" id="mail" name="mail" required value="<?php echo isset($_POST['mail']) ? htmlspecialchars($_POST['mail']) : ''; ?>">

    <label for="telefon">Telefon Numarası <sup style="color:#f1c40f;">*</sup></label>
    <input type="tel" id="telefon" name="telefon" required pattern="[0-9\s+\-]{7,15}" title="Telefon numarası" value="<?php echo isset($_POST['telefon']) ? htmlspecialchars($_POST['telefon']) : ''; ?>">

    <div class="checkbox-group">
        <input type="checkbox" id="web_sitemiz_var" name="web_sitemiz_var" <?php echo isset($_POST['web_sitemiz_var']) ? 'checked' : ''; ?>>
        <label for="web_sitemiz_var">Web sitemiz var</label>
    </div>

    <div id="websiteContainer">
        <label for="site_adresi">Site Adresi</label>
        <input type="text" id="site_adresi" name="site_adresi" placeholder="Örnek : www.biorganizasyon.com.tr" value="<?php echo isset($_POST['site_adresi']) ? htmlspecialchars($_POST['site_adresi']) : ''; ?>">
    </div>

    <label for="konu">Konu <sup style="color:#f1c40f;">*</sup></label>
    <select id="konu" name="konu" required>
        <option value="">Seçiniz</option>
        <option value="Ürün Sponsorluğu" <?php echo (isset($_POST['konu']) && $_POST['konu'] === 'Ürün Sponsorluğu') ? 'selected' : ''; ?>>Ürün Sponsorluğu</option>
        <option value="Finansal Destek" <?php echo (isset($_POST['konu']) && $_POST['konu'] === 'Finansal Destek') ? 'selected' : ''; ?>>Finansal Destek</option>
        <option value="Hizmet Sponsorluğu" <?php echo (isset($_POST['konu']) && $_POST['konu'] === 'Hizmet Sponsorluğu') ? 'selected' : ''; ?>>Hizmet Sponsorluğu</option>
    </select>

    <label for="mesaj">Eklemek istediğiniz Mesaj</label>
    <textarea id="mesaj" name="mesaj" rows="5"><?php echo isset($_POST['mesaj']) ? htmlspecialchars($_POST['mesaj']) : ''; ?></textarea>

    <div class="button-group">
        <button type="button" id="rulesBtn">Sponsor Sözleşme</button>
        <button type="submit" id="submitBtn" disabled>Gönder</button>
    </div>
</form>

<div id="rulesModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close-btn" id="closeModal" style="cursor:pointer;">&times;</span>
        <h2>Bi Organizasyon Sponsor Sözleşmesi ve Kurallar</h2>
        <p>Saygılı ve olumlu iletişim kurulmalıdır.</p>
        <p>Reklam ve sponsorluk içerikleri açık ve doğru olmalıdır.</p>
        <p>Organizasyon kurallarına uyulmalıdır.</p>
        <p>Gizlilik ve etik kurallarına dikkat edilmelidir.</p>
        <p>Kurallara uymayan başvurular reddedilebilir.</p>
    </div>
</div>

<?php include '../app/controllers/footer.php'; ?>

<script>
const rulesBtn = document.getElementById('rulesBtn');
const modal = document.getElementById('rulesModal');
const closeModal = document.getElementById('closeModal');
const submitBtn = document.getElementById('submitBtn');
const form = document.querySelector('form');
const checkbox = document.getElementById('web_sitemiz_var');
const websiteContainer = document.getElementById('websiteContainer');
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
function toggleWebsiteField() {
    if (checkbox.checked) {
        websiteContainer.style.display = 'block';
    } else {
        websiteContainer.style.display = 'none';
        document.getElementById('site_adresi').value = '';
    }
}

checkbox.addEventListener('change', toggleWebsiteField);
window.addEventListener('load', toggleWebsiteField);

rulesBtn.onclick = () => {
    modal.style.display = 'block';
};

closeModal.onclick = () => {
    modal.style.display = 'none';
    submitBtn.disabled = false;
};

window.onclick = (event) => {
    if (event.target === modal) {
        modal.style.display = 'none';
        submitBtn.disabled = false;
    }
};

form.addEventListener('submit', (e) => {
    if (submitBtn.disabled) {
        e.preventDefault();
        alert('Lütfen önce Sponsor Sözleşmesini okuyunuz.');
    }
});
</script>

</body>
</html>
