<?php
require_once 'load-env.php';
require_once 'auth-config.php';

// CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle OPTIONS preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

logActivity("Usuario desconectado", 'auth');
logout_user();

// Si es un fetch JSON request
$content_type = $_SERVER['CONTENT_TYPE'] ?? '';
if (strpos($content_type, 'application/json') !== false || isset($_GET['json'])) {
    header('Content-Type: application/json');
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Sesión cerrada']);
    exit;
}

// Redirigir al frontend (formulario HTML)
?>
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cerrando sesión...</title>
</head>
<body>
    <script>
        // Limpiar localStorage
        localStorage.removeItem('user_logged_in');
        localStorage.removeItem('user_data');
        
        // Redirigir al frontend
        setTimeout(function() {
            window.location.href = 'https://floreriawildgarden.vercel.app';
        }, 100);
    </script>
    <p>Cerrando sesión...</p>
</body>
</html>
