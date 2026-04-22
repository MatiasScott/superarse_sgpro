<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?php echo BASE_PATH; ?>/img/logo_sgpro.png">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/css/compiled.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gradient-to-br from-emerald-50 via-teal-50 to-cyan-50 font-sans min-h-screen">
    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

    <div class="main-content">
        <main class="max-w-3xl mx-auto">
            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white p-8 rounded-t-2xl shadow-xl">
                <div class="flex items-center justify-center mb-4">
                    <div class="bg-white bg-opacity-20 p-4 rounded-xl backdrop-blur-sm">
                        <i class="fas fa-edit text-5xl"></i>
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-center mb-2">Editar Carrera</h1>
                <p class="text-center text-emerald-100 text-sm">
                    Actualiza los datos de la carrera
                </p>
            </div>

            <form action="<?php echo BASE_PATH; ?>/academic/careers/update/<?php echo htmlspecialchars($career['id']); ?>" method="POST" class="bg-white p-8 rounded-b-2xl shadow-xl space-y-6">
                <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">

                <div style="background-color: rgba(220, 252, 231, 0.6); padding: 1.5rem; border-radius: 0.75rem; border: 2px solid rgb(167, 243, 208);">
                    <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                        <div style="background-color: rgba(16, 185, 129, 0.2); padding: 0.75rem; border-radius: 0.5rem; margin-right: 0.75rem;">
                            <i class="fas fa-graduation-cap" style="font-size: 1.5rem; color: rgb(5, 150, 105);"></i>
                        </div>
                        <h3 style="font-size: 1.125rem; font-weight: bold; color: rgb(31, 41, 55);">Nombre de la Carrera</h3>
                    </div>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($career['name'] ?? ''); ?>" required style="width: 100%; padding: 0.75rem 1rem; border: 2px solid rgb(110, 231, 183); border-radius: 0.5rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);" placeholder="Ejemplo: Ingeniería en Sistemas">
                </div>

                <div style="background-color: rgba(224, 242, 254, 0.6); padding: 1.5rem; border-radius: 0.75rem; border: 2px solid rgb(186, 230, 253);">
                    <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                        <div style="background-color: rgba(14, 165, 233, 0.2); padding: 0.75rem; border-radius: 0.5rem; margin-right: 0.75rem;">
                            <i class="fas fa-code" style="font-size: 1.5rem; color: rgb(3, 105, 161);"></i>
                        </div>
                        <h3 style="font-size: 1.125rem; font-weight: bold; color: rgb(31, 41, 55);">Código</h3>
                    </div>
                    <input type="text" id="code" name="code" value="<?php echo htmlspecialchars($career['code'] ?? ''); ?>" style="width: 100%; padding: 0.75rem 1rem; border: 2px solid rgb(125, 211, 252); border-radius: 0.5rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);" placeholder="Ejemplo: ISW-01">
                </div>

                <div style="background-color: rgba(243, 244, 246, 0.8); padding: 1.5rem; border-radius: 0.75rem; border: 2px solid rgb(229, 231, 235);">
                    <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                        <div style="background-color: rgba(107, 114, 128, 0.2); padding: 0.75rem; border-radius: 0.5rem; margin-right: 0.75rem;">
                            <i class="fas fa-align-left" style="font-size: 1.5rem; color: rgb(75, 85, 99);"></i>
                        </div>
                        <h3 style="font-size: 1.125rem; font-weight: bold; color: rgb(31, 41, 55);">Descripción</h3>
                    </div>
                    <textarea id="description" name="description" rows="4" style="width: 100%; padding: 0.75rem 1rem; border: 2px solid rgb(209, 213, 219); border-radius: 0.5rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);" placeholder="Descripción opcional de la carrera"><?php echo htmlspecialchars($career['description'] ?? ''); ?></textarea>
                </div>

                <div class="flex gap-3">
                    <a href="<?php echo BASE_PATH; ?>/academic/careers" class="flex-1 px-4 py-3 rounded-lg bg-gray-500 hover:bg-gray-600 text-white text-center font-semibold">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Cancelar
                    </a>
                    <button type="submit" class="flex-1 px-4 py-3 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold">
                        <i class="fas fa-save mr-2"></i>
                        Actualizar Carrera
                    </button>
                </div>
            </form>
        </main>
    </div>

    <script src="<?php echo BASE_PATH; ?>/js/responsive.js"></script>
</body>

</html>
