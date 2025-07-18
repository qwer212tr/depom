<?php
session_start();
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['kullanici_id'])) {
    echo json_encode(['success' => false, 'message' => 'Giriş yapılmamış']);
    exit;
}

$user_id = $_SESSION['kullanici_id'];

try {
    $stmt = $db->prepare("SELECT bakiye FROM users WHERE id = :id");
    $stmt->execute([':id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode(['success' => true, 'bakiye' => number_format($user['bakiye'], 0, ',', '.')]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Kullanıcı bulunamadı']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Veritabanı hatası']);
}
