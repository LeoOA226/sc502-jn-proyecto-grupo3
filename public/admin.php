<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

$tab = $_GET['tab'] ?? 'productos';

$productos = $pdo->query("SELECT p.*, c.nombre AS categoria_nombre
                          FROM productos p
                          INNER JOIN categorias c ON p.id_categoria = c.id
                          ORDER BY p.id DESC")->fetchAll();

$usuarios = $pdo->query("SELECT id, nombre, correo, rol, telefono, direccion, fecha_creacion
                         FROM usuarios ORDER BY id DESC")->fetchAll();

$pedidos = $pdo->query("SELECT p.*, u.nombre AS cliente
                        FROM pedidos p
                        INNER JOIN usuarios u ON p.id_usuario = u.id
                        ORDER BY p.fecha DESC")->fetchAll();

$reporte = $pdo->query("SELECT
                            COUNT(*) AS total_pedidos,
                            COALESCE(SUM(total),0) AS ventas_totales,
                            COALESCE(AVG(total),0) AS ticket_promedio
                        FROM pedidos")->fetch();

$masVendidos = $pdo->query("SELECT pr.nombre, SUM(d.cantidad) AS unidades_vendidas, SUM(d.subtotal) AS monto
                            FROM detalle_pedido d
                            INNER JOIN productos pr ON pr.id = d.id_producto
                            GROUP BY d.id_producto, pr.nombre
                            ORDER BY unidades_vendidas DESC
                            LIMIT 5")->fetchAll();

$pedidosPorFecha = $pdo->query("SELECT DATE(fecha) AS fecha_dia, COUNT(*) AS pedidos, SUM(total) AS ventas
                                FROM pedidos
                                GROUP BY DATE(fecha)
                                ORDER BY fecha_dia DESC
                                LIMIT 10")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel admin - SuperGO</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Panel administrativo</h1>
        <div>
            <a class="btn btn-outline-secondary" href="index.php">Catálogo</a>
            <a class="btn btn-outline-danger" href="logout.php">Salir</a>
        </div>
    </div>

    <?php if (!empty($_GET['ok'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['ok']) ?></div>
    <?php endif; ?>

    <?php if (!empty($_GET['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <div class="mb-4 d-flex gap-2 flex-wrap">
        <a class="btn btn-outline-dark" href="admin.php?tab=productos">Productos</a>
        <a class="btn btn-outline-dark" href="admin.php?tab=usuarios">Usuarios</a>
        <a class="btn btn-outline-dark" href="admin.php?tab=pedidos">Pedidos</a>
        <a class="btn btn-outline-dark" href="admin.php?tab=reportes">Reportes</a>
    </div>

    <?php if ($tab === 'productos'): ?>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5">Gestión de productos</h2>
            <a class="btn btn-main" href="crear_producto.php">Crear producto</a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered bg-white">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($productos as $p): ?>
                    <tr>
                        <td><?= (int)$p['id'] ?></td>
                        <td><?= htmlspecialchars($p['nombre']) ?></td>
                        <td><?= htmlspecialchars($p['categoria_nombre']) ?></td>
                        <td>₡<?= number_format($p['precio'], 2) ?></td>
                        <td><?= (int)$p['stock'] ?></td>
                        <td><?= htmlspecialchars($p['descripcion']) ?></td>
                        <td>
                            <a href="editar_producto.php?id=<?= (int)$p['id'] ?>">Editar</a> |
                            <a href="eliminar_producto.php?id=<?= (int)$p['id'] ?>" onclick="return confirm('¿Eliminar producto?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php elseif ($tab === 'usuarios'): ?>
        <h2 class="h5 mb-3">Usuarios</h2>
        <div class="table-responsive">
            <table class="table table-bordered bg-white">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Teléfono</th>
                        <th>Dirección</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?= (int)$u['id'] ?></td>
                        <td><?= htmlspecialchars($u['nombre']) ?></td>
                        <td><?= htmlspecialchars($u['correo']) ?></td>
                        <td><?= htmlspecialchars($u['rol']) ?></td>
                        <td><?= htmlspecialchars($u['telefono']) ?></td>
                        <td><?= htmlspecialchars($u['direccion']) ?></td>
                        <td><?= htmlspecialchars($u['fecha_creacion']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php elseif ($tab === 'pedidos'): ?>
        <h2 class="h5 mb-3">Pedidos</h2>
        <div class="table-responsive">
            <table class="table table-bordered bg-white">
                <thead class="table-light">
                    <tr>
                        <th>Número</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Método</th>
                        <th>Estado</th>
                        <th>Total</th>
                        <th>Detalle</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($pedidos as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['numero_pedido']) ?></td>
                        <td><?= htmlspecialchars($p['cliente']) ?></td>
                        <td><?= htmlspecialchars($p['fecha']) ?></td>
                        <td><?= htmlspecialchars($p['metodo_pago']) ?></td>
                        <td><?= htmlspecialchars($p['estado']) ?></td>
                        <td>₡<?= number_format($p['total'], 2) ?></td>
                        <td><a href="detalle_pedido.php?id=<?= (int)$p['id'] ?>">Ver</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php else: ?>
        <h2 class="h5 mb-4">Reportes básicos</h2>

        <div class="row g-3 mb-4">
            <div class="col-md-4"><div class="card p-3"><strong>Total de ventas</strong><div class="fs-4">₡<?= number_format($reporte['ventas_totales'], 2) ?></div></div></div>
            <div class="col-md-4"><div class="card p-3"><strong>Pedidos registrados</strong><div class="fs-4"><?= (int)$reporte['total_pedidos'] ?></div></div></div>
            <div class="col-md-4"><div class="card p-3"><strong>Ticket promedio</strong><div class="fs-4">₡<?= number_format($reporte['ticket_promedio'], 2) ?></div></div></div>
        </div>

        <h3 class="h6">Productos más vendidos</h3>
        <div class="table-responsive mb-4">
            <table class="table table-bordered bg-white">
                <thead class="table-light">
                    <tr>
                        <th>Producto</th>
                        <th>Unidades vendidas</th>
                        <th>Monto</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($masVendidos as $m): ?>
                    <tr>
                        <td><?= htmlspecialchars($m['nombre']) ?></td>
                        <td><?= (int)$m['unidades_vendidas'] ?></td>
                        <td>₡<?= number_format($m['monto'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <h3 class="h6">Pedidos por fecha</h3>
        <div class="table-responsive">
            <table class="table table-bordered bg-white">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Pedidos</th>
                        <th>Ventas</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($pedidosPorFecha as $f): ?>
                    <tr>
                        <td><?= htmlspecialchars($f['fecha_dia']) ?></td>
                        <td><?= (int)$f['pedidos'] ?></td>
                        <td>₡<?= number_format($f['ventas'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
</body>
</html>