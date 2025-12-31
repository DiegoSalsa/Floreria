<?php
// Script para generar hash bcrypt
$password = "admin123";
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
echo "Password: " . $password . "\n";
echo "Hash: " . $hash . "\n";
?>
