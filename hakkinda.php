<?php
// hakkinda.php
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
    <title>Hakkında - Oyun Turnuvaları</title>
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

<!-- İçerik -->
<section>
    <div class="about-flex-wrapper">
        <!-- Sol: Başlık + Misyon -->
        <div class="about-text">
            <div class="heading-box">
                <h3>E-spor pazarı verilerini daha erişilebilir hale getirme</h3>
                <p>Esports Charts, espor yayınları ve canlı yayınlar için kapsamlı ölçümler sağlayan benzersiz bir analitik hizmettir.</p>
            </div>

            <div class="mission-box">
                <div class="flex items-center text-xl font-bold">
                    <img src="app/models/background/yıldız.png" alt="Yıldız" style="margin-right: 20px; width: 30px; height: 30px;">
                    Misyonumuz
                </div>
                <div class="mt-4 text-secondary">
                    E-spor görüntüleme verilerine yönelik şeffaflığı ve güveni artırmak, sponsorların, organizatörlerin ve izleyicilerin güvenilir bir üçüncü taraf aracılığıyla herhangi bir espor etkinliğinin popülaritesini daha iyi anlamalarını sağlamak.
                </div>

                <div class="social-links">
                    <a href="https://www.biorganizasyon.com.tr/tepki.pm/" target="_blank"><i class="fas fa-share-alt"></i> Sosyal Medya</a>
                    <a href="https://www.whatsapp.com/channel/0029Vb2L7mgHltYB70NEcL09" target="_blank"><i class="fab fa-whatsapp"></i> WhatsApp Kanalı</a>
                </div>
            </div>
        </div>

        <!-- Sağ: Görsel -->
        <div class="about-image">
            <img src="app/models/background/jumptron.svg" alt="about">
        </div>
    </div>
</section>


<!-- Footer -->
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
