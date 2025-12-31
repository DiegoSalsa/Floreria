<?php
require_once 'load-env.php';
require_once 'auth-config.php';

header('Content-Type: application/json');

// Aceptar GET o POST
$email = $_REQUEST['email'] ?? 'test' . time() . '@example.com';
$name = $_REQUEST['name'] ?? 'Test User ' . time();
$password = $_REQUEST['password'] ?? 'test123456';

echo "Intentando registrar: $email\n";
echo "USE_DATABASE: " . (USE_DATABASE ? 'true' : 'false') . "\n";

// Primero verificar conexión
try {
    $conn = get_db_connection();
    if (!$conn) {
        echo json_encode(['error' => 'No hay conexión a BD']);
        exit;
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Excepción en conexión: ' . $e->getMessage()]);
    exit;
}

// Verificar que tabla users existe
try {
    $check = $conn->prepare("SELECT to_regclass('public.users')");
    $check->execute();
    $exists = $check->fetch();
    if (!$exists[0]) {
        echo json_encode(['error' => 'Tabla users no existe en la BD']);
        exit;
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Error verificando tabla: ' . $e->getMessage()]);
    exit;
}

// Registrar usuario
$result = register_user($email, $name, $password);

echo "Resultado del registro: " . json_encode($result) . "\n";

// Verificar que se insertó
try {
    $conn = get_db_connection();
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users");
    $stmt->execute();
    $row = $stmt->fetch();
    $total_users = $row['count'] ?? 0;
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email ORDER BY id DESC LIMIT 1");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();
    
    echo json_encode([
        'register_result' => $result,
        'total_users_in_db' => $total_users,
        'user_found' => $user ?: 'No encontrado'
    ], JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo json_encode(['error' => 'Error al verificar: ' . $e->getMessage()]);
}
?>

