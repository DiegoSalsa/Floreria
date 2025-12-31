<?php
require_once 'load-env.php';
require_once 'auth-config.php';

// Frontend and Backend URLs
$frontend_url = 'https://floreriawildgarden.vercel.app';
$admin_url = 'https://floreria-wildgarden.onrender.com/admin-dashboard.php';

// CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle OPTIONS preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if (is_authenticated()) {
    if (isset($_POST['email'])) {
        // HTML form submitted
        header("Location: $frontend_url");
    } else {
        // JSON request
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Ya estás autenticado']);
    }
    exit;
}

$error = '';
$success = false;

// Handle JSON POST (from modal)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content_type = $_SERVER['CONTENT_TYPE'] ?? '';
    
    if (strpos($content_type, 'application/json') !== false) {
        // JSON request from modal
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        $email = sanitize($input['email'] ?? '');
        $password = $input['password'] ?? '';
        
        if (!$email || !$password) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Email y contraseña requeridos']);
            exit;
        }
        
        $result = login_user($email, $password);
        
        if ($result['success']) {
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Login exitoso', 'user' => $result['user'] ?? []]);
        } else {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => $result['error']]);
        }
        exit;
    } else {
        // HTML form
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (!$email || !$password) {
            $error = 'Email y contraseña requeridos';
        } else {
            $result = login_user($email, $password);
            
            if ($result['success']) {
                header('Location: auth-redirect.php');
                exit;
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
    <title>Login - Floreria Wildgarden</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .auth-container {
            max-width: 500px;
            margin: 80px auto;
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
            background: #FFE4E1;
            color: #8B0000;
            border: 1px solid #FF6B6B;
        }
        
        .auth-footer {
            text-align: center;
            margin-top: 20px;
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
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
        
        <h1>🌹 Iniciar Sesión</h1>
        
        <?php if ($error): ?>
            <div class="alert">
                ❌ <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="tu@email.com" autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required placeholder="Tu contraseña">
            </div>
            
            <button type="submit" class="submit-btn">Iniciar Sesión</button>
        </form>
        
        <div class="auth-footer">
            <a href="register.php">Crear cuenta</a>
            <span>|</span>
            <a href="/">Volver</a>
        </div>
    </div>
</body>
</html>
