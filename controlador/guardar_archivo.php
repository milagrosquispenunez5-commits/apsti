<?php
// CONTROLADOR — Sube un archivo a la biblioteca digital y lo guarda en la base de datos.
require_once __DIR__ . '/../modelo/biblioteca.php';

const TAMANO_MAXIMO_ARCHIVO = 15 * 1024 * 1024; // 15 MB

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo'])) {
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '') ?: 'Material digital';
    $archivo = $_FILES['archivo'];

    if (
        $titulo !== ''
        && $archivo['error'] === UPLOAD_ERR_OK
        && $archivo['size'] > 0
        && $archivo['size'] <= TAMANO_MAXIMO_ARCHIVO
    ) {
        $contenido = file_get_contents($archivo['tmp_name']);
        guardarArchivoBiblioteca(
            $titulo,
            $descripcion,
            $categoria,
            $archivo['name'],
            $archivo['type'] ?: 'application/octet-stream',
            $archivo['size'],
            $contenido
        );
    }
}

header('Location: ../vista/dashboard.php#biblioteca');
