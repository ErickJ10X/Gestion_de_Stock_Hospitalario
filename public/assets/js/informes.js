/**
 * Archivo JavaScript para la funcionalidad de los informes
 */
document.addEventListener('DOMContentLoaded', function() {
    // Cerrar alertas
    const alertCloseButtons = document.querySelectorAll('.list-alert__close');
    alertCloseButtons.forEach(button => {
        button.addEventListener('click', function() {
            const alert = this.closest('.list-alert');
            alert.style.display = 'none';
        });
    });

    // Validación de formularios de filtros
    const filtroActividadForm = document.getElementById('filtro-actividad-form');
    if (filtroActividadForm) {
        filtroActividadForm.addEventListener('reset', function() {
            setTimeout(() => {
                document.getElementById('btn-aplicar-filtro-actividad').click();
            }, 10);
        });
    }

    const filtroReposicionesForm = document.getElementById('filtro-reposiciones-form');
    if (filtroReposicionesForm) {
        filtroReposicionesForm.addEventListener('reset', function() {
            setTimeout(() => {
                document.getElementById('btn-aplicar-filtro-repo').click();
            }, 10);
        });
    }

    // Función auxiliar para exportar tabla a Excel/CSV
    window.exportarTablaCSV = function(tableId, filename = 'datos_exportados') {
        const table = document.getElementById(tableId);
        if (!table) {
            console.error('Tabla no encontrada');
            return;
        }

        let csv = [];
        const rows = table.querySelectorAll('tr');
        
        for (let i = 0; i < rows.length; i++) {
            const row = [], cols = rows[i].querySelectorAll('td, th');
            
            for (let j = 0; j < cols.length; j++) {
                // Obtener el texto plano sin HTML
                let texto = cols[j].innerText.replace(/"/g, '""');
                row.push('"' + texto + '"');
            }
            
            csv.push(row.join(','));
        }
        
        // Descargar CSV
        const csvString = csv.join('\n');
        const link = document.createElement('a');
        link.style.display = 'none';
        link.setAttribute('href', 'data:text/csv;charset=utf-8,' + encodeURIComponent('\uFEFF' + csvString));
        link.setAttribute('download', filename + '.csv');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    };
});
