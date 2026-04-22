<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$productoId = (int)($_POST['productoId'] ?? 0);
$cantidad = (int)($_POST['cantidad'] ?? 1);
$idUsuario = (int)$_SESSION['id_usuario'];

if ($productoId <= 0 || $cantidad <= 0) {
    header('Location: index.php?error=Datos inválidos para agregar al carrito');
    exit;
}

$stmt = $pdo->prepare("SELECT id, stock FROM productos WHERE id = ?");
$stmt->execute([$productoId]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    header('Location: index.php?error=El producto no existe');
    exit;
}

if ($cantidad > (int)$producto['stock']) {
    header('Location: index.php?error=La cantidad supera el stock disponible');
    exit;
}

$stmt = $pdo->prepare("SELECT id, cantidad FROM carrito WHERE id_usuario = ? AND id_producto = ?");
$stmt->execute([$idUsuario, $productoId]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if ($item) {
    $nuevaCantidad = (int)$item['cantidad'] + $cantidad;

    if ($nuevaCantidad > (int)$producto['stock']) {
        header('Location: index.php?error=No hay suficiente stock para agregar esa cantidad');
        exit;
    }

    $stmt = $pdo->prepare("UPDATE carrito SET cantidad = ? WHERE id = ?");
    $stmt->execute([$nuevaCantidad, $item['id']]);
} else {
    $stmt = $pdo->prepare("INSERT INTO carrito (id_usuario, id_producto, cantidad) VALUES (?, ?, ?)");
    $stmt->execute([$idUsuario, $productoId, $cantidad]);
}

header('Location: index.php?ok=Producto agregado al carrito');
exit;