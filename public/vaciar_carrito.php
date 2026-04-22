<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$idUsuario = (int)$_SESSION['id_usuario'];

$stmt = $pdo->prepare("DELETE FROM carrito WHERE id_usuario = ?");
$stmt->execute([$idUsuario]);

header('Location: carrito.php');
exit;