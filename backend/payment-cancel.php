<?php
/**
 * PÁGINA DE CANCELACIÓN DE PAGO
 * Se muestra si el usuario cancela durante el pago
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
        .cancel-container {
            max-width: 600px;
            margin: 80px auto;
            padding: 40px;
            text-align: center;
            background: linear-gradient(135deg, #FF8C00 0%, #FF6347 100%);
            border-radius: 10px;
            color: white;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .cancel-icon {
            font-size: 80px;
            margin-bottom: 20px;
        }
        
        .cancel-container h1 {
            font-size: 2.5em;
            margin-bottom: 15px;
            color: #FFE4B5;
        }
        
        .cancel-container p {
            font-size: 1.1em;
            margin: 15px 0;
            opacity: 0.9;
        }
        
        .cancel-actions {
            margin-top: 30px;
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .cancel-actions a {
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
        }
        
        .btn-retry {
            background: #FFE4B5;
            color: #FF6347;
        }
        
        .btn-retry:hover {
            background: #FFDAB9;
            transform: translateY(-2px);
        }
        
        .btn-home {
            background: transparent;
            border: 2px solid #FFE4B5;
            color: #FFE4B5;
        }
        
        .btn-home:hover {
            background: rgba(255, 228, 181, 0.1);
        }
    </style>
</head>
<body style="background: #f5f5f5;">
    <div class="cancel-container">
        <div class="cancel-icon">⏸️</div>
        <h1>Pago Cancelado</h1>
        <p>Cancelaste el proceso de pago</p>
        
        <p>Tu carrito sigue guardado con todos los artículos. Puedes intentar nuevamente cuando lo desees.</p>
        
        <div class="cancel-actions">
            <a href="/#catalogo" class="btn-retry">Continuar Comprando</a>
            <a href="/" class="btn-home">Volver al Inicio</a>
        </div>
    </div>
</body>
</html>
