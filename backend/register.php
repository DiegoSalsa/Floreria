<?php
require_once 'load-env.php';
require_once 'auth-config.php';

// CORS headers - Allow credentials with specific origin
header('Access-Control-Allow-Origin: https://floreriawildgarden.vercel.app');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle OPTIONS preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content_type = $_SERVER['CONTENT_TYPE'] ?? '';
    
    if (strpos($content_type, 'application/json') !== false) {
        // JSON request from modal
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        $email = sanitize($input['email'] ?? '');
        $name = sanitize($input['name'] ?? '');
        $password = $input['password'] ?? '';
        $phone = sanitize($input['phone'] ?? '');
        
        if (!$email || !$name || !$password) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Email, nombre y contraseña requeridos']);
            exit;
        }
        
        $result = register_user($email, $name, $password, $phone);
        
        if ($result['success']) {
            // Auto-login después de registrarse
            login_user($email, $password);
            
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Cuenta creada exitosamente']);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $result['error']]);
        }
        exit;
    } else {
        // HTML form
        $email = sanitize($_POST['email'] ?? '');
        $name = sanitize($_POST['name'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';
        
        if (!$email || !$name || !$password) {
            $error = 'Todos los campos son requeridos';
        } elseif ($password !== $password_confirm) {
            $error = 'Las contraseñas no coinciden';
        } else {
            $result = register_user($email, $name, $password);
            
            if ($result['success']) {
                $success = $result['message'];
            } else {
                $error = $result['error'];
            }
        }
    }
}

function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Floreria Wildgarden</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .auth-container {
            max-width: 500px;
            margin: 60px auto;
            padding: 40px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .auth-container h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #1B4332;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #1B4332;
            box-shadow: 0 0 0 3px rgba(27, 67, 50, 0.1);
        }
        
        .submit-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #1B4332 0%, #0D2818 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(27, 67, 50, 0.3);
        }
        
        .alert {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .alert-error {
            background: #FFE4E1;
            color: #8B0000;
            border: 1px solid #FF6B6B;
        }
        
        .alert-success {
            background: #E8F5E9;
            color: #1B5E20;
            border: 1px solid #4CAF50;
        }
        
        .auth-footer {
            text-align: center;
            margin-top: 20px;
        }
        
        .auth-footer a {
            color: #1B4332;
            text-decoration: none;
            font-weight: 600;
        }
        
        .auth-footer a:hover {
            text-decoration: underline;
        }
        
        .back-home {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .back-home a {
            color: #666;
            text-decoration: none;
        }
        
        .back-home a:hover {
            color: #1B4332;
        }
    </style>
</head>
<body style="background: #f5f5f5;">
    <div class="auth-container">
        <div class="back-home">
            <a href="/">← Volver al sitio</a>
        </div>
        
        <h1>🌹 Crear Cuenta</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                ❌ <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                ✅ <?php echo $success; ?>
            </div>
            <p style="text-align: center; margin-top: 20px; color: #666;">
                Redirigiendo a login en 3 segundos...
            </p>
            <script>
                setTimeout(function() {
                    window.location.href = 'https://floreria-wildgarden.onrender.com/login.php';
                }, 3000);
            </script>
        <?php else: ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="name">Nombre Completo</label>
                <input type="text" id="name" name="name" required placeholder="Tu nombre">
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="tu@email.com">
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required placeholder="Mínimo 6 caracteres">
            </div>
            
            <div class="form-group">
                <label for="password_confirm">Confirmar Contraseña</label>
                <input type="password" id="password_confirm" name="password_confirm" required placeholder="Repite tu contraseña">
            </div>
            
            <button type="submit" class="submit-btn">Crear Cuenta</button>
        </form>
        
        <div class="auth-footer">
            ¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>
        </div>
        
        <?php endif; ?>
    </div>
</body>
</html>
