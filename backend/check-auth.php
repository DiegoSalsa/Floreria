<?php
require_once 'load-env.php';
require_once 'auth-config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');

// Verificar si hay sesión activa
$logged_in = is_authenticated();

if ($logged_in) {
    $response = [
        'logged_in' => true,
        'user_email' => $_SESSION['user_email'] ?? null,
        'user_name' => $_SESSION['user_name'] ?? null,
        'user_role' => $_SESSION['user_role'] ?? null
    ];
} else {
    $response = [
        'logged_in' => false
    ];
}

http_response_code(200);
echo json_encode($response);
?>
