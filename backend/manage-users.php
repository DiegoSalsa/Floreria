<?php
require_once 'auth-config.php';

// Validar acceso de admin
if (!is_authenticated() || !is_admin()) {
    http_response_code(403);
    die('❌ Acceso denegado. Solo administradores pueden acceder a esta página.');
}

$message = '';
$error = '';

// Procesar formulario para crear usuario admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'create_admin') {
        $email = sanitize($_POST['email'] ?? '');
        $name = sanitize($_POST['name'] ?? '');
        $temp_password = bin2hex(random_bytes(8));
        
        if (!$email || !$name) {
            $error = 'Email y nombre son requeridos';
        } else {
            // Crear usuario admin
            $user_id = uniqid('user_', true);
            $password_hash = hash_password($temp_password);
            
            $user_data = [
                'id' => $user_id,
                'email' => $email,
                'name' => $name,
                'password_hash' => $password_hash,
                'role' => 'admin',
                'is_active' => true,
                'temp_password' => $temp_password,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $_SESSION['user_email'],
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            file_put_contents(USERS_DIR . "/{$user_id}.json", json_encode($user_data, JSON_PRETTY_PRINT));
            
            // Enviar email de bienvenida
            $subject = "Tu cuenta de administrador - Floreria Wildgarden";
            $html = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: linear-gradient(135deg, #1B4332 0%, #0D2818 100%);
                             color: white; padding: 20px; text-align: center; }
                    .content { padding: 20px; background: white; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Acceso de Administrador Otorgado</h1>
                    </div>
                    <div class='content'>
                        <p>Hola {$name},</p>
                        
                        <p>{$_SESSION['user_name']} te ha dado acceso de administrador a Floreria Wildgarden.</p>
                        
                        <h3>Tus datos de acceso:</h3>
                        <p><strong>Email:</strong> {$email}</p>
                        <p><strong>Contraseña temporal:</strong> <code style='background: #f0f0f0; padding: 5px 10px;'>{$temp_password}</code></p>
                        
                        <p style='margin-top: 20px;'>
                            <a href='" . (getenv('APP_URL') ?: 'http://localhost:8000') . "/login.php' style='background: #1B4332; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                                Ir a Login
                            </a>
                        </p>
                        
                        <p style='margin-top: 20px; color: #666; font-size: 12px;'>
                            Por favor cambia tu contraseña después de tu primer login.
                        </p>
                    </div>
                </div>
            </body>
            </html>
            ";
            
            send_email($email, $subject, $html);
            
            $message = "✅ Admin creado y email de bienvenida enviado a {$email}";
            logActivity("Admin creado: {$email}", 'admin');
        }
    }
}

// Obtener lista de usuarios
$all_users = [];
$users_files = glob(USERS_DIR . '/*.json');
foreach ($users_files as $file) {
    $user = json_decode(file_get_contents($file), true);
    $all_users[] = $user;
}

// Ordenar por fecha de creación
usort($all_users, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Floreria Wildgarden</title>
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
        
        header h1 { font-size: 2.5em; margin-bottom: 10px; }
        header .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #E8F5E9;
            color: #1B5E20;
            border: 1px solid #4CAF50;
        }
        
        .alert-error {
            background: #FFE4E1;
            color: #8B0000;
            border: 1px solid #FF6B6B;
        }
        
        .section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .section h2 {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #1B4332;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #1B4332;
            box-shadow: 0 0 0 3px rgba(27, 67, 50, 0.1);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .submit-btn {
            background: linear-gradient(135deg, #1B4332 0%, #0D2818 100%);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(27, 67, 50, 0.3);
        }
        
        .users-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .user-card {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #1B4332;
        }
        
        .user-card h3 {
            margin-bottom: 10px;
            color: #1B4332;
        }
        
        .user-info {
            font-size: 0.9em;
            color: #666;
            margin: 5px 0;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
            margin-top: 10px;
        }
        
        .badge-admin {
            background: #FFD700;
            color: #333;
        }
        
        .badge-customer {
            background: #4CAF50;
            color: white;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #1B4332;
            text-decoration: none;
            font-weight: 600;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .form-row { grid-template-columns: 1fr; }
            .header .header-content { flex-direction: column; text-align: center; gap: 15px; }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <div>
                <h1>👥 Gestión de Usuarios</h1>
                <p>Administra los usuarios y permisos</p>
            </div>
            <div style="text-align: right;">
                <a href="/admin-dashboard.php" style="background: rgba(255,255,255,0.2); color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; border: 1px solid white; display: inline-block; margin-right: 10px;">
                    📊 Dashboard
                </a>
                <a href="/logout.php" style="background: rgba(255,255,255,0.2); color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; border: 1px solid white; display: inline-block;">
                    🚪 Cerrar Sesión
                </a>
            </div>
        </div>
    </header>
    
    <div class="container">
        <?php if ($message): ?>
            <div class="alert alert-success">✅ <?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error">❌ <?php echo $error; ?></div>
        <?php endif; ?>
        
        <!-- CREAR NUEVO ADMIN -->
        <div class="section">
            <h2>➕ Crear Nuevo Administrador</h2>
            
            <form method="POST">
                <input type="hidden" name="action" value="create_admin">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Nombre Completo</label>
                        <input type="text" id="name" name="name" required placeholder="Ej: Juan Pérez">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required placeholder="ej@ejemplo.com">
                    </div>
                </div>
                
                <button type="submit" class="submit-btn">Crear Administrador</button>
                <p style="font-size: 0.9em; color: #666; margin-top: 10px;">
                    📧 Se enviará una contraseña temporal al email proporcionado
                </p>
            </form>
        </div>
        
        <!-- LISTA DE USUARIOS -->
        <div class="section">
            <h2>📋 Lista de Usuarios</h2>
            
            <p style="color: #666; margin-bottom: 20px;">
                Total: <strong><?php echo count($all_users); ?></strong> usuarios
                | Admins: <strong><?php echo count(array_filter($all_users, fn($u) => $u['role'] === 'admin')); ?></strong>
                | Clientes: <strong><?php echo count(array_filter($all_users, fn($u) => $u['role'] === 'customer')); ?></strong>
            </p>
            
            <div class="users-grid">
                <?php foreach ($all_users as $user): ?>
                    <div class="user-card">
                        <h3><?php echo htmlspecialchars($user['name']); ?></h3>
                        
                        <div class="user-info">
                            📧 <strong><?php echo htmlspecialchars($user['email']); ?></strong>
                        </div>
                        
                        <div class="user-info">
                            📅 Registro: <?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?>
                        </div>
                        
                        <div class="user-info">
                            Status: <?php echo $user['is_active'] ? '✅ Activo' : '⏳ Pendiente verificación'; ?>
                        </div>
                        
                        <span class="badge badge-<?php echo $user['role']; ?>">
                            <?php echo $user['role'] === 'admin' ? '👑 ADMINISTRADOR' : '🛒 CLIENTE'; ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if (empty($all_users)): ?>
                <p style="text-align: center; color: #999; padding: 40px;">
                    No hay usuarios registrados aún
                </p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
