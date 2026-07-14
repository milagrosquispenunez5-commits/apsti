<?php
// CONTROLADOR — Recibe el formulario de mesa de partes de un cliente autenticado y lo guarda.
require_once __DIR__ . '/../modelo/auth.php';
require_once __DIR__ . '/../modelo/tramite.php';
require_once __DIR__ . '/../modelo/validacion_archivos.php';

header('Content-Type: application/json; charset=utf-8');

const TAMANO_MAXIMO_DOCUMENTO = 15 * 1024 * 1024; // 15 MB

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

if (!estaAutenticado() || rolActual() !== 'cliente') {
    http_response_code(401);
    echo json_encode(['error' => 'Debes iniciar sesión como cliente para enviar un trámite.']);
    exit;
}

$usuario = obtenerUsuarioPorId(idUsuarioActual());
if (!$usuario) {
    http_response_code(401);
    echo json_encode(['error' => 'Tu sesión ya no es válida. Inicia sesión de nuevo.']);
    exit;
}

$tipoTramite = trim($_POST['tipo_tramite'] ?? '');
$detalle     = trim($_POST['detalle'] ?? '');

if (!in_array($tipoTramite, TIPOS_TRAMITE_PERMITIDOS, true)) {
    http_response_code(400);
    echo json_encode(['error' => 'El tipo de trámite seleccionado no es válido.']);
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

    $documentoMime = validarArchivoSubido($documento['name'], $documento['tmp_name']);
    if ($documentoMime === null) {
        http_response_code(400);
        echo json_encode(['error' => 'El tipo de documento no está permitido.']);
        exit;
    }

    $documentoNombre = $documento['name'];
    $documentoTamano = $documento['size'];
    $documentoContenido = file_get_contents($documento['tmp_name']);
}

$id = guardarTramitePublico(
    $usuario['id'],
    $usuario['nombre'],
    $usuario['correo'],
    $usuario['telefono'],
    $tipoTramite,
    $detalle,
    $documentoNombre,
    $documentoMime,
    $documentoTamano,
    $documentoContenido
);

if ($id !== false) {
    $codigo = 'MP-' . date('Y') . '-' . str_pad($id, 4, '0', STR_PAD_LEFT);
    http_response_code(201);
    echo json_encode(['ok' => true, 'id' => $id, 'codigo' => $codigo]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'No se pudo registrar el trámite.']);
}
