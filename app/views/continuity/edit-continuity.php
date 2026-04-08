<?php
$isProfessorDecisionMade = $continuity['professor_decision'] !== null;
$isDocenciaDecisionMade = $continuity['docencia_decision'] !== null;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?php echo BASE_PATH; ?>/img/logo_sgpro.png">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <!-- Incluir Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/css/compiled.css">
<link rel="stylesheet" href="<?php echo BASE_PATH; ?>/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
    <style>
        /* Estilo para simular deshabilitación visual y bloqueo de interacción */
        .workflow-disabled {
            filter: grayscale(100%);
            pointer-events: none;
            cursor: not-allowed;
            opacity: 0.6;
        }
         /* Asegurar que los elementos no se desborden en móvil y tablet */
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
         /* Asegurar que los elementos no se desborden en móvil y tablet */
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
         /* Asegurar que los elementos no se desborden en móvil y tablet */
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
    </style>
</head>

<body class="bg-gray-50 min-h-screen">

    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

    <div class="main-content">
        <div class="max-w-5xl mx-auto bg-white p-8 rounded-xl shadow-2xl border border-gray-200">
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-extrabold text-indigo-800 mb-2"><?= htmlspecialchars($pageTitle) ?></h1>
                <p class="text-gray-600 text-sm">Gestione las decisiones de continuidad del profesor en el próximo PAO</p>
            </div>

        <!-- Detalles de la Continuidad -->
        <div class="bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 p-6 rounded-lg border border-indigo-200 mb-8">
            <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Información de Continuidad
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white p-4 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">Profesor</p>
                    <p class="text-base font-semibold text-gray-800"><?= htmlspecialchars($professor['name'] ?? 'N/A') ?></p>
                </div>
                <div class="bg-white p-4 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">PAO</p>
                    <p class="text-base font-semibold text-gray-800"><?= htmlspecialchars($pao['title'] ?? 'N/A') ?></p>
                </div>
                <div class="bg-white p-4 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">Estado Final</p>
                    <p class="text-base font-semibold text-green-700"><?= htmlspecialchars($continuity['final_status']) ?></p>
                </div>
            </div>
        </div>

        <!-- 1. Decisión del Profesor (Primer Paso) -->
        <?php if ($canViewEditProfessorDecision || $isDocenciaDecisionMade): ?>
            <div class="mb-8 p-6 border-2 rounded-xl shadow-lg transition-all duration-300 
                    <?= $isDocenciaDecisionMade ? 'bg-gray-50 border-gray-300' : 'border-indigo-300 bg-white' ?>">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center mr-3">
                        <span class="text-indigo-700 font-bold text-lg">1</span>
                    </div>
                    <h2 class="text-2xl font-bold text-indigo-700">Decisión del Profesor</h2>
                </div>

                <?php if ($isDocenciaDecisionMade): ?>
                    <div class="bg-gradient-to-r from-orange-50 to-red-50 border-l-4 border-orange-500 text-orange-800 px-4 py-3 rounded-r-lg mb-4">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <div>
                                <p class="font-bold">Decisión Bloqueada</p>
                                <p class="text-sm">Esta decisión ya no puede ser modificada porque Talento Humano ya tomó su decisión.</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($isProfessorDecisionMade): ?>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                        <p class="text-green-700 font-medium flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Decisión registrada:
                            <strong class="text-lg ml-2"><?= $continuity['professor_decision'] == 1 ? '✅ SÍ (Desea continuar)' : '❌ NO (No desea continuar)' ?></strong>
                        </p>
                    </div>
                <?php else: ?>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                        <p class="text-yellow-700 font-medium flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Pendiente de la decisión del profesor.
                        </p>
                    </div>
                <?php endif; ?>

                <!-- Formulario de Decisión del Profesor -->
                <form action="<?= BASE_PATH ?>/continuity/update/<?= htmlspecialchars($continuity['id']) ?>" method="POST" class="<?= $isDocenciaDecisionMade ? 'workflow-disabled' : '' ?>">
                    <input type="hidden" name="update_field" value="professor_decision">

                    <div class="bg-gray-50 p-5 rounded-lg border border-gray-200">
                        <label class="block font-semibold text-gray-700 mb-4">
                            ¿Deseas continuar en el siguiente PAO?
                        </label>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <label class="flex-1 flex items-center justify-center cursor-pointer bg-white border-2 border-gray-300 rounded-lg p-4 hover:border-indigo-500 transition-all duration-200 <?= ($continuity['professor_decision'] == 1) ? 'border-indigo-600 bg-gradient-to-br from-indigo-50 to-indigo-100 shadow-lg ring-2 ring-indigo-300' : '' ?>">
                                <input type="radio" name="professor_decision" value="1" class="form-radio h-6 w-6 text-indigo-600 mr-3" required
                                    <?= ($continuity['professor_decision'] == 1) ? 'checked' : '' ?>>
                                <div class="flex items-center">
                                    <span class="text-3xl mr-2">✅</span>
                                    <div>
                                        <span class="font-bold text-gray-800 block">Sí, deseo continuar</span>
                                        <?php if ($continuity['professor_decision'] == 1): ?>
                                            <span class="text-xs text-indigo-600 font-semibold">✓ Opción seleccionada</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </label>
                            <label class="flex-1 flex items-center justify-center cursor-pointer bg-white border-2 border-gray-300 rounded-lg p-4 hover:border-red-500 transition-all duration-200 <?= ($continuity['professor_decision'] === 0) ? 'border-red-600 bg-gradient-to-br from-red-50 to-red-100 shadow-lg ring-2 ring-red-300' : '' ?>">
                                <input type="radio" name="professor_decision" value="0" class="form-radio h-6 w-6 text-red-600 mr-3" required
                                    <?= ($continuity['professor_decision'] === 0) ? 'checked' : '' ?>>
                                <div class="flex items-center">
                                    <span class="text-3xl mr-2">❌</span>
                                    <div>
                                        <span class="font-bold text-gray-800 block">No, no deseo continuar</span>
                                        <?php if ($continuity['professor_decision'] === 0): ?>
                                            <span class="text-xs text-red-600 font-semibold">✓ Opción seleccionada</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="mt-5 flex justify-end">
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-lg shadow-lg hover:from-indigo-700 hover:to-purple-700 transition duration-200 transform hover:scale-105 flex items-center" 
                            <?= $isDocenciaDecisionMade ? 'disabled' : '' ?>>
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Enviar Decisión del Profesor
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- 2. Decisión de Docencia/Talento Humano (Segundo Paso) -->
        <?php if ($canViewEditDocenciaTHDecision): ?>
            <div class="p-6 border-2 rounded-xl shadow-lg transition-all duration-300 
                    <?= $isProfessorDecisionMade ? 'bg-white border-green-300' : 'bg-gray-50 border-red-300 workflow-disabled' ?>">

                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                        <span class="text-green-700 font-bold text-lg">2</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-700">Decisión de Docencia/Talento Humano</h2>
                </div>

                <?php if (!$isProfessorDecisionMade): ?>
                    <div class="text-center p-6 bg-gradient-to-br from-red-50 to-orange-50 border-2 border-red-300 rounded-xl">
                        <div class="flex justify-center mb-3">
                            <svg class="w-16 h-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <p class="font-bold text-xl text-red-700 mb-2">🔒 Sección Bloqueada</p>
                        <p class="text-red-600">Esta decisión se habilitará automáticamente cuando el Profesor haya enviado su respuesta.</p>
                    </div>
                <?php else: ?>
                    <?php if ($continuity['docencia_decision'] !== null): ?>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                            <p class="text-green-700 font-medium flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Decisión Docencia/TH registrada:
                                <strong class="text-lg ml-2"><?= $continuity['docencia_decision'] == 1 ? '✅ SÍ (Aprobado)' : '❌ NO (No Aprobado)' ?></strong>
                            </p>
                            <p class="text-sm text-green-600 mt-1 ml-7">Por: <?= htmlspecialchars($approvedBy['name'] ?? 'N/A') ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Formulario de Decisión de Docencia/TH -->
                    <form action="<?= BASE_PATH ?>/continuity/update/<?= htmlspecialchars($continuity['id']) ?>" method="POST" class="mt-6">
                        <input type="hidden" name="update_field" value="docencia_decision">

                        <div class="bg-gray-50 p-5 rounded-lg border border-gray-200">
                            <label class="block font-semibold text-gray-700 mb-4">
                                ¿Aprobar la continuidad del profesor?
                            </label>
                            <div class="flex flex-col sm:flex-row gap-4">
                                <label class="flex-1 flex items-center justify-center cursor-pointer bg-white border-2 border-gray-300 rounded-lg p-4 hover:border-green-500 transition-all duration-200 <?= ($continuity['docencia_decision'] == 1) ? 'border-green-600 bg-gradient-to-br from-green-50 to-green-100 shadow-lg ring-2 ring-green-300' : '' ?>">
                                    <input type="radio" name="docencia_decision" value="1" class="form-radio h-6 w-6 text-green-600 mr-3" required
                                        <?= ($continuity['docencia_decision'] == 1) ? 'checked' : '' ?>>
                                    <div class="flex items-center">
                                        <span class="text-3xl mr-2">✅</span>
                                        <div>
                                            <span class="font-bold text-gray-800 block">Sí, aprobar</span>
                                            <?php if ($continuity['docencia_decision'] == 1): ?>
                                                <span class="text-xs text-green-600 font-semibold">✓ Opción seleccionada</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </label>
                                <label class="flex-1 flex items-center justify-center cursor-pointer bg-white border-2 border-gray-300 rounded-lg p-4 hover:border-red-500 transition-all duration-200 <?= ($continuity['docencia_decision'] === 0) ? 'border-red-600 bg-gradient-to-br from-red-50 to-red-100 shadow-lg ring-2 ring-red-300' : '' ?>">
                                    <input type="radio" name="docencia_decision" value="0" class="form-radio h-6 w-6 text-red-600 mr-3" required
                                        <?= ($continuity['docencia_decision'] === 0) ? 'checked' : '' ?>>
                                    <div class="flex items-center">
                                        <span class="text-3xl mr-2">❌</span>
                                        <div>
                                            <span class="font-bold text-gray-800 block">No, rechazar</span>
                                            <?php if ($continuity['docencia_decision'] === 0): ?>
                                                <span class="text-xs text-red-600 font-semibold">✓ Opción seleccionada</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="mt-5 flex justify-end">
                            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold rounded-lg shadow-lg hover:from-green-700 hover:to-emerald-700 transition duration-200 transform hover:scale-105 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Enviar Decisión de Docencia/TH
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (!$canViewEditProfessorDecision && !$canViewEditDocenciaTHDecision): ?>
            <div class="text-center p-8 bg-gradient-to-br from-blue-50 to-indigo-50 border-2 border-blue-300 rounded-xl">
                <svg class="w-16 h-16 text-blue-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-blue-700 font-medium">No tiene permisos para ver o editar las decisiones de continuidad para este registro.</p>
            </div>
        <?php endif; ?>

        <!-- Botón para volver a la gestión -->
        <div class="mt-8 flex flex-col sm:flex-row justify-between items-center gap-4 border-t pt-6">
            <a href="<?= BASE_PATH ?>/continuity" class="inline-flex items-center px-6 py-3 bg-gray-500 text-white font-semibold rounded-lg shadow-md hover:bg-gray-600 transition duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver a Gestión
            </a>
            
            <?php if ($canViewEditProfessorDecision || $canViewEditDocenciaTHDecision): ?>
                <div class="flex items-center text-sm text-gray-600 bg-blue-50 px-4 py-2 rounded-lg">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Recuerde guardar sus cambios usando los botones "Enviar Decisión"</span>
                </div>
            <?php endif; ?>
        </div>
        </div>
    </div>

</body>

</html>