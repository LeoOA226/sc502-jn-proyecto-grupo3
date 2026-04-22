<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$idUsuario = (int)$_SESSION['id_usuario'];

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$idUsuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    header("Location: logout.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar perfil</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .perfil-box{max-width:700px;margin:30px auto;background:#fff;padding:25px;border-radius:16px;box-shadow:0 6px 18px rgba(0,0,0,.08)}
        .perfil-box input,.perfil-box textarea,.perfil-box button{width:100%;padding:12px;margin-top:10px;margin-bottom:15px}
        .perfil-box img{width:120px;height:120px;object-fit:cover;border-radius:50%;margin-bottom:15px}
    </style>
</head>
<body>
    <div class="perfil-box">
        <h1>Editar perfil</h1>

        <?php if (!empty($usuario['foto_perfil'])): ?>
            <img src="<?= htmlspecialchars($usuario['foto_perfil']) ?>" alt="Foto de perfil">
        <?php else: ?>
            <p>Sin foto de perfil</p>
        <?php endif; ?>

        <form action="guardar_perfil.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="foto_actual" value="<?= htmlspecialchars($usuario['foto_perfil'] ?? '') ?>">

            <label>Nombre</label>
            <input type="text" name="nombre" required value="<?= htmlspecialchars($usuario['nombre']) ?>">

            <label>Correo</label>
            <input type="email" name="correo" required value="<?= htmlspecialchars($usuario['correo']) ?>">

            <label>Teléfono</label>
            <input type="text" name="telefono" value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>">

            <label>Dirección</label>
            <textarea name="direccion"><?= htmlspecialchars($usuario['direccion'] ?? '') ?></textarea>

            <label>Nueva foto de perfil</label>
            <input type="file" name="foto" accept="image/*">

            <button type="submit">Guardar cambios</button>
        </form>
    </div>
</body>
</html>