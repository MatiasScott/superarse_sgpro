<?php
// public/index.php

// Carga el archivo de configuración y la clase del enrutador
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/core/Router.php';

// Carga la clase de conexión a la base de datos
require_once __DIR__ . '/../app/core/Database.php';

// Inicia la sesión
session_start();

// Configura el tiempo de inactividad a 5 minutos (300 segundos)
$inactive_time = 300;

// Verifica si la sesión de usuario y la variable de tiempo existen
if (isset($_SESSION['user_id']) && isset($_SESSION['last_activity'])) {
    // Calcula el tiempo transcurrido desde la última actividad
    $elapsed_time = time() - $_SESSION['last_activity'];

    if ($elapsed_time > $inactive_time) {
        // La sesión ha expirado por inactividad
        session_unset();     // Elimina todas las variables de la sesión
        session_destroy();   // Destruye la sesión
        header("Location: /"); // Redirige al login
        exit();
    }
}

// Actualiza el tiempo de la última actividad en cada petición
$_SESSION['last_activity'] = time();

// Crea una instancia del enrutador y maneja la petición
$router = new Router();
$router->dispatch();