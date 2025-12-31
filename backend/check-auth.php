<?php
require_once 'load-env.php';
require_once 'auth-config.php';

// CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle OPTIONS preflight requests - CORS requires this
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Verificar si hay sesión activa
$logged_in = is_authenticated();

if ($logged_in) {
    http_response_code(200);
    echo json_encode([
        'logged_in' => true,
        'user_email' => $_SESSION['user_email'] ?? '',
        'user_name' => $_SESSION['user_name'] ?? '',
        'user_role' => $_SESSION['user_role'] ?? 'customer'
    ]);
} else {
    http_response_code(200);
    echo json_encode([
        'logged_in' => false
    ]);
}
?>
