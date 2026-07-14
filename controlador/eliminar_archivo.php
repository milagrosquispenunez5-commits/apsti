<?php
// CONTROLADOR — Elimina un archivo de la biblioteca digital y regresa al dashboard.
require_once __DIR__ . '/../modelo/biblioteca.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar'])) {
    eliminarArchivoBiblioteca((int) $_POST['eliminar']);
}

header('Location: ../vista/dashboard.php#biblioteca');
