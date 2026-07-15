<?php
// CONTROLADOR — Consulta pública del estado de un trámite por su código y correo electrónico.
require_once __DIR__ . '/../modelo/tramite.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$codigo = trim($_POST['codigo'] ?? '');
$correo = trim($_POST['correo'] ?? '');

if (empty($codigo) || empty($correo)) {
    http_response_code(400);
    echo json_encode(['error' => 'Debes ingresar el código del trámite y el correo electrónico.']);
    exit;
}

// El código tiene el formato MP-AAAA-NNNN (ej. MP-2026-0001)
// Extraemos la última parte que es el ID del trámite
if (!preg_match('/^MP-\d{4}-(\d+)$/i', $codigo, $matches)) {
    http_response_code(400);
    echo json_encode(['error' => 'El formato del código de trámite no es válido. Debe ser similar a MP-2026-0001.']);
    exit;
}

$idTramite = (int) $matches[1];
$tramite = obtenerTramitePorId($idTramite);

if (!$tramite) {
    http_response_code(404);
    echo json_encode(['error' => 'No se encontró ningún trámite con el código proporcionado.']);
    exit;
}

// Validar que el correo coincida con el registrado para mayor seguridad
if (strcasecmp($tramite['correo'], $correo) !== 0) {
    http_response_code(403);
    echo json_encode(['error' => 'El correo electrónico no coincide con el registrado para este trámite.']);
    exit;
}

// Preparar datos para responder de forma segura
$estadoLegible = ESTADOS_TRAMITE_PERMITIDOS[$tramite['estado']] ?? $tramite['estado'];
$fechaFormateada = date('d/m/Y H:i', strtotime($tramite['fecha_registro']));

$respuesta = [
    'ok' => true,
    'tramite' => [
        'codigo' => $codigo,
        'solicitante' => $tramite['solicitante'],
        'tipo_tramite' => $tramite['tipo_tramite'],
        'detalle' => $tramite['detalle'],
        'area_destino' => $tramite['area_destino'],
        'estado' => $tramite['estado'],
        'estado_legible' => $estadoLegible,
        'fecha' => $fechaFormateada,
        'documento' => $tramite['documento_nombre']
    ]
];

echo json_encode($respuesta);
