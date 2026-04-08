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
    <!-- Estilos para búsqueda y filtros -->
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

<body class="bg-gradient-to-br from-amber-50 via-orange-50 to-yellow-50 font-sans min-h-screen">
    <?php
    require_once __DIR__ . '/../../helpers/PermissionHelper.php';
    $canManageContracts = PermissionHelper::can('contracts', 'manage_all', $roles ?? null);
    $canCreateContracts = PermissionHelper::can('contracts', 'create', $roles ?? null);
    $canEditContracts = PermissionHelper::can('contracts', 'edit', $roles ?? null);
    $canDeleteContracts = PermissionHelper::can('contracts', 'delete', $roles ?? null);
    $hasManageOwnContracts = PermissionHelper::can('contracts', 'manage_own', $roles ?? null);
    ?>
    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

    <div class="main-content">
        <?php if ($canManageContracts || $canCreateContracts) { ?>
            <header class="mb-8">
                <div class="bg-gradient-to-r from-amber-600 to-orange-600 rounded-2xl shadow-xl p-8 text-white">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                        <div class="flex items-center space-x-4">
                            <div class="bg-white/20 backdrop-blur-sm p-4 rounded-xl">
                                <i class="fas fa-file-signature text-4xl"></i>
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold">Gestión de Contratos</h1>
                                <p class="text-amber-100 mt-1">Administre los contratos de profesores</p>
                            </div>
                        </div>
                        <a href="<?php echo BASE_PATH; ?>/contracts/create" class="bg-white hover:bg-gray-50 text-amber-600 font-bold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center space-x-2 w-full md:w-auto">
                            <i class="fas fa-plus-circle text-xl"></i>
                            <span>Crear Contrato</span>
                        </a>
                    </div>
                </div>
            </header>
        <?php } else { ?>
            <header class="mb-8">
                <div class="bg-gradient-to-r from-amber-600 to-orange-600 rounded-2xl shadow-xl p-8 text-white">
                    <div class="flex items-center space-x-4">
                        <div class="bg-white/20 backdrop-blur-sm p-4 rounded-xl">
                            <i class="fas fa-file-signature text-4xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold">Gestión de Contratos</h1>
                            <p class="text-amber-100 mt-1">Administre los contratos de profesores</p>
                        </div>
                    </div>
                </div>
            </header>
        <?php } ?>
        <main class="bg-white rounded-2xl shadow-xl overflow-hidden border-2 border-gray-200">
            <!-- CONTROLES DE BÚSQUEDA Y FILTROS -->
            <div class="search-filter-controls" style="border-radius: 2rem 2rem 0 0; margin: -2px -2px 0 -2px;">
                <div class="search-bar-container">
                    <label class="search-bar-label">🔍 Buscar contratos</label>
                    <input 
                        type="text" 
                        id="tableSearch" 
                        class="search-bar-input" 
                        placeholder="Buscar por profesor, PAO, ID, estado..."
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
                                    <i class="fas fa-hashtag text-amber-600"></i>
                                    <span>ID</span>
                                </div>
                            </th>
                            <th class="px-5 py-4 border-b-2 border-gray-200 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-user text-orange-600"></i>
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
                                    <i class="fas fa-toggle-on text-green-600"></i>
                                    <span>Estado</span>
                                </div>
                            </th>
                            <th class="px-5 py-4 border-b-2 border-gray-200 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-file-pdf text-red-600"></i>
                                    <span>Archivo</span>
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
                        <?php if (isset($contracts) && is_array($contracts) && !empty($contracts)): ?>
                            <?php foreach ($contracts as $contract): ?>
                                <tr class="hover:bg-amber-50 transition-colors duration-150">
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <span class="font-bold text-amber-600">#<?php echo htmlspecialchars($contract['id']); ?></span>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-user-circle text-orange-500"></i>
                                            <span class="font-medium"><?php echo htmlspecialchars($contract['professor_name']); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-bookmark text-blue-500"></i>
                                            <span><?php echo htmlspecialchars($contract['pao_name']); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <?php
                                        $statusClass = '';
                                        $statusIcon = '';
                                        switch ($contract['status']) {
                                            case 'Activo':
                                                $statusClass = 'bg-gradient-to-r from-green-500 to-emerald-600 text-white';
                                                $statusIcon = 'fa-check-circle';
                                                break;
                                            case 'Finalizado':
                                                $statusClass = 'bg-gradient-to-r from-red-500 to-red-600 text-white';
                                                $statusIcon = 'fa-times-circle';
                                                break;
                                            default:
                                                $statusClass = 'bg-gradient-to-r from-gray-400 to-gray-500 text-white';
                                                $statusIcon = 'fa-question-circle';
                                                break;
                                        }
                                        ?>
                                        <span class="inline-flex items-center space-x-2 px-3 py-1 font-semibold <?php echo $statusClass; ?> rounded-lg shadow-md">
                                            <i class="fas <?php echo $statusIcon; ?>"></i>
                                            <span><?php echo htmlspecialchars($contract['status']); ?></span>
                                        </span>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <?php if (!empty($contract['document_path'])): ?>
                                            <a href="<?php echo BASE_PATH . '/' . htmlspecialchars($contract['document_path']); ?>" target="_blank" class="inline-flex items-center space-x-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold px-3 py-2 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                                                <i class="fas fa-file-pdf"></i>
                                                <span>Ver Documento</span>
                                                <i class="fas fa-external-link-alt text-xs"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="inline-flex items-center space-x-2 text-gray-400 bg-gray-100 px-3 py-2 rounded-lg">
                                                <i class="fas fa-times"></i>
                                                <span>Sin archivo</span>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <?php
                                            $isOwnerContract = (int)($contract['professor_id'] ?? 0) === (int)($_SESSION['user_id'] ?? 0);
                                            $canEditRow = $canManageContracts || ($canEditContracts && (!$hasManageOwnContracts || $isOwnerContract));
                                            $canDeleteRow = $canManageContracts || ($canDeleteContracts && (!$hasManageOwnContracts || $isOwnerContract));
                                        ?>
                                        <?php if ($canEditRow || $canDeleteRow) { ?>
                                            <div class="flex items-center justify-end space-x-2">
                                                <?php if ($canEditRow): ?>
                                                    <a href="<?php echo BASE_PATH; ?>/contracts/edit/<?php echo htmlspecialchars($contract['id']); ?>" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold px-3 py-2 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5 flex items-center space-x-2">
                                                        <i class="fas fa-edit"></i>
                                                        <span>Editar</span>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if ($canDeleteRow): ?>
                                                    <form action="<?php echo BASE_PATH; ?>/contracts/delete/<?php echo htmlspecialchars($contract['id']); ?>" method="POST" onsubmit="return confirm('¿Está seguro de eliminar este contrato?')">
                                                        <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                                                        <button type="submit" class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold px-3 py-2 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5 flex items-center space-x-2">
                                                            <i class="fas fa-trash-alt"></i>
                                                            <span>Eliminar</span>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-4">
                                        <div class="bg-gray-100 p-6 rounded-full">
                                            <i class="fas fa-file-contract text-gray-400 text-5xl"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium text-lg">No hay contratos registrados</p>
                                        <p class="text-gray-400 text-sm">Comience creando un nuevo contrato</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- MENSAJE CUANDO NO HAY RESULTADOS EN LA BÚSQUEDA -->
    <div id="noResults" class="max-w-7xl mx-auto mt-6 text-center py-12" style="display: none;">
        <div class="flex flex-col items-center justify-center space-y-4">
            <div class="bg-gray-100 p-8 rounded-full">
                <i class="fas fa-search text-gray-400 text-5xl"></i>
            </div>
            <p class="text-gray-600 font-medium text-lg">
                No hay contratos que coincidan con la búsqueda
            </p>
            <p class="text-gray-400 text-sm">
                Intenta cambiar los términos de búsqueda o los filtros
            </p>
        </div>
    </div>

    <script src="<?php echo BASE_PATH; ?>/js/responsive.js"></script>
    
    <!-- Scripts para búsqueda y filtros -->
    <script src="<?php echo BASE_PATH; ?>/js/table-search-filter.js"></script>
    <script src="<?php echo BASE_PATH; ?>/js/module-config.js"></script>
    
    <!-- Inicializar búsqueda para módulo de contratos -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initializeTableSearch('contracts');
        });
    </script>
</body>

</html>