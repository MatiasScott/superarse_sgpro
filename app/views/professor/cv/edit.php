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
    <?php require_once __DIR__ . '/../../dashboard/index.php'; ?>
    <div class="main-content">
        <header class="page-header">
            <h1>Editar CV</h1>
        </header>
        <main class="bg-white p-6 rounded-lg shadow-md">
            <!-- CV edit content goes here -->
        </main>
    </div>
    <script src="<?php echo BASE_PATH; ?>/js/responsive.js"></script>
</body>
</html>