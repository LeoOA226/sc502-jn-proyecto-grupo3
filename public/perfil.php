<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$idUsuario = (int)$_SESSION['id_usuario'];

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$idUsuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi perfil</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .perfil-box{max-width:700px;margin:30px auto;background:#fff;padding:25px;border-radius:16px;box-shadow:0 6px 18px rgba(0,0,0,.08)}
        .perfil-box img{width:130px;height:130px;object-fit:cover;border-radius:50%;margin-bottom:15px}
    </style>
</head>
<body>
<a href="index.php" class="btn-volver">← Volver al catálogo</a>
    <div class="perfil-box">
        <h1>Mi perfil</h1>

        <?php if (!empty($usuario['foto_perfil'])): ?>
            <img src="<?= htmlspecialchars($usuario['foto_perfil']) ?>" alt="Foto de perfil">
        <?php else: ?>
            <p>Sin foto de perfil</p>
        <?php endif; ?>

        <p><strong>Nombre:</strong> <?= htmlspecialchars($usuario['nombre']) ?></p>
        <p><strong>Correo:</strong> <?= htmlspecialchars($usuario['correo']) ?></p>
        <p><strong>Teléfono:</strong> <?= htmlspecialchars($usuario['telefono'] ?? '') ?></p>
        <p><strong>Dirección:</strong> <?= htmlspecialchars($usuario['direccion'] ?? '') ?></p>

        <a href="editar_perfil.php">Editar perfil</a>
    </div>
</body>
</html>