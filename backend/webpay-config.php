<?php
/**
 * CONFIGURACIÓN DE WEBPAY PARA PROCESAMIENTO DE PAGOS
 * Intégrate con la API de Transbank
 */

// IMPORTANTE: Usa variables de entorno para credenciales
define('WEBPAY_COMMERCE_CODE', getenv('WEBPAY_COMMERCE_CODE')); // Tu código de comercio
define('WEBPAY_API_KEY', getenv('WEBPAY_API_KEY')); // Tu API Key
define('WEBPAY_ENVIRONMENT', getenv('WEBPAY_ENVIRONMENT') ?: 'test'); // 'test' o 'production'

// URLs base según ambiente
$webpay_urls = array(
    'test' => array(
        'initTransaction' => 'https://webpay3g.transbank.cl/webpayapi/v1.3/transactions',
        'queryStatus' => 'https://webpay3g.transbank.cl/webpayapi/v1.3/transactions/',
        'commit' => '/commit'
    ),
    'production' => array(
        'initTransaction' => 'https://webpay3g.transbank.cl/webpayapi/v1.3/transactions',
        'queryStatus' => 'https://webpay3g.transbank.cl/webpayapi/v1.3/transactions/',
        'commit' => '/commit'
    )
);

define('WEBPAY_URLS', $webpay_urls[WEBPAY_ENVIRONMENT]);

// URLs de retorno (configura tu dominio en variables de entorno)
define('APP_URL', getenv('APP_URL') ?: 'http://localhost:8000');
define('RETURN_URL', APP_URL . '/webpay-response.php');
define('SUCCESS_URL', APP_URL . '/payment-success.php');
define('FAILURE_URL', APP_URL . '/payment-failure.php');
define('CANCEL_URL', APP_URL . '/payment-cancel.php');

// Base de datos para almacenar órdenes
define('WEBPAY_DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('WEBPAY_DB_USER', getenv('DB_USER') ?: 'wildgarden_user');
define('WEBPAY_DB_PASSWORD', getenv('DB_PASSWORD') ?: '');
define('WEBPAY_DB_NAME', getenv('DB_NAME') ?: 'wildgarden_db');

// Configuración adicional
define('BUSINESS_NAME', 'Floreria Wildgarden');
define('BUSINESS_EMAIL', 'ventas@wildgardenflores.cl');
define('SUPPORT_PHONE', '+56996744579');

// Habilitar logs para debugging (desactivar en producción)
define('DEBUG_MODE', getenv('DEBUG_MODE') === 'true' || getenv('DEBUG_MODE') === '1');
define('LOG_FILE', __DIR__ . '/../logs/webpay.log');

// Crear directorio de logs si no existe
if (!is_dir(__DIR__ . '/../logs')) {
    mkdir(__DIR__ . '/../logs', 0755, true);
}

/**
 * Función para escribir logs
 */
function logWebpay($message, $data = array()) {
    if (DEBUG_MODE) {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message";
        
        if (!empty($data)) {
            $logMessage .= "\n" . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
        
        $logMessage .= "\n" . str_repeat("-", 80) . "\n";
        
        file_put_contents(LOG_FILE, $logMessage, FILE_APPEND);
    }
}

/**
 * Función para hacer requests CURL
 */
function makeWebpayRequest($url, $method = 'POST', $data = null, $headers = array()) {
    $defaultHeaders = array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . WEBPAY_API_KEY
    );
    
    $headers = array_merge($defaultHeaders, $headers);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Permitir SSL sin verificación en test
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    } elseif ($method === 'PUT') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    // Debuggear respuesta
    logWebpay('cURL Response', array(
        'url' => $url,
        'http_code' => $httpCode,
        'error' => $error,
        'response' => $response
    ));
    
    // Si hay error de conexión, reportar
    if ($error) {
        return array(
            'status' => 0,
            'response' => null,
            'error' => $error
        );
    }
    
    return array(
        'status' => $httpCode,
        'response' => json_decode($response, true),
        'error' => $error
    );
}

logWebpay('Webpay Configuration Loaded', array(
    'environment' => WEBPAY_ENVIRONMENT,
    'commerce_code' => WEBPAY_COMMERCE_CODE
));
?>
