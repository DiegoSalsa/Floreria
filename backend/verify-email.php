<?php
require_once 'auth-config.php';

$token = $_GET['token'] ?? '';
$message = '';
$success = false;

if ($token) {
    // Buscar usuario con este token
    $users = glob(USERS_DIR . '/*.json');
    $user_found = null;
    
    foreach ($users as $file) {
        $user = json_decode(file_get_contents($file), true);
        if ($user['verification_token'] === $token) {
            $user_found = $user;
            break;
        }
    }
    
    if ($user_found) {
        // Activar usuario
        $user_found['is_active'] = true;
        $user_found['verification_token'] = null;
        $user_found['updated_at'] = date('Y-m-d H:i:s');
        
        file_put_contents(USERS_DIR . "/{$user_found['id']}.json", json_encode($user_found, JSON_PRETTY_PRINT));
        
        $message = '✅ Email verificado correctamente. Ya puedes iniciar sesión.';
        $success = true;
        logActivity("Email verificado para: {$user_found['email']}", 'auth');
    } else {
        $message = '❌ Token de verificación inválido o expirado.';
    }
} else {
    $message = '❌ Token no proporcionado.';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Email</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .container { max-width: 500px; margin: 80px auto; padding: 40px;
                    background: white; border-radius: 10px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.1); text-align: center; }
        .icon { font-size: 80px; margin-bottom: 20px; }
        h1 { color: #1B4332; margin-bottom: 20px; }
        p { font-size: 1.1em; margin: 15px 0; color: #333; }
        a { display: inline-block; margin-top: 30px; padding: 12px 30px;
           background: #1B4332; color: white; text-decoration: none;
           border-radius: 5px; font-weight: 600; }
        a:hover { background: #0D2818; }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon"><?php echo $success ? '✓' : '✕'; ?></div>
        <h1><?php echo $success ? 'Verificación Exitosa' : 'Error de Verificación'; ?></h1>
        <p><?php echo $message; ?></p>
        <a href="<?php echo $success ? '/login.php' : '/'; ?>">
            <?php echo $success ? 'Ir a Login' : 'Volver al inicio'; ?>
        </a>
    </div>
</body>
</html>
