<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();
verifyCsrf();

$idUsuario = (int)$_SESSION['id_usuario'];
$idPedido = (int)($_POST['id_pedido'] ?? 0);
$idProducto = (int)($_POST['id_producto'] ?? 0);
$puntuacion = (int)($_POST['puntuacion'] ?? 0);
$comentario = trim($_POST['comentario'] ?? '');

if ($idPedido <= 0 || $idProducto <= 0 || $puntuacion < 1 || $puntuacion > 5 || $comentario === '') {
    header('Location: detalle_pedido.php?id=' . $idPedido . '&ok=' . urlencode('Datos inválidos'));
    exit;
}

$stmt = $pdo->prepare("SELECT COUNT(*)
                       FROM pedidos p
                       INNER JOIN detalle_pedido d ON d.id_pedido = p.id
                       WHERE p.id = ? AND p.id_usuario = ? AND d.id_producto = ?");
$stmt->execute([$idPedido, $idUsuario, $idProducto]);

if (!$stmt->fetchColumn()) {
    exit('No puedes valorar un producto que no compraste.');
}

try {
    $stmt = $pdo->prepare("INSERT INTO valoraciones (id_usuario, id_producto, id_pedido, puntuacion, comentario)
                           VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$idUsuario, $idProducto, $idPedido, $puntuacion, $comentario]);
    $msg = 'Valoración guardada correctamente';
} catch (PDOException $e) {
    $msg = 'Ya habías valorado ese producto en este pedido';
}

header('Location: detalle_pedido.php?id=' . $idPedido . '&ok=' . urlencode($msg));
exit;