<?php
// pubg.mobil.siralama.php
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/png" sizes="16x16" href="/new/app/models/favicon/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/new/app/models/favicon/favicon-32x32.png">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Pubg Mobil Güç Sıralama - Bi Organizasyon</title>
    <link rel="stylesheet" href="../../assets/globals.css" />
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

<div class="ranking-table">
	<h1 style="color: yellow; text-align: center;">Pubg Mobil Güç Sıralama</h1>
        </div>
        <div class="section-description">
            <p>Burada, organizasyonumuzun en güçlü takımlarını bulabilirsiniz. Güç sıralamaları düzenli olarak güncellenmekte ve her dönem, başarılarına göre yeniden değerlendirilmektedir.</p>
            <p>Bu sıralama, takımlarımızın performanslarını, projelerine katkılarını ve genel verimliliklerini göz önünde bulundurularak oluşturulmaktadır.</p>
            <p>Her bir takım, hedeflere ulaşma yolunda gösterdiği liderlik ve yenilikçi yaklaşım sayesinde bu listeye girmeyi başarmaktadır.</p>
            <p>Gelişen projeler ve başarılar, bu sıralamanın dinamik bir şekilde değişmesine yol açmakta, dolayısıyla organizasyonumuzun güçlü takımları her zaman aktif bir şekilde izlenmektedir.</p>
            <p>Not: Güç sıralamaları, sadece proje başarılarıyla sınırlı kalmayıp takım içindeki sinerji, işbirliği ve takım ruhu gibi önemli faktörler de dikkate alınarak hazırlanır. Bu sayede sadece bireysel değil, grup başarısının da vurgulanması sağlanır.</p>
        </div>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Sıra</th>
                <th>Logo</th>
                <th>Takım</th>
                <th>Puan</th>
                <th>Not</th>
            </tr>
        </thead>
        <tbody>
        <?php
        include '../../config/db.php'; // PDO bağlantısını dahil ediyoruz

        try {
            $sql = "SELECT * FROM pubg_mobil_siralama ORDER BY puan DESC";
            $stmt = $pdo->query($sql);
            $sira = 1;

            foreach ($stmt as $row) {
                echo "<tr>";
                echo "<td>" . $sira++ . "</td>";
                echo "<td><img src='" . htmlspecialchars($row['logo']) . "' alt='Logo' width='32' height='32'></td>";
                echo "<td>" . htmlspecialchars($row['isim']) . "</td>";
                echo "<td>" . $row['puan'] . "</td>";
                echo "<td>" . htmlspecialchars($row['notlar']) . "</td>";
                echo "</tr>";
            }
        } catch (PDOException $e) {
            echo "<tr><td colspan='5'>Veri çekilemedi: " . $e->getMessage() . "</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>

<?php include '../../app/controllers/footer.php'; ?>
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
    setTimeout(function() {
        location.reload();
    }, 20000);

    function toggleMenu() {
        document.querySelector('.top-bar').classList.toggle('active');
    }
</script>
</body>
</html>
