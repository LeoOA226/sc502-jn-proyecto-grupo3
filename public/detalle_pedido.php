<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$idPedido = (int)($_GET['id'] ?? 0);

$sql = "SELECT p.*, u.nombre, u.correo
        FROM pedidos p
        INNER JOIN usuarios u ON p.id_usuario = u.id
        WHERE p.id = ?";
$params = [$idPedido];

if (($_SESSION['rol'] ?? '') !== 'ADMIN') {
    $sql .= " AND p.id_usuario = ?";
    $params[] = $_SESSION['id_usuario'];
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$pedido = $stmt->fetch();

if (!$pedido) {
    exit('Pedido no encontrado o sin permisos.');
}

$stmt = $pdo->prepare("SELECT d.*, pr.nombre AS producto_nombre
                       FROM detalle_pedido d
                       INNER JOIN productos pr ON d.id_producto = pr.id
                       WHERE d.id_pedido = ?");
$stmt->execute([$idPedido]);
$detalles = $stmt->fetchAll();

$valoradas = [];
if (($_SESSION['rol'] ?? '') === 'CLIENTE') {
    $stmt = $pdo->prepare("SELECT id_producto FROM valoraciones WHERE id_usuario = ? AND id_pedido = ?");
    $stmt->execute([$_SESSION['id_usuario'], $idPedido]);
    $valoradas = array_map('intval', array_column($stmt->fetchAll(), 'id_producto'));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del pedido</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <p><a href="<?= ($_SESSION['rol'] ?? '') === 'ADMIN' ? 'admin.php?tab=pedidos' : 'historial.php' ?>">← Volver</a></p>

    <?php if (!empty($_GET['ok'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['ok']) ?></div>
    <?php endif; ?>

    <div class="card p-4 mb-4 card-sg">
        <h1 class="h3">Pedido <?= htmlspecialchars($pedido['numero_pedido']) ?></h1>
        <p><strong>Cliente:</strong> <?= htmlspecialchars($pedido['nombre']) ?></p>
        <p><strong>Correo:</strong> <?= htmlspecialchars($pedido['correo']) ?></p>
        <p><strong>Fecha:</strong> <?= htmlspecialchars($pedido['fecha']) ?></p>
        <p><strong>Método:</strong> <?= htmlspecialchars($pedido['metodo_pago']) ?></p>
        <p><strong>Estado:</strong> <?= htmlspecialchars($pedido['estado']) ?></p>
        <p><strong>Total:</strong> ₡<?= number_format($pedido['total'], 2) ?></p>
    </div>

    <div class="card p-4 card-sg">
        <h2 class="h5 mb-3">Productos comprados</h2>
        <div class="table-responsive">
            <table class="table table-bordered bg-white">
                <thead class="table-light">
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio unitario</th>
                        <th>Subtotal</th>
                        <th>Valoración</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($detalles as $d): ?>
                    <tr>
                        <td><?= htmlspecialchars($d['producto_nombre']) ?></td>
                        <td><?= (int)$d['cantidad'] ?></td>
                        <td>₡<?= number_format($d['precio_unitario'], 2) ?></td>
                        <td>₡<?= number_format($d['subtotal'], 2) ?></td>
                        <td>
                            <?php if (($_SESSION['rol'] ?? '') === 'CLIENTE'): ?>
                                <?php if (in_array((int)$d['id_producto'], $valoradas, true)): ?>
                                    Ya valorado
                                <?php else: ?>
                                    <form action="guardar_valoracion.php" method="post">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                                        <input type="hidden" name="id_pedido" value="<?= (int)$pedido['id'] ?>">
                                        <input type="hidden" name="id_producto" value="<?= (int)$d['id_producto'] ?>">

                                        <select class="form-select mb-2" name="puntuacion" required>
                                            <option value="">Puntaje</option>
                                            <option value="5">5</option>
                                            <option value="4">4</option>
                                            <option value="3">3</option>
                                            <option value="2">2</option>
                                            <option value="1">1</option>
                                        </select>

                                        <textarea class="form-control mb-2" name="comentario" maxlength="255" placeholder="Comentario breve" required></textarea>
                                        <button class="btn btn-sm btn-main" type="submit">Guardar</button>
                                    </form>
                                <?php endif; ?>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>