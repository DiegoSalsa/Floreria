<?php
require_once 'auth-config.php';

logActivity("Usuario desconectado", 'auth');
logout_user();
?>
