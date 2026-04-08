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
<body class="bg-gray-100 font-sans">
    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

    <div class="main-content">
        <!-- Cabecera principal -->
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-3xl shadow-xl p-8 mb-6 max-w-3xl mx-auto">
            <div class="flex items-center justify-center gap-4">
                <div class="bg-white/20 backdrop-blur-sm p-4 rounded-2xl">
                    <i class="fas fa-user-check text-4xl text-white"></i>
                </div>
                <div class="text-white">
                    <h2 class="text-3xl font-bold mb-1">Proceso de Continuidad</h2>
                    <p class="text-green-100 text-sm">Inicie el proceso de decisión de continuidad para el próximo período</p>
                </div>
            </div>
        </div>

        <main class="max-w-3xl mx-auto bg-white rounded-3xl shadow-xl overflow-hidden">
            <form action="<?php echo BASE_PATH; ?>/continuity/store" method="POST" class="p-8">
                <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                
                <!-- Sección Profesor -->
                <div class="bg-blue-50 rounded-2xl p-6 mb-6 border border-blue-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="bg-blue-500 text-white p-3 rounded-xl">
                            <i class="fas fa-chalkboard-teacher text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Profesor</h3>
                    </div>
                    <select id="professor_id" name="professor_id" required 
                            class="w-full px-4 py-3 border-0 rounded-xl bg-white shadow-sm focus:ring-2 focus:ring-blue-400 outline-none text-gray-700">
                        <option value="">Seleccione un profesor</option>
                        <?php foreach ($professors as $professor): ?>
                            <option value="<?php echo htmlspecialchars($professor['id']); ?>">
                                <?php echo htmlspecialchars($professor['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-xs text-blue-600 mt-2 flex items-center gap-1">
                        <i class="fas fa-info-circle"></i>
                        Seleccione el profesor para evaluar su continuidad
                    </p>
                </div>

                <!-- Sección Próximo PAO -->
                <div class="bg-purple-50 rounded-2xl p-6 mb-8 border border-purple-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="bg-purple-500 text-white p-3 rounded-xl">
                            <i class="fas fa-calendar-alt text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Próximo PAO</h3>
                    </div>
                    <select id="pao_id" name="pao_id" required 
                            class="w-full px-4 py-3 border-0 rounded-xl bg-white shadow-sm focus:ring-2 focus:ring-purple-400 outline-none text-gray-700">
                        <option value="">Seleccione un PAO</option>
                        <?php foreach ($paos as $pao): ?>
                            <option value="<?php echo htmlspecialchars($pao['id']); ?>">
                                <?php echo htmlspecialchars($pao['title']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-xs text-purple-600 mt-2 flex items-center gap-1">
                        <i class="fas fa-info-circle"></i>
                        Seleccione el Programa Académico Operativo del próximo período
                    </p>
                </div>

                <!-- Información adicional -->
                <div class="bg-green-50 rounded-2xl p-6 mb-8 border border-green-200">
                    <div class="flex items-start gap-3">
                        <div class="bg-green-100 p-2 rounded-lg">
                            <i class="fas fa-lightbulb text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 mb-2">¿Qué incluye el proceso?</h4>
                            <ul class="space-y-2 text-sm text-gray-600">
                                <li class="flex items-center gap-2">
                                    <i class="fas fa-check text-green-500"></i>
                                    Evaluación del desempeño del profesor
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="fas fa-check text-green-500"></i>
                                    Análisis de resultados académicos
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="fas fa-check text-green-500"></i>
                                    Decisión de continuidad para el próximo período
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="flex gap-4">
                    <a href="<?php echo BASE_PATH; ?>/continuity" 
                       class="flex-1 py-4 px-6 bg-gray-600 text-white rounded-xl text-center font-bold hover:bg-gray-700 transition-all duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-arrow-left"></i>
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="flex-1 py-4 px-6 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl font-bold hover:from-green-600 hover:to-emerald-700 transition-all duration-200 flex items-center justify-center gap-2 shadow-lg">
                        <i class="fas fa-play-circle"></i>
                        Iniciar Proceso
                    </button>
                </div>
            </form>
        </main>
    </div>
    <script src="<?php echo BASE_PATH; ?>/js/responsive.js"></script>
</body>
</html>