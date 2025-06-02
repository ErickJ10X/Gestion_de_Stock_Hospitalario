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

    console.log('Inicialización de lectura.js completada');
});
