<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="icon" type="image/png" href="<?php echo BASE_PATH; ?>/img/logo_sgpro.jpg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/css/compiled.css">
<link rel="stylesheet" href="<?php echo BASE_PATH; ?>/css/responsive.css">
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

<body class="bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 font-sans min-h-screen">
    <?php
    require_once __DIR__ . '/../../helpers/PermissionHelper.php';
    $canManagePortfolios = PermissionHelper::can('portfolios', 'manage_all', $roles ?? null);
    $canCreatePortfolios = PermissionHelper::can('portfolios', 'create', $roles ?? null);
    $canEditPortfolios = PermissionHelper::can('portfolios', 'edit', $roles ?? null);
    $canDeletePortfolios = PermissionHelper::can('portfolios', 'delete', $roles ?? null);
    $hasManageOwnPortfolios = PermissionHelper::can('portfolios', 'manage_own', $roles ?? null);
    ?>
    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>
    <div class="main-content">
        <?php if ($canManagePortfolios || $canCreatePortfolios) { ?>
            <header class="mb-8">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-xl p-8 text-white">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                        <div class="flex items-center space-x-4">
                            <div class="bg-white/20 backdrop-blur-sm p-4 rounded-xl">
                                <i class="fas fa-briefcase text-4xl"></i>
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold">Gestión de Portafolios</h1>
                                <p class="text-indigo-100 mt-1">Administre los portafolios académicos</p>
                            </div>
                        </div>
                        <a href="<?php echo BASE_PATH; ?>/portfolios/create" class="bg-white hover:bg-gray-50 text-indigo-600 font-bold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center space-x-2 w-full md:w-auto">
                            <i class="fas fa-plus-circle text-xl"></i>
                            <span>Crear Nuevo Portafolio</span>
                        </a>
                    </div>
                </div>
            </header>
        <?php } else { ?>
            <header class="mb-8">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-xl p-8 text-white">
                    <div class="flex items-center space-x-4">
                        <div class="bg-white/20 backdrop-blur-sm p-4 rounded-xl">
                            <i class="fas fa-briefcase text-4xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold">Gestión de Portafolios</h1>
                            <p class="text-indigo-100 mt-1">Administre los portafolios académicos</p>
                        </div>
                    </div>
                </div>
            </header>
        <?php } ?>
        <main class="bg-white rounded-2xl shadow-xl overflow-hidden border-2 border-gray-200">
            <div class="search-filter-controls" style="border-radius: 2rem 2rem 0 0; margin: -2px -2px 0 -2px;">
                <div class="search-bar-container">
                    <label class="search-bar-label">Buscar portafolios</label>
                    <input 
                        type="text" 
                        id="tableSearch" 
                        class="search-bar-input" 
                        placeholder="Buscar por profesor, PAO o unidades..."
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
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-user text-indigo-600"></i>
                                    <span>Profesor</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-book text-purple-600"></i>
                                    <span>PAO</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center justify-center space-x-2">
                                    <i class="fas fa-tasks text-green-600"></i>
                                    <span>Unidades Completas</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center justify-end space-x-2">
                                    <i class="fas fa-cog text-blue-600"></i>
                                    <span>Acciones</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (!empty($portfolios)): ?>
                            <?php foreach ($portfolios as $portfolio): ?>
                                <tr class="hover:bg-indigo-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-3">
                                            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 p-2 rounded-lg">
                                                <i class="fas fa-user-tie text-white"></i>
                                            </div>
                                            <span class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($portfolio['professor_name']); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-bookmark text-purple-500"></i>
                                            <span class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($portfolio['pao_name']); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <?php
                                        $portfolioType = $portfolio['portfolio_type'] ?? 'academico';
                                        $approvedUnits = count(array_filter($portfolio['units'], function ($unit) {
                                            return !empty($unit['unit_approved']) && $unit['unit_approved'] == 1;
                                        }));

                                        $unitsWithFiles = 0;
                                        foreach ($portfolio['units'] as $unit) {
                                            $hasFiles = false;
                                            $docenciaColumn = 'docencia_' . $portfolioType . '_path';
                                            $practicasColumn = 'practicas_' . $portfolioType . '_path';
                                            $titulacionColumn = 'titulacion_' . $portfolioType . '_path';

                                            if ($portfolioType === 'academico') {
                                                $hasFiles = !empty($unit[$docenciaColumn]);
                                            } elseif ($portfolioType === 'practico') {
                                                $hasFiles = !empty($unit[$docenciaColumn]) && !empty($unit[$practicasColumn]);
                                            } elseif ($portfolioType === 'titulacion') {
                                                $hasFiles = !empty($unit[$docenciaColumn]) && !empty($unit[$practicasColumn]) && !empty($unit[$titulacionColumn]);
                                            }

                                            if ($hasFiles) {
                                                $unitsWithFiles++;
                                            }
                                        }
                                        ?>
                                        <div class="inline-flex items-center space-x-2">
                                            <div class="flex items-center bg-gradient-to-r from-green-500 to-emerald-600 text-white font-bold px-4 py-2 rounded-lg shadow-md">
                                                <i class="fas fa-check-circle mr-2"></i>
                                                <span><?php echo $approvedUnits; ?> / 4</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <?php
                                        $isOwnerPortfolio = (int)($portfolio['professor_id'] ?? 0) === (int)($_SESSION['user_id'] ?? 0);
                                        $canEditRow = $canManagePortfolios || ($canEditPortfolios && (!$hasManageOwnPortfolios || $isOwnerPortfolio));
                                        $canDeleteRow = $canManagePortfolios || ($canDeletePortfolios && (!$hasManageOwnPortfolios || $isOwnerPortfolio));
                                        ?>
                                        <div class="flex items-center justify-end space-x-2">
                                            <?php if ($canEditRow): ?>
                                                <a href="<?php echo BASE_PATH; ?>/portfolios/edit/<?php echo htmlspecialchars($portfolio['id']); ?>" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold px-4 py-2 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5 flex items-center space-x-2">
                                                    <i class="fas fa-edit"></i>
                                                    <span>Editar</span>
                                                </a>
                                            <?php endif; ?>
                                            
                                            <?php if ($canDeleteRow): ?>
                                                <form action="<?php echo BASE_PATH; ?>/portfolios/delete/<?php echo htmlspecialchars($portfolio['id']); ?>" method="POST" onsubmit="return confirm('¿Está seguro de eliminar este portafolio?')">
                                                    <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                                                    <button type="submit" class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold px-4 py-2 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5 flex items-center space-x-2">
                                                        <i class="fas fa-trash-alt"></i>
                                                        <span>Eliminar</span>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-4">
                                        <div class="bg-gray-100 p-6 rounded-full">
                                            <i class="fas fa-folder-open text-gray-400 text-5xl"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium text-lg">No hay portafolios creados</p>
                                        <p class="text-gray-400 text-sm">Comience creando un nuevo portafolio</p>
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
                No hay portafolios que coincidan con la búsqueda
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
            initializeTableSearch('portfolios');
        });
    </script>
</body>

</html>
