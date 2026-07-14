<?php
// MODELO — Conexión a la base de datos "apsti".
// La primera vez crea la base y la tabla si no existen.
// Prueba con contraseña 'root' (esta máquina) y sin contraseña (XAMPP).
mysqli_report(MYSQLI_REPORT_OFF);
$conexion = @new mysqli('127.0.0.1', 'root', 'root');
if ($conexion->connect_errno) {
    $conexion = @new mysqli('127.0.0.1', 'root', '');
}
if ($conexion->connect_errno) {
    die('No se pudo conectar a MySQL: ' . $conexion->connect_error);
}

$conexion->set_charset('utf8mb4');
$conexion->query("CREATE DATABASE IF NOT EXISTS apsti CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci");
$conexion->select_db('apsti');
$conexion->query("CREATE TABLE IF NOT EXISTS mensajes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(150) NOT NULL,
    mensaje TEXT NOT NULL,
    fecha_envio TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci");

$conexion->query("CREATE TABLE IF NOT EXISTS tramites (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    solicitante VARCHAR(150) NOT NULL,
    correo VARCHAR(150) NULL,
    telefono VARCHAR(30) NULL,
    tipo_tramite VARCHAR(120) NOT NULL,
    area_destino VARCHAR(120) NOT NULL,
    origen ENUM('publico', 'interno') NOT NULL DEFAULT 'interno',
    estado ENUM('pendiente', 'proceso', 'atendido') NOT NULL DEFAULT 'pendiente',
    documento_nombre VARCHAR(255) NULL,
    documento_mime VARCHAR(100) NULL,
    documento_tamano INT UNSIGNED NULL,
    documento_contenido LONGBLOB NULL,
    fecha_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci");

// Migración: agrega columnas nuevas si la tabla "tramites" ya existía sin ellas
$columnasNuevasTramites = [
    'correo'              => "ALTER TABLE tramites ADD COLUMN correo VARCHAR(150) NULL AFTER solicitante",
    'telefono'            => "ALTER TABLE tramites ADD COLUMN telefono VARCHAR(30) NULL AFTER correo",
    'origen'              => "ALTER TABLE tramites ADD COLUMN origen ENUM('publico', 'interno') NOT NULL DEFAULT 'interno' AFTER area_destino",
    'documento_nombre'    => "ALTER TABLE tramites ADD COLUMN documento_nombre VARCHAR(255) NULL",
    'documento_mime'      => "ALTER TABLE tramites ADD COLUMN documento_mime VARCHAR(100) NULL",
    'documento_tamano'    => "ALTER TABLE tramites ADD COLUMN documento_tamano INT UNSIGNED NULL",
    'documento_contenido' => "ALTER TABLE tramites ADD COLUMN documento_contenido LONGBLOB NULL",
];
foreach ($columnasNuevasTramites as $columna => $alterSql) {
    $existe = $conexion->query("SHOW COLUMNS FROM tramites LIKE '$columna'");
    if ($existe && $existe->num_rows === 0) {
        $conexion->query($alterSql);
    }
}

$conexion->query("CREATE TABLE IF NOT EXISTS biblioteca (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    descripcion VARCHAR(255) NULL,
    categoria VARCHAR(60) NOT NULL DEFAULT 'Material digital',
    nombre_archivo VARCHAR(255) NOT NULL,
    tipo_mime VARCHAR(100) NOT NULL,
    tamano_bytes INT UNSIGNED NOT NULL,
    contenido LONGBLOB NOT NULL,
    fecha_subida TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci");
