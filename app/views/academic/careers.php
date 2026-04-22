<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?php echo BASE_PATH; ?>/img/logo_sgpro.png">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/css/compiled.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar-item-text {
            color: #ffffff;
        }

        .sidebar-item-text-logout {
            color: #f87171;
        }

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

<body class="bg-gradient-to-br from-emerald-50 via-teal-50 to-cyan-50 font-sans min-h-screen">
    <?php
    require_once __DIR__ . '/../../helpers/PermissionHelper.php';
    $canManageCareers = PermissionHelper::can('careers', 'manage_all', $roles ?? null);
    $canCreateCareers = PermissionHelper::can('careers', 'create', $roles ?? null);
    $canEditCareers = PermissionHelper::can('careers', 'edit', $roles ?? null);
    $canDeleteCareers = PermissionHelper::can('careers', 'delete', $roles ?? null);
    ?>
    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

    <div class="main-content">
        <header class="mb-8">
            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-2xl shadow-xl p-8 text-white">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <div class="flex items-center space-x-4">
                        <div class="bg-white/20 backdrop-blur-sm p-4 rounded-xl">
                            <i class="fas fa-graduation-cap text-4xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold">Gestión de Carreras</h1>
                            <p class="text-emerald-100 mt-1">Administre las carreras académicas del sistema</p>
                        </div>
                    </div>

                    <?php if ($canManageCareers || $canCreateCareers): ?>
                        <a href="<?php echo BASE_PATH; ?>/academic/careers/create" class="bg-white hover:bg-gray-100 text-emerald-700 font-bold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center space-x-2 w-full lg:w-auto">
                            <i class="fas fa-plus-circle text-xl"></i>
                            <span>Crear Carrera</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <main class="bg-white rounded-2xl shadow-xl overflow-hidden border-2 border-gray-200">
            <div class="table-responsive">
                <table class="min-w-full leading-normal">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-5 py-4 border-b-2 border-gray-200 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-hashtag text-emerald-600"></i>
                                    <span>ID</span>
                                </div>
                            </th>
                            <th class="px-5 py-4 border-b-2 border-gray-200 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-graduation-cap text-teal-600"></i>
                                    <span>Carrera</span>
                                </div>
                            </th>
                            <th class="px-5 py-4 border-b-2 border-gray-200 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-code text-cyan-600"></i>
                                    <span>Código</span>
                                </div>
                            </th>
                            <th class="px-5 py-4 border-b-2 border-gray-200 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-align-left text-slate-600"></i>
                                    <span>Descripción</span>
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
                        <?php if (isset($careers) && is_array($careers) && !empty($careers)): ?>
                            <?php foreach ($careers as $career): ?>
                                <tr class="hover:bg-emerald-50 transition-colors duration-150">
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <span class="font-bold text-emerald-600">#<?php echo htmlspecialchars($career['id']); ?></span>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="flex items-center space-x-3">
                                            <div class="bg-gradient-to-br from-emerald-500 to-teal-600 p-2 rounded-lg">
                                                <i class="fas fa-user-graduate text-white"></i>
                                            </div>
                                            <span class="font-semibold text-gray-800"><?php echo htmlspecialchars($career['name']); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-gray-700">
                                        <?php echo htmlspecialchars($career['code'] ?? ''); ?>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-gray-700">
                                        <?php echo htmlspecialchars($career['description'] ?? ''); ?>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="flex items-center space-x-2">
                                            <?php if ($canManageCareers || $canEditCareers): ?>
                                                <a href="<?php echo BASE_PATH; ?>/academic/careers/edit/<?php echo htmlspecialchars($career['id']); ?>" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold px-3 py-2 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5 flex items-center space-x-2">
                                                    <i class="fas fa-edit"></i>
                                                    <span>Editar</span>
                                                </a>
                                            <?php endif; ?>
                                            <?php if ($canManageCareers || $canDeleteCareers): ?>
                                                <form action="<?php echo BASE_PATH; ?>/academic/careers/delete/<?php echo htmlspecialchars($career['id']); ?>" method="POST" onsubmit="return confirm('¿Eliminar esta carrera?');">
                                                    <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                                                    <button type="submit" class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold px-3 py-2 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5 flex items-center space-x-2">
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
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-4">
                                        <div class="bg-gray-100 p-6 rounded-full">
                                            <i class="fas fa-graduation-cap text-gray-400 text-5xl"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium text-lg">No hay carreras registradas</p>
                                        <p class="text-gray-400 text-sm">Comience creando una nueva carrera</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script src="<?php echo BASE_PATH; ?>/js/responsive.js"></script>
</body>

</html>