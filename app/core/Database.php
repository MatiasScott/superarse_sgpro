<?php
// app/core/Database.php

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        // Carga las credenciales de configuración
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (\PDOException $e) {
            // Mostrar error detallado con recomendaciones
            $errorCode = $e->getCode();
            $errorMsg = $e->getMessage();
            
            $debugInfo = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Error de Conexión a Base de Datos</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .error-container { background-color: #ffebee; border: 2px solid #c62828; border-radius: 5px; padding: 20px; max-width: 900px; margin: 0 auto; }
        h1 { color: #c62828; }
        .error-details { background-color: #fff; border-left: 4px solid #c62828; padding: 15px; margin: 15px 0; font-family: monospace; }
        .success-steps { background-color: #e8f5e9; border-left: 4px solid #2e7d32; padding: 15px; margin: 15px 0; }
        .success-steps h3 { color: #2e7d32; margin-top: 0; }
        ol { padding-left: 20px; }
        li { margin-bottom: 10px; }
        code { background-color: #f0f0f0; padding: 2px 6px; border-radius: 3px; }
        .warning { background-color: #fff3e0; border-left: 4px solid #f57c00; padding: 15px; margin: 15px 0; }
        .warning h4 { color: #f57c00; margin-top: 0; }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>⚠️ Error de Conexión a Base de Datos</h1>
        
        <div class="error-details">
            <strong>Código de Error:</strong> {$errorCode}<br>
            <strong>Mensaje:</strong> {$errorMsg}
        </div>
        
        <div class="warning">
            <h4>Información de Conexión Actual:</h4>
            <p><strong>Host:</strong> <code>DB_HOST</code><br>
            <strong>Usuario:</strong> <code>DB_USER</code><br>
            <strong>Base de Datos:</strong> <code>DB_NAME</code></p>
        </div>
        
        <div class="success-steps">
            <h3>✓ Soluciones Recomendadas:</h3>
            <ol>
                <li><strong>Si estás en XAMPP local:</strong>
                    <ul>
                        <li>Asegúrate de que MySQL está corriendo en XAMPP</li>
                        <li>Verifica que creaste la base de datos <code>superar1_landing_sgpro</code></li>
                        <li>Usuario local por defecto: <code>root</code> (sin contraseña)</li>
                        <li>Si usas credenciales diferentes, crea <code>.env.local</code> (ver paso 2)</li>
                    </ul>
                </li>
                <li><strong>Crear archivo de configuración local <code>.env.local</code>:</strong>
                    <pre>DB_HOST_LOCAL=localhost
DB_USER_LOCAL=root
DB_PASS_LOCAL=tu_contraseña_aqui
DB_NAME_LOCAL=superar1_landing_sgpro</pre>
                </li>
                <li><strong>Importar la base de datos:</strong>
                    <ul>
                        <li>Accede a phpMyAdmin: <code>http://localhost/phpmyadmin</code></li>
                        <li>Crea la base de datos <code>superar1_landing_sgpro</code></li>
                        <li>Importa el archivo SQL: <code>BDD/superar1_landing_sgpro.sql</code></li>
                    </ul>
                </li>
                <li><strong>Para cambiar credenciales manualmente:</strong>
                    <p>Edita <code>app/config/config.php</code> y modifica:</p>
                    <pre>define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_contraseña');
define('DB_NAME', 'tu_base_de_datos');</pre>
                </li>
            </ol>
        </div>
    </div>
</body>
</html>
HTML;
            
            // En modo desarrollo, mostrar error detallado
            if (defined('ENVIRONMENT') && ENVIRONMENT !== 'production') {
                die($debugInfo);
            } else {
                // En producción, registrar en log y mostrar error genérico
                error_log("Database connection failed: " . $errorMsg);
                die("<h1>Error de Conexión</h1><p>No se pudo conectar a la base de datos. Por favor, intenta de nuevo más tarde.</p>");
            }
        }
    }

    // Método para obtener la única instancia de la clase (Patrón Singleton)
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Método para obtener el objeto PDO
    public function getConnection() {
        return $this->pdo;
    }
}