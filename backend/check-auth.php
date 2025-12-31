<?php
require_once 'load-env.php';
require_once 'auth-config.php';

// CORS headers - especificar dominio de Vercel
header('Access-Control-Allow-Origin: https://floreriawildgarden.vercel.app');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

// Handle preflight
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
