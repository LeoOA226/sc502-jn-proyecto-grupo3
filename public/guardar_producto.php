<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

$nombre = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$precio = (float)($_POST['precio'] ?? 0);
$stock = (int)($_POST['stock'] ?? 0);
$id_categoria = (int)($_POST['id_categoria'] ?? 0);

if ($nombre === '' || $descripcion === '' || $precio <= 0 || $stock < 0 || $id_categoria <= 0) {
    header("Location: crear_producto.php");
    exit;
}

$ruta_imagen = null;

if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
    $archivoTmp = $_FILES['imagen']['tmp_name'];
    $nombreOriginal = $_FILES['imagen']['name'];
    $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

    $extPermitidas = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    if (!in_array($extension, $extPermitidas)) {
        header("Location: admin.php?error=Formato de imagen no permitido");
        exit;
    }

    $nombreNuevo = uniqid('prod_', true) . '.' . $extension;
    $rutaFisica = __DIR__ . '/uploads/productos/' . $nombreNuevo;
    $ruta_imagen = 'uploads/productos/' . $nombreNuevo;

    if (!move_uploaded_file($archivoTmp, $rutaFisica)) {
        header("Location: admin.php?error=No se pudo subir la imagen");
        exit;
    }
}

$stmt = $pdo->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, ruta_imagen, id_categoria)
                       VALUES (?, ?, ?, ?, ?, ?)");
$stmt->execute([$nombre, $descripcion, $precio, $stock, $ruta_imagen, $id_categoria]);

header("Location: admin.php?ok=Producto creado correctamente");
exit;