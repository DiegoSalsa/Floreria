<?php
/**
 * TEST ENDPOINT - Verificar que todo está funcionando
 * Acceder a: /test-config.php
 */

require_once 'load-env.php';
require_once 'webpay-config.php';
require_once 'auth-config.php';

header('Content-Type: application/json');

$tests = [
    'database_config' => [
        'status' => (DB_HOST && DB_USER && DB_NAME) ? 'OK' : 'FAIL',
        'message' => DB_HOST ? "Conectado a: " . DB_HOST : "BD no configurada"
    ],
    'webpay_config' => [
        'status' => (WEBPAY_COMMERCE_CODE && WEBPAY_API_KEY) ? 'OK' : 'FAIL',
        'message' => WEBPAY_COMMERCE_CODE ? "Commerce Code: " . substr(WEBPAY_COMMERCE_CODE, 0, 10) . '...' : "Credenciales no cargadas",
        'environment' => WEBPAY_ENVIRONMENT
    ],
    'email_config' => [
        'status' => (MAIL_HOST && MAIL_PASSWORD) ? 'OK' : 'FAIL',
        'message' => MAIL_HOST ? "Email service: " . MAIL_HOST : "Email no configurado",
        'service' => MAIL_HOST ? 'Brevo API' : 'None'
    ],
    'environment' => [
        'debug_mode' => DEBUG_MODE ? 'ON' : 'OFF',
        'app_url' => APP_URL,
        'php_version' => phpversion()
    ]
];

// Test de conexión a base de datos
$db_test = 'FAIL';
if (USE_DATABASE) {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if ($conn->connect_error) {
        $tests['database_connection'] = [
            'status' => 'FAIL',
            'error' => $conn->connect_error
        ];
    } else {
        $tests['database_connection'] = ['status' => 'OK'];
        $conn->close();
    }
}

// Test de autenticación
$tests['auth_system'] = [
    'status' => function_exists('login_user') ? 'OK' : 'FAIL',
    'message' => 'Auth functions loaded'
];

echo json_encode($tests, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
