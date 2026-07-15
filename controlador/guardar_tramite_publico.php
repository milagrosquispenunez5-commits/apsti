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

$idUsuario = null;
$solicitante = '';
$correo = '';
$telefono = null;

if (estaAutenticado() && rolActual() === 'cliente') {
    $usuario = obtenerUsuarioPorId(idUsuarioActual());
    if ($usuario) {
        $idUsuario = $usuario['id'];
        $solicitante = $usuario['nombre'];
        $correo = $usuario['correo'];
        $telefono = $usuario['telefono'];
    }
}

if ($idUsuario === null) {
    // Es un trámite público registrado por un invitado
    $solicitante = trim($_POST['solicitante'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    
    if (empty($solicitante) || empty($correo)) {
        http_response_code(400);
        echo json_encode(['error' => 'Debes completar tu nombre completo y correo electrónico para enviar el trámite.']);
        exit;
    }
    
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['error' => 'El formato del correo electrónico no es válido.']);
        exit;
    }
    
    if (empty($telefono)) {
        $telefono = null;
    }
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
    $idUsuario,
    $solicitante,
    $correo,
    $telefono,
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
