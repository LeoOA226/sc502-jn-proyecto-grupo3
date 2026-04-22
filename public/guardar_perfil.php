<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$idUsuario = (int)$_SESSION['id_usuario'];
$nombre = trim($_POST['nombre'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$direccion = trim($_POST['direccion'] ?? '');
$fotoActual = trim($_POST['foto_actual'] ?? '');

if ($nombre === '' || $correo === '') {
    header("Location: editar_perfil.php");
    exit;
}

$rutaFoto = $fotoActual;

if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
    $tmp = $_FILES['foto']['tmp_name'];
    $nombreOriginal = $_FILES['foto']['name'];
    $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

    $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($extension, $permitidas)) {
        header("Location: editar_perfil.php");
        exit;
    }

    $nuevoNombre = uniqid('perfil_', true) . '.' . $extension;
    $rutaFisica = __DIR__ . '/uploads/perfiles/' . $nuevoNombre;
    $rutaFoto = 'uploads/perfiles/' . $nuevoNombre;

    if (!move_uploaded_file($tmp, $rutaFisica)) {
        header("Location: editar_perfil.php");
        exit;
    }

    if ($fotoActual && file_exists(__DIR__ . '/' . $fotoActual)) {
        unlink(__DIR__ . '/' . $fotoActual);
    }
}

$stmt = $pdo->prepare("UPDATE usuarios
                       SET nombre = ?, correo = ?, telefono = ?, direccion = ?, foto_perfil = ?
                       WHERE id = ?");
$stmt->execute([$nombre, $correo, $telefono, $direccion, $rutaFoto, $idUsuario]);

$_SESSION['nombre'] = $nombre;
$_SESSION['correo'] = $correo;

header("Location: perfil.php");
exit;