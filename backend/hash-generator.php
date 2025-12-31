<?php
// Generar hash bcrypt para la contraseña admin123
$password = 'admin123';
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

echo "Contraseña: $password\n";
echo "Hash: $hash\n";
echo "\nUSO: Ejecutar en Railway:\n";
echo "UPDATE admin_users SET password_hash = '$hash' WHERE username = 'admin';\n";
?>
