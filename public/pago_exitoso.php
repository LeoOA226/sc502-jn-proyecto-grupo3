<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();
verifyCsrf();

$idUsuario = (int)$_SESSION['id_usuario'];
$telefono = trim($_POST['telefono'] ?? '');
$direccion = trim($_POST['direccion'] ?? '');
$metodoPago = trim($_POST['metodo_pago'] ?? '');

if ($telefono === '' || $direccion === '' || $metodoPago === '') {
    header('Location: checkout.php?error=Completa todos los campos');
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("SELECT c.id, c.cantidad, p.id AS producto_id, p.nombre, p.precio, p.stock
                           FROM carrito c
                           INNER JOIN productos p ON c.id_producto = p.id
                           WHERE c.id_usuario = ?
                           FOR UPDATE");
    $stmt->execute([$idUsuario]);
    $items = $stmt->fetchAll();

    if (!$items) {
        throw new Exception('Tu carrito está vacío.');
    }

    $subtotal = 0;
    foreach ($items as $it) {
        if ((int)$it['cantidad'] > (int)$it['stock']) {
            throw new Exception('No hay stock suficiente para ' . $it['nombre']);
        }
        $subtotal += $it['cantidad'] * $it['precio'];
    }

    $impuestos = $subtotal * 0.13;
    $envio = 2500;
    $total = $subtotal + $impuestos + $envio;
    $numeroPedido = 'SG-' . date('YmdHis') . '-' . random_int(100, 999);

    $stmt = $pdo->prepare("UPDATE usuarios SET telefono = ?, direccion = ? WHERE id = ?");
    $stmt->execute([$telefono, $direccion, $idUsuario]);

    $stmt = $pdo->prepare("INSERT INTO pedidos (numero_pedido, id_usuario, subtotal, impuestos, envio, total, estado, direccion_envio, telefono_envio, metodo_pago)
                           VALUES (?, ?, ?, ?, ?, ?, 'pagado', ?, ?, ?)");
    $stmt->execute([$numeroPedido, $idUsuario, $subtotal, $impuestos, $envio, $total, $direccion, $telefono, $metodoPago]);
    $idPedido = (int)$pdo->lastInsertId();

    $stmtDetalle = $pdo->prepare("INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, precio_unitario, subtotal)
                                  VALUES (?, ?, ?, ?, ?)");
    $stmtStock = $pdo->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");

    foreach ($items as $it) {
        $sub = $it['cantidad'] * $it['precio'];
        $stmtDetalle->execute([$idPedido, $it['producto_id'], $it['cantidad'], $it['precio'], $sub]);
        $stmtStock->execute([$it['cantidad'], $it['producto_id']]);
    }

    $stmt = $pdo->prepare("DELETE FROM carrito WHERE id_usuario = ?");
    $stmt->execute([$idUsuario]);

    $pdo->commit();

    header('Location: pago_exitoso.php?pedido=' . urlencode($numeroPedido) . '&id=' . $idPedido);
    exit;

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    header('Location: carrito.php?error=' . urlencode($e->getMessage()));
    exit;
}