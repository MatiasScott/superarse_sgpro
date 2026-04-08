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

<body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 font-sans min-h-screen">
    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>
    <?php $isProfesor = isset($roles) && in_array('Profesor', array_column($roles, 'role_name')); ?>

    <div class="main-content">
        <header class="mb-8">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl shadow-xl p-8 text-white">
                <div class="flex items-center space-x-4">
                    <div class="bg-white/20 backdrop-blur-sm p-4 rounded-xl">
                        <i class="fas fa-clipboard-check text-4xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold">Editar Evaluación</h1>
                        <p class="text-blue-100 mt-1">Actualizar información de la evaluación académica</p>
                    </div>
                </div>
            </div>
        </header>

        <main class="max-w-4xl mx-auto">
            <form action="<?php echo BASE_PATH; ?>/evaluations/update/<?php echo htmlspecialchars($evaluation['id']); ?>" method="POST" enctype="multipart/form-data" class="space-y-6">

                <!-- Información Básica -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                    <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-gray-200">
                        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 p-3 rounded-lg">
                            <i class="fas fa-user-tie text-white text-xl"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Información de Participantes</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <?php if (!$isProfesor): ?>
                            <div class="group">
                                <label for="professor_id" class="flex items-center space-x-2 text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-chalkboard-teacher text-blue-500"></i>
                                    <span>Profesor</span>
                                </label>
                                <select id="professor_id" name="professor_id" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white hover:border-blue-400">
                                    <?php foreach ($professors as $professor): ?>
                                        <option value="<?php echo htmlspecialchars($professor['id']); ?>" <?php echo ($professor['id'] == $evaluation['professor_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($professor['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="group">
                                <label for="pao_id" class="flex items-center space-x-2 text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-book text-purple-500"></i>
                                    <span>PAO</span>
                                </label>
                                <select id="pao_id" name="pao_id" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white hover:border-purple-400">
                                    <?php if (empty($paos)): ?>
                                        <option value="">-- No hay PAO disponible --</option>
                                    <?php else: ?>
                                        <?php foreach ($paos as $pao): ?>
                                            <option value="<?php echo htmlspecialchars($pao['id']); ?>" <?php echo ($pao['id'] == $evaluation['pao_id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($pao['title'] ?? $pao['name'] ?? 'PAO ' . $pao['id']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="group">
                                <label for="evaluator_id" class="flex items-center space-x-2 text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-user-check text-green-500"></i>
                                    <span>Persona que evalúa</span>
                                </label>
                                <select id="evaluator_id" name="evaluator_id" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 bg-white hover:border-green-400">
                                    <?php foreach ($evaluators as $evaluator): ?>
                                        <option value="<?php echo htmlspecialchars($evaluator['id']); ?>" <?php echo ($evaluator['id'] == $evaluation['evaluator_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($evaluator['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php else: ?>
                            <div class="group">
                                <label class="flex items-center space-x-2 text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-chalkboard-teacher text-blue-500"></i>
                                    <span>Profesor</span>
                                </label>
                                <div class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-50 text-gray-700"><?php $professorName = ''; foreach ($professors as $professor) { if ($professor['id'] == $evaluation['professor_id']) { $professorName = $professor['name']; break; } } echo htmlspecialchars($professorName !== '' ? $professorName : ('ID ' . $evaluation['professor_id'])); ?></div>
                                <input type="hidden" id="professor_id" name="professor_id" value="<?php echo htmlspecialchars($evaluation['professor_id']); ?>">
                            </div>
                            <div class="group">
                                <label class="flex items-center space-x-2 text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-book text-purple-500"></i>
                                    <span>PAO</span>
                                </label>
                                <div class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-50 text-gray-700"><?php $paoName = ''; foreach ($paos as $pao) { if ($pao['id'] == $evaluation['pao_id']) { $paoName = $pao['title'] ?? $pao['name'] ?? ('PAO ' . $pao['id']); break; } } echo htmlspecialchars($paoName !== '' ? $paoName : ('ID ' . $evaluation['pao_id'])); ?></div>
                                <input type="hidden" id="pao_id" name="pao_id" value="<?php echo htmlspecialchars($evaluation['pao_id']); ?>">
                            </div>
                            <div class="group">
                                <label class="flex items-center space-x-2 text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-user-check text-green-500"></i>
                                    <span>Persona que evalúa</span>
                                </label>
                                <div class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-50 text-gray-700"><?php $evaluatorName = ''; foreach ($evaluators as $evaluator) { if ($evaluator['id'] == $evaluation['evaluator_id']) { $evaluatorName = $evaluator['name']; break; } } echo htmlspecialchars($evaluatorName !== '' ? $evaluatorName : ('ID ' . $evaluation['evaluator_id'])); ?></div>
                                <input type="hidden" id="evaluator_id" name="evaluator_id" value="<?php echo htmlspecialchars($evaluation['evaluator_id']); ?>">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Calificación y Comentarios -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                    <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-gray-200">
                        <div class="bg-gradient-to-br from-yellow-500 to-orange-600 p-3 rounded-lg">
                            <i class="fas fa-star text-white text-xl"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Calificación y Observaciones</h2>
                    </div>

                    <?php if (!$isProfesor): ?>
                    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        <!-- Autoevaluación 20% -->
                        <div class="bg-white rounded-xl p-4 border-2 border-blue-200">
                            <label for="autoevaluacion" class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-user text-blue-500 mr-1"></i> Autoevaluación (20%)
                            </label>
                            <input type="number" step="0.01" id="autoevaluacion" name="autoevaluacion" 
                                   value="<?php echo htmlspecialchars($evaluation['autoevaluacion'] ?? 0); ?>"
                                   class="w-full px-3 py-2 border-0 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-400 outline-none text-gray-700 text-center font-semibold" 
                                placeholder="0.00" min="0" max="5" required oninput="calcularPuntajeTotal()">
                            <p class="text-xs text-gray-500 text-center mt-1">Máx: 5 puntos</p>
                        </div>

                        <!-- Coevaluación 20% -->
                        <div class="bg-white rounded-xl p-4 border-2 border-green-200">
                            <label for="coevaluacion_20" class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-users text-green-500 mr-1"></i> Coevaluación Directivos (20%)
                            </label>
                            <input type="number" step="0.01" id="coevaluacion_20" name="coevaluacion_20" 
                                value="<?php echo htmlspecialchars($evaluation['coevaluacion_20'] ?? min((float)($evaluation['coevaluacion'] ?? 0), 5)); ?>"
                                   class="w-full px-3 py-2 border-0 rounded-lg bg-gray-50 focus:ring-2 focus:ring-green-400 outline-none text-gray-700 text-center font-semibold" 
                                placeholder="0.00" min="0" max="5" required oninput="calcularPuntajeTotal()">
                            <p class="text-xs text-gray-500 text-center mt-1">Máx: 5 puntos</p>
                        </div>

                        <!-- Coevaluación 30% -->
                        <div class="bg-white rounded-xl p-4 border-2 border-emerald-200">
                            <label for="coevaluacion_30" class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-users text-emerald-500 mr-1"></i> Coevaluación Pares (30%)
                            </label>
                            <input type="number" step="0.01" id="coevaluacion_30" name="coevaluacion_30" 
                                value="<?php echo htmlspecialchars($evaluation['coevaluacion_30'] ?? min((float)($evaluation['coevaluacion'] ?? 0), 5)); ?>"
                                   class="w-full px-3 py-2 border-0 rounded-lg bg-gray-50 focus:ring-2 focus:ring-emerald-400 outline-none text-gray-700 text-center font-semibold" 
                                placeholder="0.00" min="0" max="5" required oninput="calcularPuntajeTotal()">
                            <p class="text-xs text-gray-500 text-center mt-1">Máx: 5 puntos</p>
                        </div>

                        <!-- Heteroevaluación 30% -->
                        <div class="bg-white rounded-xl p-4 border-2 border-purple-200">
                            <label for="heteroevaluacion" class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-user-graduate text-purple-500 mr-1"></i> Heteroevaluación (30%)
                            </label>
                            <input type="number" step="0.01" id="heteroevaluacion" name="heteroevaluacion" 
                                   value="<?php echo htmlspecialchars($evaluation['heteroevaluacion'] ?? 0); ?>"
                                   class="w-full px-3 py-2 border-0 rounded-lg bg-gray-50 focus:ring-2 focus:ring-purple-400 outline-none text-gray-700 text-center font-semibold" 
                                placeholder="0.00" min="0" max="5" required oninput="calcularPuntajeTotal()">
                            <p class="text-xs text-gray-500 text-center mt-1">Máx: 5 puntos</p>
                        </div>

                        <input type="hidden" id="coevaluacion" name="coevaluacion" value="<?php echo htmlspecialchars($evaluation['coevaluacion'] ?? 0); ?>">
                    </div>
                    <?php else: ?>
                    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-xl p-4 text-blue-800 text-sm">
                        Los puntajes detallados no están disponibles para el rol docente. Solo puede visualizar el puntaje total.
                    </div>
                    <input type="hidden" id="autoevaluacion" name="autoevaluacion" value="<?php echo htmlspecialchars($evaluation['autoevaluacion'] ?? 0); ?>">
                    <input type="hidden" id="coevaluacion_20" name="coevaluacion_20" value="<?php echo htmlspecialchars($evaluation['coevaluacion_20'] ?? min((float)($evaluation['coevaluacion'] ?? 0), 5)); ?>">
                    <input type="hidden" id="coevaluacion_30" name="coevaluacion_30" value="<?php echo htmlspecialchars($evaluation['coevaluacion_30'] ?? min((float)($evaluation['coevaluacion'] ?? 0), 5)); ?>">
                    <input type="hidden" id="heteroevaluacion" name="heteroevaluacion" value="<?php echo htmlspecialchars($evaluation['heteroevaluacion'] ?? 0); ?>">
                    <input type="hidden" id="coevaluacion" name="coevaluacion" value="<?php echo htmlspecialchars($evaluation['coevaluacion'] ?? 0); ?>">
                    <?php endif; ?>

                    <!-- Puntaje Total -->
                    <div class="bg-gradient-to-r from-yellow-400 to-amber-400 rounded-xl p-4 mb-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-calculator text-white text-xl"></i>
                                <span class="text-white font-bold text-lg">Puntaje Total:</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="number" step="0.01" id="score" name="score" readonly
                                       value="<?php echo htmlspecialchars($evaluation['score']); ?>"
                                       class="w-24 px-3 py-2 border-0 rounded-lg bg-white text-gray-800 text-center font-bold text-xl" 
                                       placeholder="0.00" required>
                                <span class="text-white font-bold text-xl">/ 100</span>
                            </div>
                        </div>
                    </div>

                    <div class="group">
                        <label class="flex items-center space-x-2 text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-comments text-indigo-500"></i>
                            <span>Historial de comentarios</span>
                        </label>
                        <div class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-50 text-gray-700 min-h-[110px] whitespace-pre-wrap"><?php echo !empty($evaluation['comments']) ? nl2br(htmlspecialchars($evaluation['comments'])) : 'Sin comentarios previos.'; ?></div>
                        <input type="hidden" name="comments" value="<?php echo htmlspecialchars($evaluation['comments'] ?? ''); ?>">
                    </div>

                    <div class="group mt-4">
                        <label for="new_comment" class="flex items-center space-x-2 text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-pen text-indigo-500"></i>
                            <span>Nuevo comentario</span>
                        </label>
                        <textarea id="new_comment" name="new_comment" rows="4" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400 resize-none" placeholder="Escriba un nuevo comentario para agregar al historial..."></textarea>
                    </div>
                </div>

                <!-- Estados -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                    <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-gray-200">
                        <div class="bg-gradient-to-br from-teal-500 to-cyan-600 p-3 rounded-lg">
                            <i class="fas fa-tasks text-white text-xl"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Estados de Evaluación</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="group">
                            <label for="status" class="flex items-center space-x-2 text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-clipboard-list text-teal-500"></i>
                                <span>Estado</span>
                            </label>
                            <?php if (!$isProfesor): ?>
                            <select id="status" name="status" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 bg-white hover:border-teal-400">
                                <option value="Pendiente de subida" <?php echo ($evaluation['status'] == 'Pendiente de subida') ? 'selected' : ''; ?>>⏳ Pendiente de subida</option>
                                <option value="Pendiente de firma" <?php echo ($evaluation['status'] == 'Pendiente de firma') ? 'selected' : ''; ?>>✍️ Pendiente de firma</option>
                                <option value="Firmado" <?php echo ($evaluation['status'] == 'Firmado') ? 'selected' : ''; ?>>✅ Firmado</option>
                            </select>
                            <?php else: ?>
                            <div class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-50 text-gray-700"><?php echo htmlspecialchars($evaluation['status']); ?></div>
                            <input type="hidden" id="status" name="status" value="<?php echo htmlspecialchars($evaluation['status']); ?>">
                            <?php endif; ?>
                        </div>

                        <div class="group">
                            <label for="final_status" class="flex items-center space-x-2 text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-flag-checkered text-cyan-500"></i>
                                <span>Estado Final</span>
                            </label>
                            <?php if (!$isProfesor): ?>
                            <select id="final_status" name="final_status" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200 bg-white hover:border-cyan-400">
                                <option value="En proceso" <?php echo ($evaluation['final_status'] == 'En proceso') ? 'selected' : ''; ?>>🔄 En proceso</option>
                                <option value="Completa" <?php echo ($evaluation['final_status'] == 'Completa') ? 'selected' : ''; ?>>✅ Completa</option>
                                <option value="Cancelada" <?php echo ($evaluation['final_status'] == 'Cancelada') ? 'selected' : ''; ?>>❌ Cancelada</option>
                            </select>
                            <?php else: ?>
                            <div class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-50 text-gray-700"><?php echo htmlspecialchars($evaluation['final_status']); ?></div>
                            <input type="hidden" id="final_status" name="final_status" value="<?php echo htmlspecialchars($evaluation['final_status']); ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Gestión de Documentos -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                    <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-gray-200">
                        <div class="bg-gradient-to-br from-green-500 to-emerald-600 p-3 rounded-lg">
                            <i class="fas fa-file-signature text-white text-xl"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Gestión de Documentos</h2>
                    </div>

                    <!-- Archivos Actuales -->
                    <div class="mb-6 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-5 border border-blue-200">
                        <h3 class="flex items-center space-x-2 text-base font-semibold text-gray-800 mb-4">
                            <i class="fas fa-folder-open text-blue-600"></i>
                            <span>Archivos Actuales</span>
                        </h3>
                        
                        <div class="space-y-3">
                            <?php
                                $basePathPrefix = rtrim(BASE_PATH, '/');
                                $normalizeEvaluationFileUrl = static function ($url) use ($basePathPrefix) {
                                    if (empty($url)) {
                                        return '';
                                    }

                                    $url = trim((string)$url);
                                    $url = str_replace('\\\\', '/', $url);
                                    $url = str_replace('\\', '/', $url);

                                    // Si ya es absoluta (http/https), se respeta.
                                    if (preg_match('#^https?://#i', $url)) {
                                        return $url;
                                    }

                                    // Quitar prefijos relativos comunes.
                                    if (strpos($url, './') === 0) {
                                        $url = substr($url, 2);
                                    }

                                    // Rutas antiguas con /public/uploads/... o /landing_sgpro/public/uploads/...
                                    if (strpos($url, '/public/uploads/evaluations/') !== false) {
                                        return $basePathPrefix . '/uploads/evaluations/' . basename($url);
                                    }

                                    // Ruta sin slash inicial: public/uploads/evaluations/...
                                    if (strpos($url, 'public/uploads/evaluations/') === 0) {
                                        return $basePathPrefix . '/uploads/evaluations/' . basename($url);
                                    }

                                    // Ruta relativa uploads/evaluations/... -> convertir a absoluta con BASE_PATH.
                                    if (strpos($url, 'uploads/evaluations/') === 0) {
                                        return $basePathPrefix . '/' . $url;
                                    }

                                    // Cualquier ruta iniciando en /uploads/... -> prefijar BASE_PATH.
                                    if (strpos($url, '/uploads/evaluations/') === 0) {
                                        return $basePathPrefix . $url;
                                    }

                                    return $url;
                                };

                                $initialFileUrl = $evaluation['initial_file_path'] ?? '';
                                $signedFileUrl = $evaluation['signed_file_path'] ?? '';
                                $initialFileUrl = $normalizeEvaluationFileUrl($initialFileUrl);
                                $signedFileUrl = $normalizeEvaluationFileUrl($signedFileUrl);
                            ?>
                            <?php if (!empty($initialFileUrl)): ?>
                                <div class="flex items-center space-x-3 bg-white rounded-lg p-3 border border-blue-200 hover:shadow-md transition-shadow">
                                    <i class="fas fa-file-pdf text-red-500 text-2xl"></i>
                                    <a href="<?php echo htmlspecialchars($initialFileUrl); ?>" target="_blank" class="text-blue-600 hover:text-blue-800 font-medium hover:underline flex-1">
                                        Archivo de Evaluación Original
                                    </a>
                                    <i class="fas fa-external-link-alt text-gray-400"></i>
                                </div>
                            <?php else: ?>
                                <div class="flex items-center space-x-3 bg-gray-50 rounded-lg p-3 border border-gray-200">
                                    <i class="fas fa-times-circle text-gray-400 text-2xl"></i>
                                    <span class="text-gray-500">No hay archivo original subido</span>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($signedFileUrl)): ?>
                                <div class="flex items-center space-x-3 bg-white rounded-lg p-3 border border-green-200 hover:shadow-md transition-shadow">
                                    <i class="fas fa-file-signature text-green-500 text-2xl"></i>
                                    <a href="<?php echo htmlspecialchars($signedFileUrl); ?>" target="_blank" class="text-green-600 hover:text-green-800 font-medium hover:underline flex-1">
                                        Archivo Firmado por Profesor
                                    </a>
                                    <i class="fas fa-external-link-alt text-gray-400"></i>
                                </div>
                            <?php else: ?>
                                <div class="flex items-center space-x-3 bg-green-50 rounded-lg p-3 border border-green-200">
                                    <i class="fas fa-info-circle text-green-500 text-2xl"></i>
                                    <span class="text-green-700 text-sm">Archivo subido y firmado por el Coordinador Académico</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (!$isProfesor): ?>
                    <!-- Subir Nuevo Archivo -->
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg p-5 border border-green-200">
                        <h3 class="flex items-center space-x-2 text-base font-semibold text-gray-800 mb-4">
                            <i class="fas fa-cloud-upload-alt text-green-600"></i>
                            <span>Subir Nuevo Archivo</span>
                        </h3>
                        
                        <div class="group">
                            <label for="signed_file" class="flex items-center space-x-2 text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-file-pdf text-red-500"></i>
                                <span>Archivo firmado por el profesor</span>
                            </label>
                            <input type="file" id="signed_file" name="signed_file" accept=".pdf" class="block w-full text-sm text-gray-600 file:mr-4 file:py-3 file:px-6 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gradient-to-r file:from-red-500 file:to-pink-600 file:text-white hover:file:from-red-600 hover:file:to-pink-700 file:cursor-pointer file:transition-all file:duration-200 file:shadow-md hover:file:shadow-lg cursor-pointer border-2 border-dashed border-red-300 rounded-lg p-3 hover:border-red-500 transition-colors bg-white">
                            <p class="mt-2 text-xs text-gray-500 flex items-center space-x-1">
                                <i class="fas fa-info-circle"></i>
                                <span>Solo archivos PDF permitidos</span>
                            </p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Botón de Guardar -->
                <div class="flex items-center space-x-4">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center space-x-2">
                        <i class="fas fa-save text-xl"></i>
                        <span>Actualizar Evaluación</span>
                    </button>
                    <a href="<?php echo BASE_PATH; ?>/evaluations" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center space-x-2">
                        <i class="fas fa-times"></i>
                        <span>Cancelar</span>
                    </a>
                </div>
            </form>
        </main>
    </div>

    <script>
        // Calcular puntaje total automáticamente
        function calcularPuntajeTotal() {
            const autoevaluacionInput = document.getElementById('autoevaluacion');
            const coevaluacion20Input = document.getElementById('coevaluacion_20');
            const coevaluacion30Input = document.getElementById('coevaluacion_30');
            const heteroevaluacionInput = document.getElementById('heteroevaluacion');
            const coevaluacionInput = document.getElementById('coevaluacion');
            const scoreInput = document.getElementById('score');

            if (!autoevaluacionInput || !coevaluacion20Input || !coevaluacion30Input || !heteroevaluacionInput || !coevaluacionInput || !scoreInput) {
                return;
            }

            const autoevaluacion = parseFloat(autoevaluacionInput.value) || 0;
            const coevaluacion20 = parseFloat(coevaluacion20Input.value) || 0;
            const coevaluacion30 = parseFloat(coevaluacion30Input.value) || 0;
            const heteroevaluacion = parseFloat(heteroevaluacionInput.value) || 0;
            
            // Validar límites
            if (autoevaluacion > 5) {
                autoevaluacionInput.value = 5;
                return calcularPuntajeTotal();
            }
            if (coevaluacion20 > 5) {
                coevaluacion20Input.value = 5;
                return calcularPuntajeTotal();
            }
            if (coevaluacion30 > 5) {
                coevaluacion30Input.value = 5;
                return calcularPuntajeTotal();
            }
            if (heteroevaluacion > 5) {
                heteroevaluacionInput.value = 5;
                return calcularPuntajeTotal();
            }
            
            const coevaluacion = (coevaluacion20 * 0.40) + (coevaluacion30 * 0.60);
            coevaluacionInput.value = coevaluacion.toFixed(2);

            const sumatoria = (autoevaluacion * 0.20) + (coevaluacion20 * 0.20) + (coevaluacion30 * 0.30) + (heteroevaluacion * 0.30);
            const total = (sumatoria - 1) * 25;
            scoreInput.value = total.toFixed(2);
        }

        // Calcular el total al cargar la página
        window.addEventListener('DOMContentLoaded', function() {
            calcularPuntajeTotal();
        });
    </script>
</body>

</html>