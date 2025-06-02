document.addEventListener('DOMContentLoaded', function() {
    console.log('Inicializando lectura.js...');
    
    // Búsqueda en la tabla de histórico
    const buscarInput = document.getElementById('buscarHistorico');
    if (buscarInput) {
        console.log('Input de búsqueda encontrado');
        buscarInput.addEventListener('keyup', function() {
            const value = this.value.toLowerCase();
            const rows = document.querySelectorAll("#tablaHistorico tbody tr");
            console.log(`Filtrando ${rows.length} filas con valor: ${value}`);
            
            rows.forEach(function(row) {
                const text = row.textContent.toLowerCase();
                row.style.display = text.indexOf(value) > -1 ? "" : "none";
            });
        });
    } else {
        console.log('Input de búsqueda no encontrado');
    }
    
    // Búsqueda en la tabla de lecturas
    const buscarLecturasInput = document.getElementById('buscarLecturas');
    if (buscarLecturasInput) {
        console.log('Input de búsqueda de lecturas encontrado');
        buscarLecturasInput.addEventListener('keyup', function() {
            const value = this.value.toLowerCase();
            const rows = document.querySelectorAll(".table tbody tr");
            console.log(`Filtrando ${rows.length} filas con valor: ${value}`);
            
            rows.forEach(function(row) {
                const text = row.textContent.toLowerCase();
                row.style.display = text.indexOf(value) > -1 ? "" : "none";
            });
        });
    }
    
    // Manejo del modal de eliminación
    const modalEliminar = document.getElementById('modal-eliminar');
    const modalBackdrop = document.getElementById('modal-backdrop');
    
    if (modalEliminar && modalBackdrop) {
        console.log('Modal de eliminación encontrado');
        
        document.querySelectorAll('.btn-eliminar').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const idLectura = this.getAttribute('data-id');
                console.log(`Click en eliminar lectura ID: ${idLectura}`);
                document.getElementById('idLecturaEliminar').value = idLectura;
                modalEliminar.style.display = 'flex';
                modalBackdrop.style.display = 'block';
            });
        });
        
        const cerrarModal = document.getElementById('cerrar-modal');
        if (cerrarModal) {
            cerrarModal.addEventListener('click', function() {
                console.log('Cerrando modal');
                modalEliminar.style.display = 'none';
                modalBackdrop.style.display = 'none';
            });
        }
        
        const cancelarEliminar = document.getElementById('cancelar-eliminar');
        if (cancelarEliminar) {
            cancelarEliminar.addEventListener('click', function() {
                console.log('Cancelando eliminación');
                modalEliminar.style.display = 'none';
                modalBackdrop.style.display = 'none';
            });
        }
    } else {
        console.log('Modal de eliminación no encontrado');
    }
    
    // Cerrar alertas
    document.querySelectorAll('.list-alert__close').forEach(function(btn) {
        btn.addEventListener('click', function() {
            console.log('Cerrando alerta');
            this.parentElement.style.display = 'none';
        });
    });

    // Validaciones de formularios - especificamos qué formulario validar
    const formRegistrarLectura = document.querySelector('.form-registro-lectura');
    if (formRegistrarLectura) {
        console.log('Formulario de registro encontrado');
        formRegistrarLectura.addEventListener('submit', function(e) {
            const producto = document.getElementById('id_producto');
            const botiquin = document.getElementById('id_botiquin');
            const cantidad = document.getElementById('cantidad_disponible');
            
            if (!producto || !botiquin || !cantidad) {
                console.error('No se encontraron todos los campos del formulario');
                return;
            }
            
            if (!producto.value || !botiquin.value || !cantidad.value) {
                console.log('Validación fallida: campos obligatorios incompletos');
                e.preventDefault();
                alert('Por favor, complete todos los campos obligatorios');
            } else {
                console.log('Formulario válido, enviando...');
            }
        });
    } else {
        console.log('Formulario de registro no encontrado');
    }
    
    // Validación del formulario de periodicidad
    const formPeriodicidad = document.querySelector('.form-periodicidad');
    if (formPeriodicidad) {
        console.log('Formulario de periodicidad encontrado');
        formPeriodicidad.addEventListener('submit', function(e) {
            const producto = document.getElementById('producto');
            const botiquin = document.getElementById('botiquin');
            const periodicidad = document.getElementById('periodicidad');
            
            if (!producto || !botiquin || !periodicidad) {
                console.error('No se encontraron todos los campos del formulario de periodicidad');
                return;
            }
            
            if (!producto.value || !botiquin.value || !periodicidad.value) {
                console.log('Validación fallida: campos obligatorios de periodicidad incompletos');
                e.preventDefault();
                alert('Por favor, complete todos los campos obligatorios');
            } else {
                console.log('Formulario de periodicidad válido, enviando...');
            }
        });
    }

    // Ordenación de tabla en el histórico
    document.querySelectorAll('#tablaHistorico thead th').forEach((headerCell, index) => {
        headerCell.style.cursor = 'pointer';
        headerCell.title = 'Clic para ordenar';
        
        headerCell.addEventListener('click', () => {
            // Determinar dirección de ordenación
            const currentIsAscending = headerCell.classList.contains('sort-asc');
            
            // Limpiar clases de ordenación en todos los headers
            document.querySelectorAll('#tablaHistorico th').forEach(th => {
                th.classList.remove('sort-asc', 'sort-desc');
            });
            
            // Establecer nueva dirección
            headerCell.classList.add(currentIsAscending ? 'sort-desc' : 'sort-asc');
            
            const tableBody = document.querySelector('#tablaHistorico tbody');
            const rows = Array.from(tableBody.querySelectorAll('tr'));
            
            // Ordenar filas
            const sortedRows = rows.sort((a, b) => {
                const aValue = a.cells[index].textContent.trim();
                const bValue = b.cells[index].textContent.trim();
                
                // Si es una fecha (columna de fecha de lectura)
                if (index === 4) { 
                    // Convertir formato dd/mm/yyyy HH:ii a objeto Date
                    const aDate = new Date(aValue.split(' ')[0].split('/').reverse().join('-') + ' ' + (aValue.split(' ')[1] || ''));
                    const bDate = new Date(bValue.split(' ')[0].split('/').reverse().join('-') + ' ' + (bValue.split(' ')[1] || ''));
                    return currentIsAscending ? bDate - aDate : aDate - bDate;
                }
                
                // Si es un número (ID o cantidad)
                if (index === 0 || index === 3) {
                    return currentIsAscending 
                        ? parseInt(bValue) - parseInt(aValue)
                        : parseInt(aValue) - parseInt(bValue);
                }
                
                // Ordenación normal para texto
                return currentIsAscending 
                    ? bValue.localeCompare(aValue)
                    : aValue.localeCompare(bValue);
            });
            
            // Vaciar y rellenar la tabla con filas ordenadas
            while (tableBody.firstChild) {
                tableBody.removeChild(tableBody.firstChild);
            }
            
            sortedRows.forEach(row => {
                tableBody.appendChild(row);
            });
        });
    });

    // Añadir estilo para ordenación
    if (!document.getElementById('sortStyleSheet')) {
        const style = document.createElement('style');
        style.id = 'sortStyleSheet';
        style.textContent = `
            .sort-asc::after {
                content: " ↑";
                color: #007bff;
            }
            .sort-desc::after {
                content: " ↓";
                color: #007bff;
            }
        `;
        document.head.appendChild(style);
    }

    console.log('Inicialización de lectura.js completada');
});
