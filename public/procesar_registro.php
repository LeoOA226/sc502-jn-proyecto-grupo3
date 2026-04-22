<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

$nombre = trim($_POST['nombre'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$contrasena = $_POST['contrasena'] ?? '';

if ($nombre === '' || $correo === '' || $contrasena === '') {
    header('Location: registro.php?error=Todos los campos son obligatorios');
    exit;
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    header('Location: registro.php?error=El correo no es válido');
    exit;
}

if (strlen($contrasena) < 6) {
    header('Location: registro.php?error=La contraseña debe tener al menos 6 caracteres');
    exit;
}

$stmt = $pdo->prepare('SELECT id FROM usuarios WHERE correo = ?');
$stmt->execute([$correo]);
if ($stmt->fetch()) {
    header('Location: registro.php?error=Ese correo ya está registrado');
    exit;
}

$hash = password_hash($contrasena, PASSWORD_DEFAULT);
$stmt = $pdo->prepare('INSERT INTO usuarios (nombre, correo, contrasena, rol) VALUES (?, ?, ?, ?)');
$stmt->execute([$nombre, $correo, $hash, 'CLIENTE']);

header('Location: login.php?msg=Cuenta creada correctamente');
exit;
