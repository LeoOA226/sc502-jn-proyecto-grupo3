<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT ruta_imagen FROM productos WHERE id = ?");
    $stmt->execute([$id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->execute([$id]);

    if ($producto && !empty($producto['ruta_imagen'])) {
        $rutaFisica = __DIR__ . '/' . $producto['ruta_imagen'];
        if (file_exists($rutaFisica)) {
            unlink($rutaFisica);
        }
    }
}

header("Location: admin.php?ok=Producto eliminado correctamente");
exit;