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

<body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 font-sans min-h-screen">
    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>
    <?php
        $isProfesor = isset($roles) && is_array($roles) && in_array('Profesor', array_column($roles, 'role_name'));
        $isAdminEvaluator = isset($_SESSION['user_role']) && ($_SESSION['user_role'] == 5 || $_SESSION['user_role'] == 1);
        $canEditEvaluation = $isAdminEvaluator || $isProfesor;
        $canDeleteEvaluation = $isAdminEvaluator && !$isProfesor;
    ?>
    <div class="main-content">
        <?php if (!empty($_SESSION['flash_success'])): ?>
            <div class="max-w-7xl mx-auto mb-6">
                <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-xl">
                    <p class="text-sm text-green-700"><?php echo htmlspecialchars($_SESSION['flash_success']); ?></p>
                </div>
            </div>
            <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>
        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="max-w-7xl mx-auto mb-6">
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl">
                    <p class="text-sm text-red-700"><?php echo htmlspecialchars($_SESSION['flash_error']); ?></p>
                </div>
            </div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] == 1 || $_SESSION['user_role'] == 2)) { ?>
            <header class="mb-8">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl shadow-xl p-8 text-white">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                        <div class="flex items-center space-x-4">
                            <div class="bg-white/20 backdrop-blur-sm p-4 rounded-xl">
                                <i class="fas fa-clipboard-check text-4xl"></i>
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold">Gestión de Evaluaciones</h1>
                                <p class="text-blue-100 mt-1">Administre las evaluaciones académicas</p>
                            </div>
                        </div>
                        <a href="<?php echo BASE_PATH; ?>/evaluations/create" class="bg-white hover:bg-gray-50 text-blue-600 font-bold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center space-x-2 w-full md:w-auto">
                            <i class="fas fa-plus-circle text-xl"></i>
                            <span>Crear Evaluación</span>
                        </a>
                    </div>
                </div>
            </header>
        <?php } else { ?>
            <header class="mb-8">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl shadow-xl p-8 text-white">
                    <div class="flex items-center space-x-4">
                        <div class="bg-white/20 backdrop-blur-sm p-4 rounded-xl">
                            <i class="fas fa-clipboard-check text-4xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold">Gestión de Evaluaciones</h1>
                            <p class="text-blue-100 mt-1">Administre las evaluaciones académicas</p>
                        </div>
                    </div>
                </div>
            </header>
        <?php } ?>

        <div class="mb-6">
            <div class="bg-white/80 backdrop-blur-sm border-2 border-blue-100 rounded-2xl p-4 shadow-md">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <p class="text-sm font-semibold text-gray-700">Documentos de referencia para evaluaciones</p>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="<?php echo BASE_PATH; ?>/uploads/evaluations/NORMATIVA_EVALUACIONES.pdf" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center space-x-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold px-4 py-2 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                            <i class="fas fa-book"></i>
                            <span>Normativa</span>
                        </a>
                        <a href="<?php echo BASE_PATH; ?>/uploads/evaluations/PROCEDIMIENTO_EVALUACIONES.pdf" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center space-x-2 bg-gradient-to-r from-teal-600 to-cyan-600 hover:from-teal-700 hover:to-cyan-700 text-white font-semibold px-4 py-2 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                            <i class="fas fa-list-check"></i>
                            <span>Procedimiento</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <main class="bg-white rounded-2xl shadow-xl overflow-hidden border-2 border-gray-200">
            <div class="search-filter-controls" style="border-radius: 2rem 2rem 0 0; margin: -2px -2px 0 -2px;">
                <div class="search-bar-container">
                    <label class="search-bar-label">Buscar evaluaciones</label>
                    <input 
                        type="text" 
                        id="tableSearch" 
                        class="search-bar-input" 
                        placeholder="Buscar por profesor, escuela, PAO, puntaje o estado..."
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
                                    <i class="fas fa-hashtag text-blue-600"></i>
                                    <span>ID</span>
                                </div>
                            </th>
                            <th class="px-5 py-4 border-b-2 border-gray-200 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-user text-indigo-600"></i>
                                    <span>Profesor</span>
                                </div>
                            </th>
                            <th class="px-5 py-4 border-b-2 border-gray-200 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-school text-blue-600"></i>
                                    <span>Escuela</span>
                                </div>
                            </th>
                            <th class="px-5 py-4 border-b-2 border-gray-200 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-book text-purple-600"></i>
                                    <span>PAO</span>
                                </div>
                            </th>
                            <th class="px-5 py-4 border-b-2 border-gray-200 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-star text-yellow-600"></i>
                                    <span>Puntaje</span>
                                </div>
                            </th>
                            <th class="px-5 py-4 border-b-2 border-gray-200 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-info-circle text-green-600"></i>
                                    <span>Estado</span>
                                </div>
                            </th>
                            <th class="px-5 py-4 border-b-2 border-gray-200 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-file-alt text-teal-600"></i>
                                    <span>Documentos</span>
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
                        <?php if (isset($evaluations) && is_array($evaluations) && !empty($evaluations)): ?>
                            <?php foreach ($evaluations as $evaluation): ?>
                                <tr class="hover:bg-blue-50 transition-colors duration-150">
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <span class="font-bold text-blue-600">#<?php echo htmlspecialchars($evaluation['id']); ?></span>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-user-circle text-indigo-500"></i>
                                            <span class="font-medium"><?php echo htmlspecialchars($evaluation['professor_name']); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-school text-blue-500"></i>
                                            <span><?php echo htmlspecialchars($evaluation['professor_school'] ?? 'Sin escuela'); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-bookmark text-purple-500"></i>
                                            <span><?php echo htmlspecialchars($evaluation['pao_name']); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-star text-yellow-500"></i>
                                            <span class="font-bold text-gray-800"><?php echo htmlspecialchars($evaluation['score']); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <?php
                                        $statusClass = '';
                                        $statusIcon = '';
                                        switch ($evaluation['status']) {
                                            case 'Pendiente de firma':
                                                $statusClass = 'bg-gradient-to-r from-yellow-400 to-yellow-500 text-white';
                                                $statusIcon = 'fa-clock';
                                                break;
                                            case 'Firmado':
                                                $statusClass = 'bg-gradient-to-r from-green-500 to-green-600 text-white';
                                                $statusIcon = 'fa-check-circle';
                                                break;
                                            default:
                                                $statusClass = 'bg-gradient-to-r from-gray-400 to-gray-500 text-white';
                                                $statusIcon = 'fa-info-circle';
                                                break;
                                        }
                                        ?>
                                        <span class="inline-flex items-center space-x-2 px-3 py-1 font-semibold <?php echo $statusClass; ?> rounded-lg shadow-md">
                                            <i class="fas <?php echo $statusIcon; ?>"></i>
                                            <span><?php echo htmlspecialchars($evaluation['status']); ?></span>
                                        </span>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <?php
                                            $initialFileUrl = $evaluation['initial_file_path'] ?? '';
                                            $signedFileUrl = $evaluation['signed_file_path'] ?? '';

                                            if (!empty($initialFileUrl) && strpos($initialFileUrl, '/public/uploads/evaluations/') !== false) {
                                                $initialFileUrl = rtrim(BASE_PATH, '/') . '/uploads/evaluations/' . basename($initialFileUrl);
                                            }
                                            if (!empty($signedFileUrl) && strpos($signedFileUrl, '/public/uploads/evaluations/') !== false) {
                                                $signedFileUrl = rtrim(BASE_PATH, '/') . '/uploads/evaluations/' . basename($signedFileUrl);
                                            }
                                        ?>
                                        <div class="flex flex-col space-y-1">
                                            <?php if (!empty($initialFileUrl)): ?>
                                                <a href="<?php echo htmlspecialchars($initialFileUrl); ?>" target="_blank" class="inline-flex items-center space-x-2 text-blue-600 hover:text-blue-800 font-medium hover:underline">
                                                    <i class="fas fa-file-pdf"></i>
                                                    <span>Original</span>
                                                    <i class="fas fa-external-link-alt text-xs"></i>
                                                </a>
                                            <?php endif; ?>
                                            <?php if (!empty($signedFileUrl)): ?>
                                                <a href="<?php echo htmlspecialchars($signedFileUrl); ?>" target="_blank" class="inline-flex items-center space-x-2 text-green-600 hover:text-green-800 font-medium hover:underline">
                                                    <i class="fas fa-file-signature"></i>
                                                    <span>Firmado</span>
                                                    <i class="fas fa-external-link-alt text-xs"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <?php if ($canEditEvaluation || $canDeleteEvaluation) { ?>
                                            <div class="flex items-center space-x-2">
                                                <?php if ($canEditEvaluation): ?>
                                                    <a href="<?php echo BASE_PATH; ?>/evaluations/edit/<?php echo htmlspecialchars($evaluation['id']); ?>" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold px-3 py-2 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5 flex items-center space-x-2">
                                                        <i class="fas fa-edit"></i>
                                                        <span>Editar</span>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if ($canDeleteEvaluation): ?>
                                                    <a href="<?php echo BASE_PATH; ?>/evaluations/delete/<?php echo htmlspecialchars($evaluation['id']); ?>" class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold px-3 py-2 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5 flex items-center space-x-2" onclick="return confirm('¿Está seguro de eliminar esta evaluación?')">
                                                        <i class="fas fa-trash-alt"></i>
                                                        <span>Eliminar</span>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-4">
                                        <div class="bg-gray-100 p-6 rounded-full">
                                            <i class="fas fa-clipboard text-gray-400 text-5xl"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium text-lg">No hay evaluaciones registradas</p>
                                        <p class="text-gray-400 text-sm">Comience creando una nueva evaluación</p>
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
                No hay evaluaciones que coincidan con la búsqueda
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
            initializeTableSearch('evaluations');
        });
    </script>
</body>

</html>