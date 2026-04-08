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
    <?php
    require_once __DIR__ . '/../../helpers/PermissionHelper.php';
    $canManagePao = PermissionHelper::can('pao', 'manage_all', $roles ?? null);
    ?>
    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>
    <?php if ($canManagePao){ ?>
    <div class="main-content">
        <?php } ?>
        <main class="max-w-3xl mx-auto">
            <!-- Header con Gradiente -->
            <div class="bg-gradient-to-r from-pink-500 to-purple-500 text-white p-8 rounded-t-2xl shadow-xl">
                <div class="flex items-center justify-center mb-4">
                    <div class="bg-white bg-opacity-20 p-4 rounded-xl backdrop-blur-sm">
                        <i class="fas fa-edit text-5xl"></i>
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-center mb-2">Editar PAO</h1>
                <p class="text-center text-pink-100 text-sm">
                    Modifique la información del Programa Académico Operativo
                </p>
            </div>

            <form action="<?php echo BASE_PATH; ?>/pao/update/<?php echo htmlspecialchars($pao['id']); ?>" method="POST" class="bg-white p-8 rounded-b-2xl shadow-xl space-y-6">
                
                <!-- Sección Nombre del PAO -->
                <div style="background-color: rgba(243, 232, 255, 0.5); padding: 1.5rem; border-radius: 0.75rem; border: 2px solid rgb(233, 213, 255);">
                    <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                        <div style="background-color: rgba(168, 85, 247, 0.2); padding: 0.75rem; border-radius: 0.5rem; margin-right: 0.75rem;">
                            <i class="fas fa-file-alt" style="font-size: 1.5rem; color: rgb(147, 51, 234);"></i>
                        </div>
                        <h3 style="font-size: 1.125rem; font-weight: bold; color: rgb(31, 41, 55);">Nombre del PAO</h3>
                    </div>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($pao['title']); ?>" required style="width: 100%; padding: 0.75rem 1rem; border: 2px solid rgb(216, 180, 254); border-radius: 0.5rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);" placeholder="Ejemplo: PAO 2024-2025">
                    <p style="margin-top: 0.5rem; font-size: 0.75rem; color: rgb(147, 51, 234); display: flex; align-items: center;">
                        <i class="fas fa-info-circle" style="margin-right: 0.25rem;"></i>
                        Ingrese el nombre del Programa Académico Operativo
                    </p>
                </div>

                <!-- Sección Fechas -->
                <div style="background-color: rgba(219, 234, 254, 0.5); padding: 1.5rem; border-radius: 0.75rem; border: 2px solid rgb(191, 219, 254);">
                    <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                        <div style="background-color: rgba(59, 130, 246, 0.2); padding: 0.75rem; border-radius: 0.5rem; margin-right: 0.75rem;">
                            <i class="fas fa-calendar-alt" style="font-size: 1.5rem; color: rgb(37, 99, 235);"></i>
                        </div>
                        <h3 style="font-size: 1.125rem; font-weight: bold; color: rgb(31, 41, 55);">Periodo de Vigencia</h3>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; color: rgb(55, 65, 81); margin-bottom: 0.5rem;">Fecha de Inicio</label>
                            <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($pao['start_date']); ?>" required style="width: 100%; padding: 0.75rem 1rem; border: 2px solid rgb(147, 197, 253); border-radius: 0.5rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; color: rgb(55, 65, 81); margin-bottom: 0.5rem;">Fecha de Fin</label>
                            <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($pao['end_date']); ?>" required style="width: 100%; padding: 0.75rem 1rem; border: 2px solid rgb(147, 197, 253); border-radius: 0.5rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
                        </div>
                    </div>
                    <p style="margin-top: 0.75rem; font-size: 0.75rem; color: rgb(37, 99, 235); display: flex; align-items: center;">
                        <i class="fas fa-info-circle" style="margin-right: 0.25rem;"></i>
                        Defina el periodo de vigencia del PAO
                    </p>
                </div>

                <!-- Botones de Acción -->
                <div style="display: flex; gap: 1rem; padding-top: 1.5rem;">
                    <a href="<?php echo BASE_PATH; ?>/pao" style="flex: 1; padding: 0.75rem 1.5rem; background-color: rgb(107, 114, 128); color: white; border-radius: 0.75rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); text-align: center; font-weight: 600; text-decoration: none; display: inline-block;">
                        <i class="fas fa-arrow-left" style="margin-right: 0.5rem;"></i>
                        Cancelar
                    </a>
                    <button type="submit" style="flex: 1; padding: 0.75rem 1.5rem; background: linear-gradient(to right, rgb(236, 72, 153), rgb(168, 85, 247)); color: white; border-radius: 0.75rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); font-weight: 600; border: none; cursor: pointer;">
                        <i class="fas fa-save" style="margin-right: 0.5rem;"></i>
                        Actualizar PAO
                    </button>
                </div>
            </form>
        </main>
    </div>
    <script src="<?php echo BASE_PATH; ?>/js/responsive.js"></script>
</body>
</html>
