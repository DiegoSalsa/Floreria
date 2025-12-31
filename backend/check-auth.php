<?php
require_once 'load-env.php';
require_once 'auth-config.php';

header('Content-Type: application/json');

// Verificar si hay sesión activa
if (is_authenticated()) {
    echo json_encode([
        'logged_in' => true,
        'user_email' => $_SESSION['user_email'] ?? null,
        'user_name' => $_SESSION['user_name'] ?? null,
        'user_role' => $_SESSION['user_role'] ?? null
    ]);
} else {
    echo json_encode([
        'logged_in' => false
    ]);
}
?>
