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
