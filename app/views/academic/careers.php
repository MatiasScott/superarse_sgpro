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
</head>
<body class="bg-gray-100 font-sans">
    <?php require_once __DIR__ . '/../dashboard/index.php'; ?>
    <?php if ($_SESSION['user_role'] == 1 || $_SESSION['user_role'] == 4){ ?>   
    <div class="main-content">
        <header class="page-header">
            <h1>Gestión de Carreras</h1>
            <a href="<?php echo BASE_PATH; ?>/academic/careers/create" class="btn-responsive btn-primary">
                + Crear Nueva Carrera
            </a>
        </header>
        <?php } ?>       
        <main class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="table-responsive">
                <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            ID
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Nombre de la Carrera
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($careers) && is_array($careers) && !empty($careers)): ?>
                        <?php foreach ($careers as $career): ?>
                            <tr>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <?php echo htmlspecialchars($career['id']); ?>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <?php echo htmlspecialchars($career['name']); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" class="text-center py-4 text-gray-500">No hay carreras registradas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                </table>
            </div>
        </main>
    </div>
    <script src="<?php echo BASE_PATH; ?>/js/responsive.js"></script>
</body>
</html>