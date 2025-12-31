<?php
require_once 'load-env.php';
require_once 'auth-config.php';

// Verificar que estamos logueados
if (!is_authenticated()) {
    header('Location: login.php');
    exit;
}

$user_email = $_SESSION['user_email'] ?? '';
$user_name = $_SESSION['user_name'] ?? '';
$user_role = $_SESSION['user_role'] ?? 'customer';

// Crear JSON para localStorage
$user_data = [
    'email' => $user_email,
    'name' => $user_name,
    'role' => $user_role
];
$user_json = json_encode($user_data);

$frontend_url = 'https://floreriawildgarden.vercel.app';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Iniciando sesión...</title>
    <script>
        // Guardar datos INMEDIATAMENTE
        try {
            localStorage.setItem('user_logged_in', 'true');
            localStorage.setItem('user_data', '<?php echo addslashes($user_json); ?>');
            console.log('localStorage guardado:', {
                user_logged_in: localStorage.getItem('user_logged_in'),
                user_data: localStorage.getItem('user_data')
            });
        } catch (e) {
            console.error('Error guardando localStorage:', e);
        }
        
        // Redirigir después de guardar
        setTimeout(function() {
            window.location.href = '<?php echo $frontend_url; ?>';
        }, 200);
    </script>
</head>
<body>
    <p>Iniciando sesión...</p>
</body>
</html>
