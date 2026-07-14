<?php
// CONTROLADOR — Expone en JSON el material de la biblioteca digital para el sitio público.
require_once __DIR__ . '/../modelo/biblioteca.php';

header('Content-Type: application/json; charset=utf-8');

$archivos = listarArchivosBiblioteca();

$datos = array_map(function ($a) {
    return [
        'id'          => (int) $a['id'],
        'titulo'      => $a['titulo'],
        'descripcion' => $a['descripcion'],
        'categoria'   => $a['categoria'],
        'tamano'      => formatearTamano($a['tamano_bytes']),
        'ver'         => 'controlador/descargar_archivo.php?id=' . $a['id'] . '&ver=1',
        'descarga'    => 'controlador/descargar_archivo.php?id=' . $a['id'],
    ];
}, $archivos);

echo json_encode($datos);
