<?php
require_once 'auth-config.php';

logActivity("Usuario desconectado", 'auth');
logout_user();

// Redirigir a página que limpia localStorage
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cerrando sesión...</title>
</head>
<body>
    <script>
        // Limpiar localStorage
        localStorage.removeItem('user_logged_in');
        localStorage.removeItem('user_data');
        
        // Redirigir al frontend
        setTimeout(function() {
            window.location.href = 'https://floreriawildgarden.vercel.app';
        }, 100);
    </script>
    <p>Cerrando sesión...</p>
</body>
</html>
