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
                        <h1 class="text-3xl font-bold">Crear Nuevo Portafolio</h1>
                        <p class="text-indigo-100 mt-1">Configura un portafolio académico para un profesor</p>
                    </div>
                </div>
            </div>
        </header>

        <main class="max-w-2xl mx-auto">
            <div class="bg-white rounded-2xl shadow-xl border-2 border-gray-200 overflow-hidden">
                <!-- Header de la card -->
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6">
                    <div class="flex items-center justify-center space-x-3">
                        <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                            <i class="fas fa-folder-plus text-white text-3xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-white">Nuevo Portafolio</h2>
                    </div>
                    <p class="text-center text-blue-100 mt-3 text-sm">
                        Complete la información necesaria para crear el portafolio académico
                    </p>
                </div>

                <!-- Formulario -->
                <form action="<?php echo BASE_PATH; ?>/portfolios/store" method="POST" class="p-8 space-y-6">
                    <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                    <!-- Profesor -->
                    <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl p-6 border-2 border-blue-200">
                        <label for="professor_id" class="flex items-center space-x-3 text-base font-bold text-gray-800 mb-3">
                            <div class="bg-gradient-to-br from-blue-500 to-cyan-600 p-2 rounded-lg">
                                <i class="fas fa-chalkboard-teacher text-white"></i>
                            </div>
                            <span>Profesor Asignado</span>
                        </label>
                        <select id="professor_id" name="professor_id" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 hover:border-blue-400">
                            <option value="">Seleccione un profesor</option>
                            <?php foreach ($professors as $professor): ?>
                                <option value="<?php echo htmlspecialchars($professor['id']); ?>">
                                    <?php echo htmlspecialchars($professor['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="mt-2 text-xs text-gray-600 flex items-center space-x-1">
                            <i class="fas fa-info-circle text-blue-500"></i>
                            <span>Seleccione el profesor responsable del portafolio</span>
                        </p>
                    </div>

                    <!-- PAO -->
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-6 border-2 border-purple-200">
                        <label for="pao_id" class="flex items-center space-x-3 text-base font-bold text-gray-800 mb-3">
                            <div class="bg-gradient-to-br from-purple-500 to-pink-600 p-2 rounded-lg">
                                <i class="fas fa-book text-white"></i>
                            </div>
                            <span>Programa Académico (PAO)</span>
                        </label>
                        <select id="pao_id" name="pao_id" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 hover:border-purple-400">
                            <option value="">Seleccione un PAO</option>
                            <?php foreach ($paos as $pao): ?>
                                <option value="<?php echo htmlspecialchars($pao['id']); ?>">
                                    <?php echo htmlspecialchars($pao['title']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="mt-2 text-xs text-gray-600 flex items-center space-x-1">
                            <i class="fas fa-info-circle text-purple-500"></i>
                            <span>Seleccione el Programa Académico Operativo asociado</span>
                        </p>
                    </div>

                    <!-- Información adicional -->
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-5 border-2 border-green-200">
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-lightbulb text-green-600 text-xl mt-1"></i>
                            <div>
                                <h3 class="font-bold text-gray-800 mb-2">¿Qué incluye un portafolio?</h3>
                                <ul class="text-sm text-gray-700 space-y-1">
                                    <li class="flex items-center space-x-2">
                                        <i class="fas fa-check text-green-600"></i>
                                        <span>4 Unidades académicas organizadas</span>
                                    </li>
                                    <li class="flex items-center space-x-2">
                                        <i class="fas fa-check text-green-600"></i>
                                        <span>Archivos de Docencia, Prácticas y Titulación</span>
                                    </li>
                                    <li class="flex items-center space-x-2">
                                        <i class="fas fa-check text-green-600"></i>
                                        <span>Gestión y seguimiento centralizado</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4">
                        <button type="submit" class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center space-x-3">
                            <i class="fas fa-plus-circle text-xl"></i>
                            <span>Crear Portafolio</span>
                        </button>
                        <a href="<?php echo BASE_PATH; ?>/portfolios" class="bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center space-x-3">
                            <i class="fas fa-arrow-left text-xl"></i>
                            <span>Cancelar</span>
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>
    <script src="<?php echo BASE_PATH; ?>/js/responsive.js"></script>
</body>
</html>
