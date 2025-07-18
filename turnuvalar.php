<?php
require_once 'config/db.php';

$sql = "SELECT baslik, aciklama, resim, kayit_tarihi, oyun, saat, katilim, yayin_platformu, lokasyon, aktif, tarih, kayit_linki 
        FROM turnuvalar 
        ORDER BY aktif DESC, tarih ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$etkinlikler = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $aciklama = $row['aciklama'];
    $kisaAciklama = mb_substr($aciklama, 0, 100) . '...';

    $etkinlikler[] = array_merge($row, ['kisa_aciklama' => $kisaAciklama]);
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <!-- ✅ Favicon dosyaları doğru klasöre göre güncellendi -->
    <!-- ✅ Favicon dosyaları doğru klasöre göre güncellendi -->
    <link rel="icon" type="image/png" sizes="16x16" href="/new/app/models/favicon/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/new/app/models/favicon/favicon-32x32.png">

    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Turnuvalar - Bi Organizasyon</title>
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

<!-- Etkinlikler -->
<section>
    <h2 style="color: yellow; text-align: center;">Yaklaşan Turnuvalar</h2>

    <section style="max-width: 1000px; margin: 60px auto 20px auto; color: white; font-size: 20px; line-height: 1.4; text-align: center;">
        <p>Mobil oyun dünyasında heyecan dorukta! PUBG Mobile, Free Fire gibi popüler oyunlar için düzenlenen <strong>turnuvalar</strong>, oyunculara rekabet dolu ve eğlenceli anlar sunuyor.</p>
        <p>Bilgisayar oyunları alanında ise büyük <strong>turnuvalar</strong> hız kesmeden devam ediyor. League of Legends, CS:GO ve Dota 2 gibi e-spor devleri, bölgesel ve uluslararası şampiyonalarda oyuncuları bir araya getiriyor.</p>
        <p>İster mobil ister bilgisayar oyuncusu olun, yakında gerçekleşecek <strong>turnuva etkinlikleriyle</strong> rekabetin ve eğlencenin tadını çıkarabilirsiniz!</p>
    </section>

    <div class="etkinlik-listesi">
        <?php if (count($etkinlikler) > 0): ?>
            <?php foreach ($etkinlikler as $e): ?>
                <div class="etkinlik">
                    <img src="app/models/event/<?= htmlspecialchars($e['resim']) ?>" alt="<?= htmlspecialchars($e['baslik']) ?>" />
                    <h3><?= htmlspecialchars($e['baslik']) ?></h3>
                    <p class="aciklama"><?= htmlspecialchars($e['kisa_aciklama']) ?></p>

                    <a href="javascript:void(0);" class="devami" onclick="showPopup(`<?= addslashes(htmlspecialchars($e['aciklama'])) ?>`)">
                        Devamını Göster
                    </a>

                    <div class="detaylar">
                        <p><strong>Kayıt Tarihi:</strong> <?= htmlspecialchars($e['kayit_tarihi']) ?></p>
                        <p><strong>Oyun:</strong> <?= htmlspecialchars($e['oyun']) ?></p>
                        <p><strong>Saat:</strong> <?= htmlspecialchars($e['saat']) ?></p>
                        <p><strong>Katılım:</strong> <?= htmlspecialchars($e['katilim']) ?></p>
                        <p><strong>Yayın Platformu:</strong> <?= htmlspecialchars($e['yayin_platformu']) ?></p>
                        <p><strong>Lokasyon:</strong> <?= htmlspecialchars($e['lokasyon']) ?></p>
                    </div>

                    <div class="status-wrapper">
                        <?php if ((int)$e['aktif'] === 1 && !empty($e['kayit_linki'])): ?>
                            <span class="status-dot status-aktif" title="Şuan Aktif – Kayıt Olabilirsiniz"></span>
                            <span class="status-label">Aktif</span>
                            <a href="<?= htmlspecialchars($e['kayit_linki']) ?>" class="kayit-button" target="_blank" rel="noopener noreferrer">Kayıt Olmak İçin Tıkla.</a>
                        <?php elseif ((int)$e['aktif'] === 1): ?>
                            <span class="status-dot status-aktif" title="Şuan Aktif – Kayıt Olabilirsiniz"></span>
                            <span class="status-label">Aktif</span>
                        <?php else: ?>
                            <span class="status-dot status-kapali" title="Turnuva Bitti"></span>
                            <span class="status-label">Kapalı</span>
                        <?php endif; ?>
                    </div>

                    <p class="tarih">Tarih: <?= htmlspecialchars($e['tarih']) ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color:white;">Yaklaşan turnuva bulunmamaktadır.</p>
        <?php endif; ?>
    </div>
</section>

<!-- Açıklama Modal -->
<div id="popup" class="popup" style="display: none;">
    <div class="popup-content">
        <span class="close" onclick="hidePopup()">&times;</span>
        <div id="popup-text"></div>
    </div>
</div>

<!-- Footer -->
<?php include 'app/controllers/footer.php'; ?>

<!-- JavaScript -->
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
    function showPopup(text) {
        document.getElementById('popup-text').innerHTML = text;
        document.getElementById('popup').style.display = 'flex';
    }

    function hidePopup() {
        document.getElementById('popup').style.display = 'none';
    }
</script>

</body>
</html>
