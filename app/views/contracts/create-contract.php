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
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-3xl shadow-xl p-8 mb-6 max-w-3xl mx-auto">
            <div class="flex items-center justify-center gap-4">
                <div class="bg-white/20 backdrop-blur-sm p-4 rounded-2xl">
                    <i class="fas fa-file-contract text-4xl text-white"></i>
                </div>
                <div class="text-white">
                    <h2 class="text-3xl font-bold mb-1">Nuevo Contrato</h2>
                    <p class="text-indigo-100 text-sm">Complete la información necesaria para crear el contrato académico</p>
                </div>
            </div>
        </div>

        <main class="max-w-3xl mx-auto bg-white rounded-3xl shadow-xl overflow-hidden">
            <form action="<?php echo BASE_PATH; ?>/contracts/store" method="POST" enctype="multipart/form-data" class="p-8">
                
                <!-- Sección Profesor Asignado -->
                <div class="bg-blue-50 rounded-2xl p-6 mb-6 border border-blue-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="bg-blue-500 text-white p-3 rounded-xl">
                            <i class="fas fa-user-tie text-xl"></i>
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
                        Seleccione el profesor responsable del contrato
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

                <!-- Grid de Fechas -->
                <div class="grid md:grid-cols-2 gap-6 mb-6">
                    <!-- Fecha de Inicio -->
                    <div class="bg-green-50 rounded-2xl p-6 border border-green-100">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fas fa-calendar-plus text-green-500 text-lg"></i>
                            <label for="start_date" class="font-bold text-gray-800">Fecha de Inicio</label>
                        </div>
                        <input type="date" id="start_date" name="start_date" 
                               class="w-full px-4 py-3 border-0 rounded-xl bg-white shadow-sm focus:ring-2 focus:ring-green-400 outline-none text-gray-700" 
                               required>
                    </div>

                    <!-- Fecha de Fin -->
                    <div class="bg-red-50 rounded-2xl p-6 border border-red-100">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fas fa-calendar-times text-red-500 text-lg"></i>
                            <label for="end_date" class="font-bold text-gray-800">Fecha de Fin</label>
                        </div>
                        <input type="date" id="end_date" name="end_date" 
                               class="w-full px-4 py-3 border-0 rounded-xl bg-white shadow-sm focus:ring-2 focus:ring-red-400 outline-none text-gray-700" 
                               required>
                    </div>
                </div>

                <!-- Estado -->
                <div class="bg-cyan-50 rounded-2xl p-6 mb-6 border border-cyan-100">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fas fa-check-circle text-cyan-500 text-lg"></i>
                        <label for="status" class="font-bold text-gray-800">Estado</label>
                    </div>
                    <select id="status" name="status" required 
                            class="w-full px-4 py-3 border-0 rounded-xl bg-white shadow-sm focus:ring-2 focus:ring-cyan-400 outline-none text-gray-700 font-medium">
                        <option value="Activo">✅ Activo</option>
                        <option value="Finalizado">🏁 Finalizado</option>
                    </select>
                </div>

                <!-- Documento -->
                <div class="bg-orange-50 rounded-2xl p-6 mb-8 border border-orange-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="bg-orange-500 text-white p-3 rounded-xl">
                            <i class="fas fa-file-pdf text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Documento del Contrato</h3>
                    </div>
                    
                    <!-- Preview del archivo seleccionado -->
                    <div id="file-preview" class="hidden mb-4">
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-300 rounded-xl p-6 shadow-md">
                            <div class="flex items-center gap-4">
                                <div class="bg-green-500 text-white p-4 rounded-xl shadow-lg">
                                    <i class="fas fa-file-pdf text-4xl"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-green-800 mb-1">✓ Archivo seleccionado</p>
                                    <p id="file-name-display" class="text-base font-semibold text-gray-800"></p>
                                    <p id="file-size-display" class="text-xs text-gray-600 mt-1"></p>
                                </div>
                                <button type="button" onclick="clearFile()" 
                                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200 flex items-center gap-2">
                                    <i class="fas fa-times"></i>
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Área de subida -->
                    <div id="upload-area" class="relative border-2 border-dashed border-orange-200 rounded-xl p-10 hover:border-orange-400 hover:bg-orange-50/50 transition-all duration-200 bg-white cursor-pointer">
                        <input type="file" id="document" name="document" 
                               accept=".pdf"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                               onchange="handleFileSelect(this, 50)">
                        <div class="text-center pointer-events-none">
                            <div class="mb-4">
                                <i class="fas fa-cloud-upload-alt text-6xl text-orange-400"></i>
                            </div>
                            <p class="text-base text-gray-700 mb-2">
                                <span class="font-bold text-orange-600 text-lg">Elegir archivo PDF</span> <span class="text-gray-500">o arrastrar aquí</span>
                            </p>
                            <p class="text-sm text-gray-500 mt-3">
                                <i class="fas fa-info-circle mr-1"></i>
                                Solo archivos PDF • Máximo 50MB • Opcional
                            </p>
                        </div>
                    </div>
                    <p id="file-error" class="text-sm text-red-600 mt-3 hidden font-semibold"></p>
                </div>

                <!-- Botones de acción -->
                <div class="flex gap-4">
                    <a href="<?php echo BASE_PATH; ?>/contracts" 
                       class="flex-1 py-4 px-6 bg-gray-600 text-white rounded-xl text-center font-bold hover:bg-gray-700 transition-all duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-arrow-left"></i>
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="flex-1 py-4 px-6 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl font-bold hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 flex items-center justify-center gap-2 shadow-lg">
                        <i class="fas fa-save"></i>
                        Guardar Contrato
                    </button>
                </div>
            </form>
        </main>
    </div>

    <script src="<?php echo BASE_PATH; ?>/js/responsive.js"></script>
    <script>
        function handleFileSelect(input, maxSizeMB) {
            const filePreview = document.getElementById('file-preview');
            const uploadArea = document.getElementById('upload-area');
            const fileNameDisplay = document.getElementById('file-name-display');
            const fileSizeDisplay = document.getElementById('file-size-display');
            const fileError = document.getElementById('file-error');
            
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const maxSize = maxSizeMB * 1024 * 1024;
                const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                
                if (file.size > maxSize) {
                    fileError.innerHTML = `<i class="fas fa-exclamation-triangle mr-2"></i>El archivo es demasiado grande. Máximo: ${maxSizeMB}MB • Actual: ${fileSizeMB}MB`;
                    fileError.classList.remove('hidden');
                    filePreview.classList.add('hidden');
                    uploadArea.classList.remove('hidden');
                    input.value = '';
                } else {
                    fileNameDisplay.textContent = file.name;
                    fileSizeDisplay.innerHTML = `<i class="fas fa-weight mr-1"></i>Tamaño: ${fileSizeMB} MB`;
                    filePreview.classList.remove('hidden');
                    uploadArea.classList.add('hidden');
                    fileError.classList.add('hidden');
                }
            }
        }

        function clearFile() {
            const input = document.getElementById('document');
            const filePreview = document.getElementById('file-preview');
            const uploadArea = document.getElementById('upload-area');
            const fileError = document.getElementById('file-error');
            
            input.value = '';
            filePreview.classList.add('hidden');
            uploadArea.classList.remove('hidden');
            fileError.classList.add('hidden');
        }
    </script>
</body>

</html>