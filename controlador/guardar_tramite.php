<?php
// CONTROLADOR — Registra un nuevo trámite desde el dashboard y regresa a él.
require_once __DIR__ . '/../modelo/tramite.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $solicitante = trim($_POST['solicitante'] ?? '');
    $tipoTramite = trim($_POST['tipo_tramite'] ?? '');
    $areaDestino = trim($_POST['area_destino'] ?? '');

    if ($solicitante !== '' && $tipoTramite !== '' && $areaDestino !== '') {
        guardarTramite($solicitante, $tipoTramite, $areaDestino);
    }
}

header('Location: ../vista/dashboard.php#mesa-partes');
