<?php
require_once 'load-env.php';
require_once 'auth-config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Solo POST permitido']);
    exit;
}

$email = $_POST['email'] ?? 'test@example.com';
$name = $_POST['name'] ?? 'Test User';
$password = $_POST['password'] ?? 'test123456';

// Registrar usuario
$result = register_user($email, $name, $password);

// Verificar BD directamente
try {
    $conn = get_db_connection();
    
    // Verificar que tabla existe
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users");
    $stmt->execute();
    $table_exists = true;
    $user_count = $stmt->fetch()['count'] ?? 0;
} catch (Exception $e) {
    $table_exists = false;
    $user_count = 'Tabla no existe: ' . $e->getMessage();
}

// Intentar buscar el usuario que acabamos de registrar
try {
    $conn = get_db_connection();
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user_found = $stmt->fetch();
} catch (Exception $e) {
    $user_found = null;
}

echo json_encode([
    'register_result' => $result,
    'table_exists' => $table_exists,
    'users_in_table' => $user_count,
    'user_found_after_register' => $user_found ?: 'No encontrado',
    'database_config' => [
        'DB_HOST' => DB_HOST,
        'DB_NAME' => DB_NAME,
        'DB_USER' => DB_USER,
        'USE_DATABASE' => USE_DATABASE
    ]
], JSON_PRETTY_PRINT);
?>
