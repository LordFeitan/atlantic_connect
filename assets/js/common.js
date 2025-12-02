// Configuración común para DataTables
const DATA_TABLE_DEFAULTS = {
    language: {
        url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
    },
    responsive: true,
    autoWidth: false
};

// Función helper para inicializar tablas
function initDataTable(selector, options = {}) {
    return $(selector).DataTable({
        ...DATA_TABLE_DEFAULTS,
        ...options
    });
}
