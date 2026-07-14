<?php
// CONTROLADOR — Registro público de clientes (rol fijo 'cliente'; nunca lo manda el cliente).
require_once __DIR__ . '/../modelo/usuario.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$nombre = trim($_POST['nombre'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$clave = $_POST['clave'] ?? '';

if ($nombre === '' || $correo === '' || $clave === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Nombre, correo y contraseña son obligatorios.']);
    exit;
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'El correo ingresado no es válido.']);
    exit;
}

if (strlen($clave) < 6) {
    http_response_code(400);
    echo json_encode(['error' => 'La contraseña debe tener al menos 6 caracteres.']);
    exit;
}

if (existeCorreo($correo)) {
    http_response_code(409);
    echo json_encode(['error' => 'Ya existe una cuenta con ese correo.']);
    exit;
}

$id = registrarCliente($nombre, $correo, $telefono, $clave);

if ($id !== false) {
    http_response_code(201);
    echo json_encode(['ok' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'No se pudo crear la cuenta.']);
}
