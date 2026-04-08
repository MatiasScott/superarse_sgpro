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
            color: #ffffff;
        }

        .sidebar-item-text-logout {
            color: #f87171;
        }

        @media (max-width: 1024px) {
            .main-content {
                padding: 1rem;
                margin-left: 0 !important;
                width: 100% !important;
            }

            header {
                margin-top: 3.5rem !important;
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

        <header class="mb-8">
            <div class="bg-gradient-to-r from-slate-700 to-indigo-700 rounded-2xl shadow-xl p-8 text-white">
                <div class="flex items-center space-x-4">
                    <div class="bg-white/20 backdrop-blur-sm p-4 rounded-xl">
                        <i class="fas fa-shield-alt text-4xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold">Administración de Permisos</h1>
                        <p class="text-indigo-100 mt-1">Configura permisos por módulo, acción y rol sin tocar código.</p>
                    </div>
                </div>
            </div>
        </header>

        <main class="bg-white rounded-2xl shadow-xl overflow-hidden border-2 border-gray-200">
            <div class="px-6 pt-6">
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                    La acción <strong>Gestionar propio (global)</strong> se mantiene por compatibilidad y aplica como permiso general sobre recursos propios.
                    Ahora también puedes configurar acciones explícitas por módulo: <strong>Ver, Crear, Editar y Eliminar</strong>.
                </div>
            </div>
            <form action="<?php echo BASE_PATH; ?>/permissions/update" method="POST" class="p-6 space-y-6">
                <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                <?php foreach ($permissionMatrix as $module => $actions): ?>
                    <section class="border border-gray-200 rounded-xl overflow-hidden">
                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                            <h2 class="font-bold text-gray-800"><?php echo htmlspecialchars($moduleLabels[$module] ?? $module); ?></h2>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-100 text-gray-700">
                                    <tr>
                                        <th class="text-left px-4 py-3 border-b border-gray-200 w-48">Acción</th>
                                        <?php foreach ($rolesCatalog as $roleName): ?>
                                            <th class="text-center px-4 py-3 border-b border-gray-200"><?php echo htmlspecialchars($roleName); ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($actions as $action => $allowedRoles): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 border-b border-gray-100 font-medium text-gray-700">
                                                <?php echo htmlspecialchars($actionLabels[$action] ?? $action); ?>
                                            </td>
                                            <?php foreach ($rolesCatalog as $roleName): ?>
                                                <?php $checked = in_array($roleName, $allowedRoles, true); ?>
                                                <td class="px-4 py-3 border-b border-gray-100 text-center">
                                                    <input
                                                        type="checkbox"
                                                        name="permissions[<?php echo htmlspecialchars($module); ?>][<?php echo htmlspecialchars($action); ?>][]"
                                                        value="<?php echo htmlspecialchars($roleName); ?>"
                                                        <?php echo $checked ? 'checked' : ''; ?>
                                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                    >
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                <?php endforeach; ?>

                <div class="flex items-center justify-end pt-2">
                    <button
                        type="submit"
                        class="bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200"
                    >
                        <i class="fas fa-save mr-2"></i>
                        Guardar permisos
                    </button>
                </div>
            </form>
        </main>

        <section class="mt-8 bg-white rounded-2xl shadow-xl overflow-hidden border-2 border-gray-200">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="font-bold text-gray-800">Historial de cambios de permisos</h2>
            </div>

            <div class="p-6">
                <?php
                $historyFilters = $historyFilters ?? ['date_from' => '', 'date_to' => '', 'user' => '', 'module' => ''];
                $queryString = http_build_query(array_filter($historyFilters, function ($value) {
                    return trim((string)$value) !== '';
                }));
                $excelUrl = BASE_PATH . '/permissions/export-history-excel' . ($queryString !== '' ? '?' . $queryString : '');
                $pdfUrl = BASE_PATH . '/permissions/export-history-pdf' . ($queryString !== '' ? '?' . $queryString : '');
                ?>

                <form action="<?php echo BASE_PATH; ?>/permissions" method="GET" class="mb-6 border border-gray-200 rounded-xl p-4 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Desde</label>
                            <input
                                type="date"
                                name="date_from"
                                value="<?php echo htmlspecialchars($historyFilters['date_from'] ?? ''); ?>"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Hasta</label>
                            <input
                                type="date"
                                name="date_to"
                                value="<?php echo htmlspecialchars($historyFilters['date_to'] ?? ''); ?>"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Usuario (nombre o ID)</label>
                            <input
                                type="text"
                                name="user"
                                value="<?php echo htmlspecialchars($historyFilters['user'] ?? ''); ?>"
                                placeholder="Ej: admin o 1"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Módulo</label>
                            <select
                                name="module"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            >
                                <option value="">Todos</option>
                                <?php foreach ($moduleLabels as $moduleKey => $moduleText): ?>
                                    <option value="<?php echo htmlspecialchars($moduleKey); ?>" <?php echo (($historyFilters['module'] ?? '') === $moduleKey) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($moduleText); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap items-center gap-3">
                        <button
                            type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2 rounded-lg text-sm"
                        >
                            <i class="fas fa-filter mr-1"></i>
                            Filtrar
                        </button>

                        <a
                            href="<?php echo BASE_PATH; ?>/permissions"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold px-4 py-2 rounded-lg text-sm"
                        >
                            Limpiar
                        </a>

                        <a
                            href="<?php echo htmlspecialchars($excelUrl); ?>"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2 rounded-lg text-sm"
                        >
                            <i class="fas fa-file-excel mr-1"></i>
                            Exportar Excel
                        </a>

                        <a
                            href="<?php echo htmlspecialchars($pdfUrl); ?>"
                            class="bg-rose-600 hover:bg-rose-700 text-white font-semibold px-4 py-2 rounded-lg text-sm"
                        >
                            <i class="fas fa-file-pdf mr-1"></i>
                            Exportar PDF
                        </a>
                    </div>
                </form>

                <?php if (!empty($historyEntries)): ?>
                    <div class="space-y-4">
                        <?php foreach ($historyEntries as $entry): ?>
                            <article class="border border-gray-200 rounded-xl p-4">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
                                    <p class="text-sm font-semibold text-gray-800">
                                        <?php echo htmlspecialchars($entry['user_name'] ?? 'Desconocido'); ?>
                                        <span class="text-gray-500 font-normal">(ID <?php echo htmlspecialchars((string)($entry['user_id'] ?? 'N/A')); ?>)</span>
                                    </p>
                                    <p class="text-xs text-gray-500"><?php echo htmlspecialchars($entry['timestamp'] ?? ''); ?></p>
                                </div>

                                <?php if (!empty($entry['changes']) && is_array($entry['changes'])): ?>
                                    <ul class="space-y-2">
                                        <?php foreach ($entry['changes'] as $change): ?>
                                            <li class="text-sm text-gray-700 bg-gray-50 rounded-lg p-3">
                                                <p class="font-medium text-gray-800 mb-1">
                                                    <?php echo htmlspecialchars(($moduleLabels[$change['module']] ?? $change['module']) . ' / ' . ($actionLabels[$change['action']] ?? $change['action'])); ?>
                                                </p>
                                                <p>
                                                    <span class="font-semibold text-green-700">Agregados:</span>
                                                    <?php echo !empty($change['added']) ? htmlspecialchars(implode(', ', $change['added'])) : 'Ninguno'; ?>
                                                </p>
                                                <p>
                                                    <span class="font-semibold text-red-700">Removidos:</span>
                                                    <?php echo !empty($change['removed']) ? htmlspecialchars(implode(', ', $change['removed'])) : 'Ninguno'; ?>
                                                </p>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="text-sm text-gray-500">Sin detalle de cambios.</p>
                                <?php endif; ?>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-sm text-gray-500">Aún no hay cambios registrados en permisos.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>
</body>

</html>
