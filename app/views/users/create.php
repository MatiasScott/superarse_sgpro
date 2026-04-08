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
        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="max-w-3xl mx-auto mt-6">
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl">
                    <p class="text-sm text-red-700"><?php echo htmlspecialchars($_SESSION['flash_error']); ?></p>
                </div>
            </div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>
        <?php if (!empty($_SESSION['flash_success'])): ?>
            <div class="max-w-3xl mx-auto mt-6">
                <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-xl">
                    <p class="text-sm text-green-700"><?php echo htmlspecialchars($_SESSION['flash_success']); ?></p>
                </div>
            </div>
            <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>

        <?php $old = $_SESSION['old_user'] ?? ['name'=>'','email'=>'','role_id'=>'','dedication'=>'','escuela'=>'']; unset($_SESSION['old_user']); ?>
        <main class="max-w-3xl mx-auto">
            <!-- Header con Gradiente -->
            <div class="bg-gradient-to-r from-blue-500 to-indigo-500 text-white p-8 rounded-t-2xl shadow-xl">
                <div class="flex items-center justify-center mb-4">
                    <div class="bg-white bg-opacity-20 p-4 rounded-xl backdrop-blur-sm">
                        <i class="fas fa-user-plus text-5xl"></i>
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-center mb-2">Nuevo Usuario</h1>
                <p class="text-center text-blue-100 text-sm">
                    Complete la información necesaria para crear el usuario en el sistema
                </p>
            </div>

            <form action="<?php echo BASE_PATH; ?>/users/store" method="POST" class="bg-white p-8 rounded-b-2xl shadow-xl space-y-6">
                
                <!-- Sección Información Personal -->
                <div style="background-color: rgba(219, 234, 254, 0.5); padding: 1.5rem; border-radius: 0.75rem; border: 2px solid rgb(191, 219, 254);">
                    <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                        <div style="background-color: rgba(59, 130, 246, 0.2); padding: 0.75rem; border-radius: 0.5rem; margin-right: 0.75rem;">
                            <i class="fas fa-user" style="font-size: 1.5rem; color: rgb(37, 99, 235);"></i>
                        </div>
                        <h3 style="font-size: 1.125rem; font-weight: bold; color: rgb(31, 41, 55);">Información Personal</h3>
                    </div>
                    <input type="text" id="name" name="name" required style="width: 100%; padding: 0.75rem 1rem; border: 2px solid rgb(147, 197, 253); border-radius: 0.5rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);" placeholder="Nombre completo del usuario" value="<?php echo htmlspecialchars($old['name'] ?? ''); ?>">
                    <p style="margin-top: 0.5rem; font-size: 0.75rem; color: rgb(37, 99, 235); display: flex; align-items: center;">
                        <i class="fas fa-info-circle" style="margin-right: 0.25rem;"></i>
                        Ingrese el nombre completo del usuario
                    </p>
                </div>

                <!-- Sección Credenciales -->
                <div style="background-color: rgba(243, 232, 255, 0.5); padding: 1.5rem; border-radius: 0.75rem; border: 2px solid rgb(233, 213, 255);">
                    <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                        <div style="background-color: rgba(168, 85, 247, 0.2); padding: 0.75rem; border-radius: 0.5rem; margin-right: 0.75rem;">
                            <i class="fas fa-key" style="font-size: 1.5rem; color: rgb(147, 51, 234);"></i>
                        </div>
                        <h3 style="font-size: 1.125rem; font-weight: bold; color: rgb(31, 41, 55);">Credenciales de Acceso</h3>
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: rgb(55, 65, 81); margin-bottom: 0.5rem;">Correo Electrónico</label>
                        <input type="email" id="email" name="email" required style="width: 100%; padding: 0.75rem 1rem; border: 2px solid rgb(216, 180, 254); border-radius: 0.5rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);" placeholder="usuario@ejemplo.com" value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: rgb(55, 65, 81); margin-bottom: 0.5rem;">Contraseña</label>
                        <input type="password" id="password" name="password" required style="width: 100%; padding: 0.75rem 1rem; border: 2px solid rgb(216, 180, 254); border-radius: 0.5rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);" placeholder="Contraseña segura">
                    </div>
                    <p style="margin-top: 0.5rem; font-size: 0.75rem; color: rgb(147, 51, 234); display: flex; align-items: center;">
                        <i class="fas fa-info-circle" style="margin-right: 0.25rem;"></i>
                        El usuario utilizará estos datos para iniciar sesión
                    </p>
                </div>

                <!-- Sección Rol -->
                <div style="background-color: rgba(254, 243, 199, 0.5); padding: 1.5rem; border-radius: 0.75rem; border: 2px solid rgb(253, 224, 71);">
                    <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                        <div style="background-color: rgba(234, 179, 8, 0.2); padding: 0.75rem; border-radius: 0.5rem; margin-right: 0.75rem;">
                            <i class="fas fa-shield-alt" style="font-size: 1.5rem; color: rgb(202, 138, 4);"></i>
                        </div>
                        <h3 style="font-size: 1.125rem; font-weight: bold; color: rgb(31, 41, 55);">Rol del Usuario</h3>
                    </div>
                    <select id="role_id" name="role_id" required style="width: 100%; padding: 12px 40px 12px 16px; border: 2px solid rgb(250, 204, 21); border-radius: 0.5rem; font-size: 14px; height: 48px; appearance: none; background: white url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27%23666%27 stroke-width=%272%27%3e%3cpolyline points=%276 9 12 15 18 9%27/%3e%3c/svg%3e') no-repeat right 12px center; background-size: 20px; cursor: pointer;">
                        <option value="">Seleccione un rol</option>
                        <?php if (isset($allRoles) && is_array($allRoles)): ?>
                            <?php foreach ($allRoles as $role): ?>
                                <option value="<?php echo htmlspecialchars($role['id']); ?>" <?php echo (isset($old['role_id']) && $old['role_id'] == $role['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($role['role_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <p style="margin-top: 0.5rem; font-size: 0.75rem; color: rgb(202, 138, 4); display: flex; align-items: center;">
                        <i class="fas fa-info-circle" style="margin-right: 0.25rem;"></i>
                        Define los permisos y accesos del usuario en el sistema
                    </p>
                </div>

                <!-- Sección Dedicación -->
                <div style="background-color: rgba(254, 243, 199, 0.5); padding: 1.5rem; border-radius: 0.75rem; border: 2px solid rgb(253, 224, 71);">
                    <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                        <div style="background-color: rgba(234, 179, 8, 0.2); padding: 0.75rem; border-radius: 0.5rem; margin-right: 0.75rem;">
                            <i class="fas fa-briefcase" style="font-size: 1.5rem; color: rgb(202, 138, 4);"></i>
                        </div>
                        <h3 style="font-size: 1.125rem; font-weight: bold; color: rgb(31, 41, 55);">Dedicación</h3>
                    </div>
                    <select id="dedication" name="dedication" required style="width: 100%; padding: 12px 40px 12px 16px; border: 2px solid rgb(250, 204, 21); border-radius: 0.5rem; font-size: 14px; height: 48px; appearance: none; background: white url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27%23666%27 stroke-width=%272%27%3e%3cpolyline points=%276 9 12 15 18 9%27/%3e%3c/svg%3e') no-repeat right 12px center; background-size: 20px; cursor: pointer;">
                        <option value="">Seleccione dedicación</option>
                        <?php $dedications = ['TIEMPO COMPLETO', 'TIEMPO PARCIAL', 'MEDIO TIEMPO', 'OTROS']; ?>
                        <?php foreach ($dedications as $dedication): ?>
                            <option value="<?php echo htmlspecialchars($dedication); ?>" <?php echo (($old['dedication'] ?? '') === $dedication) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($dedication); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p style="margin-top: 0.5rem; font-size: 0.75rem; color: rgb(202, 138, 4); display: flex; align-items: center;">
                        <i class="fas fa-info-circle" style="margin-right: 0.25rem;"></i>
                        Seleccione la dedicación del usuario
                    </p>
                </div>

                <!-- Sección Escuela -->
                <div style="background-color: rgba(224, 231, 255, 0.6); padding: 1.5rem; border-radius: 0.75rem; border: 2px solid rgb(165, 180, 252);">
                    <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                        <div style="background-color: rgba(99, 102, 241, 0.15); padding: 0.75rem; border-radius: 0.5rem; margin-right: 0.75rem;">
                            <i class="fas fa-school" style="font-size: 1.5rem; color: rgb(79, 70, 229);"></i>
                        </div>
                        <h3 style="font-size: 1.125rem; font-weight: bold; color: rgb(31, 41, 55);">Escuela</h3>
                    </div>
                    <select id="escuela" name="escuela" style="width: 100%; padding: 12px 40px 12px 16px; border: 2px solid rgb(165, 180, 252); border-radius: 0.5rem; font-size: 14px; height: 48px; appearance: none; background: white url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27%23666%27 stroke-width=%272%27%3e%3cpolyline points=%276 9 12 15 18 9%27/%3e%3c/svg%3e') no-repeat right 12px center; background-size: 20px; cursor: pointer;">
                        <?php
                        $escuelasOpciones = [
                            '' => 'Sin escuela',
                            'ECSOS' => 'Escuela de Construcción y Extracción Sostenible',
                            'ECAVET' => 'Escuela de Ciencias Agropecuarias y Veterinarias',
                            'ECSET' => 'Escuela de Ciencias Sociales, Empresariales y Tecnológicas',
                        ];
                        ?>
                        <?php foreach ($escuelasOpciones as $val => $label): ?>
                            <option value="<?php echo htmlspecialchars($val); ?>" <?php echo (($old['escuela'] ?? '') === $val) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p style="margin-top: 0.5rem; font-size: 0.75rem; color: rgb(79, 70, 229); display: flex; align-items: center;">
                        <i class="fas fa-info-circle" style="margin-right: 0.25rem;"></i>
                        Campo opcional para indicar la escuela del profesor
                    </p>
                </div>

                <!-- Botones de Acción -->
                <div style="display: flex; gap: 1rem; padding-top: 1.5rem;">
                    <a href="<?php echo BASE_PATH; ?>/users" style="flex: 1; padding: 0.75rem 1.5rem; background-color: rgb(107, 114, 128); color: white; border-radius: 0.75rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); text-align: center; font-weight: 600; text-decoration: none; display: inline-block;">
                        <i class="fas fa-arrow-left" style="margin-right: 0.5rem;"></i>
                        Cancelar
                    </a>
                    <button type="submit" style="flex: 1; padding: 0.75rem 1.5rem; background: linear-gradient(to right, rgb(59, 130, 246), rgb(99, 102, 241)); color: white; border-radius: 0.75rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); font-weight: 600; border: none; cursor: pointer;">
                        <i class="fas fa-user-plus" style="margin-right: 0.5rem;"></i>
                        Crear Usuario
                    </button>
                </div>
            </form>
        </main>
    </div>

    <script src="<?php echo BASE_PATH; ?>/js/responsive.js"></script>
</body>

</html>