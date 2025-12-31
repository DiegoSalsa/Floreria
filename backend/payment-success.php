<?php
/**
 * PÁGINA DE ÉXITO DE PAGO
 * Se muestra después de que el usuario completa el pago en Webpay
 */

require_once 'auth-config.php';
require_once 'webpay-config.php';

// Obtener token de transacción
$token = isset($_GET['token_ws']) ? $_GET['token_ws'] : '';
$order_id = isset($_GET['TBK_ORDER_ID']) ? $_GET['TBK_ORDER_ID'] : '';

if (empty($token)) {
    header('Location: /payment-failure.php?reason=no_token');
    exit;
}

// Guardar transacción
$data_dir = __DIR__ . '/data';
if (!is_dir($data_dir)) {
    mkdir($data_dir, 0755, true);
}

$transaction_data = [
    'order_id' => $order_id,
    'token' => $token,
    'status' => 'completed',
    'payment_method' => 'webpay',
    'created_at' => date('Y-m-d H:i:s'),
    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
];

$filename = $data_dir . '/order_' . $order_id . '.json';
file_put_contents($filename, json_encode($transaction_data, JSON_PRETTY_PRINT));

// Si está autenticado, enviar email de confirmación
if (is_authenticated()) {
    $order_data = [
        'order_id' => $order_id,
        'amount' => $_GET['TBK_AMOUNT'] ?? '0',
        'cart_items' => isset($_SESSION['last_cart']) ? json_encode($_SESSION['last_cart']) : '[]'
    ];
    
    send_purchase_confirmation($_SESSION['user_email'], $_SESSION['user_name'], $order_data);
}

logActivity("Pago completado: Orden {$order_id}", 'payment');
logWebpay('Usuario redirigido desde Webpay', array('token' => $token));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago Exitoso - Floreria Wildgarden</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .success-container {
            max-width: 600px;
            margin: 80px auto;
            padding: 40px;
            text-align: center;
            background: linear-gradient(135deg, #1B4332 0%, #0D2818 100%);
            border-radius: 10px;
            color: white;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .success-icon {
            font-size: 80px;
            margin-bottom: 20px;
            animation: scaleUp 0.6s ease-out;
        }
        
        .success-container h1 {
            font-size: 2.5em;
            margin-bottom: 15px;
            color: #90EE90;
        }
        
        .success-container p {
            font-size: 1.1em;
            margin: 15px 0;
            opacity: 0.9;
        }
        
        .order-number {
            background: rgba(255,255,255,0.1);
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            font-family: monospace;
            font-size: 1.2em;
        }
        
        .success-actions {
            margin-top: 30px;
            display: flex;
            gap: 15px;
            justify-content: center;
        }
        
        .success-actions a {
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
        }
        
        .btn-home {
            background: #90EE90;
            color: #1B4332;
        }
        
        .btn-home:hover {
            background: #7FD87F;
            transform: translateY(-2px);
        }
        
        .btn-contact {
            background: transparent;
            border: 2px solid #90EE90;
            color: #90EE90;
        }
        
        .btn-contact:hover {
            background: rgba(144, 238, 144, 0.1);
        }
        
        @keyframes scaleUp {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }
    </style>
</head>
<body style="background: #f5f5f5;">
    <div class="success-container">
        <div class="success-icon">✓</div>
        <h1>¡Pago Exitoso!</h1>
        <p>Gracias por tu compra en Floreria Wildgarden</p>
        
        <div class="order-number">
            <p style="margin: 0; font-size: 0.9em; opacity: 0.8;">Número de Transacción</p>
            <p style="margin: 5px 0 0 0;"><?php echo htmlspecialchars($token); ?></p>
        </div>
        
        <p>Recibirás un correo de confirmación en tu bandeja de entrada con los detalles de tu pedido.</p>
        <p>Nuestro equipo se pondrá en contacto contigo pronto para confirmar el envío.</p>
        
        <div class="success-actions">
            <a href="/" class="btn-home">Volver al Inicio</a>
            <a href="https://wa.me/56996744579?text=Hola%20Floreria%20Wildgarden%2C%20realic%C3%A9%20una%20compra%20y%20me%20gustar%C3%ADa%20confirmar%20los%20detalles" class="btn-contact">Contactar por WhatsApp</a>
        </div>
    </div>
</body>
</html>
