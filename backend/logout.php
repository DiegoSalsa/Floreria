<?php
require_once 'auth-config.php';

logActivity("Usuario desconectado", 'auth');
logout_user();

// Redirigir al frontend con script que limpie localStorage
$frontend_url = 'https://floreriawildgarden.vercel.app';
echo "<script>
    localStorage.removeItem('user_logged_in');
    localStorage.removeItem('user_data');
    window.location.href = '$frontend_url';
</script>";
?>
