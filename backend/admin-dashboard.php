<?php
/**
 * DASHBOARD DE ADMINISTRACIÓN
 * Solo administradores pueden acceder
 */

require_once 'auth-config.php';

// Validar que usuario está autenticado Y es admin
if (!is_authenticated()) {
    header('Location: /login.php');
    exit;
}

if (!is_admin()) {
    http_response_code(403);
    die('❌ Acceso denegado. Solo administradores pueden acceder a esta página.');
}

// Obtener transacciones de /data/
$dataDir = __DIR__ . '/data';
$transactions = [];

if (is_dir($dataDir)) {
    $files = glob($dataDir . '/*.json');
    foreach ($files as $file) {
        $data = json_decode(file_get_contents($file), true);
        if ($data) {
            $transactions[] = $data;
        }
    }
    
    // Ordenar por fecha descendente
    usort($transactions, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
}

// Calcular totales
$totalVentas = 0;
$totalOrdenes = count($transactions);
$ventasHoy = 0;

$hoy = date('Y-m-d');
foreach ($transactions as $t) {
    $totalVentas += $t['amount'];
    if (substr($t['created_at'], 0, 10) === $hoy) {
        $ventasHoy += $t['amount'];
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Transacciones</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            color: #333;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background: linear-gradient(135deg, #1B4332 0%, #0D2818 100%);
            color: white;
            padding: 30px 0;
            margin-bottom: 30px;
            text-align: center;
        }
        
        header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-left: 4px solid #1B4332;
        }
        
        .stat-card h3 {
            color: #999;
            font-size: 0.9em;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        
        .stat-card .value {
            font-size: 2em;
            font-weight: bold;
            color: #1B4332;
        }
        
        .transactions-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .transactions-section h2 {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #1B4332;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background: #f9f9f9;
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #ddd;
            font-weight: 600;
        }
        
        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        
        tr:hover {
            background: #f9f9f9;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85em;
            font-weight: 600;
        }
        
        .status-pending {
            background: #FFF3CD;
            color: #856404;
        }
        
        .status-success {
            background: #D4EDDA;
            color: #155724;
        }
        
        .amount {
            color: #1B4332;
            font-weight: bold;
        }
        
        .empty-message {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #999;
            font-size: 0.9em;
        }
        
        @media (max-width: 768px) {
            table {
                font-size: 0.9em;
            }
            
            .stat-card .value {
                font-size: 1.5em;
            }
            
            header h1 {
                font-size: 1.5em;
            }
        }
    </style>
</head>
<body>
    <header>
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>📊 Dashboard de Administración</h1>
                <p>Floreria Wildgarden | Usuario: <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong></p>
            </div>
            <div style="text-align: right;">
                <a href="/logout.php" style="background: rgba(255,255,255,0.2); color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; border: 1px solid white; display: inline-block; transition: 0.3s;">
                    🚪 Cerrar Sesión
                </a>
            </div>
        </div>
    </header>
    
    <div class="container">
        <!-- ESTADÍSTICAS -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Órdenes Totales</h3>
                <div class="value"><?php echo $totalOrdenes; ?></div>
            </div>
            
            <div class="stat-card">
                <h3>Ventas Totales</h3>
                <div class="value">$<?php echo number_format($totalVentas, 0, '.', ''); ?></div>
            </div>
            
            <div class="stat-card">
                <h3>Ventas Hoy</h3>
                <div class="value">$<?php echo number_format($ventasHoy, 0, '.', ''); ?></div>
            </div>
            
            <div class="stat-card">
                <h3>Promedio por Orden</h3>
                <div class="value">$<?php echo $totalOrdenes > 0 ? number_format($totalVentas / $totalOrdenes, 0, '.', '') : '0'; ?></div>
            </div>
        </div>
        
        <!-- TABLA DE TRANSACCIONES -->
        <div class="transactions-section">
            <h2>Transacciones Recientes</h2>
            
            <?php if (empty($transactions)): ?>
                <div class="empty-message">
                    <p>No hay transacciones registradas aún.</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Orden ID</th>
                            <th>Cliente</th>
                            <th>Email</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $t): ?>
                        <tr>
                            <td><small><?php echo substr($t['order_id'], 0, 20); ?>...</small></td>
                            <td><?php echo htmlspecialchars($t['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($t['customer_email']); ?></td>
                            <td class="amount">$<?php echo number_format($t['amount'], 0, '.', ''); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $t['status']; ?>">
                                    <?php echo ucfirst($t['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($t['created_at'])); ?></td>
                            <td>
                                <a href="#" onclick="showDetails('<?php echo htmlspecialchars(json_encode($t)); ?>')">Ver</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <div class="footer">
            <p>Dashboard Admin - Actualizado: <?php echo date('d/m/Y H:i:s'); ?></p>
            <p>⚠️ Protege esta URL con contraseña en producción</p>
        </div>
    </div>
    
    <!-- Modal de detalles -->
    <div id="detailsModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
        <div style="background: white; margin: 50px auto; padding: 30px; max-width: 600px; border-radius: 8px; max-height: 80vh; overflow-y: auto;">
            <h2>Detalles de la Orden</h2>
            <div id="detailsContent" style="margin-top: 20px;"></div>
            <button onclick="document.getElementById('detailsModal').style.display='none'" style="margin-top: 20px; padding: 10px 20px; background: #1B4332; color: white; border: none; border-radius: 4px; cursor: pointer;">Cerrar</button>
        </div>
    </div>
    
    <script>
        function showDetails(jsonData) {
            const data = JSON.parse(jsonData.replace(/&quot;/g, '"'));
            
            let html = `
                <p><strong>Orden:</strong> ${data.order_id}</p>
                <p><strong>Nombre:</strong> ${data.customer_name}</p>
                <p><strong>Email:</strong> ${data.customer_email}</p>
                <p><strong>Teléfono:</strong> ${data.customer_phone}</p>
                <p><strong>Dirección:</strong> ${data.customer_address}</p>
                <p><strong>Ciudad:</strong> ${data.customer_city}</p>
                <p><strong>Monto:</strong> $${data.amount}</p>
                <p><strong>Estado:</strong> ${data.status}</p>
                <p><strong>Fecha:</strong> ${data.created_at}</p>
                
                <h3>Productos:</h3>
                <table style="width: 100%; border-collapse: collapse;">
            `;
            
            const items = JSON.parse(data.cart_items);
            items.forEach(item => {
                html += `
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd;">${item.quantity}x ${item.name}</td>
                        <td style="padding: 8px; border: 1px solid #ddd; text-align: right;">$${item.price}</td>
                        <td style="padding: 8px; border: 1px solid #ddd; text-align: right;">$${item.quantity * item.price}</td>
                    </tr>
                `;
            });
            
            html += `</table>`;
            
            document.getElementById('detailsContent').innerHTML = html;
            document.getElementById('detailsModal').style.display = 'block';
        }
    </script>
</body>
</html>
