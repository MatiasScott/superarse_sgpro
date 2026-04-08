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

require_once __DIR__ . '/../../helpers/PermissionHelper.php';

$canViewPortfolios = PermissionHelper::can('portfolios', 'view', $roles);
$canViewEvaluations = PermissionHelper::can('evaluations', 'view', $roles);
$canViewContinuity = PermissionHelper::can('continuity', 'view', $roles);
$canViewAssignments = PermissionHelper::can('assignments', 'view', $roles);
$canViewContracts = PermissionHelper::can('contracts', 'view', $roles);
$canViewInvoices = PermissionHelper::can('invoices', 'view', $roles);
$canViewReports = PermissionHelper::can('reports', 'view', $roles);
$canViewSubjects = PermissionHelper::can('subjects', 'view', $roles);
$canViewCareers = PermissionHelper::can('careers', 'view', $roles);
$canViewUsers = PermissionHelper::can('users', 'view', $roles);
$canViewPao = PermissionHelper::can('pao', 'view', $roles);
$canViewPermissionAdmin = PermissionHelper::can('permissions', 'view', $roles);

$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';

if (defined('BASE_PATH') && BASE_PATH !== '' && strpos($requestPath, BASE_PATH) === 0) {
    $currentPath = substr($requestPath, strlen(BASE_PATH));
} else {
    $currentPath = $requestPath;
}

$currentPath = '/' . ltrim((string)$currentPath, '/');
if ($currentPath === '//') {
    $currentPath = '/';
}

$isActivePath = function ($patterns) use ($currentPath) {
    foreach ((array)$patterns as $pattern) {
        if ($pattern === '/' && $currentPath === '/') {
            return true;
        }

        if ($pattern !== '/' && strpos($currentPath, $pattern) === 0) {
            return true;
        }
    }

    return false;
};

$renderMenuItem = function ($item) use ($isActivePath) {
    if (empty($item['visible'])) {
        return;
    }

    $isActive = $isActivePath($item['active_patterns'] ?? []);
    $baseClass = 'flex items-center space-x-3 py-3 px-4 rounded-xl transition-all duration-200 sidebar-item-text';
    $stateClass = $isActive
        ? 'active bg-gradient-to-r from-blue-600 to-indigo-600 shadow-lg'
        : 'hover:bg-gradient-to-r hover:from-blue-600 hover:to-indigo-600';
    ?>
    <li class="mb-2">
        <a href="<?php echo BASE_PATH . $item['path']; ?>" class="<?php echo $baseClass . ' ' . $stateClass; ?>">
            <i class="<?php echo htmlspecialchars($item['icon']); ?> text-lg"></i>
            <span class="font-medium"><?php echo htmlspecialchars($item['label']); ?></span>
        </a>
    </li>
    <?php
};

$menuSections = [
    [
        'title' => 'Principal',
        'items' => [
            [
                'label' => 'Dashboard',
                'path' => '/dashboard',
                'icon' => 'fas fa-tachometer-alt',
                'visible' => true,
                'active_patterns' => ['/', '/dashboard'],
            ],
            [
                'label' => 'Mi Perfil',
                'path' => '/professor/cv',
                'icon' => 'fas fa-user-circle',
                'visible' => true,
                'active_patterns' => ['/professor'],
            ],
        ],
    ],
    [
        'title' => 'Gestión Docente',
        'items' => [
            [
                'label' => 'Portafolios',
                'path' => '/portfolios',
                'icon' => 'fas fa-folder-open',
                'visible' => $canViewPortfolios,
                'active_patterns' => ['/portfolios', '/professor/portfolio'],
            ],
            [
                'label' => 'Evaluaciones',
                'path' => '/evaluations',
                'icon' => 'fas fa-chart-line',
                'visible' => $canViewEvaluations,
                'active_patterns' => ['/evaluations'],
            ],
            [
                'label' => 'Continuidad',
                'path' => '/continuity',
                'icon' => 'fas fa-sync-alt',
                'visible' => $canViewContinuity,
                'active_patterns' => ['/continuity'],
            ],
        ],
    ],
    [
        'title' => 'Académico',
        'items' => [
            [
                'label' => 'Asignaciones',
                'path' => '/academic/assignments',
                'icon' => 'fas fa-tasks',
                'visible' => $canViewAssignments,
                'active_patterns' => ['/academic/assignments', '/assignments'],
            ],
            [
                'label' => 'Materias',
                'path' => '/academic/subjects',
                'icon' => 'fas fa-book-open',
                'visible' => $canViewSubjects,
                'active_patterns' => ['/academic/subjects', '/subjects'],
            ],
            [
                'label' => 'Carreras',
                'path' => '/academic/careers',
                'icon' => 'fas fa-graduation-cap',
                'visible' => $canViewCareers,
                'active_patterns' => ['/academic/careers', '/careers'],
            ],
            [
                'label' => 'Gestión de PAO',
                'path' => '/pao',
                'icon' => 'fas fa-calendar-alt',
                'visible' => $canViewPao,
                'active_patterns' => ['/pao'],
            ],
        ],
    ],
    [
        'title' => 'Administración',
        'items' => [
            [
                'label' => 'Contratos',
                'path' => '/contracts',
                'icon' => 'fas fa-file-contract',
                'visible' => $canViewContracts,
                'active_patterns' => ['/contracts'],
            ],
            [
                'label' => 'Facturas',
                'path' => '/invoices',
                'icon' => 'fas fa-file-invoice-dollar',
                'visible' => $canViewInvoices,
                'active_patterns' => ['/invoices'],
            ],
            [
                'label' => 'Reportes',
                'path' => '/reports',
                'icon' => 'fas fa-chart-bar',
                'visible' => $canViewReports,
                'active_patterns' => ['/reports'],
            ],
            [
                'label' => 'Gestión de Usuarios',
                'path' => '/users',
                'icon' => 'fas fa-users-cog',
                'visible' => $canViewUsers,
                'active_patterns' => ['/users'],
            ],
            [
                'label' => 'Permisos',
                'path' => '/permissions',
                'icon' => 'fas fa-shield-alt',
                'visible' => $canViewPermissionAdmin,
                'active_patterns' => ['/permissions'],
            ],
        ],
    ],
];
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
        <?php foreach ($menuSections as $section): ?>
            <?php
            $visibleItems = array_values(array_filter($section['items'], function ($item) {
                return !empty($item['visible']);
            }));
            ?>

            <?php if (!empty($visibleItems)): ?>
                <div class="mb-5">
                    <p class="px-4 mb-2 text-[11px] uppercase tracking-[0.15em] text-blue-200/80 font-semibold">
                        <?php echo htmlspecialchars($section['title']); ?>
                    </p>
                    <ul>
                        <?php foreach ($visibleItems as $item): ?>
                            <?php $renderMenuItem($item); ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <ul>
            <li class="mt-6 pt-6 border-t border-gray-600">
                <form action="<?php echo BASE_PATH; ?>/logout" method="POST">
                    <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                    <button type="submit"
                        class="w-full flex items-center space-x-3 py-3 px-4 rounded-xl hover:bg-gradient-to-r hover:from-red-600 hover:to-rose-600 transition-all duration-200 text-red-300 hover:text-white group text-left">
                        <i class="fas fa-sign-out-alt text-lg group-hover:text-white"></i>
                        <span class="font-medium">Cerrar Sesión</span>
                    </button>
                </form>
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