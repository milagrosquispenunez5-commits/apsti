<?php
// CONTROLADOR — Sirve el documento adjunto de un trámite (desde el dashboard).
require_once __DIR__ . '/../modelo/tramite.php';

$id = (int) ($_GET['id'] ?? 0);
$documento = $id > 0 ? obtenerDocumentoTramite($id) : null;

if (!$documento) {
    http_response_code(404);
    echo 'Documento no encontrado.';
    exit;
}

$disposicion = isset($_GET['ver']) ? 'inline' : 'attachment';

header('Content-Type: ' . $documento['documento_mime']);
header('Content-Disposition: ' . $disposicion . '; filename="' . basename($documento['documento_nombre']) . '"');
header('Content-Length: ' . strlen($documento['documento_contenido']));
echo $documento['documento_contenido'];
