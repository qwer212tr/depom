<?php
// session_start() fonksiyonunun, bu menü kodunun çağrıldığı PHP dosyasının
// en başında (herhangi bir HTML çıktısından önce) çağrıldığından emin olun.
// Genellikle projenizin ana giriş noktası olan index.php veya ortak bir header dosyası içinde yer alır.
?>
<div class="menu">
    <ul>
        <li><a href="/index.php">Ana Sayfa</a></li>
        <li><a href="/hakkinda.php">Hakkında</a></li>
        <li><a href="/iletisim.php">İletişim</a></li>
        <li><a href="/etkinlikler.php">Etkinlikler</a></li>

        <li class="dropdown">
            <a href="#">Genel Sıralama</a>
            <ul class="dropdown-content">
                <li><a href="/profil/turnuva.siralama/pubg.mobil.siralama.php">Pubg Mobil</a></li>
                <li><a href="/siralama/csgo_siralama.php">CS:GO</a></li>
                <li><a href="/siralama/valorant_siralama.php">Valorant</a></li>
                <li><a href="/siralama/lol_siralama.php">League of Legends</a></li>
                </ul>
        </li>

        <li><a href="/turnuvalar.php">Turnuvalar</a></li>

        <li class="dropdown">
            <a href="#">Turnuva Skor</a>
            <ul class="dropdown-content">
                <li><a href="/turnuva.skor/pubg_mobil_skor.php">Pubg Mobil</a></li>
                <li><a href="/turnuva.skor/csgo_skor.php">CS:GO</a></li>
                <li><a href="/turnuva.skor/valorant_skor.php">Valorant</a></li>
                <li><a href="/turnuva.skor/lol_skor.php">League of Legends</a></li>
                </ul>
        </li>

        <li class="dropdown">
            <a href="#">Başvuru</a>
            <ul class="dropdown-content">
                <li><a href="/basvuru/ekip_basvuru.php">Ekip Başvuru</a></li>
                <li><a href="/basvuru/yayin_basvuru.php">Yayıncı Başvuru</a></li>
                <li><a href="/basvuru/sponsor_basvuru.php">Sponsor Başvuru</a></li>
                <li><a href="/basvuru/gonullu_basvuru.php">Gönüllü Başvuru</a></li>
                </ul>
        </li>

        <li><a href="/medya.php">Medya Merkezi</a></li>

        <?php
        // Kullanıcı giriş yapmamışsa (yani 'kullanici_id' session değişkeni ayarlanmamışsa)
        if (!isset($_SESSION['kullanici_id'])):
        ?>
            <li><a href="/register.php">Kaydol</a></li>
            <li><a href="/login.php">Giriş</a></li>
        <?php
        // Kullanıcı giriş yapmışsa (yani 'kullanici_id' session değişkeni ayarlanmışsa)
        else:
        ?>
            <li class="dropdown">
                <a href="#">Turnuva Kayıt</a>
                <ul class="dropdown-content">
                    <li><a href="/profil/turnuva.kayit/pubg.mobil.turnuva.kayit.php">Pubg Mobil</a></li>
                    <li><a href="/profil/turnuva.kayit/csgo_turnuva_kayit.php">CS:GO</a></li>
                    <li><a href="/profil/turnuva.kayit/valorant_turnuva_kayit.php">Valorant</a></li>
                    </ul>
            </li>
            
            <li class="dropdown">
                <a href="#">Profil</a>
                <ul class="dropdown-content">
                    <li><a href="/profil/dashboard.php">Profilim</a></li>
                    <li><a href="/profil/ayarlar.php">Ayarlar</a></li>
                    <li><a href="/logout.php">Çıkış</a></li>
                    </ul>
            </li>
        <?php endif; ?>
    </ul>
</div>