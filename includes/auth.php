<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function estaLogueado(): bool
{
    return isset($_SESSION['id_usuario']);
}

function requireLogin(): void
{
    if (!estaLogueado()) {
        header('Location: login.php?error=Debes iniciar sesión');
        exit;
    }
}

function requireAdmin(): void
{
    requireLogin();
    if (($_SESSION['rol'] ?? '') !== 'ADMIN') {
        header('Location: index.php?error=Acceso denegado');
        exit;
    }
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        exit('Solicitud inválida. Token CSRF incorrecto.');
    }
}