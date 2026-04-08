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
    <style>
        .form-section {
            display: none;
        }

        .form-section.active {
            display: block;
        }

        .tab-button.active {
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            color: white;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);
        }

        .tab-button {
            transition: all 0.3s ease;
        }

        .tab-button:hover {
            transform: translateY(-2px);
        }

        .subtab-button.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: #667eea;
            box-shadow: 0 2px 4px rgba(102, 126, 234, 0.3);
        }

        .subtab-button {
            transition: all 0.3s ease;
            border-color: #e5e7eb;
        }

        .subtab-button:hover {
            border-color: #667eea;
            color: #667eea;
            background-color: #f3f4f6;
        }

        .sidebar-item-text {
            color: #ffffff;
        }

        .sidebar-item-text-logout {
            color: #f87171;
        }

        @media (max-width: 640px) {
            .tab-button {
                font-size: 0.7rem;
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }

            .tab-button .emoji {
                display: none;
            }
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="date"],
        textarea,
        select {
            font-size: 0.875rem;
            padding: 0.5rem 0.75rem;
        }

        label {
            font-size: 0.8rem;
            margin-bottom: 0.25rem;
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

<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 font-sans min-h-screen">

    <?php require_once __DIR__ . '/../../partials/sidebar.php'; ?>

    <?php
    $oldProfessorCv = $_SESSION['old_professor_cv'] ?? [];
    $professorCv = isset($professorCv) && is_array($professorCv) ? $professorCv : [];
    $oldProfessorCv = is_array($oldProfessorCv) ? $oldProfessorCv : [];
    $professorCv = array_merge($professorCv, $oldProfessorCv);
    ?>

    <div class="main-content">
        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="max-w-5xl mx-auto mb-4">
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl shadow-sm">
                    <p class="text-sm text-red-700"><?php echo htmlspecialchars($_SESSION['flash_error']); ?></p>
                </div>
            </div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['flash_success'])): ?>
            <div class="max-w-5xl mx-auto mb-4">
                <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-xl shadow-sm">
                    <p class="text-sm text-green-700"><?php echo htmlspecialchars($_SESSION['flash_success']); ?></p>
                </div>
            </div>
            <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>

        <?php unset($_SESSION['old_professor_cv']); ?>

        <header class="mb-8">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl shadow-xl p-8 text-white">
                <div class="flex items-center space-x-4">
                    <div class="bg-white/20 backdrop-blur-sm p-4 rounded-xl">
                        <i class="fas fa-user-circle text-4xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold">Mi Perfil</h1>
                        <p class="text-blue-100 mt-1">Información completa del currículum vitae</p>
                    </div>
                </div>
            </div>
        </header>

        <main class="bg-white rounded-2xl shadow-xl overflow-hidden border-2 border-gray-200">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
                <nav class="flex space-x-2 sm:space-x-3 px-4 sm:px-6 pt-4 pb-3 overflow-x-auto" aria-label="Tabs">
                    <button id="tab-personales" class="tab-button active shrink-0 px-4 py-3 text-sm font-semibold text-gray-600 hover:text-blue-600 transition-all duration-300 rounded-xl whitespace-nowrap flex items-center space-x-2">
                        <i class="fas fa-user"></i>
                        <span>Datos Personales</span>
                    </button>
                    <button id="tab-instruccion" class="tab-button shrink-0 px-4 py-3 text-sm font-semibold text-gray-600 hover:text-blue-600 transition-all duration-300 rounded-xl whitespace-nowrap flex items-center space-x-2">
                        <i class="fas fa-graduation-cap"></i>
                        <span>Instrucción</span>
                    </button>
                    <button id="tab-experiencia" class="tab-button shrink-0 px-4 py-3 text-sm font-semibold text-gray-600 hover:text-blue-600 transition-all duration-300 rounded-xl whitespace-nowrap flex items-center space-x-2">
                        <i class="fas fa-briefcase"></i>
                        <span>Experiencia</span>
                    </button>
                    <button id="tab-investigacion" class="tab-button shrink-0 px-4 py-3 text-sm font-semibold text-gray-600 hover:text-blue-600 transition-all duration-300 rounded-xl whitespace-nowrap flex items-center space-x-2">
                        <i class="fas fa-microscope"></i>
                        <span>Investigación</span>
                    </button>
                    <button id="tab-referencias" class="tab-button shrink-0 px-4 py-3 text-sm font-semibold text-gray-600 hover:text-blue-600 transition-all duration-300 rounded-xl whitespace-nowrap flex items-center space-x-2">
                        <i class="fas fa-address-book"></i>
                        <span>Referencias</span>
                    </button>
                </nav>
            </div>

            <div id="section-personales" class="form-section active p-6">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 mb-6 border-l-4 border-blue-600">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-xl w-10 h-10 flex items-center justify-center mr-3 shadow-md">
                            <i class="fas fa-user"></i>
                        </div>
                        Datos Personales
                    </h2>
                    <p class="text-sm text-gray-600 mt-2 ml-13">Complete su información personal y de contacto</p>
                </div>
                <form action="<?php echo BASE_PATH; ?>/professor/cv/store" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                        <div>
                            <label class="block text-gray-700 text-xs font-semibold mb-1" for="surnames">Apellidos:</label>
                            <input type="text" id="surnames" name="surnames" required class="shadow-sm border border-gray-300 rounded-md w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="<?php echo htmlspecialchars($professorCv['surnames'] ?? ''); ?>">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-xs font-semibold mb-1" for="first_name">Nombres:</label>
                            <input type="text" id="first_name" name="first_name" required class="shadow-sm border border-gray-300 rounded-md w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="<?php echo htmlspecialchars($professorCv['first_name'] ?? ''); ?>">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-xs font-semibold mb-1" for="cedula_passport">Cédula/Pasaporte:</label>
                            <input type="text" id="cedula_passport" name="cedula_passport" required class="shadow-sm border border-gray-300 rounded-md w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="<?php echo htmlspecialchars($professorCv['cedula_passport'] ?? ''); ?>">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-xs font-semibold mb-1" for="nationality">Nacionalidad:</label>
                            <input type="text" id="nationality" name="nationality" class="shadow-sm border border-gray-300 rounded-md w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="<?php echo htmlspecialchars($professorCv['nationality'] ?? ''); ?>">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-xs font-semibold mb-1" for="birth_date">Fecha de Nacimiento:</label>
                            <input type="date" id="birth_date" name="birth_date" class="shadow-sm border border-gray-300 rounded-md w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="<?php echo htmlspecialchars($professorCv['birth_date'] ?? ''); ?>">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-xs font-semibold mb-1" for="city">Ciudad:</label>
                            <input type="text" id="city" name="city" class="shadow-sm border border-gray-300 rounded-md w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="<?php echo htmlspecialchars($professorCv['city'] ?? ''); ?>">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-xs font-semibold mb-1" for="address">Dirección:</label>
                            <input type="text" id="address" name="address" class="shadow-sm border border-gray-300 rounded-md w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="<?php echo htmlspecialchars($professorCv['address'] ?? ''); ?>">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-xs font-semibold mb-1" for="phone">Teléfono Fijo:</label>
                            <input type="tel" id="phone" name="phone" class="shadow-sm border border-gray-300 rounded-md w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="<?php echo htmlspecialchars($professorCv['phone'] ?? ''); ?>">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-xs font-semibold mb-1" for="cell_phone">Celular:</label>
                            <input type="tel" id="cell_phone" name="cell_phone" class="shadow-sm border border-gray-300 rounded-md w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="<?php echo htmlspecialchars($professorCv['cell_phone'] ?? ''); ?>">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-xs font-semibold mb-1" for="email">Correo Electrónico:</label>
                            <input type="email" id="email" name="email" required class="shadow-sm border border-gray-300 rounded-md w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="<?php echo htmlspecialchars($professorCv['email'] ?? ''); ?>">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-xs font-semibold mb-1" for="photo">Fotografía:</label>
                            <?php if (!empty($professorCv['photo_path'])): ?>
                                <img src="<?php echo htmlspecialchars(BASE_PATH . str_replace('/public', '', $professorCv['photo_path'])); ?>"
                                    alt="Foto de perfil"
                                    class="mb-2 w-20 h-20 object-cover rounded-lg shadow-sm border-2 border-gray-200">
                            <?php endif; ?>
                            <input type="file" id="photo" name="photo" class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-gradient-to-r file:from-blue-500 file:to-blue-600 file:text-white hover:file:from-blue-600 hover:file:to-blue-700 file:cursor-pointer file:transition-all">
                        </div>
                    </div>
                    <div class="flex justify-end mt-4">
                        <button type="submit" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold py-2 px-5 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 text-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>

            <div id="section-instruccion" class="form-section p-6">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-4 mb-6 border-l-4 border-green-600">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800 flex items-center">
                                <div class="bg-gradient-to-br from-green-500 to-emerald-600 text-white rounded-xl w-10 h-10 flex items-center justify-center mr-3 shadow-md">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                Formación Académica
                            </h2>
                            <p class="text-sm text-gray-600 mt-2 ml-13">Registre sus títulos y certificaciones</p>
                        </div>
                        <a href="#" id="openModalBtn" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white text-sm font-semibold rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            <i class="fas fa-plus-circle"></i>
                            <span>Añadir Grado</span>
                        </a>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nivel</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Institución</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Título</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Registro SENESCYT</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($educationList as $education): ?>
                                <tr data-id="<?php echo $education['id']; ?>" data-type="education" data-fields="education_level,institution_name,degree_title,senescyt_register">
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($education['education_level']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($education['institution_name']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($education['degree_title']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($education['senescyt_register']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="flex items-center gap-2">
                                            <button onclick="editRow(this)" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Editar
                                            </button>
                                            <button onclick="deleteRecord(<?php echo $education['id']; ?>, 'education')" class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="section-experiencia" class="form-section p-6">
                <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-4 mb-6 border-l-4 border-purple-600">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <div class="bg-gradient-to-br from-purple-500 to-pink-600 text-white rounded-xl w-10 h-10 flex items-center justify-center mr-3 shadow-md">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        Experiencia Profesional
                    </h2>
                    <p class="text-sm text-gray-600 mt-2 ml-13">Docencia, gestión académica y trayectoria profesional</p>
                </div>
                <div class="flex flex-wrap gap-2 mb-6">
                    <button id="subtab-docente" class="subtab-button active px-4 py-2.5 border rounded-xl text-sm font-semibold flex items-center space-x-2">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Docente</span>
                    </button>
                    <button id="subtab-gestion" class="subtab-button px-4 py-2.5 border rounded-xl text-sm font-semibold flex items-center space-x-2">
                        <i class="fas fa-user-tie"></i>
                        <span>Gestión Académica</span>
                    </button>
                    <button id="subtab-profesional" class="subtab-button px-4 py-2.5 border rounded-xl text-sm font-semibold flex items-center space-x-2">
                        <i class="fas fa-building"></i>
                        <span>Profesional</span>
                    </button>
                </div>

                <div id="subsection-docente" class="sub-section active overflow-x-auto">
                    <a href="#" id="openDocenteModalBtn" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-purple-500 to-purple-600 text-white text-sm font-semibold rounded-xl hover:from-purple-600 hover:to-purple-700 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 mb-4">
                        <i class="fas fa-plus-circle"></i>
                        <span>Añadir Experiencia Docente</span>
                    </a>
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Desde</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Hasta</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">IES</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Denominación</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Asignaturas</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($teachingExperienceList as $exp): ?>
                                <tr data-id="<?php echo $exp['id']; ?>" data-type="teaching-experience" data-fields="start_date,end_date,ies,denomination,subjects">
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($exp['start_date']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($exp['end_date']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($exp['ies']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($exp['denomination']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($exp['subjects']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="flex items-center gap-2">
                                            <button onclick="editRow(this)" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Editar
                                            </button>
                                            <button onclick="deleteRecord(<?php echo $exp['id']; ?>, 'teaching-experience')" class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div id="subsection-gestion" class="sub-section overflow-x-auto" style="display: none;">
                    <a href="#" id="openGestionModalBtn" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-purple-500 to-purple-600 text-white text-sm font-semibold rounded-xl hover:from-purple-600 hover:to-purple-700 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 mb-4">
                        <i class="fas fa-plus-circle"></i>
                        <span>Añadir Experiencia en Gestión</span>
                    </a>
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Desde</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Hasta</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">IES</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Puesto</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Descripción</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($academicManagementExperienceList as $exp): ?>
                                <tr data-id="<?php echo $exp['id']; ?>" data-type="academic-management" data-fields="start_date,end_date,ies_name,position,activities_description">
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($exp['start_date']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($exp['end_date']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($exp['ies_name']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($exp['position']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($exp['activities_description']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="flex items-center gap-2">
                                            <button onclick="editRow(this)" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Editar
                                            </button>
                                            <button onclick="deleteRecord(<?php echo $exp['id']; ?>, 'academic-management')" class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div id="subsection-profesional" class="sub-section overflow-x-auto" style="display: none;">
                    <a href="#" id="openProfesionalModalBtn" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-purple-500 to-purple-600 text-white text-sm font-semibold rounded-xl hover:from-purple-600 hover:to-purple-700 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 mb-4">
                        <i class="fas fa-plus-circle"></i>
                        <span>Añadir Experiencia Profesional</span>
                    </a>
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Desde</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Hasta</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Empresa</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Puesto</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Descripción</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($professionalExperienceList as $exp): ?>
                                <tr data-id="<?php echo $exp['id']; ?>" data-type="professional-experience" data-fields="start_date,end_date,company_name,position,activities_description">
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($exp['start_date']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($exp['end_date']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($exp['company_name']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($exp['position']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($exp['activities_description']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="flex items-center gap-2">
                                            <button onclick="editRow(this)" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Editar
                                            </button>
                                            <button onclick="deleteRecord(<?php echo $exp['id']; ?>, 'professional-experience')" class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="section-investigacion" class="form-section p-6">
                <div class="bg-gradient-to-r from-indigo-50 to-blue-50 rounded-xl p-4 mb-6 border-l-4 border-indigo-600">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <div class="bg-gradient-to-br from-indigo-500 to-blue-600 text-white rounded-xl w-10 h-10 flex items-center justify-center mr-3 shadow-md">
                            <i class="fas fa-microscope"></i>
                        </div>
                        Investigación y Producción Científica
                    </h2>
                    <p class="text-sm text-gray-600 mt-2 ml-13">Proyectos, publicaciones, ponencias y dirección de tesis</p>
                </div>
                <div class="flex flex-wrap gap-2 mb-6">
                    <button id="subtab-proyectos" class="subtab-button active px-4 py-2.5 border rounded-xl text-sm font-semibold whitespace-nowrap flex items-center space-x-2">
                        <i class="fas fa-project-diagram"></i>
                        <span>Proyectos</span>
                    </button>
                    <button id="subtab-ponencias" class="subtab-button px-4 py-2.5 border rounded-xl text-sm font-semibold whitespace-nowrap flex items-center space-x-2">
                        <i class="fas fa-microphone"></i>
                        <span>Ponencias</span>
                    </button>
                    <button id="subtab-publicaciones" class="subtab-button px-4 py-2.5 border rounded-xl text-sm font-semibold whitespace-nowrap flex items-center space-x-2">
                        <i class="fas fa-book"></i>
                        <span>Publicaciones</span>
                    </button>
                    <button id="subtab-vinculacion" class="subtab-button px-4 py-2.5 border rounded-xl text-sm font-semibold whitespace-nowrap flex items-center space-x-2">
                        <i class="fas fa-handshake"></i>
                        <span>Vinculación</span>
                    </button>
                    <button id="subtab-tesis" class="subtab-button px-4 py-2.5 border rounded-xl text-sm font-semibold whitespace-nowrap flex items-center space-x-2">
                        <i class="fas fa-user-graduate"></i>
                        <span>Tesis Dirigidas</span>
                    </button>
                </div>

                <div id="subsection-proyectos" class="sub-section active overflow-x-auto">
                    <a href="#" id="openProyectosModalBtn" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-indigo-500 to-blue-600 text-white text-sm font-semibold rounded-xl hover:from-indigo-600 hover:to-blue-700 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 mb-4">
                        <i class="fas fa-plus-circle"></i>
                        <span>Añadir Proyecto de Investigación</span>
                    </a>
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Denominación</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ámbito</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Responsabilidad</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Entidad</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Año</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Duración</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($researchProjectsList as $project): ?>
                                <tr data-id="<?php echo $project['id']; ?>" data-type="research-projects" data-fields="denomination,scope,responsibility,entity_name,year,duration">
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($project['denomination']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($project['scope']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($project['responsibility']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($project['entity_name']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($project['year']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($project['duration']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="flex items-center gap-2">
                                            <button onclick="editRow(this)" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Editar
                                            </button>
                                            <button onclick="deleteRecord(<?php echo $project['id']; ?>, 'research-projects')" class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div id="subsection-ponencias" class="sub-section overflow-x-auto" style="display: none;">
                    <a href="#" id="openPonenciasModalBtn" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-indigo-500 to-blue-600 text-white text-sm font-semibold rounded-xl hover:from-indigo-600 hover:to-blue-700 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 mb-4">
                        <i class="fas fa-plus-circle"></i>
                        <span>Añadir Ponencia</span>
                    </a>
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nombre del Evento</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Institución</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Año</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ponencia</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($presentationsList as $presentation): ?>
                                <tr data-id="<?php echo $presentation['id']; ?>" data-type="presentations" data-fields="event_name,institution_name,year,presentation">
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($presentation['event_name']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($presentation['institution_name']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($presentation['year']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($presentation['presentation']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="flex items-center gap-2">
                                            <button onclick="editRow(this)" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Editar
                                            </button>
                                            <button onclick="deleteRecord(<?php echo $presentation['id']; ?>, 'presentations')" class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div id="subsection-publicaciones" class="sub-section overflow-x-auto" style="display: none;">
                    <a href="#" id="openPublicacionesModalBtn" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-indigo-500 to-blue-600 text-white text-sm font-semibold rounded-xl hover:from-indigo-600 hover:to-blue-700 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 mb-4">
                        <i class="fas fa-plus-circle"></i>
                        <span>Añadir Publicación</span>
                    </a>
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tipo</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Título</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Editorial/Revista</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ISBN/ISSN</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Autoría</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($publicationsList as $publication): ?>
                                <tr data-id="<?php echo $publication['id']; ?>" data-type="publications" data-fields="production_type,publication_title,publisher_magazine,isbn_issn,authorship">
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($publication['production_type']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($publication['publication_title']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($publication['publisher_magazine']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($publication['isbn_issn']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($publication['authorship']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="flex items-center gap-2">
                                            <button onclick="editRow(this)" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Editar
                                            </button>
                                            <button onclick="deleteRecord(<?php echo $publication['id']; ?>, 'publications')" class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div id="subsection-vinculacion" class="sub-section overflow-x-auto" style="display: none;">
                    <a href="#" id="openVinculacionModalBtn" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-indigo-500 to-blue-600 text-white text-sm font-semibold rounded-xl hover:from-indigo-600 hover:to-blue-700 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 mb-4">
                        <i class="fas fa-plus-circle"></i>
                        <span>Añadir Proyecto de Vinculación</span>
                    </a>
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Institución</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nombre del Proyecto</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($outreachProjectsList as $project): ?>
                                <tr data-id="<?php echo $project['id']; ?>" data-type="outreach-projects" data-fields="institution_name,project_name">
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($project['institution_name']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($project['project_name']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="flex items-center gap-2">
                                            <button onclick="editRow(this)" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Editar
                                            </button>
                                            <button class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div id="subsection-tesis" class="sub-section overflow-x-auto" style="display: none;">
                    <a href="#" id="openTesisModalBtn" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-indigo-500 to-blue-600 text-white text-sm font-semibold rounded-xl hover:from-indigo-600 hover:to-blue-700 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 mb-4">
                        <i class="fas fa-plus-circle"></i>
                        <span>Añadir Dirección de Tesis</span>
                    </a>
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nombre del Alumno</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Título de Tesis</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Programa Académico</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Universidad</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($thesisDirectionList as $thesis): ?>
                                <tr data-id="<?php echo $thesis['id']; ?>" data-type="thesis-direction" data-fields="student_name,thesis_title,academic_program,university_name">
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($thesis['student_name']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($thesis['thesis_title']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($thesis['academic_program']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($thesis['university_name']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="flex items-center gap-2">
                                            <button onclick="editRow(this)" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Editar
                                            </button>
                                            <button onclick="deleteRecord(<?php echo $thesis['id']; ?>, 'thesis-direction')" class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="section-referencias" class="form-section p-6">
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl p-4 mb-6 border-l-4 border-amber-600">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <div class="bg-gradient-to-br from-amber-500 to-orange-600 text-white rounded-xl w-10 h-10 flex items-center justify-center mr-3 shadow-md">
                            <i class="fas fa-address-book"></i>
                        </div>
                        Referencias
                    </h2>
                    <p class="text-sm text-gray-600 mt-2 ml-13">Contactos laborales y personales de referencia</p>
                </div>
                <div class="flex flex-wrap gap-2 mb-6">
                    <button id="subtab-laborales" class="subtab-button active px-4 py-2.5 border rounded-xl text-sm font-semibold flex items-center space-x-2">
                        <i class="fas fa-briefcase"></i>
                        <span>Laborales</span>
                    </button>
                    <button id="subtab-personales" class="subtab-button px-4 py-2.5 border rounded-xl text-sm font-semibold flex items-center space-x-2">
                        <i class="fas fa-users"></i>
                        <span>Personales</span>
                    </button>
                </div>

                <div id="subsection-laborales" class="sub-section active overflow-x-auto">
                    <a href="#" id="openLaboralesModalBtn" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white text-sm font-semibold rounded-xl hover:from-amber-600 hover:to-orange-700 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 mb-4">
                        <i class="fas fa-plus-circle"></i>
                        <span>Añadir Referencia Laboral</span>
                    </a>
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Persona de Contacto</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Relación / Cargo</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Organización / Empresa</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Número de Contacto</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($workReferencesList as $ref): ?>
                                <tr data-id="<?php echo $ref['id']; ?>" data-type="work-references" data-fields="contact_person,relation_position,organization_company,contact_number">
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($ref['contact_person']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($ref['relation_position']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($ref['organization_company']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($ref['contact_number']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="flex items-center gap-2">
                                            <button onclick="editRow(this)" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Editar
                                            </button>
                                            <button onclick="deleteRecord(<?php echo $ref['id']; ?>, 'work-references')" class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div id="subsection-personales" class="sub-section overflow-x-auto" style="display: none;">
                    <a href="#" id="openPersonalesModalBtn" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white text-sm font-semibold rounded-xl hover:from-amber-600 hover:to-orange-700 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 mb-4">
                        <i class="fas fa-plus-circle"></i>
                        <span>Añadir Referencia Personal</span>
                    </a>
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Persona de Contacto</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tipo de Relación</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Número de Contacto</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($personalReferencesList as $ref): ?>
                                <tr data-id="<?php echo $ref['id']; ?>" data-type="personal-references" data-fields="contact_person,relationship_type,contact_number">
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($ref['contact_person']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($ref['relationship_type']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($ref['contact_number']); ?></td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="flex items-center gap-2">
                                            <button onclick="editRow(this)" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Editar
                                            </button>
                                            <button onclick="deleteRecord(<?php echo $ref['id']; ?>, 'personal-references')" class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal for Adding New Degree -->
    <div id="educationModal" class="fixed inset-0 bg-gray-900 bg-opacity-60 hidden justify-center items-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2" id="modalTitle">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Añadir Nuevo Grado
                </h3>
                <button id="closeModalBtn" class="text-gray-400 hover:text-gray-600 text-3xl leading-none transition-colors">&times;</button>
            </div>
            <form id="educationForm" action="<?php echo BASE_PATH; ?>/professor/education/store" method="POST" class="px-6 py-4">
                <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                <input type="hidden" name="id" id="educationId">
                <div class="mb-3">
                    <label for="education_level" class="block text-gray-700 text-sm font-semibold mb-1.5">Nivel:</label>
                    <input type="text" name="education_level" id="education_level" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all" placeholder="Ej: Doctorado, Maestría, Licenciatura">
                </div>
                <div class="mb-3">
                    <label for="institution_name" class="block text-gray-700 text-sm font-semibold mb-1.5">Institución:</label>
                    <input type="text" name="institution_name" id="institution_name" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all" placeholder="Nombre de la institución educativa">
                </div>
                <div class="mb-3">
                    <label for="degree_title" class="block text-gray-700 text-sm font-semibold mb-1.5">Título:</label>
                    <input type="text" name="degree_title" id="degree_title" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all" placeholder="Título obtenido">
                </div>
                <div class="mb-4">
                    <label for="senescyt_register" class="block text-gray-700 text-sm font-semibold mb-1.5">Registro SENESCYT:</label>
                    <input type="text" name="senescyt_register" id="senescyt_register" class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all" placeholder="Número de registro (opcional)">
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-gray-200">
                    <button type="button" id="cancelBtn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancelar</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 rounded-lg shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="gestionModal" class="fixed inset-0 bg-gray-900 bg-opacity-60 hidden justify-center items-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4 bg-gradient-to-r from-indigo-50 to-blue-50">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2" id="gestionModalTitle">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Añadir Experiencia en Gestión
                </h3>
                <button id="closeGestionModalBtn" class="text-gray-400 hover:text-gray-600 text-3xl leading-none transition-colors">&times;</button>
            </div>
            <form id="gestionForm" action="<?php echo BASE_PATH; ?>/professor/academic-management/store" method="POST" class="px-6 py-4">
                <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                <input type="hidden" name="id" id="gestionId">
                <div class="mb-3">
                    <label for="gestion_start_date" class="block text-gray-700 text-sm font-semibold mb-1.5">Desde:</label>
                    <input type="date" name="start_date" id="gestion_start_date" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                </div>
                <div class="mb-3">
                    <label for="gestion_end_date" class="block text-gray-700 text-sm font-semibold mb-1.5">Hasta:</label>
                    <input type="date" name="end_date" id="gestion_end_date" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                </div>
                <div class="mb-3">
                    <label for="gestion_ies_name" class="block text-gray-700 text-sm font-semibold mb-1.5">IES:</label>
                    <input type="text" name="ies_name" id="gestion_ies_name" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" placeholder="Institución de Educación Superior">
                </div>
                <div class="mb-3">
                    <label for="gestion_position" class="block text-gray-700 text-sm font-semibold mb-1.5">Puesto:</label>
                    <input type="text" name="position" id="gestion_position" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" placeholder="Cargo o puesto en gestión">
                </div>
                <div class="mb-4">
                    <label for="gestion_activities_description" class="block text-gray-700 text-sm font-semibold mb-1.5">Descripción:</label>
                    <textarea name="activities_description" id="gestion_activities_description" rows="3" class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all resize-none" placeholder="Descripción de actividades realizadas"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-gray-200">
                    <button type="button" id="cancelGestionBtn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancelar</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 rounded-lg shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="profesionalModal" class="fixed inset-0 bg-gray-900 bg-opacity-60 hidden justify-center items-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4 bg-gradient-to-r from-blue-50 to-cyan-50">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2" id="profesionalModalTitle">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Añadir Experiencia Profesional
                </h3>
                <button id="closeProfesionalModalBtn" class="text-gray-400 hover:text-gray-600 text-3xl leading-none transition-colors">&times;</button>
            </div>
            <form id="profesionalForm" action="<?php echo BASE_PATH; ?>/professor/professional-experience/store" method="POST" class="px-6 py-4">
                <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                <input type="hidden" name="id" id="profesionalId">
                <div class="mb-3">
                    <label for="profesional_start_date" class="block text-gray-700 text-sm font-semibold mb-1.5">Desde:</label>
                    <input type="date" name="start_date" id="profesional_start_date" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                </div>
                <div class="mb-3">
                    <label for="profesional_end_date" class="block text-gray-700 text-sm font-semibold mb-1.5">Hasta:</label>
                    <input type="date" name="end_date" id="profesional_end_date" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                </div>
                <div class="mb-3">
                    <label for="profesional_company_name" class="block text-gray-700 text-sm font-semibold mb-1.5">Empresa:</label>
                    <input type="text" name="company_name" id="profesional_company_name" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" placeholder="Nombre de la empresa u organización">
                </div>
                <div class="mb-3">
                    <label for="profesional_position" class="block text-gray-700 text-sm font-semibold mb-1.5">Puesto:</label>
                    <input type="text" name="position" id="profesional_position" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" placeholder="Cargo o puesto desempeñado">
                </div>
                <div class="mb-4">
                    <label for="profesional_activities_description" class="block text-gray-700 text-sm font-semibold mb-1.5">Descripción:</label>
                    <textarea name="activities_description" id="profesional_activities_description" rows="3" class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none" placeholder="Descripción de actividades y logros"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-gray-200">
                    <button type="button" id="cancelProfesionalBtn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancelar</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 rounded-lg shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="docenteModal" class="fixed inset-0 bg-gray-900 bg-opacity-60 hidden justify-center items-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4 bg-gradient-to-r from-purple-50 to-violet-50">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2" id="docenteModalTitle">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Añadir Experiencia Docente
                </h3>
                <button id="closeDocenteModalBtn" class="text-gray-400 hover:text-gray-600 text-3xl leading-none transition-colors">&times;</button>
            </div>
            <form id="docenteForm" action="<?php echo BASE_PATH; ?>/professor/teaching-experience/store" method="POST" class="px-6 py-4">
                <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                <input type="hidden" name="id" id="docenteId">
                <div class="mb-3">
                    <label for="docente_start_date" class="block text-gray-700 text-sm font-semibold mb-1.5">Desde:</label>
                    <input type="date" name="start_date" id="docente_start_date" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                </div>
                <div class="mb-3">
                    <label for="docente_end_date" class="block text-gray-700 text-sm font-semibold mb-1.5">Hasta:</label>
                    <input type="date" name="end_date" id="docente_end_date" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                </div>
                <div class="mb-3">
                    <label for="docente_ies_name" class="block text-gray-700 text-sm font-semibold mb-1.5">IES:</label>
                    <input type="text" name="ies_name" id="docente_ies_name" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all" placeholder="Institución de Educación Superior">
                </div>
                <div class="mb-3">
                    <label for="docente_position" class="block text-gray-700 text-sm font-semibold mb-1.5">Denominación:</label>
                    <input type="text" name="position" id="docente_position" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all" placeholder="Cargo o denominación">
                </div>
                <div class="mb-4">
                    <label for="docente_subjects" class="block text-gray-700 text-sm font-semibold mb-1.5">Asignaturas:</label>
                    <textarea name="subjects" id="docente_subjects" rows="3" class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all resize-none" placeholder="Lista de asignaturas impartidas"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-gray-200">
                    <button type="button" id="cancelDocenteBtn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancelar</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 rounded-lg shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="proyectosModal" class="fixed inset-0 bg-gray-900 bg-opacity-60 hidden justify-center items-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4 bg-gradient-to-r from-teal-50 to-cyan-50">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2" id="proyectosModalTitle">
                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Añadir Proyecto de Investigación
                </h3>
                <button id="closeProyectosModalBtn" class="text-gray-400 hover:text-gray-600 text-3xl leading-none transition-colors">&times;</button>
            </div>
            <form id="proyectosForm" action="<?php echo BASE_PATH; ?>/professor/research-projects/store" method="POST" class="px-6 py-4">
                <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                <input type="hidden" name="id" id="proyectosId">
                <div class="mb-3">
                    <label for="proyectos_denomination" class="block text-gray-700 text-sm font-semibold mb-1.5">Denominación:</label>
                    <input type="text" name="denomination" id="proyectos_denomination" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all" placeholder="Nombre del proyecto">
                </div>
                <div class="mb-3">
                    <label for="proyectos_scope" class="block text-gray-700 text-sm font-semibold mb-1.5">Ámbito:</label>
                    <input type="text" name="scope" id="proyectos_scope" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all" placeholder="Nacional, Internacional, etc.">
                </div>
                <div class="mb-3">
                    <label for="proyectos_responsibility" class="block text-gray-700 text-sm font-semibold mb-1.5">Responsabilidad:</label>
                    <input type="text" name="responsibility" id="proyectos_responsibility" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all" placeholder="Director, Colaborador, etc.">
                </div>
                <div class="mb-3">
                    <label for="proyectos_entity" class="block text-gray-700 text-sm font-semibold mb-1.5">Entidad:</label>
                    <input type="text" name="entity_name" id="proyectos_entity" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all" placeholder="Entidad financiadora o ejecutora">
                </div>
                <div class="mb-3">
                    <label for="proyectos_year" class="block text-gray-700 text-sm font-semibold mb-1.5">Año:</label>
                    <input type="text" name="year" id="proyectos_year" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all" placeholder="Año de inicio">
                </div>
                <div class="mb-4">
                    <label for="proyectos_duration" class="block text-gray-700 text-sm font-semibold mb-1.5">Duración:</label>
                    <input type="text" name="duration" id="proyectos_duration" class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all" placeholder="Duración en meses o años">
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-gray-200">
                    <button type="button" id="cancelProyectosBtn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancelar</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-600 hover:to-teal-700 rounded-lg shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="ponenciasModal" class="fixed inset-0 bg-gray-900 bg-opacity-60 hidden justify-center items-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4 bg-gradient-to-r from-pink-50 to-rose-50">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2" id="ponenciasModalTitle">
                    <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Añadir Ponencia
                </h3>
                <button id="closePonenciasModalBtn" class="text-gray-400 hover:text-gray-600 text-3xl leading-none transition-colors">&times;</button>
            </div>
            <form id="ponenciasForm" action="<?php echo BASE_PATH; ?>/professor/presentations/store" method="POST" class="px-6 py-4">
                <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                <input type="hidden" name="id" id="ponenciasId">
                <div class="mb-3">
                    <label for="ponencias_event_name" class="block text-gray-700 text-sm font-semibold mb-1.5">Nombre del Evento:</label>
                    <input type="text" name="event_name" id="ponencias_event_name" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all" placeholder="Congreso, Seminario, etc.">
                </div>
                <div class="mb-3">
                    <label for="ponencias_institution_name" class="block text-gray-700 text-sm font-semibold mb-1.5">Institución:</label>
                    <input type="text" name="institution_name" id="ponencias_institution_name" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all" placeholder="Institución organizadora">
                </div>
                <div class="mb-3">
                    <label for="ponencias_year" class="block text-gray-700 text-sm font-semibold mb-1.5">Año:</label>
                    <input type="text" name="year" id="ponencias_year" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all" placeholder="Año del evento">
                </div>
                <div class="mb-4">
                    <label for="ponencias_presentation" class="block text-gray-700 text-sm font-semibold mb-1.5">Ponencia:</label>
                    <textarea name="presentation" id="ponencias_presentation" rows="3" class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all resize-none" placeholder="Título y descripción de la ponencia"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-gray-200">
                    <button type="button" id="cancelPonenciasBtn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancelar</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-pink-500 to-pink-600 hover:from-pink-600 hover:to-pink-700 rounded-lg shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="publicacionesModal" class="fixed inset-0 bg-gray-900 bg-opacity-60 hidden justify-center items-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4 bg-gradient-to-r from-orange-50 to-amber-50">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2" id="publicacionesModalTitle">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Añadir Publicación
                </h3>
                <button id="closePublicacionesModalBtn" class="text-gray-400 hover:text-gray-600 text-3xl leading-none transition-colors">&times;</button>
            </div>
            <form id="publicacionesForm" action="<?php echo BASE_PATH; ?>/professor/publications/store" method="POST" class="px-6 py-4">
                <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                <input type="hidden" name="id" id="publicacionesId">
                <div class="mb-3">
                    <label for="publicaciones_production_type" class="block text-gray-700 text-sm font-semibold mb-1.5">Tipo:</label>
                    <input type="text" name="production_type" id="publicaciones_production_type" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all" placeholder="Libro, Artículo, Capítulo, etc.">
                </div>
                <div class="mb-3">
                    <label for="publicaciones_title" class="block text-gray-700 text-sm font-semibold mb-1.5">Título:</label>
                    <input type="text" name="publication_title" id="publicaciones_title" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all" placeholder="Título de la publicación">
                </div>
                <div class="mb-3">
                    <label for="publicaciones_publisher" class="block text-gray-700 text-sm font-semibold mb-1.5">Editorial/Revista:</label>
                    <input type="text" name="publisher_magazine" id="publicaciones_publisher" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all" placeholder="Nombre de la editorial o revista">
                </div>
                <div class="mb-3">
                    <label for="publicaciones_isbn" class="block text-gray-700 text-sm font-semibold mb-1.5">ISBN/ISSN:</label>
                    <input type="text" name="isbn_issn" id="publicaciones_isbn" class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all" placeholder="Número ISBN o ISSN">
                </div>
                <div class="mb-4">
                    <label for="publicaciones_authorship" class="block text-gray-700 text-sm font-semibold mb-1.5">Autoría:</label>
                    <input type="text" name="authorship" id="publicaciones_authorship" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all" placeholder="Autor, Coautor, etc.">
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-gray-200">
                    <button type="button" id="cancelPublicacionesBtn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancelar</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 rounded-lg shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="vinculacionModal" class="fixed inset-0 bg-gray-900 bg-opacity-60 hidden justify-center items-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4 bg-gradient-to-r from-violet-50 to-purple-50">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2" id="vinculacionModalTitle">
                    <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Añadir Proyecto de Vinculación
                </h3>
                <button id="closeVinculacionModalBtn" class="text-gray-400 hover:text-gray-600 text-3xl leading-none transition-colors">&times;</button>
            </div>
            <form id="vinculacionForm" action="<?php echo BASE_PATH; ?>/professor/outreach-projects/store" method="POST" class="px-6 py-4">
                <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                <input type="hidden" name="id" id="vinculacionId">
                <div class="mb-3">
                    <label for="vinculacion_institution_name" class="block text-gray-700 text-sm font-semibold mb-1.5">Institución:</label>
                    <input type="text" name="institution_name" id="vinculacion_institution_name" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all" placeholder="Institución beneficiaria">
                </div>
                <div class="mb-4">
                    <label for="vinculacion_project_name" class="block text-gray-700 text-sm font-semibold mb-1.5">Nombre del Proyecto:</label>
                    <input type="text" name="project_name" id="vinculacion_project_name" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all" placeholder="Nombre del proyecto de vinculación">
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-gray-200">
                    <button type="button" id="cancelVinculacionBtn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancelar</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-violet-500 to-violet-600 hover:from-violet-600 hover:to-violet-700 rounded-lg shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="tesisModal" class="fixed inset-0 bg-gray-900 bg-opacity-60 hidden justify-center items-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4 bg-gradient-to-r from-sky-50 to-blue-50">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2" id="tesisModalTitle">
                    <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Añadir Dirección de Tesis
                </h3>
                <button id="closeTesisModalBtn" class="text-gray-400 hover:text-gray-600 text-3xl leading-none transition-colors">&times;</button>
            </div>
            <form id="tesisForm" action="<?php echo BASE_PATH; ?>/professor/thesis-direction/store" method="POST" class="px-6 py-4">
                <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                <input type="hidden" name="id" id="tesisId">
                <div class="mb-3">
                    <label for="tesis_student_name" class="block text-gray-700 text-sm font-semibold mb-1.5">Nombre del Alumno:</label>
                    <input type="text" name="student_name" id="tesis_student_name" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all" placeholder="Nombre completo del tesista">
                </div>
                <div class="mb-3">
                    <label for="tesis_thesis_title" class="block text-gray-700 text-sm font-semibold mb-1.5">Título de Tesis:</label>
                    <input type="text" name="thesis_title" id="tesis_thesis_title" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all" placeholder="Título del trabajo de investigación">
                </div>
                <div class="mb-3">
                    <label for="tesis_academic_program" class="block text-gray-700 text-sm font-semibold mb-1.5">Programa Académico:</label>
                    <input type="text" name="academic_program" id="tesis_academic_program" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all" placeholder="Maestría, Doctorado, etc.">
                </div>
                <div class="mb-4">
                    <label for="tesis_university_name" class="block text-gray-700 text-sm font-semibold mb-1.5">Universidad:</label>
                    <input type="text" name="university_name" id="tesis_university_name" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all" placeholder="Nombre de la universidad">
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-gray-200">
                    <button type="button" id="cancelTesisBtn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancelar</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 rounded-lg shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="laboralesModal" class="fixed inset-0 bg-gray-900 bg-opacity-60 hidden justify-center items-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4 bg-gradient-to-r from-amber-50 to-yellow-50">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2" id="laboralesModalTitle">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Añadir Referencia Laboral
                </h3>
                <button id="closeLaboralesModalBtn" class="text-gray-400 hover:text-gray-600 text-3xl leading-none transition-colors">&times;</button>
            </div>
            <form id="laboralesForm" action="<?php echo BASE_PATH; ?>/professor/work-references/store" method="POST" class="px-6 py-4">
                <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                <input type="hidden" name="id" id="laboralesId">
                <div class="mb-3">
                    <label for="laborales_contact_person" class="block text-gray-700 text-sm font-semibold mb-1.5">Persona de Contacto:</label>
                    <input type="text" name="contact_person" id="laborales_contact_person" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all" placeholder="Nombre completo de la referencia">
                </div>
                <div class="mb-3">
                    <label for="laborales_relation_position" class="block text-gray-700 text-sm font-semibold mb-1.5">Relación / Cargo:</label>
                    <input type="text" name="relation_position" id="laborales_relation_position" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all" placeholder="Supervisor, Gerente, etc.">
                </div>
                <div class="mb-3">
                    <label for="laborales_organization_company" class="block text-gray-700 text-sm font-semibold mb-1.5">Organización / Empresa:</label>
                    <input type="text" name="organization_company" id="laborales_organization_company" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all" placeholder="Nombre de la organización">
                </div>
                <div class="mb-4">
                    <label for="laborales_contact_number" class="block text-gray-700 text-sm font-semibold mb-1.5">Número de Contacto:</label>
                    <input type="tel" name="contact_number" id="laborales_contact_number" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all" placeholder="Teléfono o celular">
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-gray-200">
                    <button type="button" id="cancelLaboralesBtn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancelar</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 rounded-lg shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="personalesModal" class="fixed inset-0 bg-gray-900 bg-opacity-60 hidden justify-center items-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4 bg-gradient-to-r from-emerald-50 to-teal-50">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2" id="personalesModalTitle">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Añadir Referencia Personal
                </h3>
                <button id="closePersonalesModalBtn" class="text-gray-400 hover:text-gray-600 text-3xl leading-none transition-colors">&times;</button>
            </div>
            <form id="personalesForm" action="<?php echo BASE_PATH; ?>/professor/personal-references/store" method="POST" class="px-6 py-4">
                <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                <input type="hidden" name="id" id="personalesId">
                <div class="mb-3">
                    <label for="personales_contact_person" class="block text-gray-700 text-sm font-semibold mb-1.5">Persona de Contacto:</label>
                    <input type="text" name="contact_person" id="personales_contact_person" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all" placeholder="Nombre completo de la referencia">
                </div>
                <div class="mb-3">
                    <label for="personales_relationship_type" class="block text-gray-700 text-sm font-semibold mb-1.5">Tipo de Relación:</label>
                    <input type="text" name="relationship_type" id="personales_relationship_type" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all" placeholder="Amigo, Familiar, Conocido, etc.">
                </div>
                <div class="mb-4">
                    <label for="personales_contact_number" class="block text-gray-700 text-sm font-semibold mb-1.5">Número de Contacto:</label>
                    <input type="tel" name="contact_number" id="personales_contact_number" required class="shadow-sm border border-gray-300 rounded-lg w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all" placeholder="Teléfono o celular">
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-gray-200">
                    <button type="button" id="cancelPersonalesBtn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancelar</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 rounded-lg shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript for Tabs and Sub-tabs -->

    <script src="<?php echo BASE_PATH; ?>/js/modales.js"></script>
    <script src="<?php echo BASE_PATH; ?>/js/global.js"></script>
    <script src="<?php echo BASE_PATH; ?>/js/responsive.js"></script>

    <script>
        // Variable para rastrear la fila que se está editando
        let currentEditingRow = null;

        // Función genérica para editar fila
        function editRow(button) {
            const row = button.closest('tr');

            // Si ya hay una fila en edición, cancelar
            if (currentEditingRow && currentEditingRow !== row) {
                cancelEdit(currentEditingRow);
            }

            currentEditingRow = row;
            const cells = row.querySelectorAll('td');
            const actionCell = cells[cells.length - 1]; // Última celda (acciones)

            // Convertir cada celda (excepto la última) en input editable
            for (let i = 0; i < cells.length - 1; i++) {
                const cell = cells[i];
                const originalValue = cell.textContent.trim();
                cell.setAttribute('data-original', originalValue);
                cell.innerHTML = `<input type="text" value="${originalValue}" class="w-full px-2 py-1 border border-blue-400 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">`;
            }

            // Cambiar botones de acción a Guardar/Cancelar
            actionCell.innerHTML = `
                <div class="flex items-center gap-2">
                    <button onclick="saveEdit(this)" class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Guardar
                    </button>
                    <button onclick="cancelEdit(this.closest('tr'))" class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-500 hover:bg-gray-600 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        Cancelar
                    </button>
                </div>
            `;
        }

        // Función para cancelar edición
        function cancelEdit(row) {
            const cells = row.querySelectorAll('td');
            const id = row.getAttribute('data-id');
            const type = row.getAttribute('data-type');

            // Restaurar valores originales
            for (let i = 0; i < cells.length - 1; i++) {
                const cell = cells[i];
                const originalValue = cell.getAttribute('data-original');
                cell.textContent = originalValue;
                cell.removeAttribute('data-original');
            }

            // Restaurar botones originales
            const actionCell = cells[cells.length - 1];
            actionCell.innerHTML = `
                <div class="flex items-center gap-2">
                    <button onclick="editRow(this)" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Editar
                    </button>
                    <button onclick="deleteRecord(${id}, '${type}')" class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Eliminar
                    </button>
                </div>
            `;

            currentEditingRow = null;
        }

        // Función para guardar edición
        function saveEdit(button) {
            const row = button.closest('tr');
            const cells = row.querySelectorAll('td');
            const id = row.getAttribute('data-id');
            const type = row.getAttribute('data-type');

            // Recopilar valores de los inputs
            const formData = new FormData();
            formData.append('id', id);

            const inputs = row.querySelectorAll('input');
            const fieldNames = row.getAttribute('data-fields').split(',');

            inputs.forEach((input, index) => {
                formData.append(fieldNames[index], input.value);
            });

            // Enviar datos al servidor
            fetch(`<?php echo BASE_PATH; ?>/professor/${type}/update/${id}`, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Actualizar celdas con los nuevos valores
                        inputs.forEach((input, index) => {
                            cells[index].textContent = input.value;
                            cells[index].removeAttribute('data-original');
                        });

                        // Restaurar botones
                        const actionCell = cells[cells.length - 1];
                        actionCell.innerHTML = `
                        <div class="flex items-center gap-2">
                            <button onclick="editRow(this)" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Editar
                            </button>
                            <button onclick="deleteRecord(${id}, '${type}')" class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Eliminar
                            </button>
                        </div>
                    `;

                        currentEditingRow = null;

                        // Mostrar mensaje de éxito
                        alert('Registro actualizado correctamente');
                    } else {
                        alert('Error al actualizar: ' + (data.message || 'Error desconocido'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al guardar los cambios');
                });
        }

        // Función unificada para eliminar
        function deleteRecord(id, type) {
            const csrfToken = <?php echo json_encode($_SESSION['csrf_token'] ?? ''); ?>;
            const messages = {
                'education': '¿Está seguro de eliminar este registro de educación?',
                'teaching-experience': '¿Está seguro de eliminar esta experiencia docente?',
                'academic-management': '¿Está seguro de eliminar esta experiencia en gestión académica?',
                'professional-experience': '¿Está seguro de eliminar esta experiencia profesional?',
                'research-projects': '¿Está seguro de eliminar este proyecto de investigación?',
                'presentations': '¿Está seguro de eliminar esta ponencia?',
                'publications': '¿Está seguro de eliminar esta publicación?',
                'outreach-projects': '¿Está seguro de eliminar este proyecto de vinculación?',
                'thesis-direction': '¿Está seguro de eliminar esta dirección de tesis?',
                'work-references': '¿Está seguro de eliminar esta referencia laboral?',
                'personal-references': '¿Está seguro de eliminar esta referencia personal?'
            };

            if (confirm(messages[type] || '¿Está seguro de eliminar este registro?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?php echo BASE_PATH; ?>/professor/' + type + '/delete/' + id;

                const tokenField = document.createElement('input');
                tokenField.type = 'hidden';
                tokenField.name = '_csrf';
                tokenField.value = csrfToken;
                form.appendChild(tokenField);

                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>

</html>