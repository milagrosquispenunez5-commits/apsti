<?php
// CONTROLADOR — Registra un nuevo trámite desde el dashboard y regresa a él.
require_once __DIR__ . '/../modelo/auth.php';
requerirRol('administrador');

require_once __DIR__ . '/../modelo/tramite.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $solicitante = trim($_POST['solicitante'] ?? '');
    $tipoTramite = trim($_POST['tipo_tramite'] ?? '');
    $areaDestino = trim($_POST['area_destino'] ?? '');
    $detalle = trim($_POST['detalle'] ?? '');

    if (
        $solicitante !== ''
        && in_array($tipoTramite, TIPOS_TRAMITE_PERMITIDOS, true)
        && in_array($areaDestino, AREAS_DESTINO_PERMITIDAS, true)
    ) {
        guardarTramite($solicitante, $tipoTramite, $areaDestino, $detalle);
    }
}

header('Location: ../vista/dashboard.php#mesa-partes');
