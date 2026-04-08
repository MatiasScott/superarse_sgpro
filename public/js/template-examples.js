/**
 * Plantilla HTML para integrar búsqueda y filtros en las vistas
 * Copiar esta sección y adaptarla a cada módulo
 */

/*
PASO 1: Agregra al HEAD del archivo HTML
================================================

    <!-- Estilos para búsqueda y filtros -->
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/css/table-search-filter.css">

================================================

PASO 2: Agregar ANTES de la tabla principal
================================================

    <div class="search-filter-controls">
        <!-- Barra de búsqueda -->
        <div class="search-bar-container">
            <label class="search-bar-label">🔍 Buscar en esta página</label>
            <input 
                type="text" 
                id="tableSearch" 
                class="search-bar-input" 
                placeholder="Buscar por nombre, ID, correo, estado..."
            >
        </div>
        
        <!-- Filtros dinámicos -->
        <div id="filterContainer" class="filters-container"></div>
        
        <!-- Información de resultados -->
        <div class="search-results-info">
            <span class="results-count">
                Resultados: <span class="results-count-value" id="resultCount">0</span>
            </span>
        </div>
    </div>

================================================

PASO 3: Agregar DESPUÉS de la tabla
================================================

    <div id="noResults" class="text-center py-12">
        <div class="flex flex-col items-center justify-center space-y-4">
            <div class="bg-gray-100 dark:bg-gray-700 p-8 rounded-full">
                <i class="fas fa-search text-gray-400 text-5xl"></i>
            </div>
            <p class="text-gray-600 dark:text-gray-300 font-medium text-lg">
                No hay registros que coincidan con la búsqueda
            </p>
            <p class="text-gray-400 text-sm">
                Intenta cambiar los términos de búsqueda o los filtros
            </p>
        </div>
    </div>

================================================

PASO 4: Agregar ANTES de cerrar </body>
================================================

    <!-- Scripts para búsqueda y filtros -->
    <script src="<?php echo BASE_PATH; ?>/js/table-search-filter.js"></script>
    <script src="<?php echo BASE_PATH; ?>/js/module-config.js"></script>
    
    <!-- Inicializar búsqueda para este módulo -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Reemplazar 'users' con el nombre del módulo
            // Opciones: users, contracts, invoices, evaluations, portfolios, continuity, pao, notifications
            initializeTableSearch('users'); // <- CAMBIAR SEGÚN EL MÓDULO
        });
    </script>

================================================

EJEMPLOS DE INICIALIZACIÓN:
================================================

// Para cualquier módulo con configuración por defecto:
initializeTableSearch('users');

// Para personalizar opciones:
initializeTableSearch('users', {
    searchInputSelector: '#customSearch',
    filterContainerSelector: '#customFilters'
});

// Para acceso directo al objeto:
const tableSearch = new TableSearchFilter({
    module: 'users',
    tableSelector: 'table tbody',
    searchInputSelector: '#tableSearch',
    filterContainerSelector: '#filterContainer',
    resultCountSelector: '#resultCount',
    noResultsSelector: '#noResults',
    searchFields: {
        'ID': [0],
        'Nombre': [1],
        'Correo': [2]
    },
    filtersConfig: {
        'estado': {
            label: 'Estado',
            type: 'select',
            column: 3,
            autoFill: true
        }
    }
});

================================================
*/

// CONFIGURACIÓN HTML PARA CADA MÓDULO:

// USERS (Gestión de Usuarios)
const USERS_HTML = `
<div class="search-filter-controls">
    <div class="search-bar-container">
        <label class="search-bar-label">🔍 Buscar usuarios</label>
        <input 
            type="text" 
            id="tableSearch" 
            class="search-bar-input" 
            placeholder="Buscar por nombre, ID, correo..."
        >
    </div>
    <div id="filterContainer" class="filters-container"></div>
    <div class="search-results-info">
        <span class="results-count">
            Resultados: <span class="results-count-value" id="resultCount">0</span>
        </span>
    </div>
</div>
`;

// CONTRACTS (Gestión de Contratos)
const CONTRACTS_HTML = `
<div class="search-filter-controls">
    <div class="search-bar-container">
        <label class="search-bar-label">🔍 Buscar contratos</label>
        <input 
            type="text" 
            id="tableSearch" 
            class="search-bar-input" 
            placeholder="Buscar por profesor, PAO, ID..."
        >
    </div>
    <div id="filterContainer" class="filters-container"></div>
    <div class="search-results-info">
        <span class="results-count">
            Resultados: <span class="results-count-value" id="resultCount">0</span>
        </span>
    </div>
</div>
`;

// INVOICES (Gestión de Facturas)
const INVOICES_HTML = `
<div class="search-filter-controls">
    <div class="search-bar-container">
        <label class="search-bar-label">🔍 Buscar facturas</label>
        <input 
            type="text" 
            id="tableSearch" 
            class="search-bar-input" 
            placeholder="Buscar por profesor, PAO, período, monto..."
        >
    </div>
    <div id="filterContainer" class="filters-container"></div>
    <div class="search-results-info">
        <span class="results-count">
            Resultados: <span class="results-count-value" id="resultCount">0</span>
        </span>
    </div>
</div>
`;

// EVALUATIONS (Gestión de Evaluaciones)
const EVALUATIONS_HTML = `
<div class="search-filter-controls">
    <div class="search-bar-container">
        <label class="search-bar-label">🔍 Buscar evaluaciones</label>
        <input 
            type="text" 
            id="tableSearch" 
            class="search-bar-input" 
            placeholder="Buscar por profesor, PAO, puntaje..."
        >
    </div>
    <div id="filterContainer" class="filters-container"></div>
    <div class="search-results-info">
        <span class="results-count">
            Resultados: <span class="results-count-value" id="resultCount">0</span>
        </span>
    </div>
</div>
`;

// PORTFOLIOS (Gestión de Portafolios)
const PORTFOLIOS_HTML = `
<div class="search-filter-controls">
    <div class="search-bar-container">
        <label class="search-bar-label">🔍 Buscar portafolios</label>
        <input 
            type="text" 
            id="tableSearch" 
            class="search-bar-input" 
            placeholder="Buscar por profesor, PAO, unidades..."
        >
    </div>
    <div id="filterContainer" class="filters-container"></div>
    <div class="search-results-info">
        <span class="results-count">
            Resultados: <span class="results-count-value" id="resultCount">0</span>
        </span>
    </div>
</div>
`;

// CONTINUITY (Gestión de Continuidad)
const CONTINUITY_HTML = `
<div class="search-filter-controls">
    <div class="search-bar-container">
        <label class="search-bar-label">🔍 Buscar registros de continuidad</label>
        <input 
            type="text" 
            id="tableSearch" 
            class="search-bar-input" 
            placeholder="Buscar por profesor, PAO, decisiones..."
        >
    </div>
    <div id="filterContainer" class="filters-container"></div>
    <div class="search-results-info">
        <span class="results-count">
            Resultados: <span class="results-count-value" id="resultCount">0</span>
        </span>
    </div>
</div>
`;

// PAO (Gestión de PAO)
const PAO_HTML = `
<div class="search-filter-controls">
    <div class="search-bar-container">
        <label class="search-bar-label">🔍 Buscar PAO</label>
        <input 
            type="text" 
            id="tableSearch" 
            class="search-bar-input" 
            placeholder="Buscar por nombre, fecha..."
        >
    </div>
    <div id="filterContainer" class="filters-container"></div>
    <div class="search-results-info">
        <span class="results-count">
            Resultados: <span class="results-count-value" id="resultCount">0</span>
        </span>
    </div>
</div>
`;

// NOTIFICATIONS (Gestión de Notificaciones)
const NOTIFICATIONS_HTML = `
<div class="search-filter-controls">
    <div class="search-bar-container">
        <label class="search-bar-label">🔍 Buscar notificaciones</label>
        <input 
            type="text" 
            id="tableSearch" 
            class="search-bar-input" 
            placeholder="Buscar por título, mensaje..."
        >
    </div>
    <div id="filterContainer" class="filters-container"></div>
    <div class="search-results-info">
        <span class="results-count">
            Resultados: <span class="results-count-value" id="resultCount">0</span>
        </span>
    </div>
</div>
`;
