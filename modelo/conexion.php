<?php
// MODELO — Conexión estándar a la base de datos "apsti".

$host = '127.0.0.1';
$user = 'root';
$pass = ''; // Por defecto para XAMPP
$db   = 'apsti';

// Intenta conectar a la base de datos 'apsti'
$conexion = @new mysqli($host, $user, $pass, $db);

// Si falla (por ejemplo, si la contraseña de root es 'root' en tu máquina)
if ($conexion->connect_errno) {
    $pass = 'root';
    $conexion = @new mysqli($host, $user, $pass, $db);
}

// Si persiste el error, detiene la ejecución
if ($conexion->connect_errno) {
    die('Error de conexión a la base de datos: ' . $conexion->connect_error);
}

$conexion->set_charset('utf8mb4');
