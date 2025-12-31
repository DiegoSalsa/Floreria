<?php
require_once 'load-env.php';
require_once 'auth-config.php';

// Debug: Verificar conexión a BD
$conn = get_db_connection();
if (!$conn) {
    die(json_encode(['error' => 'No hay conexión a BD']));
}

// Verificar que tabla users existe
try {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users");
    $stmt->execute();
    $result = $stmt->fetch();
    $user_count = $result['count'] ?? 0;
} catch (Exception $e) {
    $user_count = 'ERROR: ' . $e->getMessage();
}

// Verificar admin_users
try {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM admin_users");
    $stmt->execute();
    $result = $stmt->fetch();
    $admin_count = $result['count'] ?? 0;
} catch (Exception $e) {
    $admin_count = 'ERROR: ' . $e->getMessage();
}

// Verificar sesión
$is_logged_in = is_authenticated();
$session_data = [
    'user_id' => $_SESSION['user_id'] ?? null,
    'user_email' => $_SESSION['user_email'] ?? null,
    'user_role' => $_SESSION['user_role'] ?? null
];

echo json_encode([
    'database_connection' => 'OK',
    'users_table_count' => $user_count,
    'admin_users_table_count' => $admin_count,
    'is_logged_in' => $is_logged_in,
    'session_data' => $session_data,
    'php_version' => phpversion(),
    'env_vars' => [
        'USE_DATABASE' => USE_DATABASE,
        'DB_HOST' => DB_HOST,
        'DB_NAME' => DB_NAME
    ]
], JSON_PRETTY_PRINT);
?>
