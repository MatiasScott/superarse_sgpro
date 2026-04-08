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
        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-3xl shadow-xl p-8 mb-6 max-w-3xl mx-auto">
            <div class="flex items-center justify-center gap-4">
                <div class="bg-white/20 backdrop-blur-sm p-4 rounded-2xl">
                    <i class="fas fa-file-invoice-dollar text-4xl text-white"></i>
                </div>
                <div class="text-white">
                    <h2 class="text-3xl font-bold mb-1">Nueva Factura</h2>
                    <p class="text-emerald-100 text-sm">Complete la información necesaria para crear la factura</p>
                </div>
            </div>
        </div>

        <main class="max-w-3xl mx-auto bg-white rounded-3xl shadow-xl overflow-hidden">
            <form action="<?php echo BASE_PATH; ?>/invoices/store" method="POST" enctype="multipart/form-data" class="p-8">
                <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                
                <?php
                // Detectar si el usuario actual es un profesor (verificando si está en la lista de profesores)
                $currentUserId = $_SESSION['user_id'] ?? null;
                
                // Buscar si el usuario actual está en la lista de profesores
                $selectedProfessorId = null;
                $selectedProfessorName = '';
                $isProfessor = false;
                
                if (isset($professors) && is_array($professors) && $currentUserId) {
                    foreach ($professors as $prof) {
                        if ($prof['id'] == $currentUserId) {
                            // El usuario actual ES un profesor
                            $isProfessor = true;
                            $selectedProfessorId = $prof['id'];
                            $selectedProfessorName = $prof['name'];
                            break;
                        }
                    }
                }
                ?>
                
                <!-- Sección Profesor Asignado -->
                <div class="bg-blue-50 rounded-2xl p-6 mb-6 border border-blue-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="bg-blue-500 text-white p-3 rounded-xl">
                            <i class="fas fa-user-tie text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Profesor Asignado</h3>
                        <?php if ($isProfessor): ?>
                            <span class="ml-auto bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">
                                <i class="fas fa-check-circle mr-1"></i>Auto-detectado
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($isProfessor && $selectedProfessorId): ?>
                        <!-- Mostrar nombre del profesor actual (solo lectura) -->
                        <input type="hidden" name="professor_id" value="<?php echo htmlspecialchars($selectedProfessorId); ?>">
                        <div class="w-full px-4 py-3 rounded-xl bg-gradient-to-r from-blue-50 to-cyan-50 border-2 border-blue-300 text-gray-800 font-semibold flex items-center gap-3">
                            <i class="fas fa-user-circle text-blue-600 text-2xl"></i>
                            <div>
                                <p class="text-sm text-blue-600 font-medium">Tu nombre de usuario</p>
                                <p class="text-lg"><?php echo htmlspecialchars($selectedProfessorName); ?></p>
                            </div>
                            <i class="fas fa-lock text-blue-400 ml-auto"></i>
                        </div>
                        <p class="text-xs text-blue-600 mt-2 flex items-center gap-1">
                            <i class="fas fa-info-circle"></i>
                            La factura se creará automáticamente a tu nombre
                        </p>
                    <?php else: ?>
                        <!-- Selector normal para administradores -->
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
                            Seleccione el profesor responsable de la factura
                        </p>
                    <?php endif; ?>
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
                        <?php if (isset($paos) && is_array($paos)): ?>
                            <?php foreach ($paos as $pao): ?>
                                <option value="<?php echo htmlspecialchars($pao['id']); ?>">
                                    <?php echo htmlspecialchars($pao['title']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <p class="text-xs text-purple-600 mt-2 flex items-center gap-1">
                        <i class="fas fa-info-circle"></i>
                        Seleccione el Programa Académico Operativo asociado
                    </p>
                </div>

                <!-- Grid de Unidad y Período -->
                <div class="grid md:grid-cols-3 gap-6 mb-6">
                    <!-- Unidad -->
                    <div class="bg-purple-50 rounded-2xl p-6 border border-purple-100">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fas fa-bookmark text-purple-500 text-lg"></i>
                            <label for="unit_number" class="font-bold text-gray-800">Unidad</label>
                        </div>
                        <select id="unit_number" name="unit_number" required 
                                class="w-full px-4 py-3 border-0 rounded-xl bg-white shadow-sm focus:ring-2 focus:ring-purple-400 outline-none text-gray-700 font-medium">
                            <option value="1">📚 Unidad 1</option>
                            <option value="2">📚 Unidad 2</option>
                            <option value="3">📚 Unidad 3</option>
                            <option value="4">📚 Unidad 4</option>
                        </select>
                    </div>

                    <!-- Mes del Período -->
                    <div class="bg-indigo-50 rounded-2xl p-6 border border-indigo-100">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fas fa-calendar-alt text-indigo-500 text-lg"></i>
                            <label for="period_month" class="font-bold text-gray-800">Mes</label>
                        </div>
                        <select id="period_month" name="period_month" required 
                                class="w-full px-4 py-3 border-0 rounded-xl bg-white shadow-sm focus:ring-2 focus:ring-indigo-400 outline-none text-gray-700 font-medium">
                            <option value="Enero">Enero</option>
                            <option value="Febrero">Febrero</option>
                            <option value="Marzo">Marzo</option>
                            <option value="Abril">Abril</option>
                            <option value="Mayo">Mayo</option>
                            <option value="Junio">Junio</option>
                            <option value="Julio">Julio</option>
                            <option value="Agosto">Agosto</option>
                            <option value="Septiembre">Septiembre</option>
                            <option value="Octubre">Octubre</option>
                            <option value="Noviembre">Noviembre</option>
                            <option value="Diciembre">Diciembre</option>
                        </select>
                    </div>

                    <!-- Año del Período -->
                    <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fas fa-calendar text-blue-500 text-lg"></i>
                            <label for="period_year" class="font-bold text-gray-800">Año</label>
                        </div>
                        <input type="number" id="period_year" name="period_year" 
                               value="<?php echo date('Y'); ?>" 
                               min="2019" max="2030" required 
                               class="w-full px-4 py-3 border-0 rounded-xl bg-white shadow-sm focus:ring-2 focus:ring-blue-400 outline-none text-gray-700 text-center font-semibold">
                    </div>
                </div>

                <!-- Grid de Fecha, Monto y Estado -->
                <div class="grid md:grid-cols-3 gap-6 mb-6">
                    <!-- Fecha de Factura -->
                    <div class="bg-teal-50 rounded-2xl p-6 border border-teal-100">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fas fa-calendar-day text-teal-500 text-lg"></i>
                            <label for="invoice_date" class="font-bold text-gray-800">Fecha</label>
                        </div>
                        <input type="date" id="invoice_date" name="invoice_date" 
                               value="<?php echo date('Y-m-d'); ?>" required 
                               class="w-full px-4 py-3 border-0 rounded-xl bg-white shadow-sm focus:ring-2 focus:ring-teal-400 outline-none text-gray-700">
                        <p class="text-xs text-teal-600 mt-2 text-center">Fecha de emisión</p>
                    </div>

                    <!-- Monto -->
                    <div class="bg-green-50 rounded-2xl p-6 border border-green-100">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fas fa-dollar-sign text-green-500 text-lg"></i>
                            <label for="amount" class="font-bold text-gray-800">Monto</label>
                        </div>
                        <input type="number" id="amount" name="amount" step="0.01" 
                               class="w-full px-4 py-3 border-0 rounded-xl bg-white shadow-sm focus:ring-2 focus:ring-green-400 outline-none text-gray-700 text-center font-semibold text-xl" 
                               placeholder="0.00" required>
                        <p class="text-xs text-green-600 mt-2 text-center">Monto total</p>
                    </div>

                    <!-- Estado -->
                    <div class="bg-cyan-50 rounded-2xl p-6 border border-cyan-100">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fas fa-check-circle text-cyan-500 text-lg"></i>
                            <label for="status" class="font-bold text-gray-800">Estado</label>
                        </div>
                        
                        <?php if ($isProfessor): ?>
                            <!-- Estado bloqueado para profesores -->
                            <input type="hidden" name="status" value="Pendiente">
                            <div class="w-full px-4 py-3 rounded-xl bg-gradient-to-r from-cyan-50 to-blue-50 border-2 border-cyan-300 text-gray-800 font-semibold flex items-center gap-3">
                                <i class="fas fa-hourglass-half text-cyan-600 text-xl"></i>
                                <span class="text-lg">⏳ Pendiente</span>
                                <i class="fas fa-lock text-cyan-400 ml-auto"></i>
                            </div>
                            <p class="text-xs text-cyan-600 mt-2 text-center">Estado por defecto</p>
                        <?php else: ?>
                            <!-- Select normal para administradores -->
                            <select id="status" name="status" required 
                                    class="w-full px-4 py-3 border-0 rounded-xl bg-white shadow-sm focus:ring-2 focus:ring-cyan-400 outline-none text-gray-700 font-medium">
                                <option value="Pendiente">⏳ Pendiente</option>
                                <option value="Pagada">✅ Pagada</option>
                                <option value="Rechazada">❌ Rechazada</option>
                            </select>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Factura PDF - Todos pueden subir -->
                <div class="bg-red-50 rounded-2xl p-6 mb-6 border border-red-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="bg-red-500 text-white p-3 rounded-xl">
                            <i class="fas fa-file-invoice text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Factura (PDF)</h3>
                        <?php if ($isProfessor): ?>
                            <span class="ml-auto bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">
                                <i class="fas fa-upload mr-1"></i>Puedes subir tu factura
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="relative border-2 border-dashed border-red-200 rounded-xl p-8 hover:border-red-400 transition-colors duration-200 bg-white">
                        <input type="file" id="payment_proof" name="payment_proof" accept=".pdf"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                               onchange="displayFileInBox(this, 'payment_proof_preview', 'payment_proof_upload')">
                        <div id="payment_proof_upload" class="text-center">
                            <i class="fas fa-cloud-upload-alt text-5xl text-red-400 mb-3"></i>
                            <p class="text-sm text-gray-600 mb-1">
                                <span class="font-semibold text-red-600">Elegir archivo PDF</span> o arrastrar aquí
                            </p>
                            <p class="text-xs text-gray-500">Solo archivos PDF (Máximo 10MB)</p>
                        </div>
                        <div id="payment_proof_preview" class="hidden">
                            <div class="flex items-center justify-between bg-gradient-to-r from-red-50 to-pink-50 p-4 rounded-lg border-2 border-red-300">
                                <div class="flex items-center gap-3">
                                    <div class="bg-red-500 p-3 rounded-lg">
                                        <i class="fas fa-file-pdf text-white text-2xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-red-600 font-semibold">Archivo seleccionado:</p>
                                        <p class="text-sm font-bold text-gray-800 file-name"></p>
                                    </div>
                                </div>
                                <button type="button" onclick="clearFileInBox('payment_proof', 'payment_proof_preview', 'payment_proof_upload')" 
                                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
                                    <i class="fas fa-times"></i>
                                    <span class="text-sm font-semibold">Cambiar</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (!$isProfessor): ?>
                <!-- Comprobante PDF (solo administradores) -->
                <div class="bg-orange-50 rounded-2xl p-6 mb-6 border border-orange-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="bg-orange-500 text-white p-3 rounded-xl">
                            <i class="fas fa-receipt text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Comprobante (PDF)</h3>
                    </div>
                    <div class="relative border-2 border-dashed border-orange-200 rounded-xl p-8 hover:border-orange-400 transition-colors duration-200 bg-white">
                        <input type="file" id="comprobante" name="comprobante" accept=".pdf"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                               onchange="displayFileInBox(this, 'comprobante_preview', 'comprobante_upload')">
                        <div id="comprobante_upload" class="text-center">
                            <i class="fas fa-cloud-upload-alt text-5xl text-orange-400 mb-3"></i>
                            <p class="text-sm text-gray-600 mb-1">
                                <span class="font-semibold text-orange-600">Elegir archivo PDF</span> o arrastrar aquí
                            </p>
                            <p class="text-xs text-gray-500">Solo archivos PDF (Máximo 10MB)</p>
                        </div>
                        <div id="comprobante_preview" class="hidden">
                            <div class="flex items-center justify-between bg-gradient-to-r from-orange-50 to-amber-50 p-4 rounded-lg border-2 border-orange-300">
                                <div class="flex items-center gap-3">
                                    <div class="bg-orange-500 p-3 rounded-lg">
                                        <i class="fas fa-file-pdf text-white text-2xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-orange-600 font-semibold">Archivo seleccionado:</p>
                                        <p class="text-sm font-bold text-gray-800 file-name"></p>
                                    </div>
                                </div>
                                <button type="button" onclick="clearFileInBox('comprobante', 'comprobante_preview', 'comprobante_upload')" 
                                        class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
                                    <i class="fas fa-times"></i>
                                    <span class="text-sm font-semibold">Cambiar</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <!-- Mensaje de comprobante para profesores -->
                <div class="bg-orange-50 rounded-2xl p-6 mb-6 border border-orange-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="bg-orange-500 text-white p-3 rounded-xl">
                            <i class="fas fa-receipt text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Comprobante (PDF)</h3>
                    </div>
                    <div class="bg-white rounded-xl p-6 text-center">
                        <i class="fas fa-lock text-4xl text-orange-400 mb-3"></i>
                        <p class="text-gray-700 mb-2">El comprobante es gestionado por el administrador</p>
                        <p class="text-sm text-orange-600">Solo puedes visualizar el archivo si ha sido cargado</p>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Observación -->
                <div class="bg-yellow-50 rounded-2xl p-6 mb-8 border border-yellow-100">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fas fa-comment-dots text-yellow-500 text-lg"></i>
                        <label for="observacion" class="font-bold text-gray-800">Observación</label>
                    </div>
                    
                    <?php if (!$isProfessor): ?>
                        <!-- Textarea editable para administradores -->
                        <textarea id="observacion" name="observacion" rows="4" 
                                  class="w-full px-4 py-3 border-0 rounded-xl bg-white shadow-sm focus:ring-2 focus:ring-yellow-400 outline-none resize-none text-gray-700" 
                                  placeholder="Agregar observaciones o comentarios sobre la factura..."></textarea>
                    <?php else: ?>
                        <!-- Mensaje informativo para profesores -->
                        <div class="bg-white rounded-xl p-6 text-center">
                            <i class="fas fa-comment-alt-lines text-4xl text-yellow-400 mb-3"></i>
                            <p class="text-gray-700 mb-2">Las observaciones serán agregadas por el administrador</p>
                            <p class="text-sm text-yellow-600">Podrás ver los comentarios después de que sean añadidos</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Botones de acción -->
                <div class="flex gap-4">
                    <a href="<?php echo BASE_PATH; ?>/invoices" 
                       class="flex-1 py-4 px-6 bg-gray-600 text-white rounded-xl text-center font-bold hover:bg-gray-700 transition-all duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-arrow-left"></i>
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="flex-1 py-4 px-6 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl font-bold hover:from-emerald-600 hover:to-teal-700 transition-all duration-200 flex items-center justify-center gap-2 shadow-lg">
                        <i class="fas fa-save"></i>
                        Guardar Factura
                    </button>
                </div>
            </form>
        </main>
    </div>

    <script>
        function displayFileInBox(input, previewId, uploadId) {
            const preview = document.getElementById(previewId);
            const upload = document.getElementById(uploadId);
            const fileName = preview.querySelector('.file-name');
            
            if (input.files && input.files[0]) {
                fileName.textContent = input.files[0].name;
                upload.classList.add('hidden');
                preview.classList.remove('hidden');
            } else {
                upload.classList.remove('hidden');
                preview.classList.add('hidden');
            }
        }
        
        function clearFileInBox(inputId, previewId, uploadId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            const upload = document.getElementById(uploadId);
            
            input.value = '';
            upload.classList.remove('hidden');
            preview.classList.add('hidden');
        }
    </script>
</body>

</html>

