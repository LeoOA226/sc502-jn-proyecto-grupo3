<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$itemId = (int)($_POST['item_id'] ?? 0);
$idUsuario = (int)$_SESSION['id_usuario'];

$stmt = $pdo->prepare("DELETE FROM carrito WHERE id = ? AND id_usuario = ?");
$stmt->execute([$itemId, $idUsuario]);

header('Location: carrito.php');
exit;