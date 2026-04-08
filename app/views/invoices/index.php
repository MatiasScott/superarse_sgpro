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

<body class="bg-gradient-to-br from-emerald-50 via-teal-50 to-cyan-50 font-sans min-h-screen">
    <?php
    require_once __DIR__ . '/../../helpers/PermissionHelper.php';
    $isProfesor = PermissionHelper::hasAnyRole(['Profesor'], $roles ?? null);
    $canManageInvoices = PermissionHelper::can('invoices', 'manage_all', $roles ?? null);
    $canCreateInvoice = $canManageInvoices || $isProfesor;
    $canEditInvoiceRow = $canManageInvoices || $isProfesor;
    ?>
    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

    <div class="main-content">
        <?php if ($canCreateInvoice) { ?>
            <header class="mb-8">
                <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-2xl shadow-xl p-8 text-white">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                        <div class="flex items-center space-x-4">
                            <div class="bg-white/20 backdrop-blur-sm p-4 rounded-xl">
                                <i class="fas fa-file-invoice-dollar text-4xl"></i>
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold">Gestión de Facturas</h1>
                                <p class="text-emerald-100 mt-1">Administre las facturas y comprobantes de pago</p>
                            </div>
                        </div>
                        <a href="<?php echo BASE_PATH; ?>/invoices/create" class="bg-white hover:bg-gray-50 text-emerald-600 font-bold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center space-x-2 w-full md:w-auto">
                            <i class="fas fa-plus-circle text-xl"></i>
                            <span>Crear Factura</span>
                        </a>
                    </div>
                </div>
            </header>
        <?php } else { ?>
            <header class="mb-8">
                <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-2xl shadow-xl p-8 text-white">
                    <div class="flex items-center space-x-4">
                        <div class="bg-white/20 backdrop-blur-sm p-4 rounded-xl">
                            <i class="fas fa-file-invoice-dollar text-4xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold">Gestión de Facturas</h1>
                            <p class="text-emerald-100 mt-1">Administre las facturas y comprobantes de pago</p>
                        </div>
                    </div>
                </div>
            </header>
        <?php } ?>
        <main class="bg-white rounded-2xl shadow-xl overflow-hidden border-2 border-gray-200">
            <!-- CONTROLES DE BÚSQUEDA Y FILTROS -->
            <div class="search-filter-controls" style="border-radius: 2rem 2rem 0 0; margin: -2px -2px 0 -2px;">
                <div class="search-bar-container">
                    <label class="search-bar-label">🔍 Buscar facturas</label>
                    <input 
                        type="text" 
                        id="tableSearch" 
                        class="search-bar-input" 
                        placeholder="Buscar por profesor, PAO, período, monto, observación..."
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
                                    <i class="fas fa-hashtag text-emerald-600"></i>
                                    <span>ID</span>
                                </div>
                            </th>
                            <th class="px-5 py-4 border-b-2 border-gray-200 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-user text-teal-600"></i>
                                    <span>Profesor</span>
                                </div>
                            </th>
                            <th class="px-5 py-4 border-b-2 border-gray-200 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-book text-cyan-600"></i>
                                    <span>PAO</span>
                                </div>
                            </th>
                            <th class="px-5 py-4 border-b-2 border-gray-200 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-bookmark text-purple-600"></i>
                                    <span>Unidad</span>
                                </div>
                            </th>
                            <th class="px-5 py-4 border-b-2 border-gray-200 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-calendar-alt text-indigo-600"></i>
                                    <span>Período</span>
                                </div>
                            </th>
                            <th class="px-5 py-4 border-b-2 border-gray-200 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-calendar text-blue-600"></i>
                                    <span>Fecha</span>
                                </div>
                            </th>
                            <th class="px-5 py-4 border-b-2 border-gray-200 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-dollar-sign text-green-600"></i>
                                    <span>Monto</span>
                                </div>
                            </th>
                            <th class="px-5 py-4 border-b-2 border-gray-200 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-file-invoice text-purple-600"></i>
                                    <span>Factura</span>
                                </div>
                            </th>
                            <th class="px-5 py-4 border-b-2 border-gray-200 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-receipt text-orange-600"></i>
                                    <span>Comprobante</span>
                                </div>
                            </th>
                            <th class="px-5 py-4 border-b-2 border-gray-200 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-comment-dots text-indigo-600"></i>
                                    <span>Observación</span>
                                </div>
                            </th>
                            <th class="px-5 py-4 border-b-2 border-gray-200 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-info-circle text-yellow-600"></i>
                                    <span>Estado</span>
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
                        <?php if (isset($invoices) && is_array($invoices) && !empty($invoices)): ?>
                            <?php foreach ($invoices as $invoice): ?>
                                <tr class="hover:bg-emerald-50 transition-colors duration-150">
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <span class="font-bold text-emerald-600">#<?php echo htmlspecialchars($invoice['id']); ?></span>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-user-circle text-teal-500"></i>
                                            <span class="font-medium"><?php echo htmlspecialchars($invoice['professor_name']); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-bookmark text-cyan-500"></i>
                                            <span><?php echo htmlspecialchars($invoice['pao_name']); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="flex items-center justify-center">
                                            <span class="inline-flex items-center px-3 py-1 rounded-lg bg-purple-100 text-purple-700 font-bold">
                                                <i class="fas fa-bookmark mr-1"></i>
                                                Unidad <?php echo htmlspecialchars($invoice['unit_number']); ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="flex items-center space-x-1">
                                            <i class="fas fa-calendar-alt text-indigo-500"></i>
                                            <span class="font-medium"><?php echo htmlspecialchars($invoice['period_month']); ?> <?php echo htmlspecialchars($invoice['period_year']); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-calendar-day text-blue-500"></i>
                                            <span><?php echo htmlspecialchars($invoice['invoice_date']); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-money-bill-wave text-green-500"></i>
                                            <span class="font-bold text-green-700">$<?php echo htmlspecialchars($invoice['amount']); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <?php if (!empty($invoice['payment_proof_path'])): ?>
                                            <a href="<?php echo BASE_PATH . '/' . htmlspecialchars($invoice['payment_proof_path']); ?>" target="_blank" class="inline-flex items-center space-x-2 bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white font-semibold px-3 py-2 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                                                <i class="fas fa-file-alt"></i>
                                                <span>Ver</span>
                                                <i class="fas fa-external-link-alt text-xs"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="inline-flex items-center space-x-2 text-gray-400 bg-gray-100 px-3 py-2 rounded-lg">
                                                <i class="fas fa-times"></i>
                                                <span>N/A</span>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <?php if (!empty($invoice['comprobante_path'])): ?>
                                            <a href="<?php echo BASE_PATH . '/' . htmlspecialchars($invoice['comprobante_path']); ?>" target="_blank" class="inline-flex items-center space-x-2 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-semibold px-3 py-2 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                                                <i class="fas fa-file-pdf"></i>
                                                <span>PDF</span>
                                                <i class="fas fa-external-link-alt text-xs"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="inline-flex items-center space-x-2 text-gray-400 bg-gray-100 px-3 py-2 rounded-lg">
                                                <i class="fas fa-times"></i>
                                                <span>Sin comprobante</span>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <?php if (!empty($invoice['observacion'])): ?>
                                            <div class="max-w-xs">
                                                <span class="text-gray-700 italic text-xs bg-indigo-50 px-2 py-1 rounded" title="<?php echo htmlspecialchars($invoice['observacion']); ?>">
                                                    <i class="fas fa-quote-left text-indigo-400"></i>
                                                    <?php echo strlen($invoice['observacion']) > 40 ? htmlspecialchars(substr($invoice['observacion'], 0, 40)) . '...' : htmlspecialchars($invoice['observacion']); ?>
                                                </span>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-gray-400 text-xs">Sin observaciones</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <?php
                                        $statusClass = '';
                                        $statusIcon = '';
                                        switch ($invoice['status']) {
                                            case 'Pagada':
                                                $statusClass = 'bg-gradient-to-r from-green-500 to-emerald-600 text-white';
                                                $statusIcon = 'fa-check-circle';
                                                break;
                                            case 'Pendiente':
                                                $statusClass = 'bg-gradient-to-r from-yellow-400 to-yellow-500 text-white';
                                                $statusIcon = 'fa-clock';
                                                break;
                                            case 'Rechazada':
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
                                            <span><?php echo htmlspecialchars($invoice['status']); ?></span>
                                        </span>

                                    </td>
                                    <?php if ($canEditInvoiceRow) { ?>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <div class="flex items-center justify-end space-x-2">
                                                <a href="<?php echo BASE_PATH; ?>/invoices/edit/<?php echo htmlspecialchars($invoice['id']); ?>" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold px-3 py-2 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5 flex items-center space-x-2">
                                                    <i class="fas fa-edit"></i>
                                                    <span>Editar</span>
                                                </a>
                                                <a href="<?php echo BASE_PATH; ?>/invoices/delete/<?php echo htmlspecialchars($invoice['id']); ?>" class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold px-3 py-2 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5 flex items-center space-x-2" onclick="return confirm('¿Está seguro de eliminar esta factura?')">
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
                                <td colspan="12" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-4">
                                        <div class="bg-gray-100 p-6 rounded-full">
                                            <i class="fas fa-file-invoice-dollar text-gray-400 text-5xl"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium text-lg">No hay facturas registradas</p>
                                        <p class="text-gray-400 text-sm">Comience creando una nueva factura</p>
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
                No hay facturas que coincidan con la búsqueda
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
    
    <!-- Inicializar búsqueda para módulo de facturas -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initializeTableSearch('invoices');
        });
    </script>
</body>

</html>
