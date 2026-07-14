<?php
// CONTROLADOR — Elimina un trámite desde el dashboard y regresa a él.
require_once __DIR__ . '/../modelo/tramite.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar'])) {
    eliminarTramite((int) $_POST['eliminar']);
}

header('Location: ../vista/dashboard.php#mesa-partes');
