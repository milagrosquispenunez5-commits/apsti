<?php
// CONTROLADOR — Valida correo/clave en el servidor y abre sesión (cliente o administrador).
require_once __DIR__ . '/../modelo/auth.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$correo = trim($_POST['correo'] ?? '');
$clave = trim($_POST['clave'] ?? '');

$usuario = iniciarSesion($correo, $clave);

if ($usuario !== false) {
    echo json_encode(['ok' => true, 'rol' => $usuario['rol'], 'nombre' => $usuario['nombre']]);
} else {
    http_response_code(401);
    echo json_encode(['error' => 'Correo o contraseña incorrectos']);
}
