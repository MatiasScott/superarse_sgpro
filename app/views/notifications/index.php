<?php
// app/views/notifications/index.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?php echo BASE_PATH; ?>/img/logo_sgpro.png">
    <title><?= htmlspecialchars($pageTitle) ?> - SGPRO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/css/compiled.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">
    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>
    
    <div class="main-content">

        <main class="p-4 lg:p-8">
                <!-- Header -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="bg-blue-100 p-3 rounded-lg">
                                <i class="fas fa-bell text-2xl text-blue-600"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">Notificaciones</h1>
                                <p class="text-gray-600">Gestiona y revisa todas tus notificaciones</p>
                            </div>
                        </div>
                        
                        <!-- Contador de no leídas -->
                        <?php if ($unreadCount > 0): ?>
                        <div class="bg-red-100 text-red-800 px-4 py-2 rounded-full">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <?= $unreadCount ?> sin leer
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Filtros y Acciones -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <div class="flex flex-wrap gap-4 items-center justify-between">
                        <div class="flex flex-wrap gap-3">
                            <!-- Filtro por estado -->
                            <a href="<?= BASE_PATH ?>/notifications" 
                               class="px-4 py-2 rounded-lg <?= !isset($_GET['unread']) ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?> transition-colors">
                                <i class="fas fa-list mr-2"></i>Todas
                            </a>
                            <a href="<?= BASE_PATH ?>/notifications?unread=1" 
                               class="px-4 py-2 rounded-lg <?= isset($_GET['unread']) && $_GET['unread'] == '1' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?> transition-colors">
                                <i class="fas fa-exclamation-circle mr-2"></i>No leídas
                            </a>
                        </div>
                        
                        <!-- Marcar todas como leídas -->
                        <?php if ($unreadCount > 0): ?>
                        <a href="<?= BASE_PATH ?>/notifications/mark-all-read" 
                           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors"
                           onclick="return confirm('¿Marcar todas las notificaciones como leídas?')">
                            <i class="fas fa-check-double mr-2"></i>
                            Marcar todas como leídas
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Lista de Notificaciones -->
                <div class="bg-white rounded-lg shadow-sm">
                    <?php if (empty($notifications)): ?>
                    <!-- Estado vacío -->
                    <div class="p-12 text-center">
                        <div class="bg-gray-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-bell-slash text-3xl text-gray-400"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">No hay notificaciones</h3>
                        <p class="text-gray-500">
                            <?= isset($_GET['unread']) ? 'No tienes notificaciones sin leer' : 'Aún no has recibido ninguna notificación' ?>
                        </p>
                    </div>
                    <?php else: ?>
                    <!-- Lista de notificaciones -->
                    <div class="divide-y divide-gray-200">
                        <?php foreach ($notifications as $notification): ?>
                        <div class="p-6 hover:bg-gray-50 transition-colors <?= !$notification['is_read'] ? 'bg-blue-50 border-l-4 border-blue-500' : '' ?>">
                            <div class="flex items-start space-x-4">
                                <!-- Icono según tipo -->
                                <div class="flex-shrink-0">
                                    <?php
                                    $iconClasses = [
                                        'success' => 'fas fa-check-circle text-green-500',
                                        'info' => 'fas fa-info-circle text-blue-500',
                                        'warning' => 'fas fa-exclamation-triangle text-yellow-500',
                                        'danger' => 'fas fa-times-circle text-red-500'
                                    ];
                                    $iconClass = $iconClasses[$notification['type']] ?? 'fas fa-bell text-gray-500';
                                    ?>
                                    <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shadow-sm">
                                        <i class="<?= $iconClass ?>"></i>
                                    </div>
                                </div>

                                <!-- Contenido -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h4 class="text-lg font-semibold text-gray-900 <?= !$notification['is_read'] ? 'font-bold' : '' ?>">
                                                <?= htmlspecialchars($notification['title']) ?>
                                                <?php if (!$notification['is_read']): ?>
                                                <span class="inline-block w-2 h-2 bg-blue-500 rounded-full ml-2"></span>
                                                <?php endif; ?>
                                            </h4>
                                            <p class="text-gray-700 mt-1 leading-relaxed">
                                                <?= htmlspecialchars($notification['message']) ?>
                                            </p>
                                            
                                            <!-- Metadatos -->
                                            <div class="flex items-center space-x-4 mt-3 text-sm text-gray-500">
                                                <span>
                                                    <i class="fas fa-database mr-1"></i>
                                                    <?= ucfirst(str_replace('_', ' ', $notification['table_name'])) ?>
                                                </span>
                                                <span>
                                                    <i class="fas fa-clock mr-1"></i>
                                                    <?php
                                                    $fecha = new DateTime($notification['created_at']);
                                                    echo $fecha->format('d/m/Y H:i');
                                                    ?>
                                                </span>
                                                <?php if ($notification['is_read'] && $notification['read_at']): ?>
                                                <span class="text-green-600">
                                                    <i class="fas fa-eye mr-1"></i>
                                                    Leída
                                                </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- Acciones -->
                                        <div class="flex-shrink-0 ml-4">
                                            <?php if (!$notification['is_read']): ?>
                                            <a href="<?= BASE_PATH ?>/notifications/mark-read/<?= $notification['id'] ?>" 
                                               class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                                               title="Marcar como leída">
                                                <i class="fas fa-eye mr-1"></i>
                                                Marcar como leída
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Paginación (si es necesaria) -->
                <?php if (count($notifications) >= 20): ?>
                <div class="mt-6 flex justify-center">
                    <div class="bg-white rounded-lg shadow-sm px-6 py-3">
                        <p class="text-gray-600 text-sm">
                            <i class="fas fa-info-circle mr-2"></i>
                            Mostrando las últimas 20 notificaciones
                        </p>
                    </div>
                </div>
                <?php endif; ?>
        </main>
    </div>

    <!-- JavaScript para actualización en tiempo real -->
    <script>
        // Actualizar contador de notificaciones cada 30 segundos
        setInterval(function() {
            fetch('<?= BASE_PATH ?>/notifications/unread-count')
                .then(response => response.json())
                .then(data => {
                    const badge = document.querySelector('#notification-badge');
                    if (badge) {
                        if (data.count > 0) {
                            badge.textContent = data.count;
                            badge.style.display = 'inline-block';
                        } else {
                            badge.style.display = 'none';
                        }
                    }
                })
                .catch(error => console.error('Error updating notification count:', error));
        }, 30000);
    </script>

    <script src="<?php echo BASE_PATH; ?>/js/responsive.js"></script>
</body>
</html>