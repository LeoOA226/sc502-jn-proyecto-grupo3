<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/auth.php';

$error = '';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $nombre = trim($_POST['nombre'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($nombre === '' || $apellidos === '' || $username === '' || $email === '' || $password === '') {
        $error = 'Debe completar todos los campos.';
    } elseif (strlen($username) < 3) {
        $error = 'El usuario debe tener al menos 3 caracteres.';
    } elseif (strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE correo = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $error = 'Ese correo ya está registrado.';
        } else {
            $nombreCompleto = trim($nombre . ' ' . $apellidos);
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, correo, contrasena, rol)
                                   VALUES (?, ?, ?, 'CLIENTE')");
            $stmt->execute([$nombreCompleto, $email, $hash]);

            $msg = 'Cuenta creada correctamente. Ahora puedes iniciar sesión.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Crear cuenta - SuperGO</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light navbar-sg sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center me-3" href="index.php">
            <img src="img/logo.png" alt="SuperGO" style="height:70px; width:auto;">
        </a>

        <div class="ms-auto d-flex align-items-center gap-2">
            <a class="nav-pill nav-pill-login text-decoration-none" href="login.php">Iniciar sesión</a>
            <a class="nav-pill nav-pill-soft text-decoration-none" href="index.php">Ver catálogo</a>
        </div>
    </div>
</nav>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card card-sg">
                <div class="card-head">
                    <p class="card-title">Crear cuenta</p>
                    <p class="card-sub">Regístrate para comprar en SuperGO.</p>
                </div>

                <div class="card-body p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <?php if ($msg): ?>
                        <div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
                    <?php endif; ?>

                    <form method="post" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nombre</label>
                                <input class="form-control" type="text" name="nombre" maxlength="40" required>
                                <div class="invalid-feedback">Ingrese su nombre.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Apellidos</label>
                                <input class="form-control" type="text" name="apellidos" maxlength="50" required>
                                <div class="invalid-feedback">Ingrese sus apellidos.</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Usuario</label>
                                <input class="form-control" type="text" name="username" minlength="3" maxlength="25" required>
                                <div class="invalid-feedback">Ingrese un usuario (mínimo 3 caracteres).</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Correo</label>
                                <input class="form-control" type="email" name="email" maxlength="80" required>
                                <div class="invalid-feedback">Ingrese un correo válido.</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Contraseña</label>
                                <input class="form-control" type="password" name="password" minlength="6" required>
                                <div class="invalid-feedback">La contraseña debe tener al menos 6 caracteres.</div>
                            </div>
                        </div>

                        <button class="btn btn-main w-100 mt-4" type="submit">Crear cuenta</button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="login.php">¿Ya tienes cuenta? Inicia sesión</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<footer class="footer-sg border-top py-3">
    <div class="container text-center small text-muted">
        SuperGO 2026. Todos los Derechos Reservados
    </div>
</footer>

<script>
(() => {
    'use strict';
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();
</script>

<script src="js/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>