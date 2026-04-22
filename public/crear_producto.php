<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

$categorias = $pdo->query("SELECT * FROM categorias ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear producto</title>
    <style>
        body{font-family:Arial;background:#f5f5f5}
        .box{max-width:700px;margin:30px auto;background:#fff;padding:25px;border-radius:12px}
        input,textarea,select,button{width:100%;padding:12px;margin-top:10px;margin-bottom:15px;box-sizing:border-box}
        button{background:#198754;color:#fff;border:none;border-radius:8px;cursor:pointer}
        a{text-decoration:none}
    </style>
</head>
<body>
<div class="box">
    <h1>Crear producto</h1>

    <form action="guardar_producto.php" method="POST" enctype="multipart/form-data">
        <label>Nombre</label>
        <input type="text" name="nombre" required>

        <label>Descripción</label>
        <textarea name="descripcion" required></textarea>

        <label>Precio</label>
        <input type="number" step="0.01" name="precio" required>

        <label>Stock</label>
        <input type="number" name="stock" min="0" required>

        <label>Imagen del producto</label>
        <input type="file" name="imagen" accept="image/*">

        <label>Categoría</label>
        <select name="id_categoria" required>
            <option value="">Seleccione una categoría</option>
            <?php foreach ($categorias as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Guardar producto</button>
    </form>

    <a href="admin.php">← Volver al panel</a>
</div>
</body>
</html>