<?php
session_start(); 

$_SESSION['last_activity'] = time();

include '../config/db.php'; 


$result = []; 

try {
    $sql = "SELECT id, team_name, wwcd, place, kills, score FROM pubg_mobil_skor ORDER BY score DESC, wwcd DESC, kills DESC, place ASC, id DESC";
    
    $stmt = $pdo->prepare($sql); 
    $stmt->execute(); 

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC); 

} catch (PDOException $e) {
    error_log("Veritabanı hatası: " . $e->getMessage()); 
    die("Beklenmeyen bir hata oluştu. Lütfen daha sonra tekrar deneyiniz."); 
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/png" sizes="16x16" href="/new/app/models/favicon/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/new/app/models/favicon/favicon-32x32.png">

    <meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta http-equiv="refresh" content="20">
    <title>Pubg Mobil Skor - Bi Organizasyon</title>

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
		<?php include '../app/controllers/header.php'; ?>
    </nav>
</div>

    <script>
    document.onkeydown = document.onmousedown = document.oncontextmenu = function(e) {
        if (e.keyCode == 123 || e.button == 2 || e.button == 3) {
            return false;
        }
    };
    document.onkeypress = function(e) {
        if (e.keyCode == 96) {
            document.oncontextmenu = null;
        }
    };
    </script>

    <main>
        <h2 style="color: yellow; text-align: center;">Pubg Mobil Güncel Skor</h2>

        <table>
            <thead>
                <tr>
                    <th>RANK</th>
                    <th>TEAM NAME</th>
                    <th>WWCD</th>
                    <th>PLACE</th>
                    <th>KILLS</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // RANK HESAPLAMA MANTIĞI:
                // Veritabanından gelen veriler zaten istediğimiz sıraya göre sıralanmış olduğu için,
                // her takıma kesintisiz (1, 2, 3...) bir sıra numarası atıyoruz.
                $rank = 1; 

                foreach ($result as $row) {
                    echo "<tr>";
                    echo "<td data-label=\"RANK\">{$rank}</td>"; 
                    echo "<td data-label=\"TEAM NAME\">{$row['team_name']}</td>"; // Varsayılan: 'team_name'
                    echo "<td data-label=\"WWCD\">{$row['wwcd']}</td>";
                    echo "<td data-label=\"PLACE\">{$row['place']}</td>";
                    echo "<td data-label=\"KILLS\">{$row['kills']}</td>";
                    echo "<td data-label=\"TOTAL\">{$row['score']}</td>"; // Varsayılan: 'score'
                    echo "</tr>";
                    
                    $rank++; // Her takım için rank'ı bir artır
                }
                ?>
            </tbody>
        </table>
    </main>

    <footer>
        <?php include '../app/controllers/footer.php'; ?>
    </footer>
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