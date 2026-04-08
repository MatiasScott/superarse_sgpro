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
        <header class="page-header">
            <h1>Crear Nueva Evaluación</h1>
        </header>

        <!-- Cabecera principal -->
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-3xl shadow-xl p-8 mb-6 max-w-3xl mx-auto">
            <div class="flex items-center justify-center gap-4">
                <div class="bg-white/20 backdrop-blur-sm p-4 rounded-2xl">
                    <i class="fas fa-clipboard-check text-4xl text-white"></i>
                </div>
                <div class="text-white">
                    <h2 class="text-3xl font-bold mb-1">Nueva Evaluación</h2>
                    <p class="text-blue-100 text-sm">Complete la información necesaria para crear la evaluación académica</p>
                </div>
            </div>
        </div>

        <main class="max-w-3xl mx-auto bg-white rounded-3xl shadow-xl overflow-hidden">
            <form action="<?php echo BASE_PATH; ?>/evaluations/store" method="POST" enctype="multipart/form-data" class="p-8">
                <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                
                <!-- Sección Profesor Asignado -->
                <div class="bg-blue-50 rounded-2xl p-6 mb-6 border border-blue-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="bg-blue-500 text-white p-3 rounded-xl">
                            <i class="fas fa-chalkboard-teacher text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Profesor Asignado</h3>
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
                        Seleccione el profesor responsable de la evaluación
                    </p>
                </div>

                <!-- Sección Programa Académico (PAO) -->
                <div class="bg-purple-50 rounded-2xl p-6 mb-6 border border-purple-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="bg-purple-500 text-white p-3 rounded-xl">
                            <i class="fas fa-book text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Programa Académico (PAO)</h3>
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
                        Seleccione el Programa Académico Operativo asociado
                    </p>
                </div>

                <!-- Sección Persona que evalúa -->
                <div class="bg-cyan-50 rounded-2xl p-6 mb-6 border border-cyan-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="bg-cyan-500 text-white p-3 rounded-xl">
                            <i class="fas fa-user-check text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Persona que evalúa</h3>
                    </div>
                    <select id="evaluator_id" name="evaluator_id" required 
                            class="w-full px-4 py-3 border-0 rounded-xl bg-white shadow-sm focus:ring-2 focus:ring-cyan-400 outline-none text-gray-700">
                        <option value="">Seleccione una persona que evalúa</option>
                        <?php foreach ($evaluators as $evaluator): ?>
                            <option value="<?php echo htmlspecialchars($evaluator['id']); ?>">
                                <?php echo htmlspecialchars($evaluator['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Sección de Puntajes -->
                <div class="bg-gradient-to-br from-yellow-50 to-amber-50 rounded-2xl p-6 mb-6 border border-yellow-200">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="bg-yellow-500 text-white p-3 rounded-xl">
                            <i class="fas fa-star text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Puntajes de Evaluación</h3>
                    </div>

                    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                        <!-- Autoevaluación 20% -->
                        <div class="bg-white rounded-xl p-4 border border-yellow-100">
                            <label for="autoevaluacion" class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-user text-blue-500 mr-1"></i> Autoevaluación (20%)
                            </label>
                            <input type="number" step="0.01" id="autoevaluacion" name="autoevaluacion" 
                                   class="w-full px-3 py-2 border-0 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-400 outline-none text-gray-700 text-center font-semibold" 
                                placeholder="0.00" min="0" max="5" required oninput="calcularPuntajeTotal()">
                            <p class="text-xs text-gray-500 text-center mt-1">Máx: 5 puntos</p>
                        </div>

                        <!-- Coevaluación 20% -->
                        <div class="bg-white rounded-xl p-4 border border-yellow-100">
                            <label for="coevaluacion_20" class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-users text-green-500 mr-1"></i> Coevaluación Directivos (20%)
                            </label>
                            <input type="number" step="0.01" id="coevaluacion_20" name="coevaluacion_20" 
                                   class="w-full px-3 py-2 border-0 rounded-lg bg-gray-50 focus:ring-2 focus:ring-green-400 outline-none text-gray-700 text-center font-semibold" 
                                placeholder="0.00" min="0" max="5" required oninput="calcularPuntajeTotal()">
                            <p class="text-xs text-gray-500 text-center mt-1">Máx: 5 puntos</p>
                        </div>

                        <!-- Coevaluación 30% -->
                        <div class="bg-white rounded-xl p-4 border border-yellow-100">
                            <label for="coevaluacion_30" class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-users text-emerald-500 mr-1"></i> Coevaluación Pares (30%)
                            </label>
                            <input type="number" step="0.01" id="coevaluacion_30" name="coevaluacion_30" 
                                   class="w-full px-3 py-2 border-0 rounded-lg bg-gray-50 focus:ring-2 focus:ring-emerald-400 outline-none text-gray-700 text-center font-semibold" 
                                placeholder="0.00" min="0" max="5" required oninput="calcularPuntajeTotal()">
                            <p class="text-xs text-gray-500 text-center mt-1">Máx: 5 puntos</p>
                        </div>

                        <!-- Heteroevaluación 30% -->
                        <div class="bg-white rounded-xl p-4 border border-yellow-100">
                            <label for="heteroevaluacion" class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-user-graduate text-purple-500 mr-1"></i> Heteroevaluación (30%)
                            </label>
                            <input type="number" step="0.01" id="heteroevaluacion" name="heteroevaluacion" 
                                   class="w-full px-3 py-2 border-0 rounded-lg bg-gray-50 focus:ring-2 focus:ring-purple-400 outline-none text-gray-700 text-center font-semibold" 
                                placeholder="0.00" min="0" max="5" required oninput="calcularPuntajeTotal()">
                            <p class="text-xs text-gray-500 text-center mt-1">Máx: 5 puntos</p>
                        </div>

                        <input type="hidden" id="coevaluacion" name="coevaluacion" value="0">
                    </div>

                    <!-- Puntaje Total -->
                    <div class="bg-gradient-to-r from-yellow-400 to-amber-400 rounded-xl p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-calculator text-white text-xl"></i>
                                <span class="text-white font-bold text-lg">Puntaje Total:</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="number" step="0.01" id="score" name="score" readonly
                                       class="w-24 px-3 py-2 border-0 rounded-lg bg-white text-gray-800 text-center font-bold text-xl" 
                                       placeholder="0.00" required>
                                <span class="text-white font-bold text-xl">/ 100</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Comentarios -->
                <div class="bg-teal-50 rounded-2xl p-6 mb-6 border border-teal-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="bg-teal-500 text-white p-3 rounded-xl">
                            <i class="fas fa-comment-dots text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Comentarios</h3>
                    </div>
                    <textarea id="comments" name="comments" rows="4" 
                              class="w-full px-4 py-3 border-0 rounded-xl bg-white shadow-sm focus:ring-2 focus:ring-teal-400 outline-none resize-none text-gray-700" 
                              placeholder="Observaciones y comentarios adicionales sobre la evaluación..."></textarea>
                </div>

                <!-- Archivo de Evaluación -->
                <div class="bg-red-50 rounded-2xl p-6 mb-8 border border-red-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="bg-red-500 text-white p-3 rounded-xl">
                            <i class="fas fa-file-pdf text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Archivo de Evaluación</h3>
                    </div>
                    <div class="relative border-2 border-dashed border-red-200 rounded-xl p-8 hover:border-red-400 transition-colors duration-200 bg-white">
                        <input type="file" id="initial_file" name="initial_file" accept=".pdf" 
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <div class="text-center">
                            <i class="fas fa-cloud-upload-alt text-5xl text-red-400 mb-3"></i>
                            <p class="text-sm text-gray-600 mb-1">
                                <span class="font-semibold text-red-600">Elegir archivo</span> o arrastrar aquí
                            </p>
                            <p class="text-xs text-gray-500">Solo archivos PDF (Máx. 10MB)</p>
                        </div>
                    </div>
                    <p id="file-name" class="text-sm text-red-600 font-medium mt-3 hidden"></p>
                </div>

                <!-- Botones de acción -->
                <div class="flex gap-4">
                    <a href="<?php echo BASE_PATH; ?>/evaluations" 
                       class="flex-1 py-4 px-6 bg-gray-600 text-white rounded-xl text-center font-bold hover:bg-gray-700 transition-all duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-arrow-left"></i>
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="flex-1 py-4 px-6 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition-all duration-200 flex items-center justify-center gap-2 shadow-lg">
                        <i class="fas fa-plus-circle"></i>
                        Crear Evaluación
                    </button>
                </div>
            </form>
        </main>

        <script>
            // Mostrar nombre del archivo seleccionado
            document.getElementById('initial_file').addEventListener('change', function(e) {
                const fileName = document.getElementById('file-name');
                if (this.files && this.files[0]) {
                    fileName.textContent = '📎 ' + this.files[0].name;
                    fileName.classList.remove('hidden');
                } else {
                    fileName.classList.add('hidden');
                }
            });

            // Calcular puntaje total automáticamente
            function calcularPuntajeTotal() {
                const autoevaluacion = parseFloat(document.getElementById('autoevaluacion').value) || 0;
                const coevaluacion20 = parseFloat(document.getElementById('coevaluacion_20').value) || 0;
                const coevaluacion30 = parseFloat(document.getElementById('coevaluacion_30').value) || 0;
                const heteroevaluacion = parseFloat(document.getElementById('heteroevaluacion').value) || 0;
                
                // Validar límites
                if (autoevaluacion > 5) {
                    document.getElementById('autoevaluacion').value = 5;
                    return calcularPuntajeTotal();
                }
                if (coevaluacion20 > 5) {
                    document.getElementById('coevaluacion_20').value = 5;
                    return calcularPuntajeTotal();
                }
                if (coevaluacion30 > 5) {
                    document.getElementById('coevaluacion_30').value = 5;
                    return calcularPuntajeTotal();
                }
                if (heteroevaluacion > 5) {
                    document.getElementById('heteroevaluacion').value = 5;
                    return calcularPuntajeTotal();
                }

                const sumatoria = ((autoevaluacion * 0.20) + (coevaluacion20 * 0.20) + (coevaluacion30 * 0.30) + (heteroevaluacion * 0.30));
                const total = (sumatoria - 1) * 25;
                document.getElementById('score').value = total.toFixed(2);
            }
        </script>
    </div>
</body>

</html>