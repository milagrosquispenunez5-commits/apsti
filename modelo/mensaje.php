<?php
// MODELO — Operaciones sobre la tabla "mensajes".
// Los controladores y las vistas usan estas funciones; nadie más toca la base.
require_once __DIR__ . '/conexion.php';

// Guarda un mensaje y devuelve su id (o false si falló)
function guardarMensaje($nombre, $correo, $mensaje)
{
    global $conexion;
    $sql = $conexion->prepare('INSERT INTO mensajes (nombre, correo, mensaje) VALUES (?, ?, ?)');
    $sql->bind_param('sss', $nombre, $correo, $mensaje);
    return $sql->execute() ? $conexion->insert_id : false;
}

// Devuelve todos los mensajes, del más reciente al más antiguo
function listarMensajes()
{
    global $conexion;
    $resultado = $conexion->query('SELECT * FROM mensajes ORDER BY fecha_envio DESC, id DESC');
    return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
}

// Elimina un mensaje por su id
function eliminarMensaje($id)
{
    global $conexion;
    $sql = $conexion->prepare('DELETE FROM mensajes WHERE id = ?');
    $sql->bind_param('i', $id);
    return $sql->execute();
}

// Marca un mensaje como leído
function marcarMensajeLeido($id)
{
    global $conexion;
    $sql = $conexion->prepare('UPDATE mensajes SET leido = 1 WHERE id = ?');
    $sql->bind_param('i', $id);
    return $sql->execute();
}
