<?php
/**
 * CONFIGURACIÓN DE AUTENTICACIÓN Y EMAILS
 * Sistema de usuarios, login, registro
 */

// ============================================
// CORS HEADERS
// ============================================
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Max-Age: 3600');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Cargar variables de entorno (.env)
require_once __DIR__ . '/load-env.php';

// ============================================
// CONFIGURACIÓN DE SESIÓN
// ============================================
session_start();
define('SESSION_TIMEOUT', 3600); // 1 hora
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 900); // 15 minutos

// ============================================
// CONFIGURACIÓN DE EMAILS SMTP
// ============================================

// Usar variables de entorno
define('MAIL_HOST', getenv('MAIL_HOST') ?: 'smtp.gmail.com');
define('MAIL_PORT', getenv('MAIL_PORT') ?: 587);
define('MAIL_USERNAME', getenv('MAIL_USERNAME'));
define('MAIL_PASSWORD', getenv('MAIL_PASSWORD'));
define('MAIL_FROM', getenv('MAIL_FROM') ?: 'noreply@wildgardenflores.cl');
define('MAIL_FROM_NAME', getenv('MAIL_FROM_NAME') ?: 'Floreria Wildgarden');

// OPCIÓN 2: Otro servidor SMTP
// define('MAIL_HOST', 'smtp.tuservidor.com');
// define('MAIL_PORT', 587);
// define('MAIL_USERNAME', 'tu_usuario');
// define('MAIL_PASSWORD', 'tu_password');

// ============================================
// CONFIGURACIÓN DE BASE DE DATOS (si usas MySQL)
// ============================================
// En producción (Render + Railway): USE_DATABASE=true (PostgreSQL)
// En desarrollo local: USE_DATABASE=false (JSON)
define('USE_DATABASE', getenv('USE_DATABASE') === 'true' || getenv('USE_DATABASE') === '1');
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: '5432');
define('DB_USER', getenv('DB_USER') ?: 'postgres');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'wildgarden_db');

// ============================================
// CONFIGURACIÓN DE ARCHIVOS (alternativa a BD)
// ============================================
define('USERS_DIR', __DIR__ . '/users');
define('SESSIONS_DIR', __DIR__ . '/sessions');

// Crear directorios si no existen
if (!is_dir(USERS_DIR)) {
    mkdir(USERS_DIR, 0755, true);
}
if (!is_dir(SESSIONS_DIR)) {
    mkdir(SESSIONS_DIR, 0755, true);
}

// ============================================
// FUNCIONES DE AUTENTICACIÓN
// ============================================

/**
 * Hashear contraseña
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Conexión a Base de Datos
 */
function get_db_connection() {
    static $conn = null;
    
    if ($conn === null) {
        try {
            $host = DB_HOST;
            $port = DB_PORT;
            $user = DB_USER;
            $password = DB_PASSWORD;
            $dbname = DB_NAME;
            
            // PostgreSQL DSN
            $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";
            
            $conn = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (Exception $e) {
            logActivity("Error DB: " . $e->getMessage(), 'error');
            return null;
        }
    }
    
    return $conn;
}

/**
 * Verificar contraseña
 */
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Crear token de sesión
 */
function create_session_token() {
    return bin2hex(random_bytes(32));
}

/**
 * Registrar nuevo usuario
 */
function register_user($email, $name, $password) {
    // Validar email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'error' => 'Email inválido'];
    }
    
    // Validar nombre
    if (strlen($name) < 3) {
        return ['success' => false, 'error' => 'El nombre debe tener al menos 3 caracteres'];
    }
    
    // Validar contraseña
    if (strlen($password) < 6) {
        return ['success' => false, 'error' => 'La contraseña debe tener al menos 6 caracteres'];
    }
    
    // Verificar si usuario ya existe
    if (USE_DATABASE) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $stmt = $conn->prepare("SELECT id FROM admin_users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        
        if ($result->num_rows > 0) {
            $conn->close();
            return ['success' => false, 'error' => 'El email ya está registrado'];
        }
    } else {
        // Buscar en carpeta users
        $users = glob(USERS_DIR . '/*.json');
        foreach ($users as $file) {
            $user = json_decode(file_get_contents($file), true);
            if ($user['email'] === $email) {
                return ['success' => false, 'error' => 'El email ya está registrado'];
            }
        }
    }
    
    // Crear usuario
    $user_id = uniqid('user_', true);
    $password_hash = hash_password($password);
    
    $user_data = [
        'id' => $user_id,
        'email' => $email,
        'name' => $name,
        'password_hash' => $password_hash,
        'role' => 'customer', // Por defecto cliente
        'is_active' => false, // Requiere confirmación de email
        'verification_token' => create_session_token(),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    if (USE_DATABASE) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $stmt = $conn->prepare("
            INSERT INTO admin_users (username, email, password_hash, role, is_active, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        
        $username = explode('@', $email)[0];
        $role = 'customer';
        $is_active = 0;
        
        $stmt->bind_param("ssssi", $username, $email, $password_hash, $role, $is_active);
        $stmt->execute();
        $conn->close();
    } else {
        file_put_contents(USERS_DIR . "/{$user_id}.json", json_encode($user_data, JSON_PRETTY_PRINT));
    }
    
    // Enviar email de confirmación
    send_verification_email($email, $name, $user_data['verification_token']);
    
    return ['success' => true, 'message' => 'Usuario registrado. Verifica tu email para activar la cuenta'];
}

/**
 * Login de usuario
 */
function login_user($email, $password) {
    $user = find_user_by_email($email);
    
    if (!$user) {
        return ['success' => false, 'error' => 'Email o contraseña incorrectos'];
    }
    
    if (!$user['is_active']) {
        return ['success' => false, 'error' => 'Por favor verifica tu email primero'];
    }
    
    if (!verify_password($password, $user['password_hash'])) {
        return ['success' => false, 'error' => 'Email o contraseña incorrectos'];
    }
    
    // Crear sesión
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['login_time'] = time();
    
    return ['success' => true, 'message' => 'Login exitoso', 'role' => $user['role']];
}

/**
 * Buscar usuario por email
 */
function find_user_by_email($email) {
    if (USE_DATABASE) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $stmt = $conn->prepare("SELECT * FROM admin_users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $conn->close();
            return $user;
        }
        $conn->close();
        return null;
    } else {
        // Buscar en carpeta users
        $users = glob(USERS_DIR . '/*.json');
        foreach ($users as $file) {
            $user = json_decode(file_get_contents($file), true);
            if ($user['email'] === $email) {
                return $user;
            }
        }
        return null;
    }
}

/**
 * Buscar usuario por ID
 */
function find_user_by_id($user_id) {
    if (USE_DATABASE) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $stmt = $conn->prepare("SELECT * FROM admin_users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $conn->close();
            return $user;
        }
        $conn->close();
        return null;
    } else {
        $file = USERS_DIR . "/{$user_id}.json";
        if (file_exists($file)) {
            return json_decode(file_get_contents($file), true);
        }
        return null;
    }
}

/**
 * Verificar si usuario está autenticado
 */
function is_authenticated() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_email']);
}

/**
 * Verificar si usuario es admin
 */
function is_admin() {
    return is_authenticated() && $_SESSION['user_role'] === 'admin';
}

/**
 * Verificar sesión no caducada
 */
function check_session() {
    if (!is_authenticated()) {
        return false;
    }
    
    if (time() - $_SESSION['login_time'] > SESSION_TIMEOUT) {
        session_destroy();
        return false;
    }
    
    return true;
}

/**
 * Logout
 */
function logout_user() {
    session_destroy();
    header('Location: /login.php');
    exit;
}

// ============================================
// FUNCIONES DE EMAIL
// ============================================

/**
 * Enviar email de verificación
 */
function send_verification_email($email, $name, $token) {
    $verification_link = (getenv('APP_URL') ?: 'http://localhost:8000') . '/verify-email.php?token=' . $token;
    
    $subject = "Confirma tu cuenta - Floreria Wildgarden";
    
    $html = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #1B4332 0%, #0D2818 100%);
                     color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background: white; }
            .button { display: inline-block; padding: 12px 30px; background: #1B4332;
                     color: white; text-decoration: none; border-radius: 5px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Bienvenido a Floreria Wildgarden</h1>
            </div>
            <div class='content'>
                <p>Hola {$name},</p>
                
                <p>Gracias por registrarte en nuestra florería online.</p>
                <p>Para activar tu cuenta, haz click en el botón de abajo:</p>
                
                <p style='text-align: center; margin: 30px 0;'>
                    <a href='{$verification_link}' class='button'>Confirmar Email</a>
                </p>
                
                <p>O copia y pega este link:</p>
                <p style='word-break: break-all; color: #666;'>{$verification_link}</p>
                
                <p>Si no creaste esta cuenta, ignora este email.</p>
                
                <hr>
                <p style='font-size: 12px; color: #999;'>
                    Floreria Wildgarden<br>
                    Avenida Ignacio Collao 989, Concepción
                </p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return send_email($email, $subject, $html);
}

/**
 * Enviar email de confirmación de compra
 */
function send_purchase_confirmation($email, $name, $order_data) {
    $subject = "Confirmación de tu Pedido #" . $order_data['order_id'];
    
    $items_html = '';
    $items = json_decode($order_data['cart_items'], true);
    
    foreach ($items as $item) {
        $subtotal = $item['price'] * $item['quantity'];
        $items_html .= "
        <tr>
            <td style='padding: 8px; border-bottom: 1px solid #ddd;'>{$item['quantity']}x {$item['name']}</td>
            <td style='padding: 8px; border-bottom: 1px solid #ddd; text-align: right;'>\${$item['price']}</td>
            <td style='padding: 8px; border-bottom: 1px solid #ddd; text-align: right;'>\$" . number_format($subtotal, 0) . "</td>
        </tr>";
    }
    
    $html = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #1B4332 0%, #0D2818 100%);
                     color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background: white; }
            table { width: 100%; border-collapse: collapse; }
            .total-row { background: #1B4332; color: white; font-weight: bold; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>¡Gracias por tu compra!</h1>
            </div>
            <div class='content'>
                <p>Hola {$name},</p>
                
                <p>Confirmamos que hemos recibido tu pedido.</p>
                
                <h3>Detalles de tu Orden:</h3>
                <p><strong>Número:</strong> {$order_data['order_id']}</p>
                <p><strong>Monto:</strong> \${$order_data['amount']}</p>
                
                <h3>Productos:</h3>
                <table>
                    <thead>
                        <tr style='background: #f0f0f0;'>
                            <th style='padding: 8px; text-align: left;'>Producto</th>
                            <th style='padding: 8px; text-align: right;'>Precio</th>
                            <th style='padding: 8px; text-align: right;'>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$items_html}
                    </tbody>
                </table>
                
                <p style='margin-top: 20px;'>Nos pondremos en contacto contigo pronto para confirmar el envío.</p>
                
                <hr>
                <p style='font-size: 12px; color: #999;'>
                    Floreria Wildgarden<br>
                    WhatsApp: +56996744579
                </p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return send_email($email, $subject, $html);
}

/**
 * Enviar email genérico
 */
function send_email($to_email, $subject, $html_body) {
    // Headers
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM . ">\r\n";
    
    // Usar mail() nativo de PHP (sin SMTP)
    // En producción, usar PHPMailer o similar
    
    $result = mail($to_email, $subject, $html_body, $headers);
    
    if ($result) {
        logActivity("Email enviado a: {$to_email}", 'email');
        return true;
    } else {
        logActivity("ERROR: No se pudo enviar email a: {$to_email}", 'error');
        return false;
    }
}

/**
 * Log de actividades
 */
function logActivity($message, $type = 'info') {
    $log_file = __DIR__ . '/logs/activity.log';
    
    if (!is_dir(dirname($log_file))) {
        mkdir(dirname($log_file), 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $user = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : 'anon';
    $log_message = "[{$timestamp}] [{$type}] [{$user}] {$message}\n";
    
    file_put_contents($log_file, $log_message, FILE_APPEND);
}

// Validar sesión en cada carga
if (is_authenticated() && !check_session()) {
    logout_user();
}
?>
