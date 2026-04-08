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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 font-sans min-h-screen">
    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>
    <div class="main-content">
        <header class="mb-8">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-xl p-8 text-white">
                <div class="flex items-center space-x-4">
                    <div class="bg-white/20 backdrop-blur-sm p-4 rounded-xl">
                        <i class="fas fa-briefcase text-4xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold"><?php echo htmlspecialchars($pageTitle); ?></h1>
                        <p class="text-indigo-100 mt-1">Profesor: <?php echo htmlspecialchars($professor['name']); ?> - PAO: <?php echo htmlspecialchars($pao['title']); ?></p>
                    </div>
                </div>
            </div>
        </header>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-xl mr-3"></i>
                    <p class="font-bold"><?php echo htmlspecialchars($_SESSION['error_message']); ?></p>
                </div>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <main class="space-y-6 max-w-6xl mx-auto">
            <!-- Selector de Tipo de Portafolio -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border-2 border-indigo-200">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="bg-indigo-100 p-3 rounded-xl">
                        <i class="fas fa-tasks text-indigo-600 text-2xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Tipo de Portafolio</h2>
                </div>
                
                <form id="portfolio-type-form" action="<?php echo BASE_PATH; ?>/portfolios/update-type" method="POST" class="grid md:grid-cols-3 gap-4">
                    <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                    <input type="hidden" name="professor_id" value="<?php echo $professor['id']; ?>">
                    <input type="hidden" name="pao_id" value="<?php echo $pao['id']; ?>">
                    
                    <?php 
                    $currentType = !empty($portfolioData) ? $portfolioData[0]['portfolio_type'] : 'academico';
                    
                    // Verificar si hay archivos subidos en alguna unidad para bloquear el cambio de tipo
                    $hasUploadedFiles = false;
                    foreach ($portfolioData as $unit) {
                        $docenciaColumn = 'docencia_' . $currentType . '_path';
                        $practicasColumn = 'practicas_' . $currentType . '_path';
                        $titulacionColumn = 'titulacion_' . $currentType . '_path';
                        
                        if (!empty($unit[$docenciaColumn]) || 
                            !empty($unit[$practicasColumn]) || 
                            !empty($unit[$titulacionColumn])) {
                            $hasUploadedFiles = true;
                            break;
                        }
                    }
                    ?>
                    
                    <?php if ($hasUploadedFiles): ?>
                        <div class="col-span-3 bg-blue-50 border-2 border-blue-300 rounded-xl p-4 text-center">
                            <i class="fas fa-lock text-blue-600 text-3xl mb-2"></i>
                            <p class="text-blue-800 font-semibold text-lg mb-1">Tipo de Portafolio: 
                                <span class="uppercase"><?php echo $currentType; ?></span>
                            </p>
                            <p class="text-blue-600 text-sm">
                                El tipo está bloqueado porque ya hay archivos subidos. No se puede cambiar el tipo una vez que se han cargado documentos.
                            </p>
                        </div>
                    <?php else: ?>
                    
                    <!-- Opción Académico -->
                    <label class="relative cursor-pointer">
                        <input type="radio" name="portfolio_type" value="academico" <?php echo $currentType === 'academico' ? 'checked' : ''; ?> 
                               class="peer sr-only" onchange="confirmTypeChange('academico')">
                        <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-6 peer-checked:border-blue-600 peer-checked:bg-blue-100 peer-checked:shadow-lg transition-all duration-200 hover:shadow-md">
                            <div class="text-center">
                                <i class="fas fa-book text-blue-600 text-3xl mb-3"></i>
                                <h3 class="font-bold text-gray-800 text-lg">Académico</h3>
                                <p class="text-sm text-gray-600 mt-2">Solo archivos de docencia</p>
                            </div>
                        </div>
                    </label>
                    
                    <!-- Opción Práctico -->
                    <label class="relative cursor-pointer">
                        <input type="radio" name="portfolio_type" value="practico" <?php echo $currentType === 'practico' ? 'checked' : ''; ?> 
                               class="peer sr-only" onchange="confirmTypeChange('practico')">
                        <div class="bg-green-50 border-2 border-green-200 rounded-xl p-6 peer-checked:border-green-600 peer-checked:bg-green-100 peer-checked:shadow-lg transition-all duration-200 hover:shadow-md">
                            <div class="text-center">
                                <i class="fas fa-flask text-green-600 text-3xl mb-3"></i>
                                <h3 class="font-bold text-gray-800 text-lg">Práctico</h3>
                                <p class="text-sm text-gray-600 mt-2">Docencia + Prácticas</p>
                            </div>
                        </div>
                    </label>
                    
                    <!-- Opción Titulación -->
                    <label class="relative cursor-pointer">
                        <input type="radio" name="portfolio_type" value="titulacion" <?php echo $currentType === 'titulacion' ? 'checked' : ''; ?> 
                               class="peer sr-only" onchange="confirmTypeChange('titulacion')">
                        <div class="bg-purple-50 border-2 border-purple-200 rounded-xl p-6 peer-checked:border-purple-600 peer-checked:bg-purple-100 peer-checked:shadow-lg transition-all duration-200 hover:shadow-md">
                            <div class="text-center">
                                <i class="fas fa-graduation-cap text-purple-600 text-3xl mb-3"></i>
                                <h3 class="font-bold text-gray-800 text-lg">Titulación</h3>
                                <p class="text-sm text-gray-600 mt-2">Docencia + Prácticas + Titulación</p>
                            </div>
                        </div>
                    </label>
                    
                    <?php endif; ?>
                </form>
            </div>

            <!-- Resumen de Progreso -->
            <?php 
            $roles = isset($_SESSION['user_id']) ? $this->roleModel->getRolesByUserId($_SESSION['user_id']) : [];
            $canApprove = PermissionHelper::can('portfolios', 'manage_all', $roles);
            
            // Calcular estadísticas de las unidades
            $totalUnits = 4;
            $approvedUnits = 0;
            $pendingUnits = 0;
            $emptyUnits = 0;
            $totalFiles = 0;
            
            foreach ($portfolioData as $unit) {
                if ($unit['unit_approved']) {
                    $approvedUnits++;
                }
                
                $docenciaColumn = 'docencia_' . $currentType . '_path';
                $practicasColumn = 'practicas_' . $currentType . '_path';
                $titulacionColumn = 'titulacion_' . $currentType . '_path';
                
                $unitHasFiles = false;
                if (!empty($unit[$docenciaColumn])) {
                    $unitHasFiles = true;
                    $files = json_decode($unit[$docenciaColumn], true);
                    $totalFiles += is_array($files) ? count($files) : 1;
                }
                if (in_array($currentType, ['practico', 'titulacion']) && !empty($unit[$practicasColumn])) {
                    $unitHasFiles = true;
                    $files = json_decode($unit[$practicasColumn], true);
                    $totalFiles += is_array($files) ? count($files) : 1;
                }
                if ($currentType === 'titulacion' && !empty($unit[$titulacionColumn])) {
                    $unitHasFiles = true;
                    $files = json_decode($unit[$titulacionColumn], true);
                    $totalFiles += is_array($files) ? count($files) : 1;
                }
                
                if ($unitHasFiles && !$unit['unit_approved']) {
                    $pendingUnits++;
                } elseif (!$unitHasFiles && !$unit['unit_approved']) {
                    $emptyUnits++;
                }
            }
            
            $progressPercentage = ($approvedUnits / $totalUnits) * 100;
            ?>
            
            <div class="bg-white rounded-2xl shadow-xl p-6 border-2 border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center space-x-2">
                        <i class="fas fa-chart-pie text-indigo-600"></i>
                        <span>Progreso del Portafolio</span>
                    </h3>
                    <span class="text-2xl font-bold text-indigo-600"><?php echo round($progressPercentage); ?>%</span>
                </div>
                
                <!-- Barra de progreso -->
                <div class="w-full bg-gray-200 rounded-full h-4 mb-4 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-emerald-600 h-4 rounded-full transition-all duration-500" style="width: <?php echo $progressPercentage; ?>%"></div>
                </div>
                
                <!-- Estadísticas -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-green-50 rounded-lg p-4 border-2 border-green-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-600 text-xs font-semibold uppercase">Aprobadas</p>
                                <p class="text-2xl font-bold text-green-700"><?php echo $approvedUnits; ?></p>
                            </div>
                            <i class="fas fa-check-circle text-green-600 text-3xl"></i>
                        </div>
                    </div>
                    
                    <div class="bg-yellow-50 rounded-lg p-4 border-2 border-yellow-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-yellow-600 text-xs font-semibold uppercase">Pendientes</p>
                                <p class="text-2xl font-bold text-yellow-700"><?php echo $pendingUnits; ?></p>
                            </div>
                            <i class="fas fa-hourglass-half text-yellow-600 text-3xl"></i>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4 border-2 border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-xs font-semibold uppercase">Vacías</p>
                                <p class="text-2xl font-bold text-gray-700"><?php echo $emptyUnits; ?></p>
                            </div>
                            <i class="fas fa-folder-open text-gray-600 text-3xl"></i>
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 rounded-lg p-4 border-2 border-blue-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-600 text-xs font-semibold uppercase">Archivos</p>
                                <p class="text-2xl font-bold text-blue-700"><?php echo $totalFiles; ?></p>
                            </div>
                            <i class="fas fa-file-pdf text-blue-600 text-3xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Unidades del Portafolio -->
            <?php 
            for ($i = 1; $i <= 4; $i++): 
                $unitPortfolio = array_filter($portfolioData, function($item) use ($i) {
                    return $item['unit_number'] == $i;
                });
                $unitPortfolio = !empty($unitPortfolio) ? reset($unitPortfolio) : null;
                
                $isApproved = $unitPortfolio && $unitPortfolio['unit_approved'];
                
                // Verificar si tiene archivos según el tipo
                $hasFiles = false;
                $fileCount = 0;
                if ($unitPortfolio) {
                    $docenciaColumn = 'docencia_' . $currentType . '_path';
                    $practicasColumn = 'practicas_' . $currentType . '_path';
                    $titulacionColumn = 'titulacion_' . $currentType . '_path';
                    
                    if (!empty($unitPortfolio[$docenciaColumn])) {
                        $hasFiles = true;
                        $files = json_decode($unitPortfolio[$docenciaColumn], true);
                        $fileCount += is_array($files) ? count($files) : 1;
                    }
                    if (in_array($currentType, ['practico', 'titulacion']) && !empty($unitPortfolio[$practicasColumn])) {
                        $hasFiles = true;
                        $files = json_decode($unitPortfolio[$practicasColumn], true);
                        $fileCount += is_array($files) ? count($files) : 1;
                    }
                    if ($currentType === 'titulacion' && !empty($unitPortfolio[$titulacionColumn])) {
                        $hasFiles = true;
                        $files = json_decode($unitPortfolio[$titulacionColumn], true);
                        $fileCount += is_array($files) ? count($files) : 1;
                    }
                }
                
                // Determinar el estado de la unidad
                $unitStatus = 'empty'; // vacía
                if ($hasFiles && !$isApproved) {
                    $unitStatus = 'pending'; // con archivos, pendiente de aprobación
                } elseif ($isApproved) {
                    $unitStatus = 'approved'; // aprobada
                }
            ?>
                <section id="unit-<?php echo $i; ?>" class="bg-white rounded-2xl shadow-xl border-2 <?php echo $isApproved ? 'border-green-400' : ($hasFiles ? 'border-yellow-400' : 'border-gray-200'); ?> overflow-hidden">
                    <!-- Header de la Unidad -->
                    <div class="bg-gradient-to-r <?php echo $isApproved ? 'from-green-600 to-emerald-600' : ($hasFiles ? 'from-yellow-500 to-orange-500' : 'from-indigo-600 to-purple-600'); ?> p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                                    <i class="fas fa-book-open text-white text-2xl"></i>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-white">Unidad <?php echo $i; ?></h2>
                                    <p class="text-white text-sm flex items-center space-x-2">
                                        <?php if ($isApproved): ?>
                                            <i class="fas fa-check-circle"></i>
                                            <span>Aprobada - <?php echo $fileCount; ?> archivo(s)</span>
                                        <?php elseif ($hasFiles): ?>
                                            <i class="fas fa-clock"></i>
                                            <span>Pendiente de aprobación - <?php echo $fileCount; ?> archivo(s)</span>
                                        <?php else: ?>
                                            <i class="fas fa-upload"></i>
                                            <span>Sin archivos - Cargar documentos</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <?php if ($isApproved): ?>
                                    <div class="flex items-center space-x-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-xl">
                                        <i class="fas fa-check-circle text-white text-xl"></i>
                                        <span class="text-white font-bold">Aprobada</span>
                                    </div>
                                <?php elseif ($hasFiles): ?>
                                    <div class="flex items-center space-x-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-xl">
                                        <i class="fas fa-hourglass-half text-white text-xl"></i>
                                        <span class="text-white font-bold">Pendiente</span>
                                    </div>
                                <?php else: ?>
                                    <div class="flex items-center space-x-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-xl">
                                        <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                                        <span class="text-white font-bold">Vacía</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <form action="<?php echo BASE_PATH; ?>/portfolios/<?php echo $unitPortfolio ? 'update/' . $unitPortfolio['id'] : 'store'; ?>" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                        <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                        <input type="hidden" name="unit_number" value="<?php echo $i; ?>">
                        <input type="hidden" name="professor_id" value="<?php echo $professor['id']; ?>">
                        <input type="hidden" name="pao_id" value="<?php echo $pao['id']; ?>">
                        <input type="hidden" name="portfolio_type" value="<?php echo $currentType; ?>">
                        <?php if ($unitPortfolio): ?>
                            <input type="hidden" name="portfolio_id" value="<?php echo $unitPortfolio['id']; ?>">
                        <?php endif; ?>

                        <!-- Archivo de Docencia (Siempre visible) -->
                        <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl p-5 border-2 border-blue-200">
                            <label for="docencia_file_<?php echo $i; ?>" class="flex items-center space-x-3 text-base font-bold text-gray-800 mb-3">
                                <div class="bg-gradient-to-br from-blue-500 to-cyan-600 p-2 rounded-lg">
                                    <i class="fas fa-chalkboard-teacher text-white"></i>
                                </div>
                                <span>Archivo de Docencia *</span>
                            </label>
                            
                            <?php 
                                // Obtener la columna correcta según el tipo de portafolio
                                $docenciaColumn = 'docencia_' . $currentType . '_path';
                                $docenciaPath = $unitPortfolio[$docenciaColumn] ?? null;
                                // Si no hay archivos en la columna nueva, buscar en la columna antigua
                                if (empty($docenciaPath) && isset($unitPortfolio['docencia_path']) && !empty($unitPortfolio['docencia_path'])) {
                                    $docenciaPath = $unitPortfolio['docencia_path'];
                                }
                                if ($unitPortfolio && !empty($docenciaPath)):
                                    $docenciaFiles = json_decode($docenciaPath, true);
                                    if (!is_array($docenciaFiles)) {
                                        $docenciaFiles = [$docenciaPath];
                                    }
                            ?>
                                <div class="bg-white rounded-lg p-3 mb-3 border border-blue-200">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <i class="fas fa-file-archive text-blue-600"></i>
                                        <span class="text-sm font-semibold text-gray-700">Archivos subidos (<?php echo count($docenciaFiles); ?>)</span>
                                    </div>
                                    <div class="space-y-2">
                                        <?php foreach ($docenciaFiles as $index => $file): ?>
                                            <div class="flex items-center justify-between bg-blue-50 rounded p-2">
                                                <div class="flex items-center space-x-2">
                                                    <i class="fas fa-file-pdf text-blue-600"></i>
                                                    <?php
                                                        $fileName = basename($file);
                                                        $parts = explode('_', $fileName, 5);
                                                        // Si el archivo tiene el formato nuevo, extraer nombre y fecha
                                                        if (count($parts) === 5 && is_numeric($parts[2])) {
                                                            $fechaHora = date('d/m/Y H:i', (int)$parts[2]);
                                                            $nombreOriginal = $parts[4];
                                                        } else {
                                                            $fechaHora = '';
                                                            $nombreOriginal = $fileName;
                                                        }
                                                    ?>
                                                    <span class="text-xs text-gray-700">
                                                        <?php echo htmlspecialchars($nombreOriginal); ?>
                                                        <?php if ($fechaHora): ?>
                                                            <span class="text-gray-400">(<?php echo $fechaHora; ?>)</span>
                                                        <?php endif; ?>
                                                    </span>
                                                </div>
                                                <a href="<?php echo BASE_PATH . htmlspecialchars($file); ?>" target="_blank" class="text-blue-600 hover:text-blue-800 text-xs font-semibold flex items-center space-x-1">
                                                    <span>Ver</span>
                                                    <i class="fas fa-external-link-alt text-xs"></i>
                                                </a>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($isApproved): ?>
                                <div class="bg-green-50 border-2 border-green-300 rounded-lg p-4 text-center">
                                    <i class="fas fa-lock text-green-600 text-2xl mb-2"></i>
                                    <p class="text-green-800 font-semibold">Unidad aprobada - No se pueden hacer cambios</p>
                                </div>
                            <?php else: ?>
                            <div class="relative">
                                <input type="file" id="docencia_file_<?php echo $i; ?>" name="docencia_files[]" accept=".pdf" multiple
                                       class="block w-full text-sm text-gray-600 file:mr-4 file:py-3 file:px-6 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gradient-to-r file:from-blue-500 file:to-cyan-600 file:text-white hover:file:from-blue-600 hover:file:to-cyan-700 file:cursor-pointer cursor-pointer border-2 border-dashed border-blue-300 rounded-lg p-3 bg-white"
                                       onchange="handleFileSelection(this, 'docencia', <?php echo $i; ?>)">
                                <p class="text-xs text-gray-500 mt-1 flex items-center">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Puedes seleccionar múltiples archivos PDF. Revisa la lista antes de guardar.
                                </p>
                            </div>
                            <!-- Contenedor para previsualizar archivos seleccionados -->
                            <div id="docencia-preview-<?php echo $i; ?>" class="mt-3 space-y-2 hidden">
                                <div class="bg-blue-50 rounded-lg p-3 border border-blue-200">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="text-sm font-bold text-blue-800">
                                            <i class="fas fa-eye mr-1"></i>
                                            Archivos seleccionados para subir
                                        </h4>
                                        <button type="button" onclick="clearFileSelection('docencia', <?php echo $i; ?>)" 
                                                class="text-xs bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                                            <i class="fas fa-times mr-1"></i>Limpiar todo
                                        </button>
                                    </div>
                                    <div id="docencia-list-<?php echo $i; ?>" class="space-y-2">
                                        <!-- Los archivos se mostrarán aquí -->
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div id="docencia-files-<?php echo $i; ?>" class="mt-2 space-y-1"></div>
                        </div>

                        <!-- Archivo de Prácticas (Visible para practico y titulacion) -->
                        <div id="practicas-section-<?php echo $i; ?>" class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-5 border-2 border-green-200 <?php echo in_array($currentType, ['practico', 'titulacion']) ? '' : 'hidden'; ?>">
                            <label for="practicas_file_<?php echo $i; ?>" class="flex items-center space-x-3 text-base font-bold text-gray-800 mb-3">
                                <div class="bg-gradient-to-br from-green-500 to-emerald-600 p-2 rounded-lg">
                                    <i class="fas fa-flask text-white"></i>
                                </div>
                                <span>Archivo de Prácticas *</span>
                            </label>
                            
                            <?php 
                                // Obtener la columna correcta según el tipo de portafolio
                                $practicasColumn = 'practicas_' . $currentType . '_path';
                                $practicasPath = $unitPortfolio[$practicasColumn] ?? null;
                                if (empty($practicasPath) && isset($unitPortfolio['practicas_path']) && !empty($unitPortfolio['practicas_path'])) {
                                    $practicasPath = $unitPortfolio['practicas_path'];
                                }
                                if ($unitPortfolio && !empty($practicasPath)):
                                    $practicasFiles = json_decode($practicasPath, true);
                                    if (!is_array($practicasFiles)) {
                                        $practicasFiles = [$practicasPath];
                                    }
                            ?>
                                <div class="bg-white rounded-lg p-3 mb-3 border border-green-200">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <i class="fas fa-file-archive text-green-600"></i>
                                        <span class="text-sm font-semibold text-gray-700">Archivos subidos (<?php echo count($practicasFiles); ?>)</span>
                                    </div>
                                    <div class="space-y-2">
                                        <?php foreach ($practicasFiles as $index => $file): ?>
                                            <div class="flex items-center justify-between bg-green-50 rounded p-2">
                                                <div class="flex items-center space-x-2">
                                                    <i class="fas fa-file-pdf text-green-600"></i>
                                                    <?php
                                                        $fileName = basename($file);
                                                        $parts = explode('_', $fileName, 5);
                                                        if (count($parts) === 5 && is_numeric($parts[2])) {
                                                            $fechaHora = date('d/m/Y H:i', (int)$parts[2]);
                                                            $nombreOriginal = $parts[4];
                                                        } else {
                                                            $fechaHora = '';
                                                            $nombreOriginal = $fileName;
                                                        }
                                                    ?>
                                                    <span class="text-xs text-gray-700">
                                                        <?php echo htmlspecialchars($nombreOriginal); ?>
                                                        <?php if ($fechaHora): ?>
                                                            <span class="text-gray-400">(<?php echo $fechaHora; ?>)</span>
                                                        <?php endif; ?>
                                                    </span>
                                                </div>
                                                <a href="<?php echo BASE_PATH . htmlspecialchars($file); ?>" target="_blank" class="text-green-600 hover:text-green-800 text-xs font-semibold flex items-center space-x-1">
                                                    <span>Ver</span>
                                                    <i class="fas fa-external-link-alt text-xs"></i>
                                                </a>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($isApproved): ?>
                                <div class="bg-green-50 border-2 border-green-300 rounded-lg p-4 text-center">
                                    <i class="fas fa-lock text-green-600 text-2xl mb-2"></i>
                                    <p class="text-green-800 font-semibold">Unidad aprobada - No se pueden hacer cambios</p>
                                </div>
                            <?php else: ?>
                            <div class="relative">
                                <input type="file" id="practicas_file_<?php echo $i; ?>" name="practicas_files[]" accept=".pdf" multiple
                                       class="block w-full text-sm text-gray-600 file:mr-4 file:py-3 file:px-6 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gradient-to-r file:from-green-500 file:to-emerald-600 file:text-white hover:file:from-green-600 hover:file:to-emerald-700 file:cursor-pointer cursor-pointer border-2 border-dashed border-green-300 rounded-lg p-3 bg-white"
                                       onchange="handleFileSelection(this, 'practicas', <?php echo $i; ?>)">
                                <p class="text-xs text-gray-500 mt-1 flex items-center">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Puedes seleccionar múltiples archivos PDF. Revisa la lista antes de guardar.
                                </p>
                            </div>
                            <!-- Contenedor para previsualizar archivos seleccionados -->
                            <div id="practicas-preview-<?php echo $i; ?>" class="mt-3 space-y-2 hidden">
                                <div class="bg-green-50 rounded-lg p-3 border border-green-200">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="text-sm font-bold text-green-800">
                                            <i class="fas fa-eye mr-1"></i>
                                            Archivos seleccionados para subir
                                        </h4>
                                        <button type="button" onclick="clearFileSelection('practicas', <?php echo $i; ?>)" 
                                                class="text-xs bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                                            <i class="fas fa-times mr-1"></i>Limpiar todo
                                        </button>
                                    </div>
                                    <div id="practicas-list-<?php echo $i; ?>" class="space-y-2">
                                        <!-- Los archivos se mostrarán aquí -->
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div id="practicas-files-<?php echo $i; ?>" class="mt-2 space-y-1"></div>
                        </div>
                        
                        <!-- Archivo de Titulación (Visible solo para titulacion) -->
                        <div id="titulacion-section-<?php echo $i; ?>" class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-5 border-2 border-purple-200 <?php echo $currentType === 'titulacion' ? '' : 'hidden'; ?>">
                            <label for="titulacion_file_<?php echo $i; ?>" class="flex items-center space-x-3 text-base font-bold text-gray-800 mb-3">
                                <div class="bg-gradient-to-br from-purple-500 to-pink-600 p-2 rounded-lg">
                                    <i class="fas fa-graduation-cap text-white"></i>
                                </div>
                                <span>Archivo de Titulación *</span>
                            </label>
                            
                            <?php 
                                // Obtener la columna correcta según el tipo de portafolio
                                $titulacionColumn = 'titulacion_' . $currentType . '_path';
                                $titulacionPath = $unitPortfolio[$titulacionColumn] ?? null;
                                if (empty($titulacionPath) && isset($unitPortfolio['titulacion_path']) && !empty($unitPortfolio['titulacion_path'])) {
                                    $titulacionPath = $unitPortfolio['titulacion_path'];
                                }
                                if ($unitPortfolio && !empty($titulacionPath)):
                                    $titulacionFiles = json_decode($titulacionPath, true);
                                    if (!is_array($titulacionFiles)) {
                                        $titulacionFiles = [$titulacionPath];
                                    }
                            ?>
                                <div class="bg-white rounded-lg p-3 mb-3 border border-purple-200">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <i class="fas fa-file-archive text-purple-600"></i>
                                        <span class="text-sm font-semibold text-gray-700">Archivos subidos (<?php echo count($titulacionFiles); ?>)</span>
                                    </div>
                                    <div class="space-y-2">
                                        <?php foreach ($titulacionFiles as $index => $file): ?>
                                            <div class="flex items-center justify-between bg-purple-50 rounded p-2">
                                                <div class="flex items-center space-x-2">
                                                    <i class="fas fa-file-pdf text-purple-600"></i>
                                                    <?php
                                                        $fileName = basename($file);
                                                        $parts = explode('_', $fileName, 5);
                                                        if (count($parts) === 5 && is_numeric($parts[2])) {
                                                            $fechaHora = date('d/m/Y H:i', (int)$parts[2]);
                                                            $nombreOriginal = $parts[4];
                                                        } else {
                                                            $fechaHora = '';
                                                            $nombreOriginal = $fileName;
                                                        }
                                                    ?>
                                                    <span class="text-xs text-gray-700">
                                                        <?php echo htmlspecialchars($nombreOriginal); ?>
                                                        <?php if ($fechaHora): ?>
                                                            <span class="text-gray-400">(<?php echo $fechaHora; ?>)</span>
                                                        <?php endif; ?>
                                                    </span>
                                                </div>
                                                <a href="<?php echo BASE_PATH . htmlspecialchars($file); ?>" target="_blank" class="text-purple-600 hover:text-purple-800 text-xs font-semibold flex items-center space-x-1">
                                                    <span>Ver</span>
                                                    <i class="fas fa-external-link-alt text-xs"></i>
                                                </a>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($isApproved): ?>
                                <div class="bg-green-50 border-2 border-green-300 rounded-lg p-4 text-center">
                                    <i class="fas fa-lock text-green-600 text-2xl mb-2"></i>
                                    <p class="text-green-800 font-semibold">Unidad aprobada - No se pueden hacer cambios</p>
                                </div>
                            <?php else: ?>
                            <div class="relative">
                                <input type="file" id="titulacion_file_<?php echo $i; ?>" name="titulacion_files[]" accept=".pdf" multiple
                                       class="block w-full text-sm text-gray-600 file:mr-4 file:py-3 file:px-6 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gradient-to-r file:from-purple-500 file:to-pink-600 file:text-white hover:file:from-purple-600 hover:file:to-pink-700 file:cursor-pointer cursor-pointer border-2 border-dashed border-purple-300 rounded-lg p-3 bg-white"
                                       onchange="handleFileSelection(this, 'titulacion', <?php echo $i; ?>)">
                                <p class="text-xs text-gray-500 mt-1 flex items-center">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Puedes seleccionar múltiples archivos PDF. Revisa la lista antes de guardar.
                                </p>
                            </div>
                            <!-- Contenedor para previsualizar archivos seleccionados -->
                            <div id="titulacion-preview-<?php echo $i; ?>" class="mt-3 space-y-2 hidden">
                                <div class="bg-purple-50 rounded-lg p-3 border border-purple-200">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="text-sm font-bold text-purple-800">
                                            <i class="fas fa-eye mr-1"></i>
                                            Archivos seleccionados para subir
                                        </h4>
                                        <button type="button" onclick="clearFileSelection('titulacion', <?php echo $i; ?>)" 
                                                class="text-xs bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                                            <i class="fas fa-times mr-1"></i>Limpiar todo
                                        </button>
                                    </div>
                                    <div id="titulacion-list-<?php echo $i; ?>" class="space-y-2">
                                        <!-- Los archivos se mostrarán aquí -->
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div id="titulacion-files-<?php echo $i; ?>" class="mt-2 space-y-1"></div>
                        </div>

                        <!-- Checklist de Aprobación (Solo visible para administradores) -->
                        <?php if ($canApprove): ?>
                        <div class="bg-gradient-to-br from-yellow-50 to-amber-50 rounded-xl p-5 border-2 border-yellow-300">
                            <div class="flex items-center justify-between">
                                <label class="flex items-center space-x-3 cursor-pointer">
                                    <input type="checkbox" name="unit_approved" value="1" 
                                           <?php echo $isApproved ? 'checked' : ''; ?>
                                           class="w-6 h-6 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-clipboard-check text-yellow-600 text-xl"></i>
                                        <span class="font-bold text-gray-800">Aprobar Unidad <?php echo $i; ?></span>
                                    </div>
                                </label>
                                <?php if ($isApproved && $unitPortfolio['approved_at']): ?>
                                    <span class="text-sm text-gray-600">
                                        <i class="fas fa-clock mr-1"></i>
                                        <?php echo date('d/m/Y H:i', strtotime($unitPortfolio['approved_at'])); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Botones de Acción -->
                        <div class="pt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php if ($isApproved): ?>
                                <button type="button" disabled class="bg-gray-400 text-white font-bold py-4 px-6 rounded-xl shadow-lg cursor-not-allowed flex items-center justify-center space-x-3 opacity-60">
                                    <i class="fas fa-lock text-xl"></i>
                                    <span>Unidad Aprobada - No Editable</span>
                                </button>
                            <?php else: ?>
                                <button type="submit" class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center space-x-3">
                                    <i class="fas fa-save text-xl"></i>
                                    <span>Guardar Unidad <?php echo $i; ?></span>
                                </button>
                            <?php endif; ?>
                            <a href="<?php echo BASE_PATH; ?>/portfolios" class="bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center space-x-3">
                                <i class="fas fa-arrow-left text-xl"></i>
                                <span>Volver</span>
                            </a>
                        </div>
                    </form>
                </section>
            <?php endfor; ?>
        </main>
    </div>
    
    <script>
        let currentType = '<?php echo $currentType; ?>';
        
        function confirmTypeChange(newType) {
            if (newType === currentType) {
                return; // No hacer nada si es el mismo tipo
            }
            
            const typeNames = {
                'academico': 'Académico',
                'practico': 'Práctico',
                'titulacion': 'Titulación'
            };
            
            const confirmMessage = `¿Está seguro de cambiar el tipo de portafolio a "${typeNames[newType]}"?\n\n` +
                                  `⚠️ ADVERTENCIA: Una vez que suba archivos en este tipo, no podrá cambiarlo.`;
            
            if (confirm(confirmMessage)) {
                updatePortfolioType();
            } else {
                // Revertir la selección al tipo anterior
                const radios = document.querySelectorAll('input[name="portfolio_type"]');
                radios.forEach(radio => {
                    if (radio.value === currentType) {
                        radio.checked = true;
                    }
                });
            }
        }
        
        function updatePortfolioType() {
            const form = document.getElementById('portfolio-type-form');
            const formData = new FormData(form);
            const selectedType = formData.get('portfolio_type');
            
            // Enviar formulario para guardar el tipo
            fetch(form.action, {
                method: 'POST',
                body: formData
            }).then(response => response.json())
              .then(data => {
                  if (data.success) {
                      // Recargar la página para mostrar los archivos correctos del nuevo tipo
                      window.location.reload();
                  }
              });
        }

        // Almacenar archivos seleccionados por tipo y unidad
        const selectedFilesStore = {};

        function handleFileSelection(input, type, unit) {
            const files = Array.from(input.files);
            const key = `${type}_${unit}`;
            
            // Guardar archivos con metadatos
            selectedFilesStore[key] = files.map((file, index) => ({
                file: file,
                name: file.name,
                size: file.size,
                id: Date.now() + index
            }));
            
            // Mostrar preview
            displayFilePreview(type, unit);
        }

        function displayFilePreview(type, unit) {
            const key = `${type}_${unit}`;
            const files = selectedFilesStore[key] || [];
            const previewContainer = document.getElementById(`${type}-preview-${unit}`);
            const listContainer = document.getElementById(`${type}-list-${unit}`);
            
            if (files.length === 0) {
                previewContainer.classList.add('hidden');
                return;
            }
            
            previewContainer.classList.remove('hidden');
            listContainer.innerHTML = '';
            
            files.forEach((fileData, index) => {
                const fileDiv = document.createElement('div');
                fileDiv.className = 'bg-white rounded-lg p-3 border border-gray-200 shadow-sm';
                fileDiv.innerHTML = `
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <div class="flex-shrink-0">
                                <i class="fas fa-file-pdf text-red-500 text-2xl"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-semibold text-gray-900 truncate">${fileData.name}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-hdd mr-1"></i>${formatFileSize(fileData.size)}
                                </div>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <button type="button" onclick="removeFile('${key}', ${index})"
                                    class="text-red-600 hover:text-red-800 hover:bg-red-50 p-2 rounded-lg transition-colors"
                                    title="Eliminar archivo">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                `;
                listContainer.appendChild(fileDiv);
            });
            
            // Agregar resumen
            const summaryDiv = document.createElement('div');
            summaryDiv.className = 'mt-3 pt-3 border-t border-gray-200 flex items-center justify-between text-xs';
            summaryDiv.innerHTML = `
                <span class="text-gray-600 font-semibold">
                    <i class="fas fa-check-circle text-green-500 mr-1"></i>
                    ${files.length} archivo(s) listo(s) para subir
                </span>
                <span class="text-gray-500">
                    Total: ${formatFileSize(files.reduce((sum, f) => sum + f.size, 0))}
                </span>
            `;
            listContainer.appendChild(summaryDiv);
        }

        function removeFile(key, index) {
            if (selectedFilesStore[key]) {
                selectedFilesStore[key].splice(index, 1);
                const [type, unit] = key.split('_');
                
                // Actualizar el input con los archivos restantes
                updateFileInput(type, unit);
                
                displayFilePreview(type, unit);
            }
        }

        function updateFileInput(type, unit) {
            const key = `${type}_${unit}`;
            const files = selectedFilesStore[key] || [];
            const inputId = `${type}_file_${unit}`;
            const input = document.getElementById(inputId);
            
            if (files.length === 0) {
                input.value = '';
            } else {
                // Crear un nuevo DataTransfer con los archivos restantes
                const dataTransfer = new DataTransfer();
                files.forEach(fileData => {
                    dataTransfer.items.add(fileData.file);
                });
                input.files = dataTransfer.files;
            }
        }

        function clearFileSelection(type, unit) {
            const key = `${type}_${unit}`;
            selectedFilesStore[key] = [];
            document.getElementById(`${type}_file_${unit}`).value = '';
            displayFilePreview(type, unit);
        }

        function formatFileSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / 1048576).toFixed(1) + ' MB';
        }

        // Confirmación antes de guardar
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[action*="update"]');
            if (form) {
                form.addEventListener('submit', function(e) {
                    // Verificar si hay archivos pendientes
                    let totalFiles = 0;
                    Object.values(selectedFilesStore).forEach(files => {
                        totalFiles += files.length;
                    });
                    
                    if (totalFiles > 0) {
                        const confirmMsg = `¿Está seguro de guardar el portafolio con ${totalFiles} archivo(s) nuevo(s)?`;
                        if (!confirm(confirmMsg)) {
                            e.preventDefault();
                            return false;
                        }
                    }
                });
            }
        });
    </script>
    <script src="<?php echo BASE_PATH; ?>/js/responsive.js"></script>
</body>
</html>


