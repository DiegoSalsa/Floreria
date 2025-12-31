<?php
/**
 * MAILER.PHP - Sistema de Emails para Transacciones
 * Usa esta función para enviar confirmaciones de pago a los clientes
 * 
 * Requerimientos:
 * - PHPMailer library (composer require phpmailer/phpmailer)
 * - O usar mail() nativo de PHP (sin autenticación SMTP)
 */

/**
 * Enviar email de confirmación de pago
 */
function sendPaymentConfirmation($customerEmail, $customerName, $orderData) {
    // OPCIÓN 1: Usar función mail() de PHP (sin autenticación)
    
    $subject = "Confirmación de tu Pedido #" . $orderData['order_id'];
    
    $itemsList = '';
    foreach ($orderData['cart_items'] as $item) {
        $subtotal = $item['price'] * $item['quantity'];
        $itemsList .= "
        <tr>
            <td style='padding: 8px; border-bottom: 1px solid #ddd;'>{$item['quantity']}x {$item['name']}</td>
            <td style='padding: 8px; border-bottom: 1px solid #ddd; text-align: right;'>\${$item['price']}</td>
            <td style='padding: 8px; border-bottom: 1px solid #ddd; text-align: right;'>\$" . number_format($subtotal, 0, '.', '') . "</td>
        </tr>";
    }
    
    $html = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9; }
            .header { background: linear-gradient(135deg, #1B4332 0%, #0D2818 100%); color: white; padding: 20px; border-radius: 5px; }
            .content { background: white; padding: 20px; margin: 20px 0; border-radius: 5px; }
            .order-info { background: #f0f0f0; padding: 15px; border-radius: 5px; margin: 15px 0; }
            table { width: 100%; border-collapse: collapse; }
            .total-row { font-weight: bold; background: #e8e8e8; }
            .footer { text-align: center; color: #999; font-size: 12px; padding: 20px; border-top: 1px solid #ddd; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>¡Gracias por tu compra!</h1>
                <p>Floreria Wildgarden</p>
            </div>
            
            <div class='content'>
                <p>Hola <strong>{$customerName}</strong>,</p>
                
                <p>Confirmamos que hemos recibido tu pedido exitosamente.</p>
                
                <div class='order-info'>
                    <p><strong>Número de Orden:</strong> {$orderData['order_id']}</p>
                    <p><strong>Fecha:</strong> " . date('d/m/Y H:i') . "</p>
                    <p><strong>Email:</strong> {$customerEmail}</p>
                </div>
                
                <h2>Detalles de tu pedido:</h2>
                <table>
                    <thead>
                        <tr>
                            <th style='padding: 8px; text-align: left; border-bottom: 2px solid #333;'>Producto</th>
                            <th style='padding: 8px; text-align: right; border-bottom: 2px solid #333;'>Precio</th>
                            <th style='padding: 8px; text-align: right; border-bottom: 2px solid #333;'>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$itemsList}
                        <tr class='total-row'>
                            <td colspan='2' style='padding: 8px; text-align: right;'>Subtotal:</td>
                            <td style='padding: 8px; text-align: right;'>\$" . number_format($orderData['subtotal'], 0, '.', '') . "</td>
                        </tr>
                        <tr class='total-row'>
                            <td colspan='2' style='padding: 8px; text-align: right;'>Envío:</td>
                            <td style='padding: 8px; text-align: right;'>\$" . number_format($orderData['shipping'], 0, '.', '') . "</td>
                        </tr>
                        <tr class='total-row' style='background: #1B4332; color: white;'>
                            <td colspan='2' style='padding: 8px; text-align: right;'><strong>TOTAL:</strong></td>
                            <td style='padding: 8px; text-align: right;'><strong>\$" . number_format($orderData['total'], 0, '.', '') . "</strong></td>
                        </tr>
                    </tbody>
                </table>
                
                <p style='margin-top: 20px;'>
                    <strong>Estado del Pedido:</strong> Tu pago fue procesado exitosamente.
                    Nos pondremos en contacto contigo pronto para confirmar la entrega.
                </p>
                
                <p>Si tienes dudas, contáctanos a:</p>
                <ul>
                    <li>WhatsApp: <a href='https://wa.me/56996744579'>+56 9 9674 4579</a></li>
                    <li>Instagram: <a href='https://instagram.com/floreria.wildgarden'>@floreria.wildgarden</a></li>
                </ul>
            </div>
            
            <div class='footer'>
                <p>Este es un correo automático. Por favor no responder.</p>
                <p>&copy; 2025 Floreria Wildgarden. Todos los derechos reservados.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Headers del email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $headers .= "From: ventas@wildgardenflores.cl" . "\r\n";
    $headers .= "Reply-To: ventas@wildgardenflores.cl" . "\r\n";
    
    // Enviar
    $result = mail($customerEmail, $subject, $html, $headers);
    
    if ($result) {
        logWebpay("Email enviado a: $customerEmail");
        return true;
    } else {
        logWebpay("ERROR: No se pudo enviar email a: $customerEmail");
        return false;
    }
}

/**
 * Enviar email de notificación al vendedor
 */
function notifyAdminNewOrder($orderData) {
    $adminEmail = 'admin@wildgardenflores.cl'; // Cambiar a tu email
    
    $itemsList = '';
    foreach ($orderData['cart_items'] as $item) {
        $itemsList .= "{$item['quantity']}x {$item['name']} - \${$item['price'] * $item['quantity']}\n";
    }
    
    $subject = "Nueva Orden Recibida #" . $orderData['order_id'];
    
    $message = "
    Nueva orden recibida en tu tienda Wildgarden:
    
    Número de Orden: {$orderData['order_id']}
    
    Cliente:
    - Nombre: {$orderData['customer_name']}
    - Email: {$orderData['customer_email']}
    - Teléfono: {$orderData['customer_phone']}
    - Dirección: {$orderData['customer_address']}
    - Ciudad: {$orderData['customer_city']}
    
    Productos:
    {$itemsList}
    
    Subtotal: \${$orderData['subtotal']}
    Envío: \${$orderData['shipping']}
    TOTAL: \${$orderData['total']}
    
    Acceder a Dashboard: https://tudominio.com/admin/
    
    ---
    Este es un correo automático del sistema.
    ";
    
    $headers = "From: sistema@wildgardenflores.cl\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    mail($adminEmail, $subject, $message, $headers);
}

/**
 * OPCIÓN ALTERNATIVA: Usar PHPMailer para SMTP (Gmail, Outlook, etc)
 * 
 * Primero instala: composer require phpmailer/phpmailer
 * 
 * Luego descomenta y configura esto:
 */

/*
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendEmailWithSMTP($toEmail, $toName, $subject, $htmlContent) {
    $mail = new PHPMailer(true);
    
    try {
        // Configuración SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';  // Cambiar a tu servidor SMTP
        $mail->SMTPAuth   = true;
        $mail->Username   = 'tu_email@gmail.com';  // Tu email
        $mail->Password   = 'tu_password_app';     // Tu contraseña de aplicación
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        // Remitente
        $mail->setFrom('ventas@wildgardenflores.cl', 'Floreria Wildgarden');
        
        // Destinatario
        $mail->addAddress($toEmail, $toName);
        
        // Contenido
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlContent;
        
        // Enviar
        $mail->send();
        
        logWebpay("Email enviado exitosamente a: $toEmail");
        return true;
        
    } catch (Exception $e) {
        logWebpay("Error al enviar email: " . $mail->ErrorInfo);
        return false;
    }
}
*/

?>
