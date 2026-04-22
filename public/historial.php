<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$stmt = $pdo->prepare("SELECT * FROM pedidos WHERE id_usuario = ? ORDER BY fecha DESC");
$stmt->execute([$_SESSION['id_usuario']]);
$pedidos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial - SuperGO</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <p><a href="index.php">← Volver al catálogo</a></p>
    <h1 class="h3 mb-4">Historial de compras</h1>

    <?php if (!$pedidos): ?>
        <div class="alert alert-info">No has realizado compras todavía.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered bg-white">
                <thead class="table-light">
                    <tr>
                        <th>Número</th>
                        <th>Fecha</th>
                        <th>Método de pago</th>
                        <th>Estado</th>
                        <th>Total</th>
                        <th>Detalle</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($pedidos as $pedido): ?>
                    <tr>
                        <td><?= htmlspecialchars($pedido['numero_pedido']) ?></td>
                        <td><?= htmlspecialchars($pedido['fecha']) ?></td>
                        <td><?= htmlspecialchars($pedido['metodo_pago']) ?></td>
                        <td><?= htmlspecialchars($pedido['estado']) ?></td>
                        <td>₡<?= number_format($pedido['total'], 2) ?></td>
                        <td><a href="detalle_pedido.php?id=<?= (int)$pedido['id'] ?>">Ver detalle</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
</body>
</html>