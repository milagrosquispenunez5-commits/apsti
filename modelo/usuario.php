<?php
// MODELO — Operaciones sobre la tabla "usuarios" (cuentas de clientes y administradores).
require_once __DIR__ . '/conexion.php';

// Registra un cliente nuevo y devuelve su id (o false si el correo ya existe / falló)
function registrarCliente($nombre, $correo, $telefono, $clave)
{
    global $conexion;
    $claveHash = password_hash($clave, PASSWORD_DEFAULT);
    $sql = $conexion->prepare("INSERT INTO usuarios (nombre, correo, telefono, clave_hash, rol) VALUES (?, ?, ?, ?, 'cliente')");
    $sql->bind_param('ssss', $nombre, $correo, $telefono, $claveHash);
    return $sql->execute() ? $conexion->insert_id : false;
}

// Devuelve el usuario con ese correo (con clave_hash incluido, para verificar login), o null
function buscarUsuarioPorCorreo($correo)
{
    global $conexion;
    $sql = $conexion->prepare('SELECT id, nombre, correo, telefono, clave_hash, rol FROM usuarios WHERE correo = ?');
    $sql->bind_param('s', $correo);
    $sql->execute();
    $resultado = $sql->get_result();
    return $resultado ? $resultado->fetch_assoc() : null;
}

// Devuelve los datos públicos (sin clave) de un usuario por su id
function obtenerUsuarioPorId($id)
{
    global $conexion;
    $sql = $conexion->prepare('SELECT id, nombre, correo, telefono, rol FROM usuarios WHERE id = ?');
    $sql->bind_param('i', $id);
    $sql->execute();
    $resultado = $sql->get_result();
    return $resultado ? $resultado->fetch_assoc() : null;
}

function existeCorreo($correo)
{
    return buscarUsuarioPorCorreo($correo) !== null;
}
