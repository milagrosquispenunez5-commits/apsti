<?php
// CONTROLADOR — Reasigna el área destino de un trámite y regresa al dashboard.
require_once __DIR__ . '/../modelo/auth.php';
requerirRol('administrador');

require_once __DIR__ . '/../modelo/tramite.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['area_destino'])) {
    $area = $_POST['area_destino'];
    if (in_array($area, AREAS_DESTINO_PERMITIDAS, true)) {
        actualizarAreaTramite((int) $_POST['id'], $area);
    }
}

header('Location: ../vista/dashboard.php#mesa-partes');
