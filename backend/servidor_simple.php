#!/usr/bin/env php
<?php
/**
 * Servidor PHP Simple para Desarrollo Local
 * Ejecutar: php servidor_simple.php
 */

$port = 8000;
$host = '127.0.0.1';

// Crear servidor
$server = stream_socket_server("tcp://$host:$port", $errno, $errstr);

if (!$server) {
    die("Error: $errstr ($errno)\n");
}

echo "\n";
echo "==============================================\n";
echo "🌹 FLORERIA WILDGARDEN - SERVIDOR PHP\n";
echo "==============================================\n";
echo "\n✅ Servidor iniciado en: http://localhost:$port\n";
echo "\n📍 URLs disponibles:\n";
echo "   🏠 Sitio: http://localhost:$port/\n";
echo "   👤 Login: http://localhost:$port/login.php\n";
echo "   ✍️ Registro: http://localhost:$port/register.php\n";
echo "   📊 Admin: http://localhost:$port/admin-dashboard.php\n";
echo "\nPresiona Ctrl+C para detener...\n\n";

while (true) {
    $client = stream_socket_accept($server, -1);
    if ($client) {
        $request = stream_get_contents($client, 1024);
        $lines = explode("\r\n", $request);
        $firstLine = $lines[0];
        
        preg_match('/GET\s+(.*?)\s+HTTP/', $firstLine, $matches);
        $path = $matches[1] ?? '/';
        
        // Parse path
        $pathinfo = parse_url($path);
        $file = $pathinfo['path'];
        
        if ($file === '/') {
            $file = '/index.html';
        }
        
        // Servir archivo
        $filepath = __DIR__ . $file;
        $filepath = realpath($filepath) ?: '';
        
        $content = '';
        $status = '404 Not Found';
        $contentType = 'text/html';
        
        if (file_exists($filepath) && strpos($filepath, __DIR__) === 0) {
            $content = file_get_contents($filepath);
            $status = '200 OK';
            
            // Detectar content type
            $ext = pathinfo($filepath, PATHINFO_EXTENSION);
            $types = [
                'css' => 'text/css',
                'js' => 'application/javascript',
                'json' => 'application/json',
                'png' => 'image/png',
                'jpg' => 'image/jpeg',
                'gif' => 'image/gif',
                'ico' => 'image/x-icon',
            ];
            $contentType = $types[$ext] ?? 'text/html';
        }
        
        // Enviar respuesta
        $response = "HTTP/1.1 $status\r\n";
        $response .= "Content-Type: $contentType; charset=utf-8\r\n";
        $response .= "Content-Length: " . strlen($content) . "\r\n";
        $response .= "Connection: close\r\n";
        $response .= "\r\n";
        $response .= $content;
        
        fwrite($client, $response);
        fclose($client);
        
        echo "[" . date('H:i:s') . "] GET $path - $status\n";
    }
}

fclose($server);
?>
