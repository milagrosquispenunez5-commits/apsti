<?php
// CONTROLADOR — Cambia el estado de un trámite (pendiente/proceso/atendido) y regresa al dashboard.
require_once __DIR__ . '/../modelo/tramite.php';

$estadosValidos = ['pendiente', 'proceso', 'atendido'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['estado'])) {
    $estado = $_POST['estado'];
    if (in_array($estado, $estadosValidos, true)) {
        actualizarEstadoTramite((int) $_POST['id'], $estado);
    }
}

header('Location: ../vista/dashboard.php#mesa-partes');
