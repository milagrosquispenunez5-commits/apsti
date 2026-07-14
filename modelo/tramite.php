<?php
// MODELO — Operaciones sobre la tabla "tramites" (mesa de partes).
require_once __DIR__ . '/conexion.php';

// Guarda un trámite registrado por el personal (desde el dashboard) y devuelve su id
function guardarTramite($solicitante, $tipoTramite, $areaDestino)
{
    global $conexion;
    $sql = $conexion->prepare('INSERT INTO tramites (solicitante, tipo_tramite, area_destino) VALUES (?, ?, ?)');
    $sql->bind_param('sss', $solicitante, $tipoTramite, $areaDestino);
    return $sql->execute() ? $conexion->insert_id : false;
}

// Guarda un trámite enviado desde el sitio público (con datos de contacto y documento opcional)
function guardarTramitePublico($solicitante, $correo, $telefono, $tipoTramite, $documentoNombre, $documentoMime, $documentoTamano, $documentoContenido)
{
    global $conexion;
    $sql = $conexion->prepare(
        "INSERT INTO tramites (solicitante, correo, telefono, tipo_tramite, area_destino, origen, documento_nombre, documento_mime, documento_tamano, documento_contenido)
         VALUES (?, ?, ?, ?, 'Por asignar', 'publico', ?, ?, ?, ?)"
    );
    $sql->bind_param('ssssssis', $solicitante, $correo, $telefono, $tipoTramite, $documentoNombre, $documentoMime, $documentoTamano, $documentoContenido);
    return $sql->execute() ? $conexion->insert_id : false;
}

// Devuelve todos los trámites (sin el documento adjunto), del más reciente al más antiguo
function listarTramites()
{
    global $conexion;
    $resultado = $conexion->query(
        'SELECT id, solicitante, correo, telefono, tipo_tramite, area_destino, origen, estado,
                documento_nombre, documento_mime, documento_tamano, fecha_registro
         FROM tramites ORDER BY fecha_registro DESC, id DESC'
    );
    return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
}

// Devuelve nombre, tipo y contenido del documento adjunto de un trámite (o null si no tiene)
function obtenerDocumentoTramite($id)
{
    global $conexion;
    $sql = $conexion->prepare('SELECT documento_nombre, documento_mime, documento_contenido FROM tramites WHERE id = ? AND documento_contenido IS NOT NULL');
    $sql->bind_param('i', $id);
    $sql->execute();
    $resultado = $sql->get_result();
    return $resultado ? $resultado->fetch_assoc() : null;
}

// Cambia el estado de un trámite (pendiente, proceso, atendido)
function actualizarEstadoTramite($id, $estado)
{
    global $conexion;
    $sql = $conexion->prepare('UPDATE tramites SET estado = ? WHERE id = ?');
    $sql->bind_param('si', $estado, $id);
    return $sql->execute();
}

// Elimina un trámite por su id
function eliminarTramite($id)
{
    global $conexion;
    $sql = $conexion->prepare('DELETE FROM tramites WHERE id = ?');
    $sql->bind_param('i', $id);
    return $sql->execute();
}
