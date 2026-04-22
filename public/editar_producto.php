<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->execute([$id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    header("Location: admin.php?error=Producto no encontrado");
    exit;
}

$categorias = $pdo->query("SELECT * FROM categorias ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar producto</title>
    <style>
        body{font-family:Arial;background:#f5f5f5}
        .box{max-width:700px;margin:30px auto;background:#fff;padding:25px;border-radius:12px}
        input,textarea,select,button{width:100%;padding:12px;margin-top:10px;margin-bottom:15px;box-sizing:border-box}
        button{background:#198754;color:#fff;border:none;border-radius:8px;cursor:pointer}
        img{max-width:180px;border-radius:10px;margin-bottom:10px}
        a{text-decoration:none}
    </style>
</head>
<body>
<div class="box">
    <h1>Editar producto</h1>

    <form action="actualizar_producto.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= (int)$producto['id'] ?>">
        <input type="hidden" name="imagen_actual" value="<?= htmlspecialchars($producto['ruta_imagen'] ?? '') ?>">

        <label>Nombre</label>
        <input type="text" name="nombre" required value="<?= htmlspecialchars($producto['nombre']) ?>">

        <label>Descripción</label>
        <textarea name="descripcion" required><?= htmlspecialchars($producto['descripcion']) ?></textarea>

        <label>Precio</label>
        <input type="number" step="0.01" name="precio" required value="<?= htmlspecialchars($producto['precio']) ?>">

        <label>Stock</label>
        <input type="number" name="stock" min="0" required value="<?= (int)$producto['stock'] ?>">

        <label>Imagen actual</label><br>
        <?php if (!empty($producto['ruta_imagen'])): ?>
            <img src="<?= htmlspecialchars($producto['ruta_imagen']) ?>" alt="Producto">
        <?php else: ?>
            <p>Sin imagen</p>
        <?php endif; ?>

        <label>Nueva imagen (opcional)</label>
        <input type="file" name="imagen" accept="image/*">

        <label>Categoría</label>
        <select name="id_categoria" required>
            <?php foreach ($categorias as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $producto['id_categoria'] == $cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Actualizar producto</button>
    </form>

    <a href="admin.php">← Volver al panel</a>
</div>
</body>
</html>