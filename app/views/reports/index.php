<?php
// app/views/reports/index.php
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'Reportes'); ?></title>
    <link rel="icon" type="image/png" href="<?php echo BASE_PATH; ?>/img/logo_sgpro.jpg">
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

        .report-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .report-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .report-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px 12px 0 0;
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

        .filter-input,
        .filter-select {
            transition: all 0.2s ease;
        }

        .filter-input:focus,
        .filter-select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
    </style>
</head>

<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 font-sans min-h-screen">
    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <header class="mb-8">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-xl p-8 text-white">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                    <div class="flex items-center space-x-4">
                        <div class="bg-white/20 backdrop-blur-sm p-4 rounded-xl">
                            <i class="fas fa-chart-bar text-4xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold">Sistema de Reportes</h1>
                            <p class="text-indigo-100 mt-1">Genera reportes en PDF o Excel de CVs, facturación, portafolios y docentes</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-xl">
                        <div class="bg-white/20 p-2 rounded-lg">
                            <i class="fas fa-user text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs text-indigo-100">Usuario</p>
                            <p class="font-semibold"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuario'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="space-y-6">
            <!-- Grid de Reportes -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Reporte de CVs -->
                <div class="report-card relative bg-white rounded-2xl shadow-xl overflow-hidden border-2 border-gray-200 p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 p-3 rounded-xl">
                                <i class="fas fa-file-alt text-white text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">Reporte de Currículos</h3>
                                <span class="inline-block mt-1 bg-gradient-to-r from-green-500 to-emerald-500 text-white text-xs font-semibold px-3 py-1 rounded-full">Completo</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-6 leading-relaxed">Descarga un reporte completo de currículos de profesores mostrando UN currículum por usuario. Puedes filtrar por nombre.</p>
                    
                    <!-- Filtro de Búsqueda por Nombre -->
                    <div class="mb-4">
                        <label for="cv-search" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-search text-indigo-500"></i> Buscar por nombre
                        </label>
                        <input type="text" id="cv-search" class="filter-input w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all" placeholder="Escribe el nombre del usuario...">
                    </div>
                    
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <a href="<?= BASE_PATH ?>/reports/cv-by-user?format=excel" class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold px-4 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5 flex items-center justify-center space-x-2" id="cv-excel-btn">
                            <i class="fas fa-file-excel text-lg"></i>
                            <span>Descargar Excel</span>
                        </a>
                        <a href="<?= BASE_PATH ?>/reports/cv-by-user?format=pdf" class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold px-4 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5 flex items-center justify-center space-x-2" id="cv-pdf-btn">
                            <i class="fas fa-file-pdf text-lg"></i>
                            <span>Ver PDF</span>
                        </a>
                    </div>
                </div>

                <!-- Reporte de Facturación -->
                <div class="report-card relative bg-white rounded-2xl shadow-xl overflow-hidden border-2 border-gray-200 p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="bg-gradient-to-br from-amber-500 to-orange-600 p-3 rounded-xl">
                                <i class="fas fa-dollar-sign text-white text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">Reporte de Facturación</h3>
                                <span class="inline-block mt-1 bg-gradient-to-r from-green-500 to-emerald-500 text-white text-xs font-semibold px-3 py-1 rounded-full">Completo</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-6 leading-relaxed">Obtén un desglose detallado de la facturación por usuario, incluyendo información de cada factura, períodos y montos totales.</p>
                    
                    <!-- Filtro de Búsqueda por Nombre -->
                    <div class="mb-4">
                        <label for="billing-search" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-search text-amber-500"></i> Buscar por nombre
                        </label>
                        <input type="text" id="billing-search" class="filter-input w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all" placeholder="Escribe el nombre del usuario...">
                    </div>
                    
                    <!-- Filtro de Año -->
                    <div class="mb-6">
                        <label for="billing-year" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar-alt text-amber-500"></i> Filtrar por Año
                        </label>
                        <select id="billing-year" class="filter-select w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all">
                            <option value="">Todos los años</option>
                            <?php
                            $currentYear = date('Y');
                            for ($year = $currentYear; $year >= 2020; $year--) {
                                echo "<option value='$year'>$year</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <a href="<?= BASE_PATH ?>/reports/billing-by-user?format=excel" class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold px-4 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5 flex items-center justify-center space-x-2" id="billing-excel-btn">
                            <i class="fas fa-file-excel text-lg"></i>
                            <span>Descargar Excel</span>
                        </a>
                        <a href="<?= BASE_PATH ?>/reports/billing-by-user?format=pdf" class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold px-4 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5 flex items-center justify-center space-x-2" id="billing-pdf-btn">
                            <i class="fas fa-file-pdf text-lg"></i>
                            <span>Ver PDF</span>
                        </a>
                        <script>
                        // Script para agregar filtros a los enlaces de descarga
                        document.addEventListener('DOMContentLoaded', function() {
                            function buildBillingUrl(format) {
                                const base = '<?= BASE_PATH ?>/reports/billing-by-user?format=' + format;
                                const name = document.getElementById('billing-search').value.trim();
                                const year = document.getElementById('billing-year').value;
                                const params = [];
                                if (name) params.push('name=' + encodeURIComponent(name));
                                if (year) params.push('year=' + encodeURIComponent(year));
                                return base + (params.length ? '&' + params.join('&') : '');
                            }
                            document.getElementById('billing-excel-btn').addEventListener('click', function(e) {
                                e.preventDefault();
                                window.location.href = buildBillingUrl('excel');
                            });
                            document.getElementById('billing-pdf-btn').addEventListener('click', function(e) {
                                e.preventDefault();
                                window.open(buildBillingUrl('pdf'), '_blank');
                            });
                        });
                        </script>
                    </div>
                </div>

                <!-- Reporte de Portafolios -->
                <div class="report-card relative bg-white rounded-2xl shadow-xl overflow-hidden border-2 border-gray-200 p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="bg-gradient-to-br from-cyan-500 to-blue-600 p-3 rounded-xl">
                                <i class="fas fa-folder-open text-white text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">Reporte de Portafolios</h3>
                                <span class="inline-block mt-1 bg-gradient-to-r from-green-500 to-emerald-500 text-white text-xs font-semibold px-3 py-1 rounded-full">Completo</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-8 leading-relaxed">Consulta el estado de todos los portafolios, incluyendo tipo, unidad, aprobación y las fechas relacionadas.</p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-auto">
                        <a href="<?= BASE_PATH ?>/reports/portfolios?format=excel" class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold px-4 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5 flex items-center justify-center space-x-2">
                            <i class="fas fa-file-excel text-lg"></i>
                            <span>Descargar Excel</span>
                        </a>
                        <a href="<?= BASE_PATH ?>/reports/portfolios?format=pdf" class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold px-4 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5 flex items-center justify-center space-x-2">
                            <i class="fas fa-file-pdf text-lg"></i>
                            <span>Ver PDF</span>
                        </a>
                    </div>
                </div>

                <!-- Reporte de Docentes -->
                <div class="report-card relative bg-white rounded-2xl shadow-xl overflow-hidden border-2 border-gray-200 p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="bg-gradient-to-br from-violet-500 to-purple-600 p-3 rounded-xl">
                                <i class="fas fa-chalkboard-teacher text-white text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">Docentes por Dedicación</h3>
                                <span class="inline-block mt-1 bg-gradient-to-r from-green-500 to-emerald-500 text-white text-xs font-semibold px-3 py-1 rounded-full">Completo</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-8 leading-relaxed">Visualiza un listado completo de docentes agrupados por dedicación (Tiempo Completo, Tiempo Parcial, etc.) con nombres y correos.</p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-auto">
                        <a href="<?= BASE_PATH ?>/reports/teachers-by-dedication?format=excel" class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold px-4 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5 flex items-center justify-center space-x-2">
                            <i class="fas fa-file-excel text-lg"></i>
                            <span>Descargar Excel</span>
                        </a>
                        <a href="<?= BASE_PATH ?>/reports/teachers-by-dedication?format=pdf" class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold px-4 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5 flex items-center justify-center space-x-2">
                            <i class="fas fa-file-pdf text-lg"></i>
                            <span>Ver PDF</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-indigo-500 rounded-2xl shadow-lg p-6">
                <div class="flex items-start space-x-3">
                    <div class="bg-indigo-100 p-3 rounded-lg">
                        <i class="fas fa-info-circle text-indigo-600 text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <h5 class="text-lg font-bold text-indigo-900 mb-3">Información Importante</h5>
                        <ul class="space-y-2">
                            <li class="flex items-start space-x-2">
                                <i class="fas fa-check-circle text-indigo-600 mt-1"></i>
                                <span class="text-indigo-800"><strong>Acceso restringido:</strong> Solo administradores y personal autorizado pueden generar reportes</span>
                            </li>
                            <li class="flex items-start space-x-2">
                                <i class="fas fa-check-circle text-indigo-600 mt-1"></i>
                                <span class="text-indigo-800"><strong>Formatos disponibles:</strong> Excel (.xlsx) y PDF (HTML imprimible)</span>
                            </li>
                            <li class="flex items-start space-x-2">
                                <i class="fas fa-check-circle text-indigo-600 mt-1"></i>
                                <span class="text-indigo-800"><strong>Datos actualizados:</strong> Los reportes se generan en tiempo real con la información actual de la base de datos</span>
                            </li>
                            <li class="flex items-start space-x-2">
                                <i class="fas fa-check-circle text-indigo-600 mt-1"></i>
                                <span class="text-indigo-800"><strong>Privacidad:</strong> Los reportes incluyen información sensible. Maneje con confidencialidad</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </main>
    </div>

        </main>
    </div>

    <script>
        // JavaScript para manejar filtros en el reporte de facturación
        document.addEventListener('DOMContentLoaded', function() {
            const yearSelect = document.getElementById('billing-year');
            const searchInput = document.getElementById('billing-search');
            const excelBtn = document.getElementById('billing-excel-btn');
            const pdfBtn = document.getElementById('billing-pdf-btn');

            function updateBillingUrls() {
                const selectedYear = yearSelect.value;
                const searchName = searchInput.value.trim();
                const basePath = '<?= BASE_PATH ?>';
                
                // Construir URLParams con año y búsqueda si están seleccionados
                let excelParams = 'format=excel';
                let pdfParams = 'format=pdf';
                
                if (selectedYear) {
                    excelParams += '&year=' + selectedYear;
                    pdfParams += '&year=' + selectedYear;
                }
                
                if (searchName) {
                    excelParams += '&name=' + encodeURIComponent(searchName);
                    pdfParams += '&name=' + encodeURIComponent(searchName);
                }
                
                // Actualizar los href de los botones
                excelBtn.href = basePath + '/reports/billing-by-user?' + excelParams;
                pdfBtn.href = basePath + '/reports/billing-by-user?' + pdfParams;
            }
            
            if (yearSelect && searchInput && excelBtn && pdfBtn) {
                yearSelect.addEventListener('change', updateBillingUrls);
                searchInput.addEventListener('keyup', updateBillingUrls);
                // Inicializar URLs
                updateBillingUrls();
            }
            
            // JavaScript para manejar filtros en el reporte de CVs
            const cvSearchInput = document.getElementById('cv-search');
            const cvExcelBtn = document.getElementById('cv-excel-btn');
            const cvPdfBtn = document.getElementById('cv-pdf-btn');
            
            function updateCvUrls() {
                const searchName = cvSearchInput.value.trim();
                const basePath = '<?= BASE_PATH ?>';
                
                // Construir URLParams
                let excelParams = 'format=excel';
                let pdfParams = 'format=pdf';
                
                if (searchName) {
                    excelParams += '&search=' + encodeURIComponent(searchName);
                    pdfParams += '&search=' + encodeURIComponent(searchName);
                }
                
                // Actualizar los href de los botones
                cvExcelBtn.href = basePath + '/reports/cv-by-user?' + excelParams;
                cvPdfBtn.href = basePath + '/reports/cv-by-user?' + pdfParams;
            }
            
            if (cvSearchInput && cvExcelBtn && cvPdfBtn) {
                cvSearchInput.addEventListener('keyup', updateCvUrls);
                // Inicializar URLs
                updateCvUrls();
            }
        });
    </script>
</body>
</html>
