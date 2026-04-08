// Scripts extraídos de los reportes PDF (búsqueda, filtros, etc.)
function applySearch() {
    const name = document.getElementById('search-name').value;
    const url = new URL(window.location.href);
    if (name) {
        url.searchParams.set('name', name);
    } else {
        url.searchParams.delete('name');
    }
    window.location.href = url.toString();
}

function clearFilters(event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    var searchInput = document.getElementById('search-name');
    if (searchInput) searchInput.value = '';
    var url = new URL(window.location.href);
    url.search = '';
    window.location.href = url.pathname;
    return false;
}

// ... (agrega aquí otros scripts de filtros, portafolios, etc.) ...
