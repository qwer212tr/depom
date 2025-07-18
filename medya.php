<?php
require_once __DIR__ . '/config/db.php';

try {
    $stmt = $pdo->query("SELECT * FROM medya_merkezi ORDER BY eklendi_at DESC");
    $medya = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Veritabanı hatası: " . $e->getMessage());
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
    <title>Medya Merkezi - Bi Organizasyon</title>
    <link rel="stylesheet" href="assets/globals.css" />
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
		<?php include 'app/controllers/header.php'; ?>
    </nav>
</div>

<main>
    <h2 class="medya-title">Medya Merkezi</h2>

    <div class="medya-container">
        <?php if (!empty($medya)): ?>
            <?php foreach ($medya as $item): ?>
<div class="medya-kart">
    <?php if (!empty($item['resim_link'])): ?>
        <img src="app/models/event/<?php echo htmlspecialchars($item['resim_link']); ?>" alt="Medya Görseli" class="medya-img">

    <?php endif; ?>

    <h3><?php echo htmlspecialchars($item['baslik']); ?></h3>
    <p><strong>Oynanma Tarihi:</strong> <?php echo htmlspecialchars($item['turnuva_tarihi']); ?></p>
    <p><strong>Oyun Türü:</strong> <?php echo htmlspecialchars($item['oyun_modu']); ?></p>

    <?php if (!empty($item['video_link'])): ?>
        <p><a href="<?php echo htmlspecialchars($item['video_link']); ?>" target="_blank">🎥 Turnuva Tekrarını İzle</a></p>
    <?php endif; ?>
</div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Henüz medya içeriği eklenmemiş.</p>
        <?php endif; ?>
    </div>
</main>

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
