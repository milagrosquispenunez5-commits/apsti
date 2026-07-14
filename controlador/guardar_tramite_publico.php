<?php
// CONTROLADOR — Recibe el formulario público de mesa de partes (con documento opcional) y lo guarda.
require_once __DIR__ . '/../modelo/tramite.php';

header('Content-Type: application/json; charset=utf-8');

const TAMANO_MAXIMO_DOCUMENTO = 15 * 1024 * 1024; // 15 MB

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$solicitante = trim($_POST['solicitante'] ?? '');
$correo      = trim($_POST['correo'] ?? '');
$telefono    = trim($_POST['telefono'] ?? '');
$tipoTramite = trim($_POST['tipo_tramite'] ?? '');

if ($solicitante === '' || $correo === '' || $tipoTramite === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan datos: nombre, correo y tipo de trámite son obligatorios.']);
    exit;
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'El correo ingresado no es válido.']);
    exit;
}

$documentoNombre = null;
$documentoMime = null;
$documentoTamano = null;
$documentoContenido = null;

if (isset($_FILES['documento']) && $_FILES['documento']['error'] !== UPLOAD_ERR_NO_FILE) {
    $documento = $_FILES['documento'];

    if ($documento['error'] !== UPLOAD_ERR_OK || $documento['size'] <= 0 || $documento['size'] > TAMANO_MAXIMO_DOCUMENTO) {
        http_response_code(400);
        echo json_encode(['error' => 'El documento no es válido o supera los 15 MB permitidos.']);
        exit;
    }

    $documentoNombre = $documento['name'];
    $documentoMime = $documento['type'] ?: 'application/octet-stream';
    $documentoTamano = $documento['size'];
    $documentoContenido = file_get_contents($documento['tmp_name']);
}

$id = guardarTramitePublico($solicitante, $correo, $telefono, $tipoTramite, $documentoNombre, $documentoMime, $documentoTamano, $documentoContenido);

if ($id !== false) {
    $codigo = 'MP-' . date('Y') . '-' . str_pad($id, 4, '0', STR_PAD_LEFT);
    http_response_code(201);
    echo json_encode(['ok' => true, 'id' => $id, 'codigo' => $codigo]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'No se pudo registrar el trámite.']);
}
