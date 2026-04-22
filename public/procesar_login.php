<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

$correo = trim($_POST['correo'] ?? '');
$contrasena = $_POST['contrasena'] ?? '';

if ($correo === '' || $contrasena === '') {
    header('Location: login.php?error=Debes completar todos los campos');
    exit;
}

$stmt = $pdo->prepare('SELECT id, nombre, correo, contrasena, rol FROM usuarios WHERE correo = ?');
$stmt->execute([$correo]);
$usuario = $stmt->fetch();

if (!$usuario || !password_verify($contrasena, $usuario['contrasena'])) {
    header('Location: login.php?error=Correo o contraseña incorrectos');
    exit;
}

$_SESSION['id_usuario'] = $usuario['id'];
$_SESSION['nombre'] = $usuario['nombre'];
$_SESSION['correo'] = $usuario['correo'];
$_SESSION['rol'] = $usuario['rol'];

header('Location: index.php');
exit;
