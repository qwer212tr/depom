<?php
// Oturum başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Bi Organizasyon</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="16x16" href="/new/app/models/favicon/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/new/app/models/favicon/favicon-32x32.png">

    <!-- Stil dosyası -->
    <link rel="stylesheet" href="/assets/globals.css" />
</head>
<body>

<!-- Logo ve Menü -->
<div class="top-bar">
    <div class="logo"></div>
    <?php include 'menu.php'; ?>
</div>
