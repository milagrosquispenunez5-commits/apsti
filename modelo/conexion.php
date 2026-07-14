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
    leido TINYINT(1) NOT NULL DEFAULT 0,
    fecha_envio TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci");

$existeLeido = $conexion->query("SHOW COLUMNS FROM mensajes LIKE 'leido'");
if ($existeLeido && $existeLeido->num_rows === 0) {
    $conexion->query("ALTER TABLE mensajes ADD COLUMN leido TINYINT(1) NOT NULL DEFAULT 0");
}

$conexion->query("CREATE TABLE IF NOT EXISTS tramites (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    solicitante VARCHAR(150) NOT NULL,
    correo VARCHAR(150) NULL,
    telefono VARCHAR(30) NULL,
    tipo_tramite VARCHAR(120) NOT NULL,
    detalle TEXT NULL,
    area_destino VARCHAR(120) NOT NULL,
    origen ENUM('publico', 'interno') NOT NULL DEFAULT 'interno',
    estado ENUM('pendiente', 'proceso', 'atendido', 'rechazado') NOT NULL DEFAULT 'pendiente',
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
    'detalle'             => "ALTER TABLE tramites ADD COLUMN detalle TEXT NULL AFTER tipo_tramite",
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

// Migración: agrega 'rechazado' al enum de estado si la tabla ya existía sin él
$columnaEstado = $conexion->query("SHOW COLUMNS FROM tramites LIKE 'estado'");
if ($columnaEstado && ($fila = $columnaEstado->fetch_assoc()) && strpos($fila['Type'], 'rechazado') === false) {
    $conexion->query("ALTER TABLE tramites MODIFY estado ENUM('pendiente', 'proceso', 'atendido', 'rechazado') NOT NULL DEFAULT 'pendiente'");
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

$conexion->query("CREATE TABLE IF NOT EXISTS usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE,
    telefono VARCHAR(30) NULL,
    clave_hash VARCHAR(255) NOT NULL,
    rol ENUM('cliente', 'administrador') NOT NULL DEFAULT 'cliente',
    fecha_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci");

// Siembra la cuenta de administrador si todavía no existe ninguna
// (mantiene las credenciales admin / 123456 que ya se usaban antes)
$existeAdmin = $conexion->query("SELECT id FROM usuarios WHERE rol = 'administrador' LIMIT 1");
if ($existeAdmin && $existeAdmin->num_rows === 0) {
    $sql = $conexion->prepare("INSERT INTO usuarios (nombre, correo, clave_hash, rol) VALUES (?, ?, ?, 'administrador')");
    $nombreAdmin = 'Administrador';
    $correoAdmin = 'admin@apsti.edu.pe';
    $claveHashAdmin = '$2y$12$AiojwuT/6s4TipifYblPXunzgcbM2c2CJrP8WCRlI/MHF/j9fLEzm'; // '123456'
    $sql->bind_param('sss', $nombreAdmin, $correoAdmin, $claveHashAdmin);
    $sql->execute();
}

// Migración: vincula cada trámite a la cuenta que lo registró (si existe)
$existeIdUsuario = $conexion->query("SHOW COLUMNS FROM tramites LIKE 'id_usuario'");
if ($existeIdUsuario && $existeIdUsuario->num_rows === 0) {
    $conexion->query("ALTER TABLE tramites ADD COLUMN id_usuario INT UNSIGNED NULL AFTER solicitante");
}
