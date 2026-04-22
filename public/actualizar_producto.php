<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

$id = (int)($_POST['id'] ?? 0);
$nombre = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$precio = (float)($_POST['precio'] ?? 0);
$stock = (int)($_POST['stock'] ?? 0);
$id_categoria = (int)($_POST['id_categoria'] ?? 0);
$imagenActual = trim($_POST['imagen_actual'] ?? '');

if ($id <= 0 || $nombre === '' || $descripcion === '' || $precio <= 0 || $stock < 0 || $id_categoria <= 0) {
    header("Location: admin.php?error=Datos inválidos");
    exit;
}

$ruta_imagen = $imagenActual;

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
        header("Location: admin.php?error=No se pudo subir la nueva imagen");
        exit;
    }

    if ($imagenActual && file_exists(__DIR__ . '/' . $imagenActual)) {
        unlink(__DIR__ . '/' . $imagenActual);
    }
}

$stmt = $pdo->prepare("UPDATE productos
                       SET nombre = ?, descripcion = ?, precio = ?, stock = ?, ruta_imagen = ?, id_categoria = ?
                       WHERE id = ?");
$stmt->execute([$nombre, $descripcion, $precio, $stock, $ruta_imagen, $id_categoria, $id]);

header("Location: admin.php?ok=Producto actualizado correctamente");
exit;