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
                    ?>
                    
                    <!-- Opción Académico -->
                    <label class="relative cursor-pointer">
                        <input type="radio" name="portfolio_type" value="academico" <?php echo $currentType === 'academico' ? 'checked' : ''; ?> 
                               class="peer sr-only" onchange="updatePortfolioType()">
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
                               class="peer sr-only" onchange="updatePortfolioType()">
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
                               class="peer sr-only" onchange="updatePortfolioType()">
                        <div class="bg-purple-50 border-2 border-purple-200 rounded-xl p-6 peer-checked:border-purple-600 peer-checked:bg-purple-100 peer-checked:shadow-lg transition-all duration-200 hover:shadow-md">
                            <div class="text-center">
                                <i class="fas fa-graduation-cap text-purple-600 text-3xl mb-3"></i>
                                <h3 class="font-bold text-gray-800 text-lg">Titulación</h3>
                                <p class="text-sm text-gray-600 mt-2">Docencia + Prácticas + Titulación</p>
                            </div>
                        </div>
                    </label>
                </form>
            </div>

            <!-- Unidades del Portafolio -->
            <?php 
            $roles = isset($_SESSION['user_id']) ? $this->roleModel->getRolesByUserId($_SESSION['user_id']) : [];
            $canApprove = PermissionHelper::can('portfolios', 'manage_all', $roles);
            
            for ($i = 1; $i <= 4; $i++): 
                $unitPortfolio = array_filter($portfolioData, function($item) use ($i) {
                    return $item['unit_number'] == $i;
                });
                $unitPortfolio = !empty($unitPortfolio) ? reset($unitPortfolio) : null;
                
                $isApproved = $unitPortfolio && $unitPortfolio['unit_approved'];
            ?>
                <section id="unit-<?php echo $i; ?>" class="bg-white rounded-2xl shadow-xl border-2 <?php echo $isApproved ? 'border-green-400' : 'border-gray-200'; ?> overflow-hidden">
                    <!-- Header de la Unidad -->
                    <div class="bg-gradient-to-r <?php echo $isApproved ? 'from-green-600 to-emerald-600' : 'from-indigo-600 to-purple-600'; ?> p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                                    <i class="fas fa-book-open text-white text-2xl"></i>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-white">Unidad <?php echo $i; ?></h2>
                                    <p class="text-indigo-100 text-sm">Gestión de archivos académicos</p>
                                </div>
                            </div>
                            <?php if ($isApproved): ?>
                                <div class="flex items-center space-x-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-xl">
                                    <i class="fas fa-check-circle text-white text-xl"></i>
                                    <span class="text-white font-bold">Aprobada</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <form action="<?php echo BASE_PATH; ?>/portfolios/update/<?php echo $unitPortfolio ? $unitPortfolio['id'] : 'new'; ?>" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                        <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                        <input type="hidden" name="unit_number" value="<?php echo $i; ?>">
                        <input type="hidden" name="professor_id" value="<?php echo $professor['id']; ?>">
                        <input type="hidden" name="pao_id" value="<?php echo $pao['id']; ?>">
                        <input type="hidden" name="portfolio_type" value="<?php echo $currentType; ?>">

                        <!-- Archivo de Docencia (Siempre visible) -->
                        <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl p-5 border-2 border-blue-200">
                            <label for="docencia_file_<?php echo $i; ?>" class="flex items-center space-x-3 text-base font-bold text-gray-800 mb-3">
                                <div class="bg-gradient-to-br from-blue-500 to-cyan-600 p-2 rounded-lg">
                                    <i class="fas fa-chalkboard-teacher text-white"></i>
                                </div>
                                <span>Archivo de Docencia *</span>
                            </label>
                            
                            <?php if ($unitPortfolio && !empty($unitPortfolio['docencia_path'])): ?>
                                <div class="bg-white rounded-lg p-3 mb-3 border border-blue-200 flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-file-pdf text-blue-600 text-xl"></i>
                                        <span class="text-sm text-gray-700 font-medium">Archivo actual disponible</span>
                                    </div>
                                    <a href="<?php echo BASE_PATH . htmlspecialchars($unitPortfolio['docencia_path']); ?>" target="_blank" class="text-blue-600 hover:text-blue-800 font-semibold text-sm flex items-center space-x-2">
                                        <span>Ver archivo</span>
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <input type="file" id="docencia_file_<?php echo $i; ?>" name="docencia_file" accept=".pdf" 
                                   class="block w-full text-sm text-gray-600 file:mr-4 file:py-3 file:px-6 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gradient-to-r file:from-blue-500 file:to-cyan-600 file:text-white hover:file:from-blue-600 hover:file:to-cyan-700 file:cursor-pointer cursor-pointer border-2 border-dashed border-blue-300 rounded-lg p-3 bg-white">
                        </div>

                        <!-- Archivo de Prácticas (Visible para practico y titulacion) -->
                        <div id="practicas-section-<?php echo $i; ?>" class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-5 border-2 border-green-200 <?php echo in_array($currentType, ['practico', 'titulacion']) ? '' : 'hidden'; ?>">
                            <label for="practicas_file_<?php echo $i; ?>" class="flex items-center space-x-3 text-base font-bold text-gray-800 mb-3">
                                <div class="bg-gradient-to-br from-green-500 to-emerald-600 p-2 rounded-lg">
                                    <i class="fas fa-flask text-white"></i>
                                </div>
                                <span>Archivo de Prácticas *</span>
                            </label>
                            
                            <?php if ($unitPortfolio && !empty($unitPortfolio['practicas_path'])): ?>
                                <div class="bg-white rounded-lg p-3 mb-3 border border-green-200 flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-file-pdf text-green-600 text-xl"></i>
                                        <span class="text-sm text-gray-700 font-medium">Archivo actual disponible</span>
                                    </div>
                                    <a href="<?php echo BASE_PATH . htmlspecialchars($unitPortfolio['practicas_path']); ?>" target="_blank" class="text-green-600 hover:text-green-800 font-semibold text-sm flex items-center space-x-2">
                                        <span>Ver archivo</span>
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <input type="file" id="practicas_file_<?php echo $i; ?>" name="practicas_file" accept=".pdf"
                                   class="block w-full text-sm text-gray-600 file:mr-4 file:py-3 file:px-6 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gradient-to-r file:from-green-500 file:to-emerald-600 file:text-white hover:file:from-green-600 hover:file:to-emerald-700 file:cursor-pointer cursor-pointer border-2 border-dashed border-green-300 rounded-lg p-3 bg-white">
                        </div>
                        
                        <!-- Archivo de Titulación (Visible solo para titulacion) -->
                        <div id="titulacion-section-<?php echo $i; ?>" class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-5 border-2 border-purple-200 <?php echo $currentType === 'titulacion' ? '' : 'hidden'; ?>">
                            <label for="titulacion_file_<?php echo $i; ?>" class="flex items-center space-x-3 text-base font-bold text-gray-800 mb-3">
                                <div class="bg-gradient-to-br from-purple-500 to-pink-600 p-2 rounded-lg">
                                    <i class="fas fa-graduation-cap text-white"></i>
                                </div>
                                <span>Archivo de Titulación *</span>
                            </label>
                            
                            <?php if ($unitPortfolio && !empty($unitPortfolio['titulacion_path'])): ?>
                                <div class="bg-white rounded-lg p-3 mb-3 border border-purple-200 flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-file-pdf text-purple-600 text-xl"></i>
                                        <span class="text-sm text-gray-700 font-medium">Archivo actual disponible</span>
                                    </div>
                                    <a href="<?php echo BASE_PATH . htmlspecialchars($unitPortfolio['titulacion_path']); ?>" target="_blank" class="text-purple-600 hover:text-purple-800 font-semibold text-sm flex items-center space-x-2">
                                        <span>Ver archivo</span>
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <input type="file" id="titulacion_file_<?php echo $i; ?>" name="titulacion_file" accept=".pdf"
                                   class="block w-full text-sm text-gray-600 file:mr-4 file:py-3 file:px-6 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gradient-to-r file:from-purple-500 file:to-pink-600 file:text-white hover:file:from-purple-600 hover:file:to-pink-700 file:cursor-pointer cursor-pointer border-2 border-dashed border-purple-300 rounded-lg p-3 bg-white">
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
    
    <script>
        function updatePortfolioType() {
            const form = document.getElementById('portfolio-type-form');
            const formData = new FormData(form);
            const selectedType = formData.get('portfolio_type');
            
            // Actualizar visibilidad de secciones
            for (let i = 1; i <= 4; i++) {
                const practicasSection = document.getElementById(`practicas-section-${i}`);
                const titulacionSection = document.getElementById(`titulacion-section-${i}`);
                
                if (selectedType === 'academico') {
                    practicasSection.classList.add('hidden');
                    titulacionSection.classList.add('hidden');
                } else if (selectedType === 'practico') {
                    practicasSection.classList.remove('hidden');
                    titulacionSection.classList.add('hidden');
                } else if (selectedType === 'titulacion') {
                    practicasSection.classList.remove('hidden');
                    titulacionSection.classList.remove('hidden');
                }
            }
            
            // Enviar formulario para guardar el tipo
            fetch(form.action, {
                method: 'POST',
                body: formData
            });
        }
    </script>
    <script src="<?php echo BASE_PATH; ?>/js/responsive.js"></script>
</body>
</html>

