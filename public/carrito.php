<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/auth.php';

$idUsuario = $_SESSION['id_usuario'] ?? null;
$items = [];
$subtotal = 0;
$impuestos = 0;
$envio = 0;
$total = 0;

if ($idUsuario) {
    $sql = "SELECT c.id, c.cantidad,
                   p.id AS producto_id,
                   p.nombre,
                   p.descripcion,
                   p.precio,
                   p.stock,
                   p.ruta_imagen,
                   (c.cantidad * p.precio) AS subtotal_item
            FROM carrito c
            INNER JOIN productos p ON c.id_producto = p.id
            WHERE c.id_usuario = ?
            ORDER BY c.id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idUsuario]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($items as $it) {
        $subtotal += (float)$it['subtotal_item'];
    }
}

$impuestos = $subtotal * 0.13;
$envio = $subtotal > 0 ? 2500 : 0;
$total = $subtotal + $impuestos + $envio;

$fotoPerfilNavbar = null;

if (estaLogueado()) {
    $stmtUser = $pdo->prepare("SELECT foto_perfil FROM usuarios WHERE id = ?");
    $stmtUser->execute([$_SESSION['id_usuario']]);
    $userData = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if ($userData && !empty($userData['foto_perfil'])) {
        $fotoPerfilNavbar = $userData['foto_perfil'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito - SuperGO</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light navbar-sg sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center me-3" href="index.php">
            <img src="img/logo.png" alt="SuperGO" style="height:70px; width:auto;">
        </a>

        <div class="ms-auto d-flex align-items-center gap-2">
    <a class="nav-pill nav-pill-soft text-decoration-none" href="index.php">Productos</a>

    <?php if (estaLogueado()): ?>
        <a href="perfil.php" class="text-decoration-none d-flex align-items-center gap-2">
            <?php if (!empty($fotoPerfilNavbar)): ?>
                <img
                    src="<?= htmlspecialchars($fotoPerfilNavbar) ?>"
                    alt="Foto de perfil"
                    class="nav-avatar"
                >
            <?php else: ?>
                <div class="nav-avatar-initial">
                    <?= strtoupper(substr($_SESSION['nombre'], 0, 1)) ?>
                </div>
            <?php endif; ?>

            <span class="fw-semibold text-dark bg-white px-3 py-2 rounded-pill">
                <?= htmlspecialchars($_SESSION['nombre']) ?>
            </span>
        </a>

        <a class="nav-pill nav-pill-soft text-decoration-none" href="logout.php">Salir</a>
    <?php else: ?>
        <a class="nav-pill nav-pill-login text-decoration-none" href="login.php">Iniciar sesión</a>
    <?php endif; ?>
</div>
    </div>
</nav>

<main class="container my-4">

    <div class="mb-3">
        <h1 class="display-6 page-title mb-1">Tu carrito</h1>
        <div class="text-muted" style="font-weight:500;">Revisa cantidades y confirma tu compra</div>
    </div>

    <?php if (!estaLogueado()): ?>
        <div class="alert alert-warning">
            Estás viendo el carrito como invitado. Para proceder al pago necesitas iniciar sesión.
        </div>
    <?php endif; ?>

    <?php if (!estaLogueado() || empty($items)): ?>
        <div class="alert alert-info">
            Tu carrito está vacío. <a href="index.php">Ir al catálogo</a>
        </div>
    <?php else: ?>
        <div class="card card-sg mb-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Producto</th>
                                <th class="text-end">Precio</th>
                                <th class="text-center" style="width:220px;">Cantidad</th>
                                <th class="text-end">Subtotal</th>
                                <th style="width:110px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $it): ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold"><?= htmlspecialchars($it['nombre']) ?></div>
                                        <div class="text-muted small"><?= htmlspecialchars($it['descripcion']) ?></div>
                                    </td>

                                    <td class="text-end">
                                        ₡ <?= number_format($it['precio'], 0, ',', '.') ?>
                                    </td>

                                    <td class="text-center">
                                        <form action="update_carrito.php" method="post" class="d-flex gap-2 justify-content-center align-items-center">
                                            <input type="hidden" name="item_id" value="<?= (int)$it['id'] ?>">
                                            <input class="form-control input-qty" type="number" min="0" max="<?= (int)$it['stock'] ?>" name="cantidad" value="<?= (int)$it['cantidad'] ?>" style="max-width:90px">
                                            <button class="btn btn-outline-primary btn-main" type="submit">Actualizar</button>
                                        </form>
                                    </td>

                                    <td class="text-end">
                                        ₡ <?= number_format($it['subtotal_item'], 0, ',', '.') ?>
                                    </td>

                                    <td class="text-end">
                                        <form action="remove_carrito.php" method="post" class="mb-0">
                                            <input type="hidden" name="item_id" value="<?= (int)$it['id'] ?>">
                                            <button class="btn btn-outline-danger btn-main" type="submit">Quitar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-6">
                <form action="vaciar_carrito.php" method="post" class="mb-0">
                    <button class="btn btn-outline-secondary btn-main px-4" type="submit">Vaciar carrito</button>
                </form>
            </div>

            <div class="col-lg-6">
                <div class="totals-box">
                    <div class="totals-row">
                        <span>Subtotal</span>
                        <strong>₡ <?= number_format($subtotal, 0, ',', '.') ?></strong>
                    </div>
                    <div class="totals-row">
                        <span>Impuestos (13%)</span>
                        <strong>₡ <?= number_format($impuestos, 0, ',', '.') ?></strong>
                    </div>
                    <div class="totals-row">
                        <span>Envío</span>
                        <strong>₡ <?= number_format($envio, 0, ',', '.') ?></strong>
                    </div>

                    <div class="totals-total">
                        <span>Total</span>
                        <strong>₡ <?= number_format($total, 0, ',', '.') ?></strong>
                    </div>

                    <div class="mt-3 text-end">
                        <a class="btn btn-main-lg text-decoration-none" href="checkout.php">
                            Proceder a pago
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>

<footer class="footer-sg border-top py-3">
    <div class="container text-center small text-muted">
        SuperGO 2026. Todos los Derechos Reservados
    </div>
</footer>

<script src="js/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>