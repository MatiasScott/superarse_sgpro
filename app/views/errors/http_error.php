<?php
$statusCode = isset($statusCode) ? (int)$statusCode : (http_response_code() ?: 500);

$defaultMessages = [
    405 => 'Esta operacion requiere un metodo HTTP permitido para continuar.',
    419 => 'Tu sesion de seguridad ha expirado o el token CSRF es invalido.',
    403 => 'No tienes permisos para realizar esta accion.',
    404 => 'La pagina solicitada no existe.',
    500 => 'Ha ocurrido un error interno del servidor.',
];

$titleMap = [
    405 => 'Metodo No Permitido',
    419 => 'Sesion Expirada',
    403 => 'Acceso Denegado',
    404 => 'Pagina No Encontrada',
    500 => 'Error Interno',
];

$title = $titleMap[$statusCode] ?? 'Error HTTP';
$message = isset($errorMessage) && is_string($errorMessage) && trim($errorMessage) !== ''
    ? $errorMessage
    : ($defaultMessages[$statusCode] ?? 'Ocurrio un error al procesar la solicitud.');

$backUrl = defined('BASE_PATH') ? BASE_PATH . '/dashboard' : '/';
if (!isset($_SESSION['user_id'])) {
    $backUrl = defined('BASE_PATH') ? BASE_PATH . '/' : '/';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($statusCode . ' - ' . $title); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-100 via-sky-50 to-indigo-100 flex items-center justify-center p-6">
    <main class="max-w-xl w-full bg-white rounded-2xl shadow-xl border border-slate-200 p-8 text-center">
        <p class="text-sm font-semibold text-sky-700 tracking-wide"><?php echo htmlspecialchars((string)$statusCode); ?></p>
        <h1 class="mt-2 text-2xl font-bold text-slate-800"><?php echo htmlspecialchars($title); ?></h1>
        <p class="mt-3 text-slate-600"><?php echo htmlspecialchars($message); ?></p>

        <div class="mt-7 flex justify-center gap-3">
            <a href="<?php echo htmlspecialchars($backUrl); ?>" class="px-5 py-2.5 rounded-xl bg-sky-600 text-white font-semibold hover:bg-sky-700 transition-colors">Volver</a>
            <a href="javascript:history.back()" class="px-5 py-2.5 rounded-xl bg-slate-200 text-slate-700 font-semibold hover:bg-slate-300 transition-colors">Atras</a>
        </div>
    </main>
</body>
</html>
