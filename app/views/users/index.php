<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="icon" type="image/png" href="<?php echo BASE_PATH; ?>/img/logo_sgpro.jpg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/css/compiled.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/css/responsive.css">
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
    </style>
</head>

<body class="bg-gradient-to-br from-violet-50 via-purple-50 to-indigo-50 font-sans min-h-screen">

    <?php
    require_once __DIR__ . '/../../helpers/PermissionHelper.php';
    $canManageUsers = PermissionHelper::can('users', 'manage_all', $roles ?? null);
    ?>

    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

    <div class="main-content">
        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="max-w-3xl mx-auto mt-6">
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl">
                    <p class="text-sm text-red-700"><?php echo htmlspecialchars($_SESSION['flash_error']); ?></p>
                </div>
            </div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>
        <?php if ($canManageUsers){ ?>
        <header class="mb-8">
            <div class="bg-gradient-to-r from-violet-600 to-purple-600 rounded-2xl shadow-xl p-8 text-white">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                    <div class="flex items-center space-x-4">
                        <div class="bg-white/20 backdrop-blur-sm p-4 rounded-xl">
                            <i class="fas fa-users text-4xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold">Gestión de Usuarios</h1>
                            <p class="text-violet-100 mt-1">Administre los usuarios del sistema</p>
                        </div>
                    </div>
                    <a href="<?php echo BASE_PATH; ?>/users/create" class="bg-white hover:bg-gray-50 text-violet-600 font-bold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center space-x-2 w-full md:w-auto">
                        <i class="fas fa-plus-circle text-xl"></i>
                        <span>Crear Usuario</span>
                    </a>
                </div>
            </div>
        </header>
        <?php } else { ?>
        <header class="mb-8">
            <div class="bg-gradient-to-r from-violet-600 to-purple-600 rounded-2xl shadow-xl p-8 text-white">
                <div class="flex items-center space-x-4">
                    <div class="bg-white/20 backdrop-blur-sm p-4 rounded-xl">
                        <i class="fas fa-users text-4xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold">Gestión de Usuarios</h1>
                        <p class="text-violet-100 mt-1">Administre los usuarios del sistema</p>
                    </div>
                </div>
            </div>
        </header>
        <?php } ?>

        <main class="bg-white rounded-2xl shadow-xl overflow-hidden border-2 border-gray-200">
            <!-- CONTROLES DE BÚSQUEDA Y FILTROS -->
            <div class="search-filter-controls" style="border-radius: 2rem 2rem 0 0; margin: -2px -2px 0 -2px;">
                <div class="search-bar-container">
                    <label class="search-bar-label">🔍 Buscar usuarios</label>
                    <input 
                        type="text" 
                        id="tableSearch" 
                        class="search-bar-input" 
                        placeholder="Buscar por nombre, ID, correo electrónico o escuela..."
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
                                <i class="fas fa-hashtag text-violet-600"></i>
                                <span>ID</span>
                            </div>
                        </th>
                        <th class="px-5 py-4 border-b-2 border-gray-200 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-user text-purple-600"></i>
                                <span>Nombre</span>
                            </div>
                        </th>
                        <th class="px-5 py-4 border-b-2 border-gray-200 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-envelope text-indigo-600"></i>
                                <span>Correo Electrónico</span>
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
                                <i class="fas fa-cog text-gray-600"></i>
                                <span>Acciones</span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($users) && is_array($users) && !empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr class="hover:bg-violet-50 transition-colors duration-150">
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <span class="font-bold text-violet-600">#<?php echo htmlspecialchars($user['id']); ?></span>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <div class="flex items-center space-x-3">
                                        <div class="bg-gradient-to-br from-purple-500 to-violet-600 p-2 rounded-lg">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                        <span class="font-semibold text-gray-800"><?php echo htmlspecialchars($user['name']); ?></span>
                                    </div>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-envelope text-indigo-500"></i>
                                        <span class="text-gray-700"><?php echo htmlspecialchars($user['email']); ?></span>
                                    </div>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-school text-blue-500"></i>
                                        <span class="text-gray-700"><?php echo htmlspecialchars($user['escuela'] ?? 'Sin escuela'); ?></span>
                                    </div>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <?php if ($canManageUsers){ ?>
                                    <div class="flex items-center space-x-2">
                                        <a href="<?php echo BASE_PATH; ?>/users/edit/<?php echo $user['id']; ?>" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold px-3 py-2 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5 flex items-center space-x-2">
                                            <i class="fas fa-edit"></i>
                                            <span>Editar</span>
                                        </a>
                                        <a href="<?php echo BASE_PATH; ?>/users/delete/<?php echo $user['id']; ?>" class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold px-3 py-2 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5 flex items-center space-x-2" onclick="return confirm('¿Está seguro de eliminar este usuario?')">
                                            <i class="fas fa-trash-alt"></i>
                                            <span>Eliminar</span>
                                        </a>
                                    </div>
                                <?php } ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center space-y-4">
                                    <div class="bg-gray-100 p-6 rounded-full">
                                        <i class="fas fa-users text-gray-400 text-5xl"></i>
                                    </div>
                                    <p class="text-gray-500 font-medium text-lg">No hay usuarios registrados</p>
                                    <p class="text-gray-400 text-sm">Comience creando un nuevo usuario</p>
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
                No hay registros que coincidan con la búsqueda
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
    
    <!-- Inicializar búsqueda para módulo de usuarios -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initializeTableSearch('users');
        });
    </script>
</body>

</html>