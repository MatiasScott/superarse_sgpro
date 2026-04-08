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
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            justify-content: center;
            align-items: center;
        }
        
        /* Forzar mismo tamaño para input, select y button */
        #name, #career_id, #create-career-btn {
            height: 38px !important;
            min-height: 38px !important;
            max-height: 38px !important;
            line-height: normal !important;
        }
        
        #create-career-btn {
            min-width: 38px !important;
            width: auto !important;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans">
    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

    <div class="main-content">
        <main class="max-w-3xl mx-auto">
            <!-- Header con Gradiente -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-500 text-white p-8 rounded-t-2xl shadow-xl">
                <div class="flex items-center justify-center mb-4">
                    <div class="bg-white bg-opacity-20 p-4 rounded-xl backdrop-blur-sm">
                        <i class="fas fa-book text-5xl"></i>
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-center mb-2">Nueva Materia</h1>
                <p class="text-center text-indigo-100 text-sm">
                    Complete la información necesaria para crear la materia académica
                </p>
            </div>

            <form action="<?php echo BASE_PATH; ?>/academic/subjects/store" method="POST" class="bg-white p-8 rounded-b-2xl shadow-xl space-y-6">
                <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                
                <!-- Sección Nombre de la Materia -->
                <div style="background-color: rgba(219, 234, 254, 0.5); padding: 1.5rem; border-radius: 0.75rem; border: 2px solid rgb(191, 219, 254);">
                    <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                        <div style="background-color: rgba(59, 130, 246, 0.2); padding: 0.75rem; border-radius: 0.5rem; margin-right: 0.75rem;">
                            <i class="fas fa-pen" style="font-size: 1.5rem; color: rgb(37, 99, 235);"></i>
                        </div>
                        <h3 style="font-size: 1.125rem; font-weight: bold; color: rgb(31, 41, 55);">Nombre de la Asignatura</h3>
                    </div>
                    <input type="text" id="name" name="name" required style="width: 100%; padding: 0.75rem 1rem; border: 2px solid rgb(147, 197, 253); border-radius: 0.5rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);" placeholder="Ejemplo: Matemáticas Aplicadas">
                    <p style="margin-top: 0.5rem; font-size: 0.75rem; color: rgb(37, 99, 235); display: flex; align-items: center;">
                        <i class="fas fa-info-circle" style="margin-right: 0.25rem;"></i>
                        Ingrese el nombre completo de la materia
                    </p>
                </div>

                <!-- Sección Carrera -->
                <div style="background-color: rgba(243, 232, 255, 0.5); padding: 1.5rem; border-radius: 0.75rem; border: 2px solid rgb(233, 213, 255);">
                    <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                        <div style="background-color: rgba(168, 85, 247, 0.2); padding: 0.75rem; border-radius: 0.5rem; margin-right: 0.75rem;">
                            <i class="fas fa-graduation-cap" style="font-size: 1.5rem; color: rgb(147, 51, 234);"></i>
                        </div>
                        <h3 style="font-size: 1.125rem; font-weight: bold; color: rgb(31, 41, 55);">Carrera Asociada</h3>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr auto; gap: 0.75rem; width: 100%;">
                        <select id="career_id" name="career_id" required style="width: 100%; padding: 12px 40px 12px 16px; border: 0px solid rgb(216, 180, 254); border-radius: 0.5rem; font-size: 13px; height: 48px; appearance: none; background: white url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27%23666%27 stroke-width=%272%27%3e%3cpolyline points=%276 9 12 15 18 9%27/%3e%3c/svg%3e') no-repeat right 12px center; background-size: 20px; cursor: pointer; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            <option value="">Seleccione una carrera</option>
                            <?php if (isset($careers) && is_array($careers)): ?>
                                <?php foreach ($careers as $career): ?>
                                    <option value="<?php echo htmlspecialchars($career['id']); ?>">
                                        <?php echo htmlspecialchars($career['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <button type="button" id="create-career-btn" style="background: linear-gradient(to right, rgb(168, 85, 247), rgb(147, 51, 234)); color: white; border-radius: 0.5rem; border: none; cursor: pointer; height: 48px; width: 48px; display: flex; align-items: center; justify-content: center;" title="Crear nueva carrera">
                            <i class="fas fa-plus" style="font-size: 1.25rem;"></i>
                        </button>
                    </div>
                    <p style="margin-top: 0.5rem; font-size: 0.75rem; color: rgb(147, 51, 234); display: flex; align-items: center;">
                        <i class="fas fa-info-circle" style="margin-right: 0.25rem;"></i>
                        Seleccione la carrera a la que pertenece esta materia
                    </p>
                </div>

                <!-- Botones de Acción -->
                <div style="display: flex; gap: 1rem; padding-top: 1.5rem;">
                    <a href="<?php echo BASE_PATH; ?>/academic/subjects" style="flex: 1; padding: 0.75rem 1.5rem; background-color: rgb(107, 114, 128); color: white; border-radius: 0.75rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); text-align: center; font-weight: 600; text-decoration: none; display: inline-block;">
                        <i class="fas fa-arrow-left" style="margin-right: 0.5rem;"></i>
                        Cancelar
                    </a>
                    <button type="submit" style="flex: 1; padding: 0.75rem 1.5rem; background: linear-gradient(to right, rgb(99, 102, 241), rgb(168, 85, 247)); color: white; border-radius: 0.75rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); font-weight: 600; border: none; cursor: pointer;">
                        <i class="fas fa-plus-circle" style="margin-right: 0.5rem;"></i>
                        Crear Materia
                    </button>
                </div>
            </form>
        </main>
    </div>

    <div id="create-career-modal" class="modal">
        <div class="bg-white p-4 sm:p-6 rounded-xl shadow-2xl w-full max-w-md mx-4">
            <div class="flex justify-between items-center border-b border-gray-200 pb-3 mb-4">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Crear Nueva Carrera
                </h2>
                <button type="button" id="close-career-modal" class="text-gray-400 hover:text-gray-600 text-2xl leading-none transition-colors">&times;</button>
            </div>
            <form id="create-career-form" class="space-y-4">
                <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                <div>
                    <label for="new_career_name" class="block text-sm font-semibold text-gray-700 mb-1.5">Nombre de la Carrera</label>
                    <input type="text" id="new_career_name" name="name" required class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" placeholder="Ej: Ingeniería en Sistemas">
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-gray-200">
                    <button type="button" id="cancel-career-btn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 rounded-lg shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('create-career-btn').addEventListener('click', function() {
            document.getElementById('create-career-modal').style.display = 'flex';
        });

        document.getElementById('cancel-career-btn').addEventListener('click', function() {
            document.getElementById('create-career-modal').style.display = 'none';
        });

        document.getElementById('close-career-modal').addEventListener('click', function() {
            document.getElementById('create-career-modal').style.display = 'none';
        });

        document.getElementById('create-career-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);

            fetch('<?php echo BASE_PATH; ?>/academic/careers/quick-store', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const select = document.getElementById('career_id');
                        const newOption = new Option(data.name, data.id, true, true);
                        select.add(newOption);
                        document.getElementById('create-career-modal').style.display = 'none';
                        form.reset();
                    } else {
                        alert('Error al crear la carrera: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ocurrió un error al intentar crear la carrera.');
                });
        });
    </script>
</body>

</html>