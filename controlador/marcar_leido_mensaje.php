<?php
// CONTROLADOR — Marca un mensaje como leído y regresa al dashboard.
require_once __DIR__ . '/../modelo/auth.php';
requerirRol('administrador');

require_once __DIR__ . '/../modelo/mensaje.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    marcarMensajeLeido((int) $_POST['id']);
}

header('Location: ../vista/dashboard.php#mensajes');
