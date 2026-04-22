<?php
$charset = 'utf8mb4';

$candidatos = [
    [
        'host' => getenv('DB_HOST') ?: 'mysql',
        'dbname' => getenv('DB_NAME') ?: 'supergo',
        'usuario' => getenv('DB_USER') ?: 'root',
        'contrasena' => getenv('DB_PASS') ?: 'root',
    ],
    [
        'host' => 'localhost',
        'dbname' => 'supergo',
        'usuario' => 'root',
        'contrasena' => '',
    ],
];

$pdo = null;
$ultimoError = 'No se pudo establecer conexión';

foreach ($candidatos as $cfg) {
    try {
        $dsn = "mysql:host={$cfg['host']};dbname={$cfg['dbname']};charset={$charset}";
        $pdo = new PDO($dsn, $cfg['usuario'], $cfg['contrasena'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        break;
    } catch (PDOException $e) {
        $ultimoError = $e->getMessage();
    }
}

if (!$pdo) {
    die('Error de conexión a la base de datos: ' . $ultimoError);
}