// SGPRO - Sistema de Responsive Simplificado
// Función principal de inicialización
document.addEventListener('DOMContentLoaded', function () {
    console.log('Inicializando sistema responsive SGPRO...');

    // Inicializar componentes
    initMobileMenu();
    initResponsiveTables();
    initWindowResize();

    // Forzar sidebar fija en desktop después de cargar
    setTimeout(forceSidebarFixed, 100);

    console.log('Sistema responsive inicializado correctamente');
});

// ========== MENÚ MÓVIL ==========
function initMobileMenu() {
    const button = document.getElementById('menu-hamburguesa');
    const sidebar = document.getElementById('sidebar');

    if (button && sidebar) {
        button.addEventListener('click', function () {
            sidebar.classList.toggle('open');
        });

        // Cerrar al hacer clic en un enlace del menú (solo en móvil)
        const menuLinks = sidebar.querySelectorAll('a');
        menuLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 1024) {
                    sidebar.classList.remove('open');
                }
            });
        });
    }
}

function closeMobileMenu() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar && window.innerWidth <= 1024) {
        sidebar.classList.remove('open');
    }
}

// ========== TABLAS RESPONSIVE ==========
function initResponsiveTables() {
    console.log('Iniciando tablas responsive...');

    // Buscar todas las tablas
    const tables = document.querySelectorAll('table');
    console.log('Tablas encontradas:', tables.length);

    if (tables.length === 0) {
        console.warn('No se encontraron tablas en la página');
        return;
    }

    // Procesar cada tabla
    tables.forEach((table, index) => {
        console.log('Procesando tabla', index + 1, 'de', tables.length);
        makeTableResponsive(table);
    });

    console.log('Todas las tablas han sido procesadas');
}

function makeTableResponsive(table) {
    try {
        // PASO 1: Forzar visibilidad de la tabla
        table.style.display = 'table';
        table.style.width = '100%';
        table.style.opacity = '1';
        table.style.visibility = 'visible';

        console.log('Tabla hecha visible:', table);

        // PASO 2: Crear o verificar contenedor responsive
        let wrapper = table.closest('.table-responsive');

        if (!wrapper) {
            console.log('Creando contenedor responsive para tabla');
            wrapper = document.createElement('div');
            wrapper.className = 'table-responsive';

            // Insertar wrapper antes de la tabla
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        } else {
            console.log('Contenedor responsive ya existe');
        }

        // PASO 3: Configurar wrapper
        wrapper.style.display = 'block';
        wrapper.style.width = '100%';
        wrapper.style.overflowX = 'auto';
        wrapper.style.webkitOverflowScrolling = 'touch';
        wrapper.style.opacity = '1';
        wrapper.style.visibility = 'visible';

        // PASO 4: Remover clases que puedan ocultar
        wrapper.classList.remove('hidden', 'hidden-mobile');
        table.classList.remove('hidden', 'hidden-mobile');

        console.log('Tabla responsive configurada correctamente');

    } catch (error) {
        console.error('Error al procesar tabla:', error);
    }
}

// ========== MANEJO DE VENTANA ==========
function initWindowResize() {
    window.addEventListener('resize', handleWindowResize);

    // Agregar eventos adicionales para mantener la sidebar fija
    window.addEventListener('load', function () {
        ensureDesktopSidebar();
        forceSidebarFixed();
    });

    // Interceptar clics para mantener sidebar
    document.addEventListener('click', function (e) {
        if (window.innerWidth > 1024) {
            setTimeout(function () {
                ensureDesktopSidebar();
                forceSidebarFixed();
            }, 10);
        }
    });

    // Interceptar cambios de hash/URL
    window.addEventListener('hashchange', function () {
        if (window.innerWidth > 1024) {
            setTimeout(function () {
                ensureDesktopSidebar();
                forceSidebarFixed();
            }, 10);
        }
    });

    // Interceptar navegación
    window.addEventListener('popstate', function () {
        if (window.innerWidth > 1024) {
            setTimeout(function () {
                ensureDesktopSidebar();
                forceSidebarFixed();
            }, 10);
        }
    });
}

function handleWindowResize() {
    const sidebar = document.querySelector('aside');
    const mainContent = document.querySelector('.main-content');
    
    if (window.innerWidth > 1024) {
        // Desktop: remover clase open y limpiar estilos inline
        if (sidebar) {
            sidebar.classList.remove('open');
            sidebar.style.transform = '';
        }
        if (mainContent) {
            mainContent.style.marginLeft = '';
            mainContent.style.width = '';
        }
    } else {
        // Móvil: cerrar menú si está abierto
        closeMobileMenu();
    }

    // Verificar que las tablas sigan siendo visibles
    ensureTablesVisible();
}

function ensureTablesVisible() {
    const tables = document.querySelectorAll('table');
    tables.forEach(table => {
        table.style.display = 'table';

        const wrapper = table.closest('.table-responsive');
        if (wrapper) {
            wrapper.style.display = 'block';
            wrapper.style.overflowX = 'auto';
        }
    });
}

// ========== UTILIDADES ==========
function ensureDesktopSidebar() {
    if (window.innerWidth > 1024) {
        const sidebar = document.querySelector('aside');
        const mainContent = document.querySelector('.main-content');

        if (sidebar) {
            // Remover clases de móvil
            sidebar.classList.remove('open');
            sidebar.style.transform = '';
            sidebar.style.width = '';
        }

        if (mainContent) {
            // Asegurar margen correcto
            mainContent.style.marginLeft = '16rem';
            mainContent.style.width = 'calc(100% - 16rem)';
        }

        // Ocultar elementos móviles
        const mobileButton = document.querySelector('.mobile-menu-button');
        const overlay = document.querySelector('.sidebar-overlay');

        if (mobileButton) {
            mobileButton.style.display = 'none';
        }
        if (overlay) {
            overlay.classList.remove('active');
        }
    }
}

// Función adicional para forzar sidebar fija
function forceSidebarFixed() {
    if (window.innerWidth > 1024) {
        const sidebar = document.querySelector('aside');

        if (sidebar) {
            // Remover cualquier clase que pueda interferir
            sidebar.classList.remove('open', 'hidden', 'transform', '-translate-x-full');
            sidebar.style.transform = '';
        }
    }
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = 'notification fade-in';

    const colors = {
        success: '#10b981',
        error: '#ef4444',
        warning: '#f59e0b',
        info: '#3b82f6'
    };

    notification.style.cssText = `
        position: fixed;
        top: 1rem;
        right: 1rem;
        background: white;
        padding: 1rem;
        border-radius: 0.5rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        border-left: 4px solid ${colors[type] || colors.success};
        z-index: 9999;
        max-width: 300px;
    `;

    notification.innerHTML = `
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <div style="flex: 1;">${message}</div>
            <button onclick="this.parentElement.parentElement.remove()" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
        </div>
    `;

    document.body.appendChild(notification);

    // Auto-remover después de 5 segundos
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// ========== FUNCIONES GLOBALES ==========
// Exportar funciones para uso global
window.SGPRO = {
    closeMobileMenu,
    showNotification,
    ensureTablesVisible,
    initResponsiveTables,
    ensureDesktopSidebar,
    forceSidebarFixed
};

// Debug: Funciones para verificar el estado
window.debugSGPRO = function () {
    console.log('=== DEBUG SGPRO ===');
    console.log('Tablas encontradas:', document.querySelectorAll('table').length);
    console.log('Contenedores responsive:', document.querySelectorAll('.table-responsive').length);
    console.log('Menú móvil existe:', !!document.querySelector('.mobile-menu-button'));
    console.log('Sidebar existe:', !!document.querySelector('aside'));
    console.log('==================');
};