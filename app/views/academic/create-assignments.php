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
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-3xl shadow-xl p-8 mb-6 max-w-3xl mx-auto">
            <div class="flex items-center justify-center gap-4">
                <div class="bg-white/20 backdrop-blur-sm p-4 rounded-2xl">
                    <i class="fas fa-user-graduate text-4xl text-white"></i>
                </div>
                <div class="text-white">
                    <h2 class="text-3xl font-bold mb-1">Nueva Asignación</h2>
                    <p class="text-blue-100 text-sm">Complete la información necesaria para asignar un profesor a una materia</p>
                </div>
            </div>
        </div>

        <main class="max-w-3xl mx-auto bg-white rounded-3xl shadow-xl overflow-hidden">
            <form action="<?php echo BASE_PATH; ?>/academic/assignments/store" method="POST" class="p-8">
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
                        <?php if (isset($professors) && is_array($professors)): ?>
                            <?php foreach ($professors as $professor): ?>
                                <option value="<?php echo htmlspecialchars($professor['id']); ?>">
                                    <?php echo htmlspecialchars($professor['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <p class="text-xs text-blue-600 mt-2 flex items-center gap-1">
                        <i class="fas fa-info-circle"></i>
                        Seleccione el profesor responsable de la materia
                    </p>
                </div>

                <!-- Sección Materia -->
                <div class="bg-purple-50 rounded-2xl p-6 mb-6 border border-purple-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="bg-purple-500 text-white p-3 rounded-xl">
                            <i class="fas fa-book text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Materia</h3>
                    </div>
                    <select id="subject_id" name="subject_id" required 
                            class="w-full px-4 py-3 border-0 rounded-xl bg-white shadow-sm focus:ring-2 focus:ring-purple-400 outline-none text-gray-700">
                        <option value="">Seleccione una materia</option>
                        <?php if (isset($subjects) && is_array($subjects)): ?>
                            <?php foreach ($subjects as $subject): ?>
                                <option value="<?php echo htmlspecialchars($subject['id']); ?>">
                                    <?php echo htmlspecialchars($subject['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <p class="text-xs text-purple-600 mt-2 flex items-center gap-1">
                        <i class="fas fa-info-circle"></i>
                        Seleccione la materia a asignar
                    </p>
                </div>

                <!-- Sección PAO -->
                <div class="bg-cyan-50 rounded-2xl p-6 mb-6 border border-cyan-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="bg-cyan-500 text-white p-3 rounded-xl">
                            <i class="fas fa-calendar-alt text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Programa Académico (PAO)</h3>
                    </div>
                    <select id="pao_id" name="pao_id" required 
                            class="w-full px-4 py-3 border-0 rounded-xl bg-white shadow-sm focus:ring-2 focus:ring-cyan-400 outline-none text-gray-700">
                        <option value="">Seleccione un PAO</option>
                        <?php if (isset($paos) && is_array($paos)): ?>
                            <?php foreach ($paos as $pao): ?>
                                <option value="<?php echo htmlspecialchars($pao['id']); ?>">
                                    <?php echo htmlspecialchars($pao['title']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Grid de Horas y Estado -->
                <div class="grid md:grid-cols-2 gap-6 mb-8">
                    <!-- Horas por Semana -->
                    <div class="bg-yellow-50 rounded-2xl p-6 border border-yellow-100">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fas fa-clock text-yellow-500 text-lg"></i>
                            <label for="hours_per_week" class="font-bold text-gray-800">Horas por Semana</label>
                        </div>
                        <input type="number" step="0.01" id="hours_per_week" name="hours_per_week" 
                               class="w-full px-4 py-3 border-0 rounded-xl bg-white shadow-sm focus:ring-2 focus:ring-yellow-400 outline-none text-gray-700 text-center font-semibold text-xl" 
                               min="0.01" max="40" placeholder="20" required>
                        <p id="hours_preview" class="text-xs text-yellow-600 mt-2 text-center">Valor decimal permitido &bull; Máximo 40 horas</p>
                    </div>

                    <!-- Estado -->
                    <div class="bg-green-50 rounded-2xl p-6 border border-green-100">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fas fa-check-circle text-green-500 text-lg"></i>
                            <label for="status" class="font-bold text-gray-800">Estado</label>
                        </div>
                        <select id="status" name="status" required 
                                class="w-full px-4 py-3 border-0 rounded-xl bg-white shadow-sm focus:ring-2 focus:ring-green-400 outline-none text-gray-700 font-medium">
                            <option value="Asignado">✅ Asignado</option>
                            <option value="Pendiente">⏳ Pendiente</option>
                            <option value="Completado">🎯 Completado</option>
                        </select>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="flex gap-4">
                    <a href="<?php echo BASE_PATH; ?>/academic/assignments" 
                       class="flex-1 py-4 px-6 bg-gray-600 text-white rounded-xl text-center font-bold hover:bg-gray-700 transition-all duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-arrow-left"></i>
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="flex-1 py-4 px-6 bg-gradient-to-r from-indigo-500 to-blue-600 text-white rounded-xl font-bold hover:from-indigo-600 hover:to-blue-700 transition-all duration-200 flex items-center justify-center gap-2 shadow-lg">
                        <i class="fas fa-save"></i>
                        Guardar Asignación
                    </button>
                </div>
            </form>
        </main>
    </div>

    <script>
    (function () {
        var input   = document.getElementById('hours_per_week');
        var preview = document.getElementById('hours_preview');
        var hint    = 'Valor decimal permitido \u2022 M\u00e1ximo 40 horas';

        function updatePreview() {
            var val = parseFloat(input.value);
            if (isNaN(val) || input.value === '') {
                preview.textContent = hint;
                preview.className = 'text-xs text-yellow-600 mt-2 text-center';
                return;
            }
            var totalMin = Math.round(val * 60);
            var h = Math.floor(totalMin / 60);
            var m = totalMin % 60;
            var txt = h > 0 && m > 0 ? h + 'h ' + m + 'min'
                    : h > 0          ? h + 'h'
                    :                  m + 'min';
            preview.textContent = '\u2248 ' + txt;
            preview.className = 'text-xs text-indigo-600 font-semibold mt-2 text-center';
        }

        input.addEventListener('input', updatePreview);
        updatePreview();
    })();
    </script>
</body>

</html>