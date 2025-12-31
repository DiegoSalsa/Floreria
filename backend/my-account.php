<?php
require_once 'auth-config.php';

// Validar que usuario está autenticado
if (!is_authenticated()) {
    header('Location: /login.php');
    exit;
}

$user = find_user_by_id($_SESSION['user_id']);

// Si es admin, mostrar también acceso especial
$is_admin = $_SESSION['user_role'] === 'admin';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Cuenta - Floreria Wildgarden</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            color: #333;
        }
        
        header {
            background: linear-gradient(135deg, #1B4332 0%, #0D2818 100%);
            color: white;
            padding: 30px 0;
            margin-bottom: 30px;
        }
        
        header h1 { font-size: 2em; margin-bottom: 10px; }
        header .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .sidebar {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            height: fit-content;
        }
        
        .user-profile {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }
        
        .user-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #1B4332 0%, #0D2818 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2.5em;
            margin: 0 auto 15px;
        }
        
        .user-profile h2 { color: #1B4332; margin-bottom: 5px; }
        .user-profile p { color: #666; font-size: 0.9em; }
        
        .sidebar-menu {
            list-style: none;
        }
        
        .sidebar-menu li {
            margin-bottom: 10px;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 12px;
            text-decoration: none;
            color: #333;
            border-radius: 5px;
            transition: 0.3s;
            font-weight: 500;
        }
        
        .sidebar-menu a:hover {
            background: #f0f0f0;
            color: #1B4332;
        }
        
        .sidebar-menu a.active {
            background: #1B4332;
            color: white;
        }
        
        .content {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .content h2 {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #1B4332;
            color: #1B4332;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        
        .info-row:last-child { border-bottom: none; }
        
        .info-label { font-weight: 600; color: #666; }
        .info-value { color: #333; }
        
        .admin-section {
            background: #FFD700;
            border-left: 4px solid #FFA500;
            padding: 20px;
            border-radius: 5px;
            margin-top: 30px;
        }
        
        .admin-section h3 {
            color: #333;
            margin-bottom: 15px;
        }
        
        .admin-links {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .admin-links a {
            background: #333;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            transition: 0.3s;
        }
        
        .admin-links a:hover {
            background: #1B4332;
        }
        
        .empty-message {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        
        .btn-logout {
            background: #FF6B6B;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            display: inline-block;
            margin-top: 20px;
            transition: 0.3s;
        }
        
        .btn-logout:hover {
            background: #FF4444;
        }
        
        @media (max-width: 768px) {
            .dashboard-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <div>
                <h1>👤 Mi Cuenta</h1>
                <p>Bienvenido, <?php echo htmlspecialchars($user['name']); ?></p>
            </div>
            <div>
                <a href="/" style="background: rgba(255,255,255,0.2); color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; border: 1px solid white; display: inline-block;">
                    🏠 Inicio
                </a>
            </div>
        </div>
    </header>
    
    <div class="container">
        <div class="dashboard-grid">
            <!-- SIDEBAR -->
            <div class="sidebar">
                <div class="user-profile">
                    <div class="user-avatar">👤</div>
                    <h2><?php echo htmlspecialchars($user['name']); ?></h2>
                    <p><?php echo htmlspecialchars($user['email']); ?></p>
                    <p style="font-size: 0.85em; color: #999; margin-top: 5px;">
                        <?php echo $is_admin ? '👑 Administrador' : '🛒 Cliente'; ?>
                    </p>
                </div>
                
                <ul class="sidebar-menu">
                    <li><a href="#" class="active">📋 Mi Perfil</a></li>
                    <li><a href="#pedidos">🛍️ Mis Pedidos</a></li>
                    <li><a href="/logout.php">🚪 Cerrar Sesión</a></li>
                </ul>
            </div>
            
            <!-- CONTENIDO PRINCIPAL -->
            <div class="content">
                <h2>📋 Mi Perfil</h2>
                
                <div class="info-row">
                    <span class="info-label">Nombre:</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['name']); ?></span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Tipo de Cuenta:</span>
                    <span class="info-value"><?php echo $is_admin ? '👑 Administrador' : '🛒 Cliente'; ?></span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Estado:</span>
                    <span class="info-value"><?php echo $user['is_active'] ? '✅ Verificado' : '⏳ Pendiente'; ?></span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Miembro desde:</span>
                    <span class="info-value"><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></span>
                </div>
                
                <?php if ($is_admin): ?>
                <div class="admin-section">
                    <h3>👑 Zona de Administrador</h3>
                    <p style="margin-bottom: 15px; color: #333;">Acceso a herramientas de administración:</p>
                    <div class="admin-links">
                        <a href="/admin-dashboard.php">📊 Dashboard</a>
                        <a href="/manage-users.php">👥 Usuarios</a>
                    </div>
                </div>
                <?php endif; ?>
                
                <a href="/logout.php" class="btn-logout">Cerrar Sesión</a>
            </div>
        </div>
    </div>
</body>
</html>
