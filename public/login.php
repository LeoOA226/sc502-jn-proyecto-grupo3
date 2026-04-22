<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/auth.php';

if (estaLogueado()) {
    header('Location: index.php');
    exit;
}

$error = '';
$msg = $_GET['ok'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE correo = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($password, $usuario['contrasena'])) {
        $_SESSION['id_usuario'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['rol'] = $usuario['rol'];

        header('Location: index.php');
        exit;
    } else {
        $error = 'Correo o contraseña incorrectos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Login - SuperGO</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-light navbar-sg sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center me-3" href="index.php">
            <img src="img/logo.png" alt="SuperGO" style="height:70px; width:auto; margin-right:8px;">
        </a>

        <button class="navbar-toggler border-0 bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navLogin">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navLogin">
            <form action="index.php" method="get" class="d-flex flex-grow-1 justify-content-center my-2 my-lg-0">
                <div class="input-group nav-search">
                    <input class="form-control" type="text" name="q" placeholder="Buscar productos...">
                    <button class="btn btn-light" type="submit">Buscar</button>
                </div>
            </form>

            <div class="d-flex align-items-center ms-lg-3 mt-2 mt-lg-0">
                <a class="nav-pill nav-pill-cart text-decoration-none me-2" href="carrito.php">
                    Ver carrito
                </a>

                <a class="nav-pill nav-pill-login text-decoration-none" href="login.php">
                    Iniciar sesión
                </a>
            </div>
        </div>
    </div>
</nav>

<section class="container auth-shell d-flex justify-content-center align-items-center py-4">
    <div style="width:100%;max-width:460px;">

        <div class="auth-hero">
            <img src="img/abarrotes.jpg" alt="SuperGO">
            <div class="overlay">
                <div>
                    <p class="title">Bienvenido a SuperGO</p>
                    <p class="subtitle">Inicia sesión para comprar rápido y ver tu carrito.</p>
                </div>
            </div>
        </div>

        <div class="card auth-card card-sg">
            <div class="card-body p-4 p-md-4">
                <h1 class="h4 mb-1" style="font-weight:700;">Ingresar</h1>
                <p class="text-muted mb-4" style="font-weight:500;">Accede con tu correo y contraseña</p>

                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">

                    <div class="mb-3">
                        <label class="form-label">Correo</label>
                        <input class="form-control" type="email" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input class="form-control" type="password" name="password" required>
                    </div>

                    <button class="btn btn-auth w-100" type="submit">Entrar</button>
                </form>

                <?php if ($error): ?>
                    <div class="alert alert-danger mt-3 mb-0"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <?php if ($msg): ?>
                    <div class="alert alert-info mt-3 mb-0"><?= htmlspecialchars($msg) ?></div>
                <?php endif; ?>

                <?php if (!empty($_GET['error'])): ?>
                    <div class="alert alert-warning mt-3 mb-0"><?= htmlspecialchars($_GET['error']) ?></div>
                <?php endif; ?>

                <div class="links mt-3">
                    <a href="registro.php">Registrarme</a>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="footer-sg border-top py-3 mt-4">
    <div class="container text-center small text-muted">
        SuperGO 2026. Todos los Derechos Reservados
    </div>
</footer>

<script src="js/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>