<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="icon" type="image/png" href="<?php echo BASE_PATH; ?>/img/logo_sgpro.jpg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .animate-gradient {
            background-size: 200% 200%;
            animation: gradient 15s ease infinite;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-emerald-600 via-teal-600 to-cyan-700 animate-gradient flex items-center justify-center min-h-screen p-4">
    <div class="w-full max-w-lg">
        <!-- Card principal -->
        <div class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl p-8 space-y-6 border border-white/20">
            
            <!-- Logo y Header -->
            <div class="text-center space-y-4">
                <div class="mx-auto w-32 h-32 bg-gradient-to-br from-emerald-600 via-teal-600 to-cyan-700 rounded-3xl shadow-2xl p-6 flex items-center justify-center transform hover:scale-105 transition-transform duration-300">
                    <img src="/landing_sgpro/public/img/logo_sgpro.jpg" 
                         alt="SGPRO" 
                         class="w-full h-full object-contain filter drop-shadow-lg"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <i class="fas fa-graduation-cap text-white text-5xl hidden"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-emerald-600 to-cyan-600 bg-clip-text text-transparent">
                        Crear Cuenta
                    </h1>
                    <p class="text-gray-600 text-sm mt-2">Regístrate en SGPRO</p>
                </div>
            </div>

            <!-- Mensaje de error -->
            <?php if (isset($error)): ?>
            <div class="bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 p-4 rounded-xl" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                    <p class="text-sm text-red-700 font-medium"><?php echo htmlspecialchars($error); ?></p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Formulario -->
            <form action="<?php echo BASE_PATH; ?>/register/store" method="POST" class="space-y-5">
                
                <!-- Nombre -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user text-emerald-600 mr-2"></i>Nombre Completo
                    </label>
                    <input type="text" id="name" name="name" required 
                           class="block w-full px-4 py-3.5 bg-gray-50 border-2 border-gray-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 hover:border-emerald-300"
                           placeholder="Ej: Juan Pérez">
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-envelope text-emerald-600 mr-2"></i>Correo Electrónico
                    </label>
                    <input type="email" id="email" name="email" required 
                           class="block w-full px-4 py-3.5 bg-gray-50 border-2 border-gray-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 hover:border-emerald-300"
                           placeholder="correo@ejemplo.com">
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-lock text-emerald-600 mr-2"></i>Contraseña
                    </label>
                    <input type="password" id="password" name="password" required 
                           class="block w-full px-4 py-3.5 bg-gray-50 border-2 border-gray-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 hover:border-emerald-300"
                           placeholder="Mínimo 8 caracteres">
                    <p class="text-xs text-gray-500 mt-1 ml-1">
                        <i class="fas fa-info-circle mr-1"></i>Debe tener al menos 8 caracteres
                    </p>
                </div>

                <!-- Rol -->
                <div>
                    <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user-tag text-emerald-600 mr-2"></i>Rol
                    </label>
                    <select id="role" name="role" required 
                            class="block w-full px-4 py-3.5 bg-gray-50 border-2 border-gray-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 hover:border-emerald-300 cursor-pointer">
                        <option value="" disabled selected>Selecciona un rol...</option>
                        <?php if (isset($roles) && is_array($roles)): ?>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo htmlspecialchars($role['id']); ?>">
                                    <?php echo htmlspecialchars($role['role_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Botón de registro -->
                <button type="submit" 
                        class="w-full py-4 px-6 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center space-x-2">
                    <i class="fas fa-user-plus text-xl"></i>
                    <span>Crear Cuenta</span>
                </button>
            </form>

            <!-- Divider -->
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-white text-gray-500">o</span>
                </div>
            </div>

            <!-- Link de login -->
            <div class="text-center">
                <p class="text-sm text-gray-600">
                    ¿Ya tienes una cuenta?
                </p>
                <a href="<?php echo BASE_PATH; ?>/" 
                   class="inline-block mt-2 font-bold text-transparent bg-gradient-to-r from-emerald-600 to-cyan-600 bg-clip-text hover:from-emerald-700 hover:to-cyan-700 transition-all duration-200">
                    <i class="fas fa-sign-in-alt mr-1"></i>Inicia sesión aquí
                </a>
            </div>

            <!-- Footer -->
            <div class="text-center pt-4 border-t border-gray-200">
                <p class="text-xs text-gray-500">
                    <i class="fas fa-shield-alt mr-1"></i>
                    Tus datos están seguros con nosotros
                </p>
            </div>
        </div>
        
        <!-- Copyright Footer -->
        <div class="mt-6 text-center">
            <div class="bg-gradient-to-r from-emerald-600 to-teal-700 rounded-2xl shadow-lg p-4">
                <p class="text-white text-sm font-medium">
                    <i class="fas fa-copyright mr-1"></i>
                    © 2025 Instituto Superarse. Todos los derechos reservados.
                </p>
            </div>
        </div>
    </div>
</body>
</html>