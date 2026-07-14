<?php
// CONTROLADOR — Devuelve el estado de la sesión actual (para que el sitio público adapte la UI).
require_once __DIR__ . '/../modelo/auth.php';

header('Content-Type: application/json; charset=utf-8');

if (estaAutenticado()) {
    echo json_encode([
        'autenticado' => true,
        'rol' => rolActual(),
        'nombre' => $_SESSION['usuario_nombre'],
    ]);
} else {
    echo json_encode(['autenticado' => false]);
}
