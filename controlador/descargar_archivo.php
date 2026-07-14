<?php
// CONTROLADOR — Sirve la descarga (o vista previa) de un archivo de la biblioteca digital.
require_once __DIR__ . '/../modelo/biblioteca.php';

$id = (int) ($_GET['id'] ?? 0);
$archivo = $id > 0 ? obtenerArchivoBiblioteca($id) : null;

if (!$archivo) {
    http_response_code(404);
    echo 'Archivo no encontrado.';
    exit;
}

$disposicion = isset($_GET['ver']) ? 'inline' : 'attachment';

header('Content-Type: ' . $archivo['tipo_mime']);
header('Content-Disposition: ' . $disposicion . '; filename="' . basename($archivo['nombre_archivo']) . '"');
header('Content-Length: ' . strlen($archivo['contenido']));
echo $archivo['contenido'];
