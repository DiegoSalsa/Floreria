<?php
/**
 * PÁGINA DE ERROR DE PAGO
 */
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago Cancelado - Floreria Wildgarden</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .error-container {
            max-width: 600px;
            margin: 80px auto;
            padding: 40px;
            text-align: center;
            background: linear-gradient(135deg, #8B0000 0%, #4d0000 100%);
            border-radius: 10px;
            color: white;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .error-icon {
            font-size: 80px;
            margin-bottom: 20px;
        }
        
        .error-container h1 {
            font-size: 2.5em;
            margin-bottom: 15px;
            color: #FFB6C1;
        }
        
        .error-container p {
            font-size: 1.1em;
            margin: 15px 0;
            opacity: 0.9;
        }
        
        .error-actions {
            margin-top: 30px;
            display: flex;
            gap: 15px;
            justify-content: center;
        }
        
        .error-actions a {
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
        }
        
        .btn-retry {
            background: #FFB6C1;
            color: #8B0000;
        }
        
        .btn-retry:hover {
            background: #FFC0CB;
            transform: translateY(-2px);
        }
        
        .btn-home {
            background: transparent;
            border: 2px solid #FFB6C1;
            color: #FFB6C1;
        }
        
        .btn-home:hover {
            background: rgba(255, 182, 193, 0.1);
        }
    </style>
</head>
<body style="background: #f5f5f5;">
    <div class="error-container">
        <div class="error-icon">✕</div>
        <h1>Pago Cancelado</h1>
        <p>No se pudo procesar tu pago</p>
        
        <p>Posibles razones:</p>
        <ul style="text-align: left; display: inline-block; margin: 20px 0;">
            <li>Fondos insuficientes en tu tarjeta</li>
            <li>Datos de la tarjeta incorrectos</li>
            <li>Transacción rechazada por el banco</li>
            <li>Cancelaste el pago</li>
        </ul>
        
        <p>Tu carrito sigue guardado. Puedes intentar nuevamente o contactarnos para ayudarte.</p>
        
        <div class="error-actions">
            <a href="/#catalogo" class="btn-retry">Volver al Carrito</a>
            <a href="https://wa.me/56996744579?text=Hola%2C%20tuve%20un%20problema%20con%20mi%20pago%20y%20necesito%20ayuda" class="btn-home">Contactar Soporte</a>
        </div>
    </div>
</body>
</html>
