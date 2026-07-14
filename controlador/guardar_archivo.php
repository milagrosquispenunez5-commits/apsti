<?php
// CONTROLADOR — Sube un archivo a la biblioteca digital y lo guarda en la base de datos.
require_once __DIR__ . '/../modelo/auth.php';
requerirRol('administrador');

require_once __DIR__ . '/../modelo/biblioteca.php';
require_once __DIR__ . '/../modelo/validacion_archivos.php';

const TAMANO_MAXIMO_ARCHIVO = 15 * 1024 * 1024; // 15 MB

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo'])) {
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    if (!in_array($categoria, CATEGORIAS_BIBLIOTECA_PERMITIDAS, true)) {
        $categoria = 'Material digital';
    }
    $archivo = $_FILES['archivo'];

    if (
        $titulo !== ''
        && $archivo['error'] === UPLOAD_ERR_OK
        && $archivo['size'] > 0
        && $archivo['size'] <= TAMANO_MAXIMO_ARCHIVO
    ) {
        $tipoMime = validarArchivoSubido($archivo['name'], $archivo['tmp_name']);
        if ($tipoMime !== null) {
            $contenido = file_get_contents($archivo['tmp_name']);
            guardarArchivoBiblioteca(
                $titulo,
                $descripcion,
                $categoria,
                $archivo['name'],
                $tipoMime,
                $archivo['size'],
                $contenido
            );
        }
    }
}

header('Location: ../vista/dashboard.php#biblioteca');
