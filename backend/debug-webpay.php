<?php
/**
 * SCRIPT DE DEBUG PARA WEBPAY
 * Verifica que las credenciales y configuración están correctas
 */

require_once 'webpay-config.php';

echo "=== DEBUG DE WEBPAY ===\n\n";

echo "Commerce Code: " . (WEBPAY_COMMERCE_CODE ?: 'NO CONFIGURADO') . "\n";
echo "API Key: " . (WEBPAY_API_KEY ? substr(WEBPAY_API_KEY, 0, 10) . '...' : 'NO CONFIGURADO') . "\n";
echo "Environment: " . WEBPAY_ENVIRONMENT . "\n";
echo "Init Transaction URL: " . WEBPAY_URLS['initTransaction'] . "\n";
echo "\n";

// Test curl
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, WEBPAY_URLS['initTransaction']);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
    'buy_order' => 'TEST123',
    'session_id' => 'test_session',
    'amount' => 1000,
    'return_url' => SUCCESS_URL
)));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Authorization: Bearer ' . WEBPAY_API_KEY
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_VERBOSE, true);

$verbose = fopen('php://temp', 'w+');
curl_setopt($ch, CURLOPT_STDERR, $verbose);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

echo "HTTP Code: " . $httpCode . "\n";
echo "Error: " . ($error ?: 'NINGUNO') . "\n";
echo "Response: " . $response . "\n\n";

rewind($verbose);
$verboseLog = stream_get_contents($verbose);
echo "Verbose Log:\n" . $verboseLog . "\n";

curl_close($ch);
?>
