<?php
/**
 * ENDPOINT PARA CREAR TRANSACCIÓN EN WEBPAY
 * POST /api/webpay/create-transaction
 */

require_once 'webpay-config.php';

header('Content-Type: application/json');

try {
    // Validar método POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    // Obtener datos
    $amount = isset($_POST['amount']) ? intval($_POST['amount']) : 0;
    $buyEmail = isset($_POST['buyEmail']) ? sanitize($_POST['buyEmail']) : '';
    $buyerName = isset($_POST['buyerName']) ? sanitize($_POST['buyerName']) : '';
    $buyerPhone = isset($_POST['buyerPhone']) ? sanitize($_POST['buyerPhone']) : '';
    $orderId = isset($_POST['orderId']) ? sanitize($_POST['orderId']) : '';
    $cartItems = isset($_POST['cartItems']) ? json_decode($_POST['cartItems'], true) : array();

    // Validar datos
    if ($amount <= 0) {
        throw new Exception('Monto inválido');
    }

    if (empty($buyEmail) || empty($buyerName)) {
        throw new Exception('Datos del cliente incompletos');
    }

    // Validar email
    if (!filter_var($buyEmail, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Email inválido');
    }

    logWebpay('Nueva solicitud de transacción', array(
        'amount' => $amount,
        'email' => $buyEmail,
        'orderId' => $orderId,
        'itemCount' => count($cartItems)
    ));

    // Crear transacción en Webpay
    $transactionData = array(
        'buy_order' => $orderId,
        'session_id' => session_id(),
        'amount' => $amount,
        'return_url' => SUCCESS_URL
    );

    $response = makeWebpayRequest(
        WEBPAY_URLS['initTransaction'],
        'POST',
        $transactionData
    );

    logWebpay('Respuesta de Webpay', $response);

    // Validar respuesta
    if (!$response['response']) {
        throw new Exception('Error al crear la transacción: ' . ($response['error'] ?: 'Sin respuesta del servidor'));
    }

    if ($response['status'] !== 201 && $response['status'] !== 200) {
        $errorMsg = isset($response['response']['message']) ? $response['response']['message'] : 'Error desconocido';
        throw new Exception('Error al crear la transacción: ' . $errorMsg);
    }

    // Guardar datos de la transacción en BD (opcional pero recomendado)
    saveTransactionData(array(
        'order_id' => $orderId,
        'amount' => $amount,
        'customer_email' => $buyEmail,
        'customer_name' => $buyerName,
        'customer_phone' => $buyerPhone,
        'status' => 'pending',
        'cart_items' => json_encode($cartItems),
        'webpay_token' => $response['response']['token'] ?? null,
        'webpay_url' => $response['response']['url'] ?? null
    ));

    // Retornar URL de redirección a Webpay
    echo json_encode(array(
        'success' => true,
        'redirect_url' => ($response['response']['url'] ?? '') . '?token_ws=' . ($response['response']['token'] ?? ''),
        'token' => $response['response']['token'] ?? null
    ));

} catch (Exception $e) {
    logWebpay('ERROR: ' . $e->getMessage());
    
    http_response_code(400);
    echo json_encode(array(
        'success' => false,
        'error' => $e->getMessage()
    ));
}

/**
 * Sanitizar entrada
 */
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Guardar datos de transacción (usa tu método preferido)
 */
function saveTransactionData($data) {
    // Opción 1: Si usas base de datos
    // $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    // if ($conn->connect_error) {
    //     logWebpay('DB Connection Error: ' . $conn->connect_error);
    //     return false;
    // }
    // 
    // $stmt = $conn->prepare("INSERT INTO transactions (order_id, amount, customer_email, customer_name, customer_phone, status, cart_items, webpay_token, webpay_url, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    // $stmt->bind_param("sdssssss", $data['order_id'], $data['amount'], $data['customer_email'], $data['customer_name'], $data['customer_phone'], $data['status'], $data['cart_items'], $data['webpay_token'], $data['webpay_url']);
    // $stmt->execute();
    // $conn->close();

    // Opción 2: Guardar en JSON (más simple para principiantes)
    $dataDir = __DIR__ . '/data';
    if (!is_dir($dataDir)) {
        mkdir($dataDir, 0755, true);
    }

    $filename = $dataDir . '/' . $data['order_id'] . '.json';
    $data['created_at'] = date('Y-m-d H:i:s');
    
    file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    logWebpay('Transacción guardada: ' . $filename);
    
    return true;
}
?>
