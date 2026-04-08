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
        <main class="max-w-3xl mx-auto">
            <!-- Header con Gradiente -->
            <div class="bg-gradient-to-r from-purple-500 to-indigo-500 text-white p-8 rounded-t-2xl shadow-xl">
                <div class="flex items-center justify-center mb-4">
                    <div class="bg-white bg-opacity-20 p-4 rounded-xl backdrop-blur-sm">
                        <i class="fas fa-edit text-5xl"></i>
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-center mb-2">Editar Materia</h1>
                <p class="text-center text-purple-100 text-sm">
                    Modifica la información de la asignatura académica
                </p>
            </div>

            <form action="<?php echo BASE_PATH; ?>/academic/subjects/update/<?php echo htmlspecialchars($subject['id']); ?>" method="POST" class="bg-white p-8 rounded-b-2xl shadow-xl space-y-6">
                <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                
                <!-- Sección Nombre de la Materia -->
                <div style="background-color: rgba(219, 234, 254, 0.5); padding: 1.5rem; border-radius: 0.75rem; border: 2px solid rgb(191, 219, 254);">
                    <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                        <div style="background-color: rgba(59, 130, 246, 0.2); padding: 0.75rem; border-radius: 0.5rem; margin-right: 0.75rem;">
                            <i class="fas fa-pen" style="font-size: 1.5rem; color: rgb(37, 99, 235);"></i>
                        </div>
                        <h3 style="font-size: 1.125rem; font-weight: bold; color: rgb(31, 41, 55);">Nombre de la Asignatura</h3>
                    </div>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($subject['name']); ?>" required style="width: 100%; padding: 0.75rem 1rem; border: 2px solid rgb(147, 197, 253); border-radius: 0.5rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);" placeholder="Ejemplo: Matemáticas Aplicadas">
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
                    <select id="career_id" name="career_id" required style="width: 100%; padding: 12px 40px 12px 16px; border: 2px solid rgb(216, 180, 254); border-radius: 0.5rem; font-size: 14px; height: 48px; appearance: none; background: white url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27%23666%27 stroke-width=%272%27%3e%3cpolyline points=%276 9 12 15 18 9%27/%3e%3c/svg%3e') no-repeat right 12px center; background-size: 20px; cursor: pointer; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        <?php foreach ($careers as $career): ?>
                            <option value="<?php echo htmlspecialchars($career['id']); ?>" <?php echo ($career['id'] == $subject['career_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($career['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
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
                    <button type="submit" style="flex: 1; padding: 0.75rem 1.5rem; background: linear-gradient(to right, rgb(168, 85, 247), rgb(99, 102, 241)); color: white; border-radius: 0.75rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); font-weight: 600; border: none; cursor: pointer;">
                        <i class="fas fa-save" style="margin-right: 0.5rem;"></i>
                        Actualizar Materia
                    </button>
                </div>
            </form>
        </main>
    </div>

    <script src="<?php echo BASE_PATH; ?>/js/responsive.js"></script>
</body>

</html>