/**
 * Configuraciones de búsqueda y filtros para cada módulo
 * Define qué campos son buscables y qué filtros están disponibles en cada página
 */

const MODULE_CONFIGS = {
    // ============ USUARIOS ============
    users: {
        searchFields: {
            'ID': [0],
            'Nombre': [1],
            'Correo': [2],
            'Escuela': [3]
        },
        filtersConfig: {
            'escuela': {
                label: 'Escuela',
                type: 'select',
                column: 3,
                autoFill: true,
                options: []
            }
        }
    },
    
    // ============ CONTRATOS ============
    contracts: {
        searchFields: {
            'ID': [0],
            'Profesor': [1],
            'PAO': [2],
            'Estado': [3]
        },
        filtersConfig: {
            'estado': {
                label: 'Estado del Contrato',
                type: 'select',
                column: 3,
                autoFill: true,
                options: []
            }
        }
    },
    
    // ============ FACTURAS ============
    invoices: {
        searchFields: {
            'ID': [0],
            'Profesor': [1],
            'PAO': [2],
            'Unidad': [3],
            'Período': [4],
            'Monto': [6],
            'Observación': [9],
            'Estado': [10]
        },
        filtersConfig: {
            'unidad': {
                label: 'Unidad',
                type: 'select',
                column: 3,
                autoFill: true,
                options: []
            },
            'estado': {
                label: 'Estado',
                type: 'select',
                column: 10,
                autoFill: true,
                options: []
            },
            'period': {
                label: 'Período',
                type: 'select',
                column: 4,
                autoFill: true,
                options: []
            }
        }
    },
    
    // ============ EVALUACIONES ============
    evaluations: {
        searchFields: {
            'ID': [0],
            'Profesor': [1],
            'Escuela': [2],
            'PAO': [3],
            'Puntaje': [4],
            'Estado': [5]
        },
        filtersConfig: {
            'escuela': {
                label: 'Escuela',
                type: 'select',
                column: 2,
                autoFill: true,
                options: []
            },
            'estado': {
                label: 'Estado',
                type: 'select',
                column: 5,
                autoFill: true,
                options: []
            }
        }
    },
    
    // ============ PORTAFOLIOS ============
    portfolios: {
        searchFields: {
            'Profesor': [0],
            'PAO': [1],
            'Unidades': [2]
        },
        filtersConfig: {
            'profesor': {
                label: 'Profesor',
                type: 'select',
                column: 0,
                autoFill: true,
                options: []
            }
        }
    },
    
    // ============ CONTINUIDAD ============
    continuity: {
        searchFields: {
            'ID': [0],
            'Profesor': [1],
            'PAO': [2],
            'DecisiónProfesor': [3],
            'DecisiónDocencia': [4],
            'EstadoFinal': [5]
        },
        filtersConfig: {
            'decisionProfesor': {
                label: 'Decisión Profesor',
                type: 'select',
                column: 3,
                autoFill: true,
                options: []
            },
            'decisionDocencia': {
                label: 'Decisión Docencia',
                type: 'select',
                column: 4,
                autoFill: true,
                options: []
            },
            'estadoFinal': {
                label: 'Estado Final',
                type: 'select',
                column: 5,
                autoFill: true,
                options: []
            }
        }
    },
    
    // ============ PAO ============
    pao: {
        searchFields: {
            'ID': [0],
            'Nombre': [1],
            'FechaInicio': [2],
            'FechaFin': [3]
        },
        filtersConfig: {
            'year': {
                label: 'Año',
                type: 'select',
                column: 2,
                autoFill: true,
                extractFunction: (text) => {
                    const match = text.match(/\d{4}/);
                    return match ? match[0] : '';
                }
            }
        }
    },

    // ============ ASIGNACIONES ============
    assignments: {
        searchFields: {
            'ID': [0],
            'Profesor': [1],
            'Materia': [2],
            'PAO': [3],
            'Horas': [4],
            'Estado': [5]
        },
        filtersConfig: {
            'estado': {
                label: 'Estado',
                type: 'select',
                column: 5,
                autoFill: true,
                options: []
            },
            'pao': {
                label: 'PAO',
                type: 'select',
                column: 3,
                autoFill: true,
                options: []
            }
        }
    },

    // ============ MATERIAS ============
    subjects: {
        searchFields: {
            'ID': [0],
            'Nombre': [1],
            'Carrera': [2]
        },
        filtersConfig: {
            'carrera': {
                label: 'Carrera',
                type: 'select',
                column: 2,
                autoFill: true,
                options: []
            }
        }
    },
    
    // ============ NOTIFICACIONES ============
    notifications: {
        searchFields: {
            'Título': [0],
            'Mensaje': [1],
            'Tabla': [2]
        },
        filtersConfig: {
            'leidas': {
                label: 'Estado de Lectura',
                type: 'select',
                column: 4,
                options: ['Leída', 'No leída']
            }
        }
    }
};

/**
 * Obtener configuración de un módulo
 */
function getModuleConfig(moduleName) {
    return MODULE_CONFIGS[moduleName] || null;
}

/**
 * Inicializar búsqueda y filtros para un módulo
 */
function initializeTableSearch(moduleName, customOptions = {}) {
    const config = getModuleConfig(moduleName);
    
    if (!config) {
        console.error(`⚠️ Módulo '${moduleName}' no configurado`);
        return null;
    }
    
    const options = {
        module: moduleName,
        tableSelector: 'table tbody',
        searchInputSelector: '#tableSearch',
        filterContainerSelector: '#filterContainer',
        resultCountSelector: '#resultCount',
        noResultsSelector: '#noResults',
        ...config,
        ...customOptions
    };
    
    // Inicializar después de que el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            return new TableSearchFilter(options);
        });
    } else {
        return new TableSearchFilter(options);
    }
}

// Exportar para uso global
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        MODULE_CONFIGS,
        getModuleConfig,
        initializeTableSearch
    };
}
