/**
 * Sistema de búsqueda y filtros para tablas
 * Proporciona funcionalidad de búsqueda en tiempo real y filtros dinámicos
 */

class TableSearchFilter {
    constructor(options = {}) {
        // Configuración del módulo
        this.module = options.module || 'default';
        this.tableSelector = options.tableSelector || 'table tbody';
        this.searchInputSelector = options.searchInputSelector || '#tableSearch';
        this.filterContainerSelector = options.filterContainerSelector || '#filterContainer';
        this.resultCountSelector = options.resultCountSelector || '#resultCount';
        this.noResultsSelector = options.noResultsSelector || '#noResults';
        this.paginationContainerSelector = options.paginationContainerSelector || null;
        this.rowsPerPage = Number.isInteger(options.rowsPerPage) ? options.rowsPerPage : 20;
        
        // Campos a buscar: { fieldName: [columnIndices], }
        this.searchFields = options.searchFields || {};
        
        // Configuración de filtros disponibles: { filterName: { column: index, options: [] } }
        this.filtersConfig = options.filtersConfig || {};
        
        // Estado actual de filtros
        this.activeFilters = {};
        this.currentPage = 1;
        
        // Datos originales
        this.allRows = [];
        this.filteredRows = [];
        
        this.init();
    }
    
    /**
     * Inicializar el sistema
     */
    init() {
        this.cacheElements();
        this.cacheAllRows();
        this.setupSearchListener();
        this.setupFilterListeners();
        this.setupPaginationElements();
        this.updateAutoFilters();
        this.applyAllFilters({ resetPage: true });
    }
    
    /**
     * Guardar referencias a elementos DOM
     */
    cacheElements() {
        this.$tbody = document.querySelector(this.tableSelector);
        this.$searchInput = document.querySelector(this.searchInputSelector);
        this.$filterContainer = document.querySelector(this.filterContainerSelector);
        this.$resultCount = document.querySelector(this.resultCountSelector);
        this.$noResults = document.querySelector(this.noResultsSelector);
        
        if (!this.$tbody) {
            console.error('❌ No se encontró tbody de la tabla');
        }

        this.$table = this.$tbody ? this.$tbody.closest('table') : null;
    }
    
    /**
     * Guardar todas las filas originales
     */
    cacheAllRows() {
        if (this.$tbody) {
            this.allRows = Array.from(this.$tbody.querySelectorAll('tr'));
            this.filteredRows = [...this.allRows];
        }
    }
    
    /**
     * Configurar listener para búsqueda
     */
    setupSearchListener() {
        if (!this.$searchInput) return;
        
        this.$searchInput.addEventListener('input', (e) => {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.performSearch(e.target.value.toLowerCase());
            }, 300);
        });
    }
    
    /**
     * Configurar listeners para filtros
     */
    setupFilterListeners() {
        if (!this.$filterContainer) return;
        
        this.$filterContainer.addEventListener('change', (e) => {
            if (e.target.matches('select, input[type="checkbox"]')) {
                const filterName = e.target.dataset.filter;
                
                if (e.target.type === 'checkbox') {
                    this.handleCheckboxFilter(filterName, e.target);
                } else {
                    this.handleSelectFilter(filterName, e.target.value);
                }
                
                this.applyAllFilters({ resetPage: true });
            }
        });
    }

    /**
     * Crear contenedor de paginación si no existe
     */
    setupPaginationElements() {
        if (!this.$tbody || this.rowsPerPage <= 0) return;

        if (this.paginationContainerSelector) {
            this.$paginationContainer = document.querySelector(this.paginationContainerSelector);
            return;
        }

        const existing = this.$table?.parentElement?.querySelector('.table-pagination-controls');
        if (existing) {
            this.$paginationContainer = existing;
            return;
        }

        const container = document.createElement('div');
        container.className = 'table-pagination-controls px-4 py-4 border-t border-gray-200 bg-gray-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3';

        if (this.$table && this.$table.parentElement) {
            this.$table.parentElement.appendChild(container);
            this.$paginationContainer = container;
        }
    }
    
    /**
     * Manejar filtro de checkbox
     */
    handleCheckboxFilter(filterName, checkbox) {
        if (!this.activeFilters[filterName]) {
            this.activeFilters[filterName] = [];
        }
        
        if (checkbox.checked) {
            if (!this.activeFilters[filterName].includes(checkbox.value)) {
                this.activeFilters[filterName].push(checkbox.value);
            }
        } else {
            this.activeFilters[filterName] = this.activeFilters[filterName].filter(v => v !== checkbox.value);
        }
        
        if (this.activeFilters[filterName].length === 0) {
            delete this.activeFilters[filterName];
        }
    }
    
    /**
     * Manejar filtro de select
     */
    handleSelectFilter(filterName, value) {
        if (value === '') {
            delete this.activeFilters[filterName];
        } else {
            this.activeFilters[filterName] = value;
        }
    }
    
    /**
     * Realizar búsqueda
     */
    performSearch(searchTerm) {
        if (searchTerm === '') {
            this.filteredRows = [...this.allRows];
        } else {
            this.filteredRows = this.allRows.filter(row => {
                return this.matchesSearch(row, searchTerm);
            });
        }
        
        this.applyAllFilters({ resetPage: true });
    }
    
    /**
     * Verificar si una fila coincide con la búsqueda
     */
    matchesSearch(row, searchTerm) {
        for (const fieldName in this.searchFields) {
            const columnIndices = this.searchFields[fieldName];
            
            for (const colIndex of columnIndices) {
                const cells = row.querySelectorAll('td');
                if (colIndex < cells.length) {
                    const cellText = cells[colIndex].textContent.toLowerCase();
                    if (cellText.includes(searchTerm)) {
                        return true;
                    }
                }
            }
        }
        
        return false;
    }
    
    /**
     * Aplicar todos los filtros
     */
    applyAllFilters(options = {}) {
        const resetPage = !!options.resetPage;
        if (resetPage) {
            this.currentPage = 1;
        }

        let visibleRows = [...this.filteredRows];
        
        for (const filterName in this.activeFilters) {
            const filterConfig = this.filtersConfig[filterName];
            if (!filterConfig) continue;
            
            const columnIndex = filterConfig.column;
            const filterValue = this.activeFilters[filterName];
            
            visibleRows = visibleRows.filter(row => {
                const cells = row.querySelectorAll('td');
                if (columnIndex >= cells.length) return true;
                
                const cellText = cells[columnIndex].textContent.trim();
                
                if (Array.isArray(filterValue)) {
                    return filterValue.length === 0 || filterValue.includes(cellText);
                } else {
                    return filterValue === '' || cellText === filterValue;
                }
            });
        }
        
        this.updateTableDisplay(visibleRows);
    }
    
    /**
     * Actualizar visualización de la tabla
     */
    updateTableDisplay(visibleRows) {
        if (!this.$tbody) {
            return;
        }

        const totalRows = visibleRows.length;
        const totalPages = this.rowsPerPage > 0 ? Math.max(1, Math.ceil(totalRows / this.rowsPerPage)) : 1;

        if (this.currentPage > totalPages) {
            this.currentPage = totalPages;
        }

        const start = (this.currentPage - 1) * this.rowsPerPage;
        const end = start + this.rowsPerPage;
        const paginatedRows = this.rowsPerPage > 0 ? visibleRows.slice(start, end) : visibleRows;

        // Reconstruir el tbody con solo las filas visibles evita huecos en blanco
        // cuando existen estilos de tabla personalizados.
        const fragment = document.createDocumentFragment();
        paginatedRows.forEach(row => {
            row.style.display = '';
            fragment.appendChild(row);
        });

        this.$tbody.innerHTML = '';
        this.$tbody.appendChild(fragment);
        
        // Actualizar contador y mensaje de no resultados
        const count = visibleRows.length;
        
        if (this.$resultCount) {
            this.$resultCount.textContent = count;
        }
        
        if (this.$noResults) {
            if (count === 0) {
                this.$noResults.style.display = '';
            } else {
                this.$noResults.style.display = 'none';
            }
        }

        this.renderPagination(totalRows, totalPages);
    }

    /**
     * Renderizar controles de paginación
     */
    renderPagination(totalRows, totalPages) {
        if (!this.$paginationContainer || this.rowsPerPage <= 0) return;

        if (totalRows === 0) {
            this.$paginationContainer.innerHTML = '';
            this.$paginationContainer.style.display = 'none';
            return;
        }

        this.$paginationContainer.style.display = '';

        const pageInfo = document.createElement('div');
        pageInfo.className = 'text-sm text-gray-600';
        const startRow = (this.currentPage - 1) * this.rowsPerPage + 1;
        const endRow = Math.min(this.currentPage * this.rowsPerPage, totalRows);
        pageInfo.textContent = `Mostrando ${startRow}-${endRow} de ${totalRows}`;

        const nav = document.createElement('div');
        nav.className = 'flex items-center gap-2';

        const prev = document.createElement('button');
        prev.type = 'button';
        prev.className = 'px-3 py-1.5 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed';
        prev.textContent = 'Anterior';
        prev.disabled = this.currentPage === 1;
        prev.addEventListener('click', () => {
            if (this.currentPage > 1) {
                this.currentPage -= 1;
                this.applyAllFilters({ resetPage: false });
            }
        });

        const current = document.createElement('span');
        current.className = 'text-sm font-semibold text-gray-700 px-2';
        current.textContent = `Página ${this.currentPage} de ${totalPages}`;

        const next = document.createElement('button');
        next.type = 'button';
        next.className = 'px-3 py-1.5 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed';
        next.textContent = 'Siguiente';
        next.disabled = this.currentPage >= totalPages;
        next.addEventListener('click', () => {
            if (this.currentPage < totalPages) {
                this.currentPage += 1;
                this.applyAllFilters({ resetPage: false });
            }
        });

        nav.appendChild(prev);
        nav.appendChild(current);
        nav.appendChild(next);

        this.$paginationContainer.innerHTML = '';
        this.$paginationContainer.appendChild(pageInfo);
        this.$paginationContainer.appendChild(nav);
    }
    
    /**
     * Renderizar filtros dinámicamente
     */
    renderFilters() {
        if (!this.$filterContainer) return;
        
        let filtersHTML = '';
        
        for (const filterName in this.filtersConfig) {
            const config = this.filtersConfig[filterName];
            const filterLabel = config.label || filterName;
            
            if (config.type === 'select') {
                filtersHTML += this.renderSelectFilter(filterName, filterLabel, config.options);
            } else if (config.type === 'checkbox') {
                filtersHTML += this.renderCheckboxFilter(filterName, filterLabel, config.options);
            }
        }
        
        if (filtersHTML) {
            this.$filterContainer.innerHTML = filtersHTML;
        }
    }
    
    /**
     * Renderizar filtro de select
     */
    renderSelectFilter(filterName, label, options) {
        let html = `
            <div class="filter-group">
                <label class="filter-label">${label}</label>
                <select class="filter-select" data-filter="${filterName}">
                    <option value="">Todos</option>
        `;
        
        (options || []).forEach(option => {
            html += `<option value="${option}">${option}</option>`;
        });
        
        html += `
                </select>
            </div>
        `;
        
        return html;
    }
    
    /**
     * Renderizar filtro de checkboxes
     */
    renderCheckboxFilter(filterName, label, options) {
        let html = `
            <div class="filter-group">
                <label class="filter-label">${label}</label>
                <div class="filter-checkboxes">
        `;
        
        (options || []).forEach(option => {
            html += `
                <label class="filter-checkbox-label">
                    <input type="checkbox" class="filter-checkbox" data-filter="${filterName}" value="${option}" />
                    <span>${option}</span>
                </label>
            `;
        });
        
        html += `
                </div>
            </div>
        `;
        
        return html;
    }
    
    /**
     * Actualizar filtros automáticamente según datos en tabla
     */
    updateAutoFilters() {
        for (const filterName in this.filtersConfig) {
            const config = this.filtersConfig[filterName];
            
            // Si se detecta que está vacío, llenar automáticamente
            if (config.autoFill && (!config.options || config.options.length === 0)) {
                config.options = this.extractColumnValues(config.column);
            }
        }
        
        // Re-renderizar con opciones actualizadas
        this.renderFilters();
    }
    
    /**
     * Extraer valores únicos de una columna
     */
    extractColumnValues(columnIndex) {
        const values = new Set();
        
        this.allRows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if (columnIndex < cells.length) {
                const value = cells[columnIndex].textContent.trim();
                if (value) {
                    values.add(value);
                }
            }
        });
        
        return Array.from(values).sort();
    }
    
    /**
     * Limpiar todos los filtros y búsqueda
     */
    clearAll() {
        if (this.$searchInput) {
            this.$searchInput.value = '';
        }
        
        this.activeFilters = {};
        
        // Limpiar checkboxes
        const checkboxes = this.$filterContainer?.querySelectorAll('input[type="checkbox"]');
        if (checkboxes) {
            checkboxes.forEach(cb => cb.checked = false);
        }
        
        // Limpiar selects
        const selects = this.$filterContainer?.querySelectorAll('select');
        if (selects) {
            selects.forEach(select => select.value = '');
        }
        
        this.filteredRows = [...this.allRows];
        this.applyAllFilters({ resetPage: true });
    }
    
    /**
     * Obtener filas actuales visibles
     */
    getVisibleRows() {
        if (!this.$tbody) {
            return [];
        }

        return Array.from(this.$tbody.querySelectorAll('tr'));
    }
    
    /**
     * Exportar datos filtrados como JSON
     */
    exportFilteredData() {
        const visibleRows = this.getVisibleRows();
        const data = [];
        
        visibleRows.forEach(row => {
            const cells = row.querySelectorAll('td');
            const rowData = [];
            
            cells.forEach(cell => {
                rowData.push(cell.textContent.trim());
            });
            
            data.push(rowData);
        });
        
        return data;
    }
}

// Exportar para uso global
if (typeof module !== 'undefined' && module.exports) {
    module.exports = TableSearchFilter;
}
