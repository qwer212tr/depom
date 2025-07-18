<?php
// index.php
session_start(); // session_start() buraya taşındı
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    
    <!-- ✅ Favicon files updated according to the correct folder -->
    <link rel="icon" type="image/png" sizes="16x16" href="/new/app/models/favicon/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/new/app/models/favicon/favicon-32x32.png">

    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Ana Sayfa - Bi Organizasyon</title>
    
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
		<?php include 'app/controllers/header.php'; ?>
        <?php include 'app/controllers/menu.php'; ?>
    </nav>
</div>

<div class="page-wrapper">
    <div class="content">

        <!-- Hero -->
        <section class="hero">
		
            <div class="hero-text">
				
                <h1>Turnuvalar <span class="pc">PC</span> ve 
                <span class="mobile">Mobil</span> için <br />
                <span class="pink">Bi Organizasyon</span></h1>
				<img src="app/models/background/002.gif" alt="Turnuva GIF" class="hero-gif" />
                <p>Türkiye'nin en <span class="pc">büyük oyun turnuvaları</span> platformunda yerini al.
                <span class="mobile">Takımını kur</span>, <span class="highlight">katıl</span>, ödülleri <span class="pink">kazan</span>!</p>
                <div class="buttons">
                    <a href="register.php" class="btn btn-primary">Kayıt Ol</a>
                    <a href="login.php" class="btn btn-secondary">Giriş Yap</a>
                </div>
            </div>
            <div class="hero-image">
                <img src="app/models/background/001.png" alt="Tournament Visual" />
            </div>
        </section>
        <!-- Game Types -->
        <section class="game-types">
            <div class="game-box pc-games">
                <h2>Bilgisayar Oyunları</h2>
                <p>FPS, MOBA gibi popüler oyunlarda rekabet dolu turnuvalar seni bekliyor.</p>
                <ul>
                    <li>Valorant, CS:GO, League of Legends</li>
                    <li>Amatör ve profesyonel ligler</li>
                    <li>Gerçek ödüller, gerçek mücadele</li>
                </ul>
            </div>

            <div class="game-box mobile-games">
                <h2>Mobil Oyunlar</h2>
                <p>Kolay katılım, hızlı maçlar ve eğlenceli ödüllerle mobil turnuvalar!</p>
                <ul>
                    <li>PUBG Mobile, Mobile Legends, Call of Duty Mobile</li>
                    <li>Her seviyeye uygun organizasyon</li>
                    <li>Anlık skorlar ve canlı yayınlar</li>
                </ul>
            </div>
        </section>

        <!-- Popular Tournaments -->
        <section class="popular-tournaments">
            <h2>Popüler Turnuvalar</h2>
            <div class="tournaments-grid">
                <div class="tournament-card">
                    <h3>Valorant Kış Turnuvası</h3>
                    <p><strong>Oyun:</strong> Valorant (PC)</p>
                    <p><strong>Tarih:</strong> 25 Temmuz 2025</p>
                    <p><strong>Ödül:</strong> 5.000 ₺</p>
                    <button>Turnuvaya Katıl</button>
                </div>
                <div class="tournament-card">
                    <h3>Mobile Legends Yaz Şampiyonası</h3>
                    <p><strong>Oyun:</strong> Mobile Legends (Mobil)</p>
                    <p><strong>Tarih:</strong> 10 Ağustos 2025</p>
                    <p><strong>Ödül:</strong> 3.000 ₺</p>
                    <button>Turnuvaya Katıl</button>
                </div>
                <div class="tournament-card">
                    <h3>CS:GO Efsaneler Kupası</h3>
                    <p><strong>Oyun:</strong> CS:GO (PC)</p>
                    <p><strong>Tarih:</strong> 5 Eylül 2025</p>
                    <p><strong>Ödül:</strong> 7.000 ₺</p>
                    <button>Turnuvaya Katıl</button>
                </div>
            </div>
        </section>

    </div>

    <?php include 'app/controllers/footer.php'; ?>
</div>

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
