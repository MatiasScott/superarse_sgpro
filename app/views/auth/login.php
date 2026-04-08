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
        
        .logo-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 1.5rem;
            border-radius: 1.5rem;
            box-shadow: 0 10px 25px -5px rgba(102, 126, 234, 0.4);
        }
        
        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }
        
        .input-with-icon {
            padding-left: 3rem;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 animate-gradient flex items-center justify-center min-h-screen p-4">
    <div class="w-full max-w-md">
        <!-- Card principal con efecto glassmorphism -->
        <div class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl p-8 space-y-6 border border-white/20">
            
            <!-- Logo y Header -->
            <div class="text-center space-y-4">
                <div class="mx-auto w-40 h-40 bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 rounded-3xl shadow-2xl p-6 flex items-center justify-center transform hover:scale-105 transition-transform duration-300">
                    <img src="/landing_sgpro/public/img/logo_sgpro.jpg" 
                         alt="SGPRO" 
                         class="w-full h-full object-contain filter drop-shadow-lg"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <i class="fas fa-graduation-cap text-white text-6xl hidden"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        SGPRO
                    </h1>
                    <p class="text-gray-600 text-sm mt-2">Sistema de Gestión de Profesores</p>
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
            <form action="<?php echo BASE_PATH; ?>/login" method="POST" class="space-y-5">
                <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-envelope text-blue-600 mr-2"></i>Correo Electrónico
                    </label>
                    <input type="email" id="email" name="email" required 
                           class="block w-full px-4 py-3.5 bg-gray-50 border-2 border-gray-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 hover:border-blue-300"
                           placeholder="correo@ejemplo.com">
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-lock text-blue-600 mr-2"></i>Contraseña
                    </label>
                    <input type="password" id="password" name="password" required 
                           class="block w-full px-4 py-3.5 bg-gray-50 border-2 border-gray-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 hover:border-blue-300"
                           placeholder="••••••••">
                </div>

                <!-- Botón de login -->
                <button type="submit" 
                        class="w-full py-4 px-6 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center space-x-2">
                    <i class="fas fa-sign-in-alt text-xl"></i>
                    <span>Iniciar Sesión</span>
                </button>
            </form>

            <!-- Registro desde login deshabilitado -->

            <!-- Footer -->
            <div class="text-center pt-4 border-t border-gray-200">
                <p class="text-xs text-gray-500">
                    <i class="fas fa-shield-alt mr-1"></i>
                    Sistema seguro y confiable
                </p>
            </div>
        </div>
        
        <!-- Copyright Footer -->
        <div class="mt-6 text-center">
            <div class="bg-gradient-to-r from-purple-600 to-indigo-700 rounded-2xl shadow-lg p-4">
                <p class="text-white text-sm font-medium">
                    <i class="fas fa-copyright mr-1"></i>
                    © 2025 Instituto Superarse. Todos los derechos reservados.
                </p>
            </div>
        </div>
    </div>

    <script>
        // Script para verificar y cargar el logo
        document.addEventListener('DOMContentLoaded', function() {
            const logoImg = document.querySelector('.logo-container img');
            
            // Rutas alternativas para el logo
            const logoUrls = [
                '/landing_sgpro/public/img/logo_sgpro.jpg',
                '/landing_sgpro/public/img/sgpro-logo.svg',
                '<?php echo BASE_PATH; ?>/img/logo_sgpro.jpg',
                '<?php echo BASE_PATH; ?>/img/sgpro-logo.svg'
            ];
            
            let currentIndex = 0;
            
            function tryNextLogo() {
                if (currentIndex < logoUrls.length) {
                    console.log('Intentando cargar logo:', logoUrls[currentIndex]);
                    logoImg.src = logoUrls[currentIndex];
                    currentIndex++;
                } else {
                    console.error('No se pudo cargar ningún logo');
                    // Como último recurso, ocultar la imagen
                    logoImg.style.display = 'none';
                }
            }
            
            logoImg.onerror = tryNextLogo;
            logoImg.onload = function() {
                console.log('Logo cargado exitosamente:', this.src);
            };
            
            // Intentar cargar el primer logo
            tryNextLogo();
        });
    </script>
    <!-- Registro desde login deshabilitado: se ha eliminado el toggle y el formulario embebido -->
</body>

</html>
