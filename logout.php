<?php
session_start();

// Tüm oturum verilerini temizle
$_SESSION = [];

// Oturumu tamamen yok et
session_destroy();

// (İsteğe bağlı) Oturum çerezini de temizle
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Giriş sayfasına veya ana sayfaya yönlendir
header("Location: ../index.php");
exit();
?>
