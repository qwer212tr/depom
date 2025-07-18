<?php
session_start();
require_once __DIR__ . '/../config/db.php'; // $pdo burada tanımlı

// Telefon numarası maskeleme
function maskPhone($phone) {
    return substr($phone, 0, 4) . ' *** ** **';
}

// E-posta maskeleme
function maskEmail($email) {
    $parts = explode('@', $email);
    $local = substr($parts[0], 0, 2) . str_repeat('*', max(0, strlen($parts[0]) - 2));
    return $local . '@' . $parts[1];
}

if (!isset($_SESSION['kullanici_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['kullanici_id'];

try {
    $stmt = $pdo->prepare("SELECT isim, soyisim, kullanici_adi, rol, bakiye, telefon, mail FROM users WHERE id = :id");

    $stmt->execute([':id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        session_destroy();
        header("Location: ../login.php");
        exit();
    }
} catch (PDOException $e) {
    die("Veritabanı hatası: " . $e->getMessage());
}

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: ../login.php');
    exit();
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
    <title>Profil - Bi Organizasyon</title>
    
    <link rel="stylesheet" href="../assets/globals.css" />
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
        <?php include '../app/controllers/menu.php'; ?>
    </nav>
</div>

<div class="dashboard">
    <h1>Hoşgeldin, <?= htmlspecialchars($user['isim'] . ' ' . $user['soyisim']) ?>!</h1>

    <!-- Hızlı Erişim Kutuları -->
    <div class="quick-access-row">
        <!-- Kutu 1: Kullanıcı Bilgileri -->
        <div class="quick-box">
            <h3>Kullanıcı Bilgileri</h3>
            <div class="user-info">
                <p><strong>Kullanıcı Adı:</strong> <?= htmlspecialchars($user['kullanici_adi']) ?></p>
                <p><strong>Ünvan:</strong> <?= htmlspecialchars($user['rol']) ?></p>
                <p><strong>Telefon Numarası:</strong> <?= htmlspecialchars(maskPhone($user['telefon'])) ?></p>
                <p><strong>Mail Adresi:</strong> <?= htmlspecialchars(maskEmail($user['mail'])) ?></p>
                <p>
                    <strong>Bakiye:</strong>
                    <span id="bakiye"><?= number_format($user['bakiye'], 0, ',', '.') ?></span> TL
                    <span id="refresh-bakiye" class="refresh-icon" title="Bakiye Yenile">🔄</span>
                </p>
            </div>
        </div>

        <!-- Kutu 2: Katıldığınız Turnuvalar -->
        <div class="quick-box">
            <h3>Katıldığınız Turnuvalar</h3>
            <p>Geçmiş ve aktif turnuvalarınızı inceleyin.</p>
        </div>

        <!-- Kutu 3: Destek Taleplerin -->
        <div class="quick-box">
            <h3>Destek Taleplerin</h3>
            <p>Gönderdiğiniz destek taleplerine göz atın.</p>
        </div>
    </div>
</div>

<?php include '../app/controllers/footer.php'; ?>

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
document.addEventListener('DOMContentLoaded', function () {
    const refreshIcon = document.getElementById('refresh-bakiye');
    const bakiyeSpan = document.getElementById('bakiye');

    refreshIcon.addEventListener('click', function () {
        fetch('get_bakiye.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bakiyeSpan.textContent = data.bakiye;
                } else {
                    alert('Bakiye alınamadı.');
                }
            })
            .catch(() => {
                alert('Bir hata oluştu.');
            });
    });
});
</script>

</body>
</html>
