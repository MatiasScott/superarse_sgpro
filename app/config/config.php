<?php
// app/config/config.php

// Función para cargar variables de entorno desde archivo .env.local
function loadEnvFile($filePath) {
    if (!file_exists($filePath)) {
        return [];
    }
    
    $env = [];
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Omitir comentarios
        if (strpos($line, '#') === 0) {
            continue;
        }
        
        // Parsear líneas KEY=VALUE
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $env[trim($key)] = trim($value);
        }
    }
    
    return $env;
}

// Cargar variables de entorno si existen
$envFile = __DIR__ . '/../../.env.local';
$env = loadEnvFile($envFile);

// Detectar ambiente automáticamente
$isLocal = (
    strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false ||
    strpos($_SERVER['HTTP_HOST'] ?? '', '127.0.0.1') !== false ||
    strpos(__DIR__, 'xampp') !== false ||
    getenv('APP_ENV') === 'local'
);

// Configuración por ambiente
if ($isLocal) {
    // Configuración LOCAL (XAMPP)
    define('DB_HOST', $env['DB_HOST_LOCAL'] ?? 'localhost');
    define('DB_USER', $env['DB_USER_LOCAL'] ?? 'root');
    define('DB_PASS', $env['DB_PASS_LOCAL'] ?? 'Superarse.2025');
    define('DB_NAME', $env['DB_NAME_LOCAL'] ?? 'superar1_landing_sgpro');
} else {
    // Configuración PRODUCCIÓN
    define('DB_HOST', $env['DB_HOST_PROD'] ?? 'localhost');
    define('DB_USER', $env['DB_USER_PROD'] ?? 'superar1_Tics');
    define('DB_PASS', $env['DB_PASS_PROD'] ?? '/Msvs5297*');
    define('DB_NAME', $env['DB_NAME_PROD'] ?? 'superar1_landing_sgpro');
}

// Detectar BASE_PATH automáticamente
$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath === '/' || $basePath === '\\') {
    $basePath = '';
}
define('BASE_PATH', $basePath);
