<?php
session_start();
require_once __DIR__ . '/config/db.php'; // buradan $pdo geliyor

$hataMesaji = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kullanici = isset($_POST['kullanici']) ? trim($_POST['kullanici']) : '';
    $sifre = isset($_POST['sifre']) ? $_POST['sifre'] : '';
    $captcha_gelen = isset($_POST['captcha']) ? trim($_POST['captcha']) : '';

    // CAPTCHA doğrulama
    if (empty($kullanici) || empty($sifre)) {
        $hataMesaji = "Lütfen kullanıcı adı/mail ve şifreyi doldurun.";
    } elseif (empty($captcha_gelen) || strtolower($captcha_gelen) !== strtolower(isset($_SESSION['captcha_kod']) ? $_SESSION['captcha_kod'] : '')) {
        $hataMesaji = "CAPTCHA kodu yanlış.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE kullanici_adi = :kullanici OR mail = :kullanici LIMIT 1");
            $stmt->execute([':kullanici' => $kullanici]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($sifre, $user['sifre_hash'])) {
                // Giriş başarılı, session başlat
                $_SESSION['kullanici_id'] = $user['id'];
                $_SESSION['kullanici_adi'] = $user['kullanici_adi'];
                $_SESSION['rol'] = $user['rol'];
    // ✅ Beni Hatırla seçiliyse cookie oluştur (30 gün geçerli)
    if (isset($_POST['beni_hatirla'])) {
        setcookie('kullanici', $kullanici, time() + (86400 * 30), "/"); // 30 gün
    } else {
        // ❌ Değilse varsa eski çerezi sil
        setcookie('kullanici', '', time() - 3600, "/");
    }
                header('Location: profil/dashboard.php');
                exit();
            } else {
                $hataMesaji = "Kullanıcı adı/mail veya şifre yanlış.";
            }
        } catch (PDOException $e) {
            $hataMesaji = "Veritabanı hatası: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    
    <!-- ✅ Favicon files updated according to the correct folder -->
    <link rel="icon" type="image/png" sizes="16x16" href="/new/app/models/favicon/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/new/app/models/favicon/favicon-32x32.png">

    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Giriş - Bi Organizasyon</title>
    
    <link rel="stylesheet" href="assets/globals.css" />
    <style>
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
        <?php include 'app/controllers/menu.php'; ?>
    </nav>
</div>



<form action="" method="post" style="max-width: 400px; margin: 80px auto; padding: 15px; background-color: #1c1c1c; border-radius: 8px; color: white; font-family: Arial, sans-serif; text-align: center;">

    <h2 style="margin-bottom: 15px; font-size: 22px;">Giriş Yap</h2>

    <?php if ($hataMesaji): ?>
        <div style="background-color: #dc3545; color: white; padding: 8px; border-radius: 5px; margin-bottom: 12px;">
            <?= htmlspecialchars($hataMesaji) ?>
        </div>
    <?php endif; ?>

<input type="text" id="kullanici" name="kullanici"
    value="<?= isset($_COOKIE['kullanici']) ? htmlspecialchars($_COOKIE['kullanici']) : '' ?>"
    required style="width: 95%; padding: 8px; border-radius: 16px; border: none;"><br>

    <label for="sifre" style="display: block; font-size: 15px; margin: 6px 0 2px;">Şifre:</label>
    <input type="password" id="sifre" name="sifre" required style="width: 95%; padding: 8px; border-radius: 16px; border: none;"><br>

    <label for="captcha" style="display: block; font-size: 15px; margin: 8px 0 4px;">Aşağıdaki kodu giriniz:</label>
    <img src="app/controllers/captcha.php" alt="CAPTCHA" style="margin: 6px 0; border: 1px solid #555;"><br>
<input type="text" id="captcha" name="captcha" required autocomplete="off"
       style="width: 100%; padding: 8px; margin-top: 4px; border: 1px solid #444; border-radius: 16px; background-color: #2a2a2a; color: white; outline: none;">
<div style="margin: 12px 0; text-align: left;">
    <label>
        <input type="checkbox" name="beni_hatirla" value="1" style="margin-right: 6px;">
        Beni Hatırla
    </label>
</div>
    <button type="submit" style="width: 100%; padding: 9px; margin-top: 14px; background-color: #007bff; border: none; border-radius: 4px; color: white; font-size: 15px; cursor: pointer;">
        Giriş Yap
    </button>

</form>

<?php include 'app/controllers/footer.php'; ?>
<script>
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
</script>
</body>
</html>
