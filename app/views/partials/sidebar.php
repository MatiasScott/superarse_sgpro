<?php
// Verificación de seguridad: asegurar que $roles esté definida
if (!isset($roles) || !is_array($roles)) {
    // Si $roles no está definida, intentar cargarla desde la base de datos
    if (isset($_SESSION['user_id'])) {
        require_once __DIR__ . '/../../models/RoleModel.php';
        $roleModel = new RoleModel();
        $roles = $roleModel->getRolesByUserId($_SESSION['user_id']);
    } else {
        // Si no hay sesión, definir como array vacío
        $roles = [];
    }
}
?>
<!-- Botón hamburguesa para móvil -->
<button class="mobile-menu-button fixed top-4 left-4 z-50 lg:hidden" id="mobileMenuButton" aria-label="Abrir menú">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
    </svg>
</button>

<!-- Overlay para cerrar menú al hacer clic fuera -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar Navigation -->
<aside class="sidebar" id="sidebar">
    <div class="text-center mb-6 p-6 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-b-3xl shadow-xl">
        <div class="bg-white/20 backdrop-blur-sm w-16 h-16 rounded-2xl mx-auto mb-3 flex items-center justify-center">
            <img src="<?php echo BASE_PATH; ?>/img/logo_sgpro.jpg" alt="SGPRO Logo" class="w-10 h-10">
        </div>
        <h2 class="text-2xl font-bold text-white mb-1">SGPRO</h2>
        <p class="text-blue-100 text-xs font-medium">Sistema de Gestión de Profesores</p>
    </div>
    
    <nav class="px-4">
        <ul>


            <li class="mb-2">
                <a href="<?php echo BASE_PATH; ?>/dashboard" 
                   class="flex items-center space-x-3 py-3 px-4 rounded-xl hover:bg-gradient-to-r hover:from-blue-600 hover:to-indigo-600 transition-all duration-200 sidebar-item-text <?php echo (strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false || $_SERVER['REQUEST_URI'] === BASE_PATH . '/' || $_SERVER['REQUEST_URI'] === BASE_PATH) ? 'active bg-gradient-to-r from-blue-600 to-indigo-600 shadow-lg' : ''; ?>">
                   <i class="fas fa-tachometer-alt text-lg"></i>
                   <span class="font-medium">Dashboard</span>
                </a>
            </li>



            <li class="mb-2">
                <a href="<?php echo BASE_PATH; ?>/professor/cv" 
                   class="flex items-center space-x-3 py-3 px-4 rounded-xl hover:bg-gradient-to-r hover:from-blue-600 hover:to-indigo-600 transition-all duration-200 sidebar-item-text <?php echo (strpos($_SERVER['REQUEST_URI'], '/professor') !== false) ? 'active bg-gradient-to-r from-blue-600 to-indigo-600 shadow-lg' : ''; ?>">
                   <i class="fas fa-user-circle text-lg"></i>
                   <span class="font-medium">Mi Perfil</span>
                </a>
            </li>
        

            <?php if (in_array('Coordinador académico', array_column($roles, 'role_name')) || in_array('Super Administrador', array_column($roles, 'role_name'))|| in_array('Profesor', array_column($roles, 'role_name'))|| in_array('Director de docencia', array_column($roles, 'role_name')) || in_array('Talento humano', array_column($roles, 'role_name'))): ?>
                <li class="mb-2">
                    <a href="<?php echo BASE_PATH; ?>/portfolios" 
                        class="flex items-center space-x-3 py-3 px-4 rounded-xl hover:bg-gradient-to-r hover:from-blue-600 hover:to-indigo-600 transition-all duration-200 sidebar-item-text <?php echo (strpos($_SERVER['REQUEST_URI'], '/portfolios') !== false) ? 'active bg-gradient-to-r from-blue-600 to-indigo-600 shadow-lg' : ''; ?>">
                        <i class="fas fa-folder-open text-lg"></i>
                        <span class="font-medium">Portafolios</span>
                    </a>
                </li>            
            <?php endif; ?>

            <?php if (in_array('Coordinador académico', array_column($roles, 'role_name')) || in_array('Super Administrador', array_column($roles, 'role_name'))|| in_array('Profesor', array_column($roles, 'role_name'))|| in_array('Director de docencia', array_column($roles, 'role_name'))|| in_array('Talento humano', array_column($roles, 'role_name'))): ?>
                <li class="mb-2">
                    <a href="<?php echo BASE_PATH; ?>/evaluations" 
                        class="flex items-center space-x-3 py-3 px-4 rounded-xl hover:bg-gradient-to-r hover:from-blue-600 hover:to-indigo-600 transition-all duration-200 sidebar-item-text <?php echo (strpos($_SERVER['REQUEST_URI'], '/evaluations') !== false) ? 'active bg-gradient-to-r from-blue-600 to-indigo-600 shadow-lg' : ''; ?>">
                        <i class="fas fa-chart-line text-lg"></i>
                        <span class="font-medium">Evaluaciones</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if (in_array('Coordinador académico', array_column($roles, 'role_name')) || in_array('Super Administrador', array_column($roles, 'role_name'))|| in_array('Profesor', array_column($roles, 'role_name'))|| in_array('Director de docencia', array_column($roles, 'role_name'))|| in_array('Talento humano', array_column($roles, 'role_name'))): ?>
                <li class="mb-2">
                    <a href="<?php echo BASE_PATH; ?>/continuity" 
                        class="flex items-center space-x-3 py-3 px-4 rounded-xl hover:bg-gradient-to-r hover:from-blue-600 hover:to-indigo-600 transition-all duration-200 sidebar-item-text <?php echo (strpos($_SERVER['REQUEST_URI'], '/continuity') !== false) ? 'active bg-gradient-to-r from-blue-600 to-indigo-600 shadow-lg' : ''; ?>">
                        <i class="fas fa-sync-alt text-lg"></i>
                        <span class="font-medium">Continuidad</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if (in_array('Coordinador académico', array_column($roles, 'role_name')) || in_array('Super Administrador', array_column($roles, 'role_name'))|| in_array('Profesor', array_column($roles, 'role_name'))|| in_array('Director de docencia', array_column($roles, 'role_name'))|| in_array('Talento humano', array_column($roles, 'role_name'))): ?>  
                <li class="mb-2">
                    <a href="<?php echo BASE_PATH; ?>/academic/assignments" 
                       class="flex items-center space-x-3 py-3 px-4 rounded-xl hover:bg-gradient-to-r hover:from-blue-600 hover:to-indigo-600 transition-all duration-200 sidebar-item-text <?php echo (strpos($_SERVER['REQUEST_URI'], '/assignments') !== false) ? 'active bg-gradient-to-r from-blue-600 to-indigo-600 shadow-lg' : ''; ?>">
                       <i class="fas fa-tasks text-lg"></i>
                       <span class="font-medium">Asignaciones</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if (in_array('Coordinador académico', array_column($roles, 'role_name')) || in_array('Super Administrador', array_column($roles, 'role_name'))|| in_array('Profesor', array_column($roles, 'role_name'))|| in_array('Director de docencia', array_column($roles, 'role_name'))|| in_array('Talento humano', array_column($roles, 'role_name'))): ?>  
                <li class="mb-2">
                    <a href="<?php echo BASE_PATH; ?>/contracts" 
                       class="flex items-center space-x-3 py-3 px-4 rounded-xl hover:bg-gradient-to-r hover:from-blue-600 hover:to-indigo-600 transition-all duration-200 sidebar-item-text <?php echo (strpos($_SERVER['REQUEST_URI'], '/contracts') !== false) ? 'active bg-gradient-to-r from-blue-600 to-indigo-600 shadow-lg' : ''; ?>">
                       <i class="fas fa-file-contract text-lg"></i>
                       <span class="font-medium">Contratos</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if (in_array('Coordinador académico', array_column($roles, 'role_name')) || in_array('Super Administrador', array_column($roles, 'role_name'))|| in_array('Profesor', array_column($roles, 'role_name'))|| in_array('Director de docencia', array_column($roles, 'role_name'))|| in_array('Talento humano', array_column($roles, 'role_name'))): ?>  
                <li class="mb-2">
                    <a href="<?php echo BASE_PATH; ?>/invoices" 
                       class="flex items-center space-x-3 py-3 px-4 rounded-xl hover:bg-gradient-to-r hover:from-blue-600 hover:to-indigo-600 transition-all duration-200 sidebar-item-text <?php echo (strpos($_SERVER['REQUEST_URI'], '/invoices') !== false) ? 'active bg-gradient-to-r from-blue-600 to-indigo-600 shadow-lg' : ''; ?>">
                       <i class="fas fa-file-invoice-dollar text-lg"></i>
                       <span class="font-medium">Facturas</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if (in_array('Coordinador académico', array_column($roles, 'role_name')) || in_array('Super Administrador', array_column($roles, 'role_name'))|| in_array('Director de docencia', array_column($roles, 'role_name'))|| in_array('Talento humano', array_column($roles, 'role_name'))): ?>  
                <li class="mb-2">
                    <a href="<?php echo BASE_PATH; ?>/reports" 
                       class="flex items-center space-x-3 py-3 px-4 rounded-xl hover:bg-gradient-to-r hover:from-blue-600 hover:to-indigo-600 transition-all duration-200 sidebar-item-text <?php echo (strpos($_SERVER['REQUEST_URI'], '/reports') !== false) ? 'active bg-gradient-to-r from-blue-600 to-indigo-600 shadow-lg' : ''; ?>">
                       <i class="fas fa-chart-bar text-lg"></i>
                       <span class="font-medium">Reportes</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if (in_array('Coordinador académico', array_column($roles, 'role_name')) || in_array('Super Administrador', array_column($roles, 'role_name'))|| in_array('Director de docencia', array_column($roles, 'role_name'))|| in_array('Talento humano', array_column($roles, 'role_name'))): ?>
                <li class="mb-2">
                    <a href="<?php echo BASE_PATH; ?>/academic/subjects" 
                        class="flex items-center space-x-3 py-3 px-4 rounded-xl hover:bg-gradient-to-r hover:from-blue-600 hover:to-indigo-600 transition-all duration-200 sidebar-item-text <?php echo (strpos($_SERVER['REQUEST_URI'], '/subjects') !== false) ? 'active bg-gradient-to-r from-blue-600 to-indigo-600 shadow-lg' : ''; ?>">
                        <i class="fas fa-book-open text-lg"></i>
                        <span class="font-medium">Materias</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if (in_array('Super Administrador', array_column($roles, 'role_name'))|| in_array('Talento humano', array_column($roles, 'role_name'))): ?>
                <li class="mb-2">
                    <a href="<?php echo BASE_PATH; ?>/users" 
                       class="flex items-center space-x-3 py-3 px-4 rounded-xl hover:bg-gradient-to-r hover:from-blue-600 hover:to-indigo-600 transition-all duration-200 sidebar-item-text <?php echo (strpos($_SERVER['REQUEST_URI'], '/users') !== false) ? 'active bg-gradient-to-r from-blue-600 to-indigo-600 shadow-lg' : ''; ?>">
                       <i class="fas fa-users-cog text-lg"></i>
                       <span class="font-medium">Gestión de Usuarios</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if (in_array('Super Administrador', array_column($roles, 'role_name'))|| in_array('Director de docencia', array_column($roles, 'role_name'))): ?>
                <li class="mb-2">
                    <a href="<?php echo BASE_PATH; ?>/pao" 
                       class="flex items-center space-x-3 py-3 px-4 rounded-xl hover:bg-gradient-to-r hover:from-blue-600 hover:to-indigo-600 transition-all duration-200 sidebar-item-text <?php echo (strpos($_SERVER['REQUEST_URI'], '/pao') !== false) ? 'active bg-gradient-to-r from-blue-600 to-indigo-600 shadow-lg' : ''; ?>">
                       <i class="fas fa-calendar-alt text-lg"></i>
                       <span class="font-medium">Gestión de PAO</span>
                    </a>
                </li>
            <?php endif; ?>

            <li class="mt-6 pt-6 border-t border-gray-600">
                <a href="<?php echo BASE_PATH; ?>/logout" 
                   class="flex items-center space-x-3 py-3 px-4 rounded-xl hover:bg-gradient-to-r hover:from-red-600 hover:to-rose-600 transition-all duration-200 text-red-300 hover:text-white group">
                   <i class="fas fa-sign-out-alt text-lg group-hover:text-white"></i>
                   <span class="font-medium">Cerrar Sesión</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>

<!-- JavaScript para menú móvil -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuButton = document.getElementById('mobileMenuButton');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    
    function toggleMenu() {
        sidebar.classList.toggle('open');
        sidebarOverlay.style.display = sidebar.classList.contains('open') ? 'block' : 'none';
        
        // Cambiar icono del botón
        const icon = mobileMenuButton.querySelector('svg path');
        if (sidebar.classList.contains('open')) {
            icon.setAttribute('d', 'M6 18L18 6M6 6l12 12'); // Icono de X
        } else {
            icon.setAttribute('d', 'M4 6h16M4 12h16M4 18h16'); // Icono de hamburguesa
        }
    }
    
    // Toggle menú al hacer clic en botón
    if (mobileMenuButton) {
        mobileMenuButton.addEventListener('click', toggleMenu);
    }
    
    // Cerrar menú al hacer clic en overlay
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', toggleMenu);
    }
    
    // Cerrar menú al hacer clic en un enlace (solo en móvil)
    const sidebarLinks = sidebar.querySelectorAll('a');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth < 1024) {
                toggleMenu();
            }
        });
    });
    
    // Cerrar menú al cambiar de tamaño a desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) {
            sidebar.classList.remove('open');
            sidebarOverlay.style.display = 'none';
        }
    });
});
</script>