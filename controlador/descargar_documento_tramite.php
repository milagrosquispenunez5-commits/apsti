<?php
// CONTROLADOR — Sirve el documento adjunto de un trámite: al administrador, o al cliente dueño del trámite.
require_once __DIR__ . '/../modelo/auth.php';
require_once __DIR__ . '/../modelo/tramite.php';

if (!estaAutenticado()) {
    header('Location: ../index.html');
    exit;
}

$id = (int) ($_GET['id'] ?? 0);
$documento = $id > 0 ? obtenerDocumentoTramite($id) : null;

if (!$documento) {
    http_response_code(404);
    echo 'Documento no encontrado.';
    exit;
}

$esDueno = rolActual() === 'cliente' && (int) $documento['id_usuario'] === idUsuarioActual();
if (rolActual() !== 'administrador' && !$esDueno) {
    http_response_code(403);
    echo 'No tienes permiso para ver este documento.';
    exit;
}

$disposicion = isset($_GET['ver']) ? 'inline' : 'attachment';

header('Content-Type: ' . $documento['documento_mime']);
header('Content-Disposition: ' . $disposicion . '; filename="' . basename($documento['documento_nombre']) . '"');
header('Content-Length: ' . strlen($documento['documento_contenido']));
echo $documento['documento_contenido'];
