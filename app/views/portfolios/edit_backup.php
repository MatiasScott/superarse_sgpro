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
                        <p class="text-indigo-100 mt-1">Gestione los archivos para cada unidad académica</p>
                    </div>
                </div>
            </div>
        </header>

        <main class="space-y-6 max-w-5xl mx-auto">
            <?php for ($i = 1; $i <= 4; $i++): ?>
                <?php
                    $unitPortfolio = array_filter($portfolioData, function($item) use ($i) {
                        return $item['unit_number'] == $i;
                    });
                    $unitPortfolio = !empty($unitPortfolio) ? reset($unitPortfolio) : null;
                    
                    // Asegúrate de tener una entrada en la base de datos para la unidad
                    if (!$unitPortfolio) {
                        $this->portfolioModel->create([
                            'professor_id' => $professor['id'],
                            'pao_id' => $pao['id'],
                            'unit_number' => $i,
                            'docencia_path' => null,
                            'practicas_path' => null,
                            'titulacion_path' => null
                        ]);
                        $unitPortfolio = $this->portfolioModel->findByKeys($professor['id'], $pao['id'], $i);
                    }
                ?>
                <section class="bg-white rounded-2xl shadow-xl border-2 border-gray-200 overflow-hidden transform hover:scale-[1.01] transition-all duration-300">
                    <!-- Header de la Unidad -->
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6">
                        <div class="flex items-center space-x-4">
                            <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                                <i class="fas fa-book-open text-white text-2xl"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-white">Unidad <?php echo $i; ?></h2>
                                <p class="text-indigo-100 text-sm">Gestión de archivos académicos</p>
                            </div>
                        </div>
                    </div>
                    
                    <form action="<?php echo BASE_PATH; ?>/portfolios/update/<?php echo htmlspecialchars($unitPortfolio['id']); ?>" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                        <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                        <input type="hidden" name="unit_number" value="<?php echo $i; ?>">

                        <!-- Archivo de Docencia -->
                        <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl p-5 border-2 border-blue-200 hover:border-blue-400 transition-colors">
                            <label for="docencia_file_<?php echo $i; ?>" class="flex items-center space-x-3 text-base font-bold text-gray-800 mb-3">
                                <div class="bg-gradient-to-br from-blue-500 to-cyan-600 p-2 rounded-lg">
                                    <i class="fas fa-chalkboard-teacher text-white"></i>
                                </div>
                                <span>Archivo de Docencia</span>
                            </label>
                            
                            <?php if (!empty($unitPortfolio['docencia_path'])): ?>
                                <div class="bg-white rounded-lg p-3 mb-3 border border-blue-200 flex items-center justify-between hover:shadow-md transition-shadow">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-file-pdf text-blue-600 text-xl"></i>
                                        <span class="text-sm text-gray-700 font-medium">Archivo actual disponible</span>
                                    </div>
                                    <a href="<?php echo BASE_PATH . htmlspecialchars($unitPortfolio['docencia_path']); ?>" target="_blank" class="text-blue-600 hover:text-blue-800 font-semibold text-sm flex items-center space-x-2 hover:underline">
                                        <span>Ver archivo</span>
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="bg-gray-50 rounded-lg p-3 mb-3 border border-gray-200 flex items-center space-x-3">
                                    <i class="fas fa-info-circle text-gray-400 text-xl"></i>
                                    <span class="text-sm text-gray-500">No hay archivo subido</span>
                                </div>
                            <?php endif; ?>
                            
                            <input type="file" id="docencia_file_<?php echo $i; ?>" name="docencia_file" class="block w-full text-sm text-gray-600 file:mr-4 file:py-3 file:px-6 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gradient-to-r file:from-blue-500 file:to-cyan-600 file:text-white hover:file:from-blue-600 hover:file:to-cyan-700 file:cursor-pointer file:transition-all file:duration-200 file:shadow-md hover:file:shadow-lg cursor-pointer border-2 border-dashed border-blue-300 rounded-lg p-3 hover:border-blue-500 transition-colors bg-white">
                        </div>

                        <!-- Archivo de Prácticas -->
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-5 border-2 border-green-200 hover:border-green-400 transition-colors">
                            <label for="practicas_file_<?php echo $i; ?>" class="flex items-center space-x-3 text-base font-bold text-gray-800 mb-3">
                                <div class="bg-gradient-to-br from-green-500 to-emerald-600 p-2 rounded-lg">
                                    <i class="fas fa-flask text-white"></i>
                                </div>
                                <span>Archivo de Prácticas</span>
                            </label>
                            
                            <?php if (!empty($unitPortfolio['practicas_path'])): ?>
                                <div class="bg-white rounded-lg p-3 mb-3 border border-green-200 flex items-center justify-between hover:shadow-md transition-shadow">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-file-pdf text-green-600 text-xl"></i>
                                        <span class="text-sm text-gray-700 font-medium">Archivo actual disponible</span>
                                    </div>
                                    <a href="<?php echo BASE_PATH . htmlspecialchars($unitPortfolio['practicas_path']); ?>" target="_blank" class="text-green-600 hover:text-green-800 font-semibold text-sm flex items-center space-x-2 hover:underline">
                                        <span>Ver archivo</span>
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="bg-gray-50 rounded-lg p-3 mb-3 border border-gray-200 flex items-center space-x-3">
                                    <i class="fas fa-info-circle text-gray-400 text-xl"></i>
                                    <span class="text-sm text-gray-500">No hay archivo subido</span>
                                </div>
                            <?php endif; ?>
                            
                            <input type="file" id="practicas_file_<?php echo $i; ?>" name="practicas_file" class="block w-full text-sm text-gray-600 file:mr-4 file:py-3 file:px-6 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gradient-to-r file:from-green-500 file:to-emerald-600 file:text-white hover:file:from-green-600 hover:file:to-emerald-700 file:cursor-pointer file:transition-all file:duration-200 file:shadow-md hover:file:shadow-lg cursor-pointer border-2 border-dashed border-green-300 rounded-lg p-3 hover:border-green-500 transition-colors bg-white">
                        </div>
                        
                        <!-- Archivo de Titulación -->
                        <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-5 border-2 border-purple-200 hover:border-purple-400 transition-colors">
                            <label for="titulacion_file_<?php echo $i; ?>" class="flex items-center space-x-3 text-base font-bold text-gray-800 mb-3">
                                <div class="bg-gradient-to-br from-purple-500 to-pink-600 p-2 rounded-lg">
                                    <i class="fas fa-graduation-cap text-white"></i>
                                </div>
                                <span>Archivo de Titulación</span>
                            </label>
                            
                            <?php if (!empty($unitPortfolio['titulacion_path'])): ?>
                                <div class="bg-white rounded-lg p-3 mb-3 border border-purple-200 flex items-center justify-between hover:shadow-md transition-shadow">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-file-pdf text-purple-600 text-xl"></i>
                                        <span class="text-sm text-gray-700 font-medium">Archivo actual disponible</span>
                                    </div>
                                    <a href="<?php echo BASE_PATH . htmlspecialchars($unitPortfolio['titulacion_path']); ?>" target="_blank" class="text-purple-600 hover:text-purple-800 font-semibold text-sm flex items-center space-x-2 hover:underline">
                                        <span>Ver archivo</span>
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="bg-gray-50 rounded-lg p-3 mb-3 border border-gray-200 flex items-center space-x-3">
                                    <i class="fas fa-info-circle text-gray-400 text-xl"></i>
                                    <span class="text-sm text-gray-500">No hay archivo subido</span>
                                </div>
                            <?php endif; ?>
                            
                            <input type="file" id="titulacion_file_<?php echo $i; ?>" name="titulacion_file" class="block w-full text-sm text-gray-600 file:mr-4 file:py-3 file:px-6 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gradient-to-r file:from-purple-500 file:to-pink-600 file:text-white hover:file:from-purple-600 hover:file:to-pink-700 file:cursor-pointer file:transition-all file:duration-200 file:shadow-md hover:file:shadow-lg cursor-pointer border-2 border-dashed border-purple-300 rounded-lg p-3 hover:border-purple-500 transition-colors bg-white">
                        </div>
                        
                        <!-- Botones de Acción -->
                        <div class="pt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <button type="submit" class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center space-x-3">
                                <i class="fas fa-save text-xl"></i>
                                <span>Guardar Unidad <?php echo $i; ?></span>
                            </button>
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
    <script src="<?php echo BASE_PATH; ?>/js/responsive.js"></script>
</body>
</html>
