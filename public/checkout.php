<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$idUsuario = (int)$_SESSION['id_usuario'];

$stmt = $pdo->prepare("SELECT c.id, c.cantidad, p.id AS producto_id, p.nombre, p.precio, p.stock,
                              (c.cantidad * p.precio) AS subtotal_item
                       FROM carrito c
                       INNER JOIN productos p ON c.id_producto = p.id
                       WHERE c.id_usuario = ?");
$stmt->execute([$idUsuario]);
$items = $stmt->fetchAll();

if (!$items) {
    header('Location: carrito.php?error=Tu carrito está vacío');
    exit;
}

$subtotal = 0;
foreach ($items as $it) {
    $subtotal += (float)$it['subtotal_item'];
}
$impuestos = $subtotal * 0.13;
$envio = 2500;
$total = $subtotal + $impuestos + $envio;

$stmt = $pdo->prepare("SELECT nombre, correo, telefono, direccion FROM usuarios WHERE id = ?");
$stmt->execute([$idUsuario]);
$usuario = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Checkout - SuperGO</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <p><a href="carrito.php">← Volver al carrito</a></p>
    <h1 class="h3 mb-4">Confirmación de compra</h1>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card p-4 card-sg">
                <form method="post" action="procesar_compra.php">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">

                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input class="form-control" type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre'] ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Correo</label>
                        <input class="form-control" type="email" name="correo" value="<?= htmlspecialchars($usuario['correo'] ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                        <input class="form-control" type="text" name="telefono" value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dirección de entrega</label>
                        <textarea class="form-control" name="direccion" required><?= htmlspecialchars($usuario['direccion'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Método de pago</label>
                        <select class="form-select" name="metodo_pago" required>
                            <option value="">Seleccione</option>
                            <option value="Tarjeta">Tarjeta</option>
                            <option value="SINPE">SINPE</option>
                            <option value="Efectivo">Efectivo contra entrega</option>
                        </select>
                    </div>

                    <button class="btn btn-main w-100" type="submit">Confirmar compra</button>
                </form>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card p-4 card-sg">
                <h2 class="h5">Resumen del pedido</h2>
                <ul class="list-group mb-3">
                    <?php foreach ($items as $it): ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><?= htmlspecialchars($it['nombre']) ?> x <?= (int)$it['cantidad'] ?></span>
                            <strong>₡<?= number_format($it['subtotal_item'], 2) ?></strong>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <p><strong>Subtotal:</strong> ₡<?= number_format($subtotal, 2) ?></p>
                <p><strong>Impuestos:</strong> ₡<?= number_format($impuestos, 2) ?></p>
                <p><strong>Envío:</strong> ₡<?= number_format($envio, 2) ?></p>
                <p class="fs-5"><strong>Total:</strong> ₡<?= number_format($total, 2) ?></p>
            </div>
        </div>
    </div>
</div>
</body>
</html>