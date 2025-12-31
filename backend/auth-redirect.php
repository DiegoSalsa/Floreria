<?php
require_once 'load-env.php';
require_once 'auth-config.php';

// Esta página se llamará DESPUÉS de un login exitoso
// Guarda los datos en localStorage y redirige

if (!is_authenticated()) {
    header('Location: login.php');
    exit;
}

$user_email = $_SESSION['user_email'] ?? '';
$user_name = $_SESSION['user_name'] ?? '';
$user_role = $_SESSION['user_role'] ?? 'customer';

$user_json = json_encode([
    'email' => $user_email,
    'name' => $user_name,
    'role' => $user_role
]);

$frontend_url = 'https://floreriawildgarden.vercel.app';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Redirigiendo...</title>
</head>
<body>
    <script>
        // Guardar datos en localStorage
        localStorage.setItem('user_logged_in', 'true');
        localStorage.setItem('user_data', <?php echo json_encode($user_json); ?>);
        
        // Esperar a que se guarde y redirigir
        setTimeout(function() {
            window.location.href = '<?php echo $frontend_url; ?>';
        }, 100);
    </script>
    <p>Redirigiendo...</p>
</body>
</html>
