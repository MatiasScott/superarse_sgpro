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
        <main class="max-w-4xl mx-auto">
            <!-- Header con Gradiente -->
            <div class="bg-gradient-to-r from-cyan-500 to-blue-500 text-white p-8 rounded-t-2xl shadow-xl">
                <div class="flex items-center justify-center mb-4">
                    <div class="bg-white bg-opacity-20 p-4 rounded-xl backdrop-blur-sm">
                        <i class="fas fa-edit text-5xl"></i>
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-center mb-2">Editar Factura</h1>
                <p class="text-center text-blue-100 text-sm">
                    Actualiza la información de la factura y sus documentos asociados
                </p>
            </div>

            <form action="<?php echo BASE_PATH; ?>/invoices/update/<?php echo htmlspecialchars($invoice['id']); ?>" method="POST" enctype="multipart/form-data" class="bg-white p-8 rounded-b-2xl shadow-xl space-y-6">
                
                <?php
                // Detectar si el usuario actual es un profesor
                $currentUserId = $_SESSION['user_id'] ?? null;
                $isProfessor = false;
                
                // Verificar si el usuario tiene el rol de Profesor
                if (isset($_SESSION['user_id'])) {
                    require_once __DIR__ . '/../../models/RoleModel.php';
                    $roleModel = new RoleModel();
                    $roles = $roleModel->getRolesByUserId($_SESSION['user_id']);
                    $roleNames = array_column($roles, 'role_name');
                    $isProfessor = in_array('Profesor', $roleNames);
                }
                ?>
                
                <!-- Sección Información General (Solo Lectura) -->
                <div class="bg-gray-50 bg-opacity-50 p-6 rounded-xl border-2 border-gray-200">
                    <div class="flex items-center mb-4">
                        <div class="bg-gray-500 bg-opacity-20 p-3 rounded-lg mr-3">
                            <i class="fas fa-info-circle text-2xl text-gray-600"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Información General</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Profesor Asignado</label>
                            <input type="text" value="<?php echo htmlspecialchars($invoice['professor_name']); ?>" disabled class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg cursor-not-allowed text-gray-600">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">PAO Asociado</label>
                            <input type="text" value="<?php echo htmlspecialchars($invoice['pao_name']); ?>" disabled class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg cursor-not-allowed text-gray-600">
                        </div>
                    </div>
                </div>

                <!-- Grid de Unidad y Período -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Unidad -->
                    <div class="bg-purple-50 bg-opacity-50 p-6 rounded-xl border-2 border-purple-200">
                        <div class="flex items-center mb-4">
                            <div class="bg-purple-500 bg-opacity-20 p-3 rounded-lg mr-3">
                                <i class="fas fa-bookmark text-2xl text-purple-600"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800">Unidad</h3>
                        </div>
                        <select id="unit_number" name="unit_number" required 
                                class="w-full px-4 py-3 border-2 border-purple-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition duration-200 font-medium">
                            <option value="1" <?php echo (isset($invoice['unit_number']) && $invoice['unit_number'] == 1) ? 'selected' : ''; ?>>📚 Unidad 1</option>
                            <option value="2" <?php echo (isset($invoice['unit_number']) && $invoice['unit_number'] == 2) ? 'selected' : ''; ?>>📚 Unidad 2</option>
                            <option value="3" <?php echo (isset($invoice['unit_number']) && $invoice['unit_number'] == 3) ? 'selected' : ''; ?>>📚 Unidad 3</option>
                            <option value="4" <?php echo (isset($invoice['unit_number']) && $invoice['unit_number'] == 4) ? 'selected' : ''; ?>>📚 Unidad 4</option>
                        </select>
                    </div>

                    <!-- Mes del Período -->
                    <div class="bg-indigo-50 bg-opacity-50 p-6 rounded-xl border-2 border-indigo-200">
                        <div class="flex items-center mb-4">
                            <div class="bg-indigo-500 bg-opacity-20 p-3 rounded-lg mr-3">
                                <i class="fas fa-calendar-alt text-2xl text-indigo-600"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800">Mes</h3>
                        </div>
                        <select id="period_month" name="period_month" required 
                                class="w-full px-4 py-3 border-2 border-indigo-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 font-medium">
                            <?php 
                            $months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                            $currentMonth = isset($invoice['period_month']) ? $invoice['period_month'] : '';
                            foreach ($months as $month): ?>
                                <option value="<?php echo $month; ?>" <?php echo ($currentMonth == $month) ? 'selected' : ''; ?>><?php echo $month; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Año del Período -->
                    <div class="bg-blue-50 bg-opacity-50 p-6 rounded-xl border-2 border-blue-200">
                        <div class="flex items-center mb-4">
                            <div class="bg-blue-500 bg-opacity-20 p-3 rounded-lg mr-3">
                                <i class="fas fa-calendar text-2xl text-blue-600"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800">Año</h3>
                        </div>
                        <input type="number" id="period_year" name="period_year" 
                               value="<?php echo isset($invoice['period_year']) ? htmlspecialchars($invoice['period_year']) : date('Y'); ?>" 
                               min="2019" max="2030" required 
                               class="w-full px-4 py-3 border-2 border-blue-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 text-center font-semibold">
                    </div>
                </div>

                <!-- Grid de Fecha, Monto y Estado -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Fecha de Factura -->
                    <div class="bg-teal-50 bg-opacity-50 p-6 rounded-xl border-2 border-teal-200">
                        <div class="flex items-center mb-4">
                            <div class="bg-teal-500 bg-opacity-20 p-3 rounded-lg mr-3">
                                <i class="fas fa-calendar-day text-2xl text-teal-600"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800">Fecha</h3>
                        </div>
                        <input type="date" id="invoice_date" name="invoice_date" 
                               value="<?php echo isset($invoice['invoice_date']) ? htmlspecialchars($invoice['invoice_date']) : date('Y-m-d'); ?>" 
                               required 
                               class="w-full px-4 py-3 border-2 border-teal-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition duration-200">
                    </div>

                    <!-- Monto -->
                    <div class="bg-green-50 bg-opacity-50 p-6 rounded-xl border-2 border-green-200">
                        <div class="flex items-center mb-4">
                            <div class="bg-green-500 bg-opacity-20 p-3 rounded-lg mr-3">
                                <i class="fas fa-dollar-sign text-2xl text-green-600"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800">Monto</h3>
                        </div>
                        <input type="number" id="amount" name="amount" value="<?php echo htmlspecialchars($invoice['amount']); ?>" step="0.01" required class="w-full px-4 py-3 border-2 border-green-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200 text-center font-semibold text-xl" placeholder="0.00">
                    </div>
                    
                    <!-- Estado -->
                    <div class="bg-cyan-50 bg-opacity-50 p-6 rounded-xl border-2 border-cyan-200">
                        <div class="flex items-center mb-4">
                            <div class="bg-cyan-500 bg-opacity-20 p-3 rounded-lg mr-3">
                                <i class="fas fa-check-circle text-2xl text-cyan-600"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800">Estado</h3>
                        </div>
                        
                        <?php if ($isProfessor): ?>
                            <!-- Estado bloqueado para profesores -->
                            <input type="hidden" name="status" value="<?php echo htmlspecialchars($invoice['status']); ?>">
                            <div class="w-full px-4 py-3 rounded-xl bg-gradient-to-r from-cyan-50 to-blue-50 border-2 border-cyan-300 text-gray-800 font-semibold flex items-center gap-3">
                                <i class="fas fa-hourglass-half text-cyan-600 text-xl"></i>
                                <span class="text-lg">⏳ <?php echo htmlspecialchars($invoice['status']); ?></span>
                                <i class="fas fa-lock text-cyan-400 ml-auto"></i>
                            </div>
                            <p class="text-xs text-cyan-600 mt-2 text-center">Estado actual (no editable)</p>
                        <?php else: ?>
                            <!-- Select normal para administradores -->
                            <select id="status" name="status" required class="w-full px-4 py-3 border-2 border-cyan-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition duration-200 font-medium">
                                <option value="Pendiente" <?php echo ($invoice['status'] == 'Pendiente') ? 'selected' : ''; ?>>⏳ Pendiente</option>
                                <option value="Pagada" <?php echo ($invoice['status'] == 'Pagada') ? 'selected' : ''; ?>>✅ Pagada</option>
                                <option value="Rechazada" <?php echo ($invoice['status'] == 'Rechazada') ? 'selected' : ''; ?>>❌ Rechazada</option>
                            </select>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Sección Factura PDF -->
                <div class="bg-red-50 bg-opacity-50 p-6 rounded-xl border-2 border-red-200">
                    <div class="flex items-center mb-4">
                        <div class="bg-red-500 bg-opacity-20 p-3 rounded-lg mr-3">
                            <i class="fas fa-file-pdf text-2xl text-red-600"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Factura PDF</h3>
                    </div>
                    
                    <?php if (!empty($invoice['payment_proof_path'])): ?>
                        <div class="bg-white p-4 rounded-lg mb-4 border-2 border-red-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-file-pdf text-3xl text-red-600 mr-3"></i>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">Factura Actual</p>
                                        <p class="text-xs text-gray-500">Archivo PDF adjunto</p>
                                    </div>
                                </div>
                                <a href="<?php echo BASE_PATH . '/' . htmlspecialchars($invoice['payment_proof_path']); ?>" target="_blank" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-red-700 bg-red-100 rounded-lg hover:bg-red-200 transition duration-200">
                                    <i class="fas fa-eye mr-2"></i>
                                    Ver PDF
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="bg-white p-4 rounded-lg mb-4 border-2 border-gray-200">
                            <p class="text-gray-500 text-center py-2">No hay factura cargada aún</p>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Todos pueden subir archivos -->
                    <div class="border-2 border-dashed border-red-300 rounded-xl p-6 text-center bg-white hover:bg-red-50 transition duration-200">
                        <input type="file" id="payment_proof" name="payment_proof" accept=".pdf" class="hidden" onchange="updateFileName(this, 'payment_proof_label')">
                        <label for="payment_proof" class="cursor-pointer">
                            <i class="fas fa-cloud-upload-alt text-4xl text-red-400 mb-3"></i>
                            <p class="text-sm font-semibold text-gray-700 mb-1" id="payment_proof_label">
                                <?php echo !empty($invoice['payment_proof_path']) ? 'Reemplazar Factura PDF' : 'Subir Factura PDF'; ?>
                            </p>
                            <p class="text-xs text-gray-500">Arrastra tu archivo aquí o haz clic para seleccionar</p>
                            <p class="text-xs text-gray-400 mt-2">Solo archivos PDF (máximo 10MB)</p>
                        </label>
                    </div>
                </div>
                
                <!-- Sección Comprobante PDF -->
                <div class="bg-orange-50 bg-opacity-50 p-6 rounded-xl border-2 border-orange-200">
                    <div class="flex items-center mb-4">
                        <div class="bg-orange-500 bg-opacity-20 p-3 rounded-lg mr-3">
                            <i class="fas fa-file-invoice text-2xl text-orange-600"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Comprobante PDF</h3>
                    </div>
                    
                    <?php if (!empty($invoice['comprobante_path'])): ?>
                        <div class="bg-white p-4 rounded-lg mb-4 border-2 border-orange-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-file-pdf text-3xl text-orange-600 mr-3"></i>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">Comprobante Actual</p>
                                        <p class="text-xs text-gray-500">Archivo PDF adjunto</p>
                                    </div>
                                </div>
                                <a href="<?php echo BASE_PATH . '/' . htmlspecialchars($invoice['comprobante_path']); ?>" target="_blank" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-orange-700 bg-orange-100 rounded-lg hover:bg-orange-200 transition duration-200">
                                    <i class="fas fa-eye mr-2"></i>
                                    Ver PDF
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="bg-white p-4 rounded-lg mb-4 border-2 border-gray-200">
                            <p class="text-gray-500 text-center py-2">No hay comprobante cargado aún</p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!$isProfessor): ?>
                    <!-- Solo administradores pueden subir comprobantes -->
                    <div class="border-2 border-dashed border-orange-300 rounded-xl p-6 text-center bg-white hover:bg-orange-50 transition duration-200">
                        <input type="file" id="comprobante" name="comprobante" accept=".pdf" class="hidden" onchange="updateFileName(this, 'comprobante_label')">
                        <label for="comprobante" class="cursor-pointer">
                            <i class="fas fa-cloud-upload-alt text-4xl text-orange-400 mb-3"></i>
                            <p class="text-sm font-semibold text-gray-700 mb-1" id="comprobante_label">
                                <?php echo !empty($invoice['comprobante_path']) ? 'Reemplazar Comprobante PDF' : 'Subir Comprobante PDF'; ?>
                            </p>
                            <p class="text-xs text-gray-500">Arrastra tu archivo aquí o haz clic para seleccionar</p>
                            <p class="text-xs text-gray-400 mt-2">Solo archivos PDF (máximo 10MB)</p>
                        </label>
                    </div>
                    <?php else: ?>
                    <!-- Mensaje para profesores: solo visualización -->
                    <div class="bg-white rounded-xl p-6 text-center border-2 border-orange-200">
                        <i class="fas fa-lock text-4xl text-orange-300 mb-3"></i>
                        <p class="text-gray-700 mb-2">El comprobante es gestionado por el administrador</p>
                        <p class="text-sm text-orange-600">Solo puedes visualizar el archivo si ha sido cargado</p>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Sección Observación -->
                <div class="bg-yellow-50 bg-opacity-50 p-6 rounded-xl border-2 border-yellow-200">
                    <div class="flex items-center mb-4">
                        <div class="bg-yellow-500 bg-opacity-20 p-3 rounded-lg mr-3">
                            <i class="fas fa-comment-alt text-2xl text-yellow-600"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Observación</h3>
                    </div>
                    
                    <?php if ($isProfessor): ?>
                        <!-- Solo lectura para profesores -->
                        <?php if (!empty($invoice['observacion'])): ?>
                            <div class="bg-white rounded-xl p-6 border-2 border-yellow-300">
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-quote-left text-yellow-500 text-2xl mt-1"></i>
                                    <p class="text-gray-700 flex-1 italic"><?php echo nl2br(htmlspecialchars($invoice['observacion'])); ?></p>
                                    <i class="fas fa-quote-right text-yellow-500 text-2xl mt-1"></i>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="bg-white rounded-xl p-6 text-center border-2 border-gray-200">
                                <i class="fas fa-comment-slash text-4xl text-gray-300 mb-2"></i>
                                <p class="text-gray-500">Aún no hay observaciones agregadas</p>
                                <p class="text-sm text-gray-400 mt-1">El administrador puede agregar comentarios</p>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <!-- Textarea editable para administradores -->
                        <textarea id="observacion" name="observacion" rows="4" class="w-full px-4 py-3 border-2 border-yellow-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition duration-200 resize-vertical" placeholder="Agregar observaciones o comentarios sobre la factura..."><?php echo isset($invoice['observacion']) ? htmlspecialchars($invoice['observacion']) : ''; ?></textarea>
                    <?php endif; ?>
                </div>

                <!-- Botones de Acción -->
                <div class="flex gap-4 pt-6">
                    <a href="<?php echo BASE_PATH; ?>/invoices" class="flex-1 py-3 px-6 bg-gray-500 text-white rounded-xl shadow-lg hover:bg-gray-600 transition duration-200 text-center font-semibold">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Cancelar
                    </a>
                    <button type="submit" class="flex-1 py-3 px-6 bg-gradient-to-r from-cyan-500 to-blue-500 text-white rounded-xl shadow-lg hover:from-cyan-600 hover:to-blue-600 transition duration-200 font-semibold">
                        <i class="fas fa-save mr-2"></i>
                        Actualizar Factura
                    </button>
                </div>
            </form>
        </main>
    </div>
    
    <script>
        function updateFileName(input, labelId) {
            const label = document.getElementById(labelId);
            if (input.files.length > 0) {
                label.textContent = input.files[0].name;
            }
        }
    </script>

    <script src="<?php echo BASE_PATH; ?>/js/responsive.js"></script>
</body>

</html>