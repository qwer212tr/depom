<?php
session_start();

// CAPTCHA kodu üret
$karakterler = 'ABCDEFGHJKLMNPRSTUVWXYZ2346789';
$captcha_kod = '';
for ($i = 0; $i < 6; $i++) {
    $captcha_kod .= $karakterler[rand(0, strlen($karakterler) - 1)];
}
$_SESSION['captcha_kod'] = $captcha_kod;

// Görüntü ayarları
header('Content-type: image/png');
$genislik = 300;
$yukseklik = 50;
$im = imagecreatetruecolor($genislik, $yukseklik);

// Renkler
$arka_plan_rengi = imagecolorallocate($im, 255, 255, 255); // Beyaz
$yazi_rengi = imagecolorallocate($im, 0, 0, 0);            // Siyah
$gurultu_rengi = imagecolorallocate($im, 180, 180, 180);   // Açık Gri

// Arka planı doldur
imagefilledrectangle($im, 0, 0, $genislik, $yukseklik, $arka_plan_rengi);

// Font ayarları
$font_dosyasi = __DIR__ . '/app/font/arial.ttf';  // Buraya dikkat!
$font_boyutu = 20;

if (file_exists($font_dosyasi)) {
    $bbox = imagettfbbox($font_boyutu, 0, $font_dosyasi, $captcha_kod);
    $metin_genisligi = $bbox[2] - $bbox[0];
    $x_baslangic = ($genislik - $metin_genisligi) / 2;
    $y = ($yukseklik / 2) + ($font_boyutu / 2) - 4;

    for ($i = 0; $i < strlen($captcha_kod); $i++) {
        $aci = rand(-20, 20);
        $karakter = $captcha_kod[$i];
        $harf_genisligi = imagettfbbox($font_boyutu, 0, $font_dosyasi, $karakter)[2];
        imagettftext($im, $font_boyutu, $aci, $x_baslangic, $y, $yazi_rengi, $font_dosyasi, $karakter);
        $x_baslangic += $harf_genisligi + 6;
    }
} else {
    $font = 5;
    $text_width = imagefontwidth($font) * strlen($captcha_kod);
    $x = ($genislik - $text_width) / 2;
    $y = ($yukseklik - imagefontheight($font)) / 2;
    imagestring($im, $font, $x, $y, $captcha_kod, $yazi_rengi);
}

// Gürültü: çizgiler
for ($i = 0; $i < 6; $i++) {
    imageline($im, rand(0, $genislik), rand(0, $yukseklik), rand(0, $genislik), rand(0, $yukseklik), $gurultu_rengi);
}

// Gürültü: noktalar
for ($i = 0; $i < 150; $i++) {
    imagesetpixel($im, rand(0, $genislik - 1), rand(0, $yukseklik - 1), $gurultu_rengi);
}

imagepng($im);
imagedestroy($im);
?>
