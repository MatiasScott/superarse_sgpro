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
        <div class="bg-gradient-to-r from-teal-500 to-emerald-600 rounded-3xl shadow-xl p-8 mb-6 max-w-3xl mx-auto">
            <div class="flex items-center justify-center gap-4">
                <div class="bg-white/20 backdrop-blur-sm p-4 rounded-2xl">
                    <i class="fas fa-edit text-4xl text-white"></i>
                </div>
                <div class="text-white">
                    <h2 class="text-3xl font-bold mb-1">Editar Asignación</h2>
                    <p class="text-teal-100 text-sm">Actualice la información de la asignación académica</p>
                </div>
            </div>
        </div>

        <main class="max-w-3xl mx-auto bg-white rounded-3xl shadow-xl overflow-hidden">
            <form action="<?php echo BASE_PATH; ?>/academic/assignments/update/<?php echo htmlspecialchars($assignment['id']); ?>" method="POST" class="p-8">
                <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                
                <!-- Información de la Asignación (Solo Lectura) -->
                <div class="bg-gray-50 rounded-2xl p-6 mb-6 border border-gray-200">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="bg-gray-500 text-white p-3 rounded-xl">
                            <i class="fas fa-info-circle text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Información de la Asignación</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Profesor</label>
                            <div class="px-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-700 font-medium">
                                <?php echo htmlspecialchars($assignment['professor_name']); ?>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Materia</label>
                            <div class="px-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-700 font-medium">
                                <?php echo htmlspecialchars($assignment['subject_name']); ?>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">PAO</label>
                            <div class="px-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-700 font-medium">
                                <?php echo htmlspecialchars($assignment['pao_name']); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grid de Horas y Estado -->
                <div class="grid md:grid-cols-2 gap-6 mb-6">
                    <!-- Horas por Semana -->
                    <div class="bg-yellow-50 rounded-2xl p-6 border border-yellow-100">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fas fa-clock text-yellow-500 text-lg"></i>
                            <label for="hours_per_week" class="font-bold text-gray-800">Horas por Semana</label>
                        </div>
                        <input type="number" step="0.01" id="hours_per_week" name="hours_per_week" 
                               value="<?php echo htmlspecialchars($assignment['hours_per_week']); ?>"
                               class="w-full px-4 py-3 border-0 rounded-xl bg-white shadow-sm focus:ring-2 focus:ring-yellow-400 outline-none text-gray-700 text-center font-semibold text-xl" 
                               min="0.01" max="40" required>
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
                            <option value="Asignado" <?php echo ($assignment['status'] == 'Asignado') ? 'selected' : ''; ?>>✅ Asignado</option>
                            <option value="Pendiente" <?php echo ($assignment['status'] == 'Pendiente') ? 'selected' : ''; ?>>⏳ Pendiente</option>
                            <option value="Completado" <?php echo ($assignment['status'] == 'Completado') ? 'selected' : ''; ?>>🎯 Completado</option>
                        </select>
                    </div>
                </div>

                <!-- Información adicional -->
                <div class="bg-blue-50 rounded-2xl p-6 mb-8 border border-blue-200">
                    <div class="flex items-start gap-3">
                        <div class="bg-blue-100 p-2 rounded-lg">
                            <i class="fas fa-lightbulb text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 mb-2">Información importante</h4>
                            <ul class="space-y-2 text-sm text-gray-600">
                                <li class="flex items-center gap-2">
                                    <i class="fas fa-check text-blue-500"></i>
                                    Las horas por semana deben estar entre 1 y 40
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="fas fa-check text-blue-500"></i>
                                    Asegúrese de que el estado refleje el progreso actual
                                </li>
                            </ul>
                        </div>
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
                            class="flex-1 py-4 px-6 bg-gradient-to-r from-teal-500 to-emerald-600 text-white rounded-xl font-bold hover:from-teal-600 hover:to-emerald-700 transition-all duration-200 flex items-center justify-center gap-2 shadow-lg">
                        <i class="fas fa-save"></i>
                        Actualizar Asignación
                    </button>
                </div>
            </form>
        </main>
    </div>

    <script src="<?php echo BASE_PATH; ?>/js/responsive.js"></script>
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