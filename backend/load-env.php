<?php
/**
 * Cargar variables de entorno desde archivo .env
 * Útil para desarrollo local
 */

$envFile = __DIR__ . '/../.env';

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Ignorar comentarios
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parsear KEY=VALUE
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remover comillas si existen
            if (preg_match('/^["\'](.+)["\']$/', $value, $matches)) {
                $value = $matches[1];
            }
            
            // Establecer variable de entorno
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}
