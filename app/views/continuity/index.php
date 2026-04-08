<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?php echo BASE_PATH; ?>/img/logo_sgpro.png">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/css/compiled.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/css/table-search-filter.css">
    <style>
        .sidebar-item-text {
            color: #ffffff;
        }

        .sidebar-item-text-logout {
            color: #f87171;
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

<body class="bg-gradient-to-br from-teal-50 via-cyan-50 to-blue-50 font-sans min-h-screen">
    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>
    <div class="main-content">
        <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] == 1 || $_SESSION['user_role'] == 4)) { ?>
            <header class="mb-8">
                <div class="bg-gradient-to-r from-teal-600 to-cyan-600 rounded-2xl shadow-xl p-8 text-white">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                        <div class="flex items-center space-x-4">
                            <div class="bg-white/20 backdrop-blur-sm p-4 rounded-xl">
                                <i class="fas fa-sync-alt text-4xl"></i>
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold">Gestión de Continuidad</h1>
                                <p class="text-teal-100 mt-1">Administre los procesos de continuidad docente</p>
                            </div>
                        </div>
                        <a href="<?php echo BASE_PATH; ?>/continuity/create" class="bg-white hover:bg-gray-50 text-teal-600 font-bold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center space-x-2 w-full md:w-auto">
                            <i class="fas fa-play-circle text-xl"></i>
                            <span>Iniciar Proceso</span>
                        </a>
                    </div>
                </div>
            </header>
        <?php } else { ?>
            <header class="mb-8">
                <div class="bg-gradient-to-r from-teal-600 to-cyan-600 rounded-2xl shadow-xl p-8 text-white">
                    <div class="flex items-center space-x-4">
                        <div class="bg-white/20 backdrop-blur-sm p-4 rounded-xl">
                            <i class="fas fa-sync-alt text-4xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold">Gestión de Continuidad</h1>
                            <p class="text-teal-100 mt-1">Administre los procesos de continuidad docente</p>
                        </div>
                    </div>
                </div>
            </header>
        <?php } ?>
        <main class="bg-white rounded-2xl shadow-xl overflow-hidden border-2 border-gray-200">
            <div class="search-filter-controls" style="border-radius: 2rem 2rem 0 0; margin: -2px -2px 0 -2px;">
                <div class="search-bar-container">
                    <label class="search-bar-label">Buscar continuidad</label>
                    <input 
                        type="text" 
                        id="tableSearch" 
                        class="search-bar-input" 
                        placeholder="Buscar por profesor, PAO, decisiones o estado..."
                    >
                </div>
                <div id="filterContainer" class="filters-container"></div>
                <div class="search-results-info">
                    <span class="results-count">
                        Resultados: <span class="results-count-value" id="resultCount">0</span>
                    </span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="min-w-full leading-normal">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-5 py-4 border-b-2 border-gray-200 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-hashtag text-teal-600"></i>
                                    <span>ID</span>
                                </div>
                            </th>
                            <th class="px-5 py-4 border-b-2 border-gray-200 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-user text-cyan-600"></i>
                                    <span>Profesor</span>
                                </div>
                            </th>
                            <th class="px-5 py-4 border-b-2 border-gray-200 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-book text-blue-600"></i>
                                    <span>PAO</span>
                                </div>
                            </th>
                            <th class="px-5 py-4 border-b-2 border-gray-200 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-user-check text-green-600"></i>
                                    <span>Decisión Profesor</span>
                                </div>
                            </th>
                            <th class="px-5 py-4 border-b-2 border-gray-200 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-briefcase text-purple-600"></i>
                                    <span>Decisión Docencia</span>
                                </div>
                            </th>
                            <th class="px-5 py-4 border-b-2 border-gray-200 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-flag-checkered text-orange-600"></i>
                                    <span>Estado Final</span>
                                </div>
                            </th>
                            <th class="px-5 py-4 border-b-2 border-gray-200 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-cog text-gray-600"></i>
                                    <span>Acciones</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($continuities) && is_array($continuities) && !empty($continuities)): ?>
                            <?php foreach ($continuities as $continuity): ?>
                                <tr class="hover:bg-teal-50 transition-colors duration-150">
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <span class="font-bold text-teal-600">#<?php echo htmlspecialchars($continuity['id']); ?></span>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-user-circle text-cyan-500"></i>
                                            <span class="font-medium"><?php echo htmlspecialchars($continuity['professor_name']); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-bookmark text-blue-500"></i>
                                            <span><?php echo htmlspecialchars($continuity['pao_name']); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <?php
                                        $decisionText = 'Pendiente';
                                        $decisionClass = 'bg-gradient-to-r from-gray-400 to-gray-500 text-white';
                                        $decisionIcon = 'fa-clock';
                                        if ($continuity['professor_decision'] !== null) {
                                            $decisionText = $continuity['professor_decision'] ? 'Sí' : 'No';
                                            if ($continuity['professor_decision']) {
                                                $decisionClass = 'bg-gradient-to-r from-green-500 to-green-600 text-white';
                                                $decisionIcon = 'fa-check-circle';
                                            } else {
                                                $decisionClass = 'bg-gradient-to-r from-red-500 to-red-600 text-white';
                                                $decisionIcon = 'fa-times-circle';
                                            }
                                        }
                                        ?>
                                        <span class="inline-flex items-center space-x-2 px-3 py-1 font-semibold <?php echo $decisionClass; ?> rounded-lg shadow-md">
                                            <i class="fas <?php echo $decisionIcon; ?>"></i>
                                            <span><?php echo htmlspecialchars($decisionText); ?></span>
                                        </span>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <?php
                                        $decisionText = 'Pendiente';
                                        $decisionClass = 'bg-gradient-to-r from-gray-400 to-gray-500 text-white';
                                        $decisionIcon = 'fa-clock';
                                        if ($continuity['docencia_decision'] !== null) {
                                            $decisionText = $continuity['docencia_decision'] ? 'Sí' : 'No';
                                            if ($continuity['docencia_decision']) {
                                                $decisionClass = 'bg-gradient-to-r from-green-500 to-green-600 text-white';
                                                $decisionIcon = 'fa-check-circle';
                                            } else {
                                                $decisionClass = 'bg-gradient-to-r from-red-500 to-red-600 text-white';
                                                $decisionIcon = 'fa-times-circle';
                                            }
                                        }
                                        ?>
                                        <span class="inline-flex items-center space-x-2 px-3 py-1 font-semibold <?php echo $decisionClass; ?> rounded-lg shadow-md">
                                            <i class="fas <?php echo $decisionIcon; ?>"></i>
                                            <span><?php echo htmlspecialchars($decisionText); ?></span>
                                        </span>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <?php
                                        $statusClass = '';
                                        $statusIcon = '';
                                        switch ($continuity['final_status']) {
                                            case 'Pendiente':
                                                $statusClass = 'bg-gradient-to-r from-yellow-400 to-yellow-500 text-white';
                                                $statusIcon = 'fa-hourglass-half';
                                                break;
                                            case 'Retenido':
                                                $statusClass = 'bg-gradient-to-r from-green-500 to-emerald-600 text-white';
                                                $statusIcon = 'fa-user-check';
                                                break;
                                            case 'No Retenido':
                                                $statusClass = 'bg-gradient-to-r from-red-500 to-red-600 text-white';
                                                $statusIcon = 'fa-user-times';
                                                break;
                                        }
                                        ?>
                                        <span class="inline-flex items-center space-x-2 px-3 py-1 font-semibold <?php echo $statusClass; ?> rounded-lg shadow-md">
                                            <i class="fas <?php echo $statusIcon; ?>"></i>
                                            <span><?php echo htmlspecialchars($continuity['final_status']); ?></span>
                                        </span>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <?php
                                        // Super Administrador (role 1) siempre puede editar
                                        // TH/Docencia (role 4) siempre puede editar
                                        // Profesor (role 5) puede editar solo si TH no ha tomado decisión
                                        $canEdit = false;
                                        if (isset($_SESSION['user_role'])) {
                                            if ($_SESSION['user_role'] == 1 || $_SESSION['user_role'] == 4) {
                                                // Super Admin y Talento Humano siempre pueden editar
                                                $canEdit = true;
                                            } elseif ($_SESSION['user_role'] == 5 && $continuity['docencia_decision'] === null) {
                                                // Profesor puede editar solo si TH no ha decidido
                                                $canEdit = true;
                                            }
                                        }
                                        ?>
                                        <div class="flex items-center justify-end space-x-2">
                                            <?php if ($canEdit): ?>
                                                <a href="<?php echo BASE_PATH; ?>/continuity/edit/<?php echo htmlspecialchars($continuity['id']); ?>" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold px-3 py-2 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5 flex items-center space-x-2">
                                                    <i class="fas fa-edit"></i>
                                                    <span>Editar</span>
                                                </a>
                                            <?php endif; ?>
                                            <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] == 1 || $_SESSION['user_role'] == 4)): ?>
                                                <a href="<?php echo BASE_PATH; ?>/continuity/delete/<?php echo htmlspecialchars($continuity['id']); ?>" class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold px-3 py-2 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5 flex items-center space-x-2" onclick="return confirm('¿Está seguro de eliminar este registro de continuidad?')">
                                                    <i class="fas fa-trash-alt"></i>
                                                    <span>Eliminar</span>
                                                </a>
                                            <?php endif; ?>
                                            <?php if (!$canEdit && isset($_SESSION['user_role']) && $_SESSION['user_role'] == 5 && $continuity['docencia_decision'] !== null): ?>
                                                <span class="inline-flex items-center space-x-2 text-gray-400 italic bg-gray-100 px-3 py-2 rounded-lg">
                                                    <i class="fas fa-lock"></i>
                                                    <span>Proceso finalizado</span>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-4">
                                        <div class="bg-gray-100 p-6 rounded-full">
                                            <i class="fas fa-sync text-gray-400 text-5xl"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium text-lg">No hay registros de continuidad</p>
                                        <p class="text-gray-400 text-sm">Comience iniciando un nuevo proceso de continuidad</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    <div id="noResults" class="max-w-7xl mx-auto mt-6 text-center py-12" style="display: none;">
        <div class="flex flex-col items-center justify-center space-y-4">
            <div class="bg-gray-100 p-8 rounded-full">
                <i class="fas fa-search text-gray-400 text-5xl"></i>
            </div>
            <p class="text-gray-600 font-medium text-lg">
                No hay registros de continuidad que coincidan con la búsqueda
            </p>
            <p class="text-gray-400 text-sm">
                Intenta cambiar el texto o los filtros
            </p>
        </div>
    </div>
    <script src="<?php echo BASE_PATH; ?>/js/responsive.js"></script>
    <script src="<?php echo BASE_PATH; ?>/js/table-search-filter.js"></script>
    <script src="<?php echo BASE_PATH; ?>/js/module-config.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initializeTableSearch('continuity');
        });
    </script>
</body>

</html>