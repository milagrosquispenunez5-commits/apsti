<?php
// CONTROLADOR — Cambia el estado de un trámite (pendiente/proceso/atendido/rechazado) y regresa al dashboard.
require_once __DIR__ . '/../modelo/auth.php';
requerirRol('administrador');

require_once __DIR__ . '/../modelo/tramite.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['estado'])) {
    $estado = $_POST['estado'];
    if (array_key_exists($estado, ESTADOS_TRAMITE_PERMITIDOS)) {
        actualizarEstadoTramite((int) $_POST['id'], $estado);
    }
}

header('Location: ../vista/dashboard.php#mesa-partes');
