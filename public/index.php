<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/auth.php';

$q = trim($_GET['q'] ?? '');
$categoria = trim($_GET['categoria'] ?? '');

$categorias = $pdo->query("SELECT * FROM categorias ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT p.*, c.nombre AS categoria_nombre
        FROM productos p
        INNER JOIN categorias c ON p.id_categoria = c.id
        WHERE 1=1";

$params = [];

if ($q !== '') {
    $sql .= " AND (p.nombre LIKE :q1 OR p.descripcion LIKE :q2)";
    $params[':q1'] = "%{$q}%";
    $params[':q2'] = "%{$q}%";
}

if ($categoria !== '') {
    $sql .= " AND c.id = :categoria";
    $params[':categoria'] = $categoria;
}

$sql .= " ORDER BY p.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Banner e imágenes por categoría
|--------------------------------------------------------------------------
*/
$bannerTitulo = 'Descubre la Frescura en tu Puerta';
$bannerSubtitulo = 'Explora nuestra selección de productos frescos, abarrotes y esenciales del hogar.';
$bannerImagen = 'img/todos.jpg';
$bannerKicker = 'Catálogo completo';

$mapaBanners = [
    'Abarrotes' => ['img/abarrotes.jpg', 'Todo lo esencial para tu despensa', 'Productos básicos y confiables para tu día a día.'],
    'Jugos y Bebidas' => ['img/bebidas.png', 'Refresca tu día', 'Jugos, gaseosas y bebidas para cualquier ocasión.'],
    'Mascotas' => ['img/mascotas.png', 'Cuidado para tu mejor amigo', 'Productos pensados para su bienestar.'],
    'Bebidas Alcohólicas' => ['img/alcohol.png', 'Momentos especiales', 'Selección ideal para celebrar y compartir.'],
    'Limpieza' => ['img/limpieza.webp', 'Tu hogar impecable', 'Artículos para mantener cada espacio limpio.'],
    'Higiene y belleza' => ['img/cuidado_personal.png', 'Cuida de ti', 'Opciones de higiene y belleza para tu rutina.'],
    'Lácteos y Huevos' => ['img/lacteos.png', 'Frescura diaria', 'Productos esenciales para tu alimentación.'],
    'Electrónicos y Herramientas' => ['img/electronicos.png', 'Utilidad y tecnología', 'Herramientas y artículos prácticos para el hogar.'],
    'Rebajas' => ['img/rebajas.png', 'Ofertas imperdibles', 'Aprovecha descuentos en productos seleccionados.']
];

$nombreCategoriaSeleccionada = '';
if ($categoria !== '') {
    foreach ($categorias as $cat) {
        if ((string)$cat['id'] === (string)$categoria) {
            $nombreCategoriaSeleccionada = $cat['nombre'];
            break;
        }
    }
}

$fotoPerfilNavbar = null;

if (estaLogueado()) {
    $stmtUser = $pdo->prepare("SELECT foto_perfil FROM usuarios WHERE id = ?");
    $stmtUser->execute([$_SESSION['id_usuario']]);
    $userData = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if ($userData && !empty($userData['foto_perfil'])) {
        $fotoPerfilNavbar = $userData['foto_perfil'];
    }
}

if ($nombreCategoriaSeleccionada !== '' && isset($mapaBanners[$nombreCategoriaSeleccionada])) {
    $bannerImagen = $mapaBanners[$nombreCategoriaSeleccionada][0];
    $bannerTitulo = $mapaBanners[$nombreCategoriaSeleccionada][1];
    $bannerSubtitulo = $mapaBanners[$nombreCategoriaSeleccionada][2];
    $bannerKicker = $nombreCategoriaSeleccionada;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo - SuperGO</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="css/styles.css">

    <style>
        .navbar-sg {
            background: #7ac943;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
        }

        .navbar-left,
        .navbar-right {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .navbar-brand img {
            height: 70px;
            width: auto;
            display: block;
        }

        .nav-link-sg,
        .nav-btn-sg {
            text-decoration: none;
            font-weight: 600;
            border-radius: 999px;
            padding: 10px 16px;
            transition: 0.2s ease;
        }

        .nav-link-sg {
            color: #1d1d1d;
            background: rgba(255,255,255,0.45);
        }

        .nav-link-sg:hover {
            background: rgba(255,255,255,0.75);
        }

        .nav-btn-sg {
            color: #fff;
            background: #1f3c88;
        }

        .nav-btn-sg:hover {
            background: #162d67;
        }

        .nav-user {
            background: #fff;
            color: #1d1d1d;
            padding: 10px 16px;
            border-radius: 999px;
            font-weight: 700;
        }

        .page-wrap {
            max-width: 1250px;
            margin: 0 auto;
            padding: 24px 20px 40px;
        }

        .search-box {
            background: #fff;
            border-radius: 18px;
            padding: 18px;
            box-shadow: 0 6px 20px rgba(0,0,0,.08);
            margin-bottom: 20px;
        }

        .search-form {
            display: grid;
            grid-template-columns: 1fr 260px 160px;
            gap: 12px;
        }

        .search-form input,
        .search-form select,
        .search-form button {
            padding: 14px;
            border-radius: 12px;
            border: 1px solid #d8d8d8;
            font-size: 15px;
        }

        .search-form button {
            border: none;
            background: #7ac943;
            color: #fff;
            font-weight: 700;
            cursor: pointer;
        }

        .search-form button:hover {
            background: #67b132;
        }

        .chips {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .chip {
            text-decoration: none;
            padding: 10px 16px;
            border-radius: 999px;
            background: #fff;
            color: #333;
            font-weight: 600;
            border: 1px solid #ddd;
        }

        .chip.active,
        .chip:hover {
            background: #7ac943;
            color: white;
            border-color: #7ac943;
        }

        .hero-banner {
            position: relative;
            border-radius: 24px;
            overflow: hidden;
            min-height: 340px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,.12);
        }

        .hero-banner img {
            width: 100%;
            height: 340px;
            object-fit: cover;
            display: block;
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, rgba(0,0,0,.58) 0%, rgba(0,0,0,.22) 55%, rgba(0,0,0,.10) 100%);
            display: flex;
            align-items: center;
        }

        .hero-content {
            color: white;
            max-width: 620px;
            padding: 30px;
        }

        .hero-kicker {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            opacity: .9;
            margin-bottom: 10px;
        }

        .hero-title {
            font-size: 40px;
            line-height: 1.1;
            font-weight: 800;
            margin-bottom: 12px;
        }

        .hero-subtitle {
            font-size: 17px;
            line-height: 1.5;
            margin-bottom: 18px;
        }

        .hero-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .hero-btn {
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 999px;
            font-weight: 700;
        }

        .hero-btn.primary {
            background: #7ac943;
            color: white;
        }

        .hero-btn.secondary {
            background: white;
            color: #1d1d1d;
        }

        .section-title {
            font-size: 30px;
            font-weight: 800;
            margin-bottom: 18px;
            color: #1f1f1f;
        }

        .productos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
            gap: 22px;
        }

        .product-card {
            background: #fff;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0,0,0,.08);
            transition: transform .18s ease;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            transform: translateY(-4px);
        }

        .product-media {
            width: 100%;
            height: 220px;
            background: #eef2f3;
        }

        .product-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .product-body {
            padding: 18px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            flex: 1;
        }

        .product-category {
            font-size: 13px;
            font-weight: 700;
            color: #7a7a7a;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .product-title {
            font-size: 22px;
            font-weight: 800;
            color: #222;
            line-height: 1.2;
        }

        .product-description {
            color: #5b5b5b;
            line-height: 1.5;
            min-height: 48px;
        }

        .product-price {
            font-size: 28px;
            font-weight: 800;
            color: #1f9d46;
        }

        .product-stock {
            font-size: 14px;
            color: #444;
            font-weight: 600;
        }

        .product-actions form {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-top: 6px;
        }

        .product-actions input {
            width: 88px;
            padding: 12px;
            border-radius: 12px;
            border: 1px solid #ccc;
        }

        .product-actions button {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 12px;
            background: #1f3c88;
            color: white;
            font-weight: 700;
            cursor: pointer;
        }

        .product-actions button:hover {
            background: #162d67;
        }

        .empty-box {
            background: white;
            padding: 28px;
            border-radius: 18px;
            box-shadow: 0 8px 24px rgba(0,0,0,.08);
        }

        .footer-sg {
            margin-top: 40px;
            text-align: center;
            color: #777;
            padding: 24px;
        }

        @media (max-width: 900px) {
            .search-form {
                grid-template-columns: 1fr;
            }

            .hero-title {
                font-size: 30px;
            }
        }
    </style>
</head>
<body>

<nav class="navbar-sg">
    <div class="navbar-left">
        <a class="navbar-brand" href="index.php">
            <img src="img/logo.png" alt="SuperGO">
        </a>

        <a class="nav-link-sg" href="index.php">Inicio</a>
        <a class="nav-link-sg" href="carrito.php">Carrito</a>
    </div>

    <div class="navbar-right">
    <?php if (estaLogueado()): ?>

    <a class="nav-link-sg" href="perfil.php" style="display:flex;align-items:center;gap:10px;">
        <?php if (!empty($fotoPerfilNavbar)): ?>
            <img
                src="<?= htmlspecialchars($fotoPerfilNavbar) ?>"
                alt="Foto de perfil"
                style="width:40px;height:40px;border-radius:50%;object-fit:cover;"
            >
        <?php else: ?>
            <div style="width:40px;height:40px;border-radius:50%;background:#ffffff;color:#198754;display:flex;align-items:center;justify-content:center;font-weight:bold;">
                <?= strtoupper(substr($_SESSION['nombre'], 0, 1)) ?>
            </div>
        <?php endif; ?>

        <span class="nav-user"><?= htmlspecialchars($_SESSION['nombre']) ?></span>
    </a>

    <?php if (($_SESSION['rol'] ?? '') === 'ADMIN'): ?>
        <a class="nav-btn-sg" href="admin.php">Panel admin</a>
    <?php else: ?>
        <a class="nav-link-sg" href="historial.php">Mis pedidos</a>
    <?php endif; ?>

    <a class="nav-link-sg" href="logout.php">Salir</a>
<?php else: ?>
    <a class="nav-btn-sg" href="login.php">Iniciar sesión</a>
    <a class="nav-link-sg" href="registro.php">Registro</a>
<?php endif; ?>
</div>
</nav>

<div class="page-wrap">

    <div class="search-box">
        <form class="search-form" method="GET" action="index.php">
            <input
                type="text"
                name="q"
                placeholder="Buscar por nombre o descripción..."
                value="<?= htmlspecialchars($q) ?>"
            >

            <select name="categoria">
                <option value="">Todas las categorías</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $categoria == $cat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Buscar</button>
        </form>
    </div>

    <div class="chips">
        <a class="chip <?= $categoria === '' ? 'active' : '' ?>" href="index.php">Todas</a>
        <?php foreach ($categorias as $cat): ?>
            <a class="chip <?= $categoria == $cat['id'] ? 'active' : '' ?>"
               href="index.php?categoria=<?= $cat['id'] ?>">
                <?= htmlspecialchars($cat['nombre']) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <section class="hero-banner">
        <img src="<?= htmlspecialchars($bannerImagen) ?>" alt="Banner">
        <div class="hero-overlay">
            <div class="hero-content">
                <div class="hero-kicker"><?= htmlspecialchars($bannerKicker) ?></div>
                <div class="hero-title"><?= htmlspecialchars($bannerTitulo) ?></div>
                <div class="hero-subtitle"><?= htmlspecialchars($bannerSubtitulo) ?></div>
                <div class="hero-actions">
                    <a class="hero-btn primary" href="#productos">Ver productos</a>
                    <a class="hero-btn secondary" href="carrito.php">Ir al carrito</a>
                </div>
            </div>
        </div>
    </section>

    <h1 id="productos" class="section-title">Catálogo</h1>

    <?php if (empty($productos)): ?>
        <div class="empty-box">
            <h2>No se encontraron productos</h2>
            <p>Prueba con otra búsqueda o selecciona otra categoría.</p>
        </div>
    <?php else: ?>
        <div class="productos-grid">
            <?php foreach ($productos as $p): ?>
                <article class="product-card">
                    <div class="product-media">
                        <img
                            src="<?= htmlspecialchars(!empty($p['ruta_imagen']) ? $p['ruta_imagen'] : 'img/todos.jpg') ?>"
                            alt="<?= htmlspecialchars($p['nombre']) ?>"
                        >
                    </div>

                    <div class="product-body">
                        <div class="product-category"><?= htmlspecialchars($p['categoria_nombre']) ?></div>
                        <div class="product-title"><?= htmlspecialchars($p['nombre']) ?></div>
                        <div class="product-description"><?= htmlspecialchars($p['descripcion']) ?></div>
                        <div class="product-price">₡<?= number_format((float)$p['precio'], 2) ?></div>
                        <div class="product-stock">Stock disponible: <?= (int)$p['stock'] ?></div>

                        <?php if ((int)$p['stock'] > 0): ?>
                            <div class="product-actions">
                                <form action="agregar_carrito.php" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                                    <input type="hidden" name="productoId" value="<?= (int)$p['id'] ?>">
                                    <input type="number" name="cantidad" min="1" max="<?= (int)$p['stock'] ?>" value="1">
                                    <button type="submit">Agregar al carrito</button>
                                </form>
                            </div>
                        <?php else: ?>
                            <div style="color:#c62828;font-weight:700;">Sin stock</div>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<footer class="footer-sg">
    SuperGO 2026. Todos los Derechos Reservados
</footer>

<script src="js/script.js"></script>
</body>
</html>