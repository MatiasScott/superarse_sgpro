<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($pageTitle); ?></title>
  <link rel="icon" type="image/png" href="<?php echo BASE_PATH; ?>/img/logo_sgpro.jpg">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/css/compiled.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
  .sidebar-item-text {
    /* Asegura que el texto sea claro */
    color: #ffffff;
  }

  .sidebar-item-text-logout {
    /* Color rojo para cerrar sesión */
    color: #f87171;
  }

  /* Clases para truncar texto en móvil */
  .line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    line-clamp: 2;
    overflow: hidden;
  }

  /* Asegurar que los elementos no se desborden en móvil y tablet */
  @media (max-width: 1024px) {
    .main-content {
      padding: 1rem;
      padding-top: 1rem;
      margin-left: 0 !important;
      width: 100% !important;
    }
    
    header {
      margin-top: 3.5rem !important;
      margin-bottom: 1rem !important;
    }
  }

  @media (min-width: 1025px) {
    .main-content {
      margin-left: 16rem !important;
      width: calc(100% - 16rem) !important;
      padding: 2rem;
    }
  }
  </style>
</head>

<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 font-sans min-h-screen">
  <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

  <!-- Main Content -->
  <div class="main-content">
    <header class="mb-6">
      <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl shadow-xl p-6 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
          <div class="flex items-center space-x-4">
            <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
              <i class="fas fa-tachometer-alt text-3xl"></i>
            </div>
            <div>
              <h1 class="text-2xl sm:text-3xl font-bold">Panel de Control</h1>
              <p class="text-blue-100 mt-1 text-sm">Sistema de Gestión de Profesores</p>
            </div>
          </div>
          <div class="flex items-center space-x-3 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-xl">
            <div class="bg-white/20 p-2 rounded-lg">
              <i class="fas fa-user text-lg"></i>
            </div>
            <div>
              <p class="text-xs text-blue-100">Bienvenido</p>
              <p class="font-semibold"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuario'); ?></p>
            </div>
          </div>
        </div>
      </div>
    </header>

    <main class="space-y-6">
      <!-- Panel de Información General -->
      <div class="bg-white rounded-2xl shadow-xl overflow-hidden border-2 border-gray-200">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b-2 border-gray-200">
          <div class="flex items-center space-x-3">
            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 p-3 rounded-xl">
              <i class="fas fa-info-circle text-white text-xl"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-800">Información General</h2>
          </div>
        </div>
        <div class="p-6">
          <div class="flex items-start space-x-4 mb-6">
            <div class="bg-blue-100 p-3 rounded-lg">
              <i class="fas fa-check-circle text-blue-600 text-2xl"></i>
            </div>
            <div>
              <p class="text-gray-700 font-medium mb-2">Bienvenido al Sistema de Gestión de Profesores (SGPRO)</p>
              <p class="text-sm text-gray-500">Gestione de forma eficiente toda la información académica y administrativa</p>
            </div>
          </div>

          <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-5 border border-blue-200">
            <div class="flex items-center space-x-2 mb-4">
              <i class="fas fa-user-shield text-blue-600 text-lg"></i>
              <p class="font-bold text-gray-800">Tus Roles Activos:</p>
            </div>
            <div class="flex flex-wrap gap-3">
              <?php foreach ($roles as $role): ?>
              <div class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-4 py-2 rounded-lg shadow-md flex items-center space-x-2">
                <i class="fas fa-shield-alt"></i>
                <span class="font-semibold"><?php echo htmlspecialchars($role['role_name']); ?></span>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Grid responsive para los paneles principales -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">

        <!-- Panel de Actividad Reciente -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border-2 border-gray-200">
          <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b-2 border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-2 sm:space-y-0">
              <div class="flex items-center space-x-3">
                <div class="bg-gradient-to-br from-blue-500 to-indigo-600 p-3 rounded-xl">
                  <i class="fas fa-clock text-white text-xl"></i>
                </div>
                <div>
                  <h2 class="text-xl font-bold text-gray-800">Actividad Reciente</h2>
                  <p class="text-xs text-gray-500">Últimas modificaciones del sistema</p>
                </div>
              </div>
            </div>
          </div>

          <div class="p-4 sm:p-6">
            <?php if (isset($recentActivities) && !empty($recentActivities)): ?>
            <div class="space-y-3 sm:space-y-4 max-h-80 sm:max-h-96 overflow-y-auto">
              <?php foreach ($recentActivities as $activity): ?>
              <?php 
                                 $activityModel = new ActivityLogModel();
                                 $description = $activityModel->formatActivityDescription($activity);
                                 $color = $activityModel->getActivityColor($activity['action']);
                                 $timeAgo = date('H:i', strtotime($activity['created_at']));
                                 $dateFormatted = date('d/m/Y', strtotime($activity['created_at']));
                              ?>
              <div
                class="flex items-start space-x-2 sm:space-x-3 p-2 sm:p-3 rounded-lg border border-gray-100 hover:bg-gray-50 transition duration-200">
                <div class="flex-shrink-0">
                  <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-full flex items-center justify-center <?php 
                                          echo $color == 'green' ? 'bg-green-100' : 
                                             ($color == 'blue' ? 'bg-blue-100' : 'bg-red-100'); 
                                       ?>">
                    <svg class="w-3 h-3 sm:w-4 sm:h-4 <?php 
                                             echo $color == 'green' ? 'text-green-600' : 
                                                   ($color == 'blue' ? 'text-blue-600' : 'text-red-600'); 
                                          ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <?php if ($activity['action'] == 'CREATE'): ?>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                      <?php elseif ($activity['action'] == 'UPDATE'): ?>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                      </path>
                      <?php else: ?>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                      </path>
                      <?php endif; ?>
                    </svg>
                  </div>
                </div>
                <div class="flex-1 min-w-0">
                  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs sm:text-sm font-medium text-gray-900 truncate">
                      <?php echo htmlspecialchars($activity['user_name'] ?? 'Usuario desconocido'); ?>
                    </p>
                    <div class="text-left sm:text-right mt-1 sm:mt-0">
                      <p class="text-xs text-gray-500"><?php echo $timeAgo; ?></p>
                      <p class="text-xs text-gray-400 hidden sm:block"><?php echo $dateFormatted; ?></p>
                    </div>
                  </div>
                  <p class="text-xs sm:text-sm text-gray-600 mt-1 line-clamp-2">
                    <?php echo htmlspecialchars($description); ?>
                  </p>
                  <?php if (!empty($activity['description'])): ?>
                  <p class="text-xs text-gray-500 mt-1 hidden sm:block">
                    ID: <?php echo htmlspecialchars($activity['record_id']); ?>
                  </p>
                  <?php endif; ?>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-6 sm:py-8">
              <svg class="w-8 h-8 sm:w-12 sm:h-12 mx-auto text-gray-400 mb-3 sm:mb-4" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z">
                </path>
              </svg>
              <p class="text-sm sm:text-base text-gray-500">No hay actividad reciente registrada</p>
              <p class="text-xs sm:text-sm text-gray-400 mt-1">Las actividades aparecerán aquí cuando se realicen
                cambios en el sistema</p>
            </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Panel de Notificaciones -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border-2 border-gray-200">
          <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b-2 border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-2 sm:space-y-0">
              <div class="flex items-center space-x-3">
                <div class="bg-gradient-to-br from-yellow-500 to-orange-600 p-3 rounded-xl relative">
                  <i class="fas fa-bell text-white text-xl"></i>
                  <?php if (isset($unreadCount) && $unreadCount > 0): ?>
                  <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold"><?php echo $unreadCount; ?></span>
                  <?php endif; ?>
                </div>
                <div>
                  <h2 class="text-xl font-bold text-gray-800">Notificaciones</h2>
                  <p class="text-xs text-gray-500">Alertas y actualizaciones del sistema</p>
                </div>
              </div>
              <?php if (isset($unreadCount) && $unreadCount > 0): ?>
              <a href="<?php echo BASE_PATH; ?>/dashboard/mark-all-notifications-read"
                class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white text-xs font-semibold px-4 py-2 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5 flex items-center space-x-2"
                onclick="return confirm('¿Marcar todas las notificaciones como leídas?')">
                <i class="fas fa-check-double"></i>
                <span>Marcar todas</span>
              </a>
              <?php endif; ?>
            </div>
          </div>

          <div class="p-4 sm:p-6">
            <?php if (isset($notifications) && !empty($notifications)): ?>
            <div class="space-y-3 sm:space-y-4 max-h-80 sm:max-h-96 overflow-y-auto">
              <?php foreach ($notifications as $notification): ?>
              <?php 
                                 $iconClasses = [
                                       'success' => 'text-green-500 bg-green-100',
                                       'info' => 'text-blue-500 bg-blue-100',
                                       'warning' => 'text-yellow-500 bg-yellow-100',
                                       'danger' => 'text-red-500 bg-red-100'
                                 ];
                                 $iconClass = $iconClasses[$notification['type']] ?? 'text-gray-500 bg-gray-100';
                                 $timeAgo = date('H:i', strtotime($notification['created_at']));
                                 $dateFormatted = date('d/m/Y', strtotime($notification['created_at']));
                              ?>
              <div
                class="flex items-start space-x-2 sm:space-x-3 p-2 sm:p-3 rounded-lg border border-gray-100 hover:bg-gray-50 transition duration-200 <?php echo !$notification['is_read'] ? 'bg-blue-50 border-blue-200' : ''; ?>">
                <div class="flex-shrink-0">
                  <div
                    class="w-6 h-6 sm:w-8 sm:h-8 rounded-full flex items-center justify-center <?php echo $iconClass; ?>">
                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <?php if ($notification['type'] == 'success'): ?>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                      <?php elseif ($notification['type'] == 'warning'): ?>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01M12 3C8.686 3 6 5.686 6 9c0 2.31 1.343 4.308 3.282 5.249.35.17.718.251 1.092.251h1.252c.374 0 .742-.081 1.092-.251C14.657 13.308 16 11.31 16 9c0-3.314-2.686-6-6-6z">
                      </path>
                      <?php elseif ($notification['type'] == 'danger'): ?>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                      </path>
                      <?php else: ?>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      <?php endif; ?>
                    </svg>
                  </div>
                </div>
                <div class="flex-1 min-w-0">
                  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <h4
                      class="text-xs sm:text-sm font-semibold text-gray-900 <?php echo !$notification['is_read'] ? 'font-bold' : ''; ?> truncate pr-2">
                      <?php echo htmlspecialchars($notification['title']); ?>
                      <?php if (!$notification['is_read']): ?>
                      <span class="inline-block w-2 h-2 bg-blue-500 rounded-full ml-1"></span>
                      <?php endif; ?>
                    </h4>
                    <div
                      class="flex flex-row sm:flex-col items-center sm:items-end justify-between sm:justify-start mt-2 sm:mt-0 gap-2">
                      <div class="text-left sm:text-right">
                        <p class="text-xs text-gray-500"><?php echo $timeAgo; ?></p>
                        <p class="text-xs text-gray-400 hidden sm:block"><?php echo $dateFormatted; ?></p>
                      </div>
                      <?php if (!$notification['is_read']): ?>
                      <a href="<?php echo BASE_PATH; ?>/dashboard/mark-notification-read/<?php echo $notification['id']; ?>"
                        class="text-blue-600 hover:text-blue-800 text-xs font-medium whitespace-nowrap"
                        title="Marcar como leída">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                          </path>
                        </svg>
                      </a>
                      <?php endif; ?>
                    </div>
                  </div>
                  <p class="text-xs sm:text-sm text-gray-600 mt-1 line-clamp-2">
                    <?php echo htmlspecialchars($notification['message']); ?>
                  </p>
                  <div
                    class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 mt-2 text-xs text-gray-500 gap-1 sm:gap-0">
                    <span class="flex items-center">
                      <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4">
                        </path>
                      </svg>
                      <?php echo ucfirst(str_replace('_', ' ', $notification['table_name'])); ?>
                    </span>
                    <?php if ($notification['is_read'] && $notification['read_at']): ?>
                    <span class="text-green-600 flex items-center">
                      <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                      </svg>
                      Leída
                    </span>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-6 sm:py-8">
              <svg class="w-8 h-8 sm:w-12 sm:h-12 mx-auto text-gray-400 mb-3 sm:mb-4" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-5 5v-5zM4.828 4.828A4 4 0 015.5 4H9v1a3 3 0 106 0V4h3.5c.266 0 .52.105.707.293l2.5 2.5a1 1 0 01.293.707V19a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 01.879-1.65z">
                </path>
              </svg>
              <p class="text-sm sm:text-base text-gray-500">No hay notificaciones</p>
              <p class="text-xs sm:text-sm text-gray-400 mt-1">Las notificaciones aparecerán aquí cuando se realicen
                acciones en el sistema</p>
            </div>
            <?php endif; ?>
          </div>
        </div>

      </div> <!-- End grid responsive -->
    </main>
  </div>


</body>

</html>