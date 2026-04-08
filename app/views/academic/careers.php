<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?php echo BASE_PATH; ?>/img/logo_sgpro.png">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/css/compiled.css">
<link rel="stylesheet" href="<?php echo BASE_PATH; ?>/css/responsive.css">
</head>
<body class="bg-gray-100 font-sans">
    <?php
    require_once __DIR__ . '/../../helpers/PermissionHelper.php';
    $canManageCareers = PermissionHelper::can('careers', 'manage_all', $roles ?? null);
    $canCreateCareers = PermissionHelper::can('careers', 'create', $roles ?? null);
    $canEditCareers = PermissionHelper::can('careers', 'edit', $roles ?? null);
    $canDeleteCareers = PermissionHelper::can('careers', 'delete', $roles ?? null);
    ?>
    <?php require_once __DIR__ . '/../dashboard/index.php'; ?>
    <div class="main-content">
        <header class="page-header">
            <h1>Gestión de Carreras</h1>
            <?php if ($canManageCareers || $canCreateCareers): ?>
                <form action="<?php echo BASE_PATH; ?>/academic/careers/store" method="POST" class="flex items-center gap-2">
                    <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                    <input type="text" name="name" required placeholder="Nueva carrera" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <button type="submit" class="btn-responsive btn-primary">+ Crear</button>
                </form>
            <?php endif; ?>
        </header>
        <main class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="table-responsive">
                <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            ID
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Nombre de la Carrera
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($careers) && is_array($careers) && !empty($careers)): ?>
                        <?php foreach ($careers as $career): ?>
                            <tr>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <?php echo htmlspecialchars($career['id']); ?>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <?php echo htmlspecialchars($career['name']); ?>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <div class="flex items-center gap-2">
                                        <?php if ($canManageCareers || $canEditCareers): ?>
                                            <form action="<?php echo BASE_PATH; ?>/academic/careers/update/<?php echo htmlspecialchars($career['id']); ?>" method="POST" class="flex items-center gap-2">
                                                <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                                                <input type="text" name="name" value="<?php echo htmlspecialchars($career['name']); ?>" required class="px-2 py-1 border border-gray-300 rounded text-xs">
                                                <button type="submit" class="btn-responsive btn-secondary text-xs">Editar</button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if ($canManageCareers || $canDeleteCareers): ?>
                                            <form action="<?php echo BASE_PATH; ?>/academic/careers/delete/<?php echo htmlspecialchars($career['id']); ?>" method="POST" onsubmit="return confirm('¿Eliminar esta carrera?');">
                                                <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                                                <button type="submit" class="btn-responsive bg-red-600 hover:bg-red-700 text-white text-xs px-3 py-2 rounded">Eliminar</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center py-4 text-gray-500">No hay carreras registradas.</td>
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