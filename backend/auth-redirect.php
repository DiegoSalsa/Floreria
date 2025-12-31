<?php
require_once 'load-env.php';
require_once 'auth-config.php';

// Verificar que estamos logueados
if (!is_authenticated()) {
    header('Location: login.php');
    exit;
}

// Simplemente redirigir al frontend
// La sesión PHP se mantiene, el frontend verificará al cargar
header('Location: https://floreriawildgarden.vercel.app');
exit;
?>
