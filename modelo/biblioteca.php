<?php
// MODELO — Operaciones sobre la tabla "biblioteca" (biblioteca digital).
// El archivo se guarda dentro de la base de datos (columna LONGBLOB).
require_once __DIR__ . '/conexion.php';

const CATEGORIAS_BIBLIOTECA_PERMITIDAS = ['Guía de estudio', 'Material digital', 'Recurso recomendado'];

// Guarda un archivo y devuelve su id (o false si falló)
function guardarArchivoBiblioteca($titulo, $descripcion, $categoria, $nombreArchivo, $tipoMime, $tamanoBytes, $contenido)
{
    global $conexion;
    $sql = $conexion->prepare('INSERT INTO biblioteca (titulo, descripcion, categoria, nombre_archivo, tipo_mime, tamano_bytes, contenido) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $sql->bind_param('sssssis', $titulo, $descripcion, $categoria, $nombreArchivo, $tipoMime, $tamanoBytes, $contenido);
    return $sql->execute() ? $conexion->insert_id : false;
}

// Devuelve el listado de archivos (sin el contenido binario) del más reciente al más antiguo
function listarArchivosBiblioteca()
{
    global $conexion;
    $resultado = $conexion->query(
        'SELECT id, titulo, descripcion, categoria, nombre_archivo, tipo_mime, tamano_bytes, fecha_subida
         FROM biblioteca ORDER BY fecha_subida DESC, id DESC'
    );
    return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
}

// Devuelve nombre, tipo y contenido de un archivo para poder descargarlo
function obtenerArchivoBiblioteca($id)
{
    global $conexion;
    $sql = $conexion->prepare('SELECT nombre_archivo, tipo_mime, contenido FROM biblioteca WHERE id = ?');
    $sql->bind_param('i', $id);
    $sql->execute();
    $resultado = $sql->get_result();
    return $resultado ? $resultado->fetch_assoc() : null;
}

// Elimina un archivo por su id
function eliminarArchivoBiblioteca($id)
{
    global $conexion;
    $sql = $conexion->prepare('DELETE FROM biblioteca WHERE id = ?');
    $sql->bind_param('i', $id);
    return $sql->execute();
}

// Convierte un tamaño en bytes a un texto legible (KB/MB)
function formatearTamano($bytes)
{
    if ($bytes >= 1048576) {
        return round($bytes / 1048576, 1) . ' MB';
    }
    if ($bytes >= 1024) {
        return round($bytes / 1024, 1) . ' KB';
    }
    return $bytes . ' B';
}
