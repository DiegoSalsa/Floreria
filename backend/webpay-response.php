<?php
/**
 * MANEJADOR DE RESPUESTA DE WEBPAY
 * Recibe la respuesta después de que el usuario completa el pago
 */

require_once 'webpay-config.php';

// Obtener token de respuesta
$token = isset($_GET['token_ws']) ? $_GET['token_ws'] : '';

logWebpay('Respuesta recibida de Webpay', array('token' => $token));

// Aquí puedes procesar la respuesta de Webpay
// Este archivo simplemente redirige al success/failure

if (empty($token)) {
    header('Location: /payment-failure.php?reason=invalid_token');
    exit;
}

// En una implementación real, verificarías el estado de la transacción
// contra los servidores de Transbank para asegurar que el pago fue exitoso

// Por ahora, redirigimos al success (el cliente confirmó el pago)
header('Location: /payment-success.php?token_ws=' . urlencode($token));
exit;
?>
