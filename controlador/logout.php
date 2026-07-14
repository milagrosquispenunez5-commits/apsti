<?php
// CONTROLADOR — Cierra la sesión activa y regresa al sitio público.
require_once __DIR__ . '/../modelo/auth.php';

cerrarSesionUsuario();

header('Location: ../index.html');
