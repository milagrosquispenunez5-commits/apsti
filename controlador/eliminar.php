<?php
// CONTROLADOR — Elimina un mensaje desde el dashboard y regresa a él.
require_once __DIR__ . '/../modelo/mensaje.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar'])) {
    eliminarMensaje((int) $_POST['eliminar']);
}

header('Location: ../vista/dashboard.php');
