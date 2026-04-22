<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$itemId = (int)($_POST['item_id'] ?? 0);
$cantidad = (int)($_POST['cantidad'] ?? 0);
$idUsuario = (int)$_SESSION['id_usuario'];

$stmt = $pdo->prepare("SELECT c.id, p.stock
                       FROM carrito c
                       INNER JOIN productos p ON c.id_producto = p.id
                       WHERE c.id = ? AND c.id_usuario = ?");
$stmt->execute([$itemId, $idUsuario]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    header('Location: carrito.php');
    exit;
}

if ($cantidad <= 0) {
    $stmt = $pdo->prepare("DELETE FROM carrito WHERE id = ? AND id_usuario = ?");
    $stmt->execute([$itemId, $idUsuario]);
    header('Location: carrito.php');
    exit;
}

if ($cantidad > (int)$item['stock']) {
    header('Location: carrito.php');
    exit;
}

$stmt = $pdo->prepare("UPDATE carrito SET cantidad = ? WHERE id = ? AND id_usuario = ?");
$stmt->execute([$cantidad, $itemId, $idUsuario]);

header('Location: carrito.php');
exit;