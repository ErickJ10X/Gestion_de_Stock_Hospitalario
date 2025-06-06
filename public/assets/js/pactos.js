/**
 * Archivo JavaScript para manejar la funcionalidad de los pactos
 */

document.addEventListener('DOMContentLoaded', () => {
    // Inicialización de tooltips y popovers de Bootstrap si están presentes
    if (typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    }

    // SISTEMA DE PAGINACIÓN PARA PACTOS
    // Variables para la paginación
    let paginaActual = 1;
    let registrosPorPagina = 10;
    let totalPaginas = Math.ceil(window.totalRegistrosPactos / registrosPorPagina);
    let datosFiltrados = window.datosPactos ? [...window.datosPactos] : [];

    // Referencias a elementos DOM para paginación
    const tablaPactos = document.querySelector('#pactosDataTable tbody');
    const paginacionPactos = document.getElementById('paginacion-pactos');
    const inicioRegistros = document.getElementById('inicio-registros-pactos');
    const finRegistros = document.getElementById('fin-registros-pactos');
    const totalRegistrosElement = document.getElementById('total-registros-pactos');
    const buscarPacto = document.getElementById('buscarPacto');
    const registrosPorPaginaSelect = document.getElementById('registrosPorPaginaPactos');

    // Función para actualizar la tabla según la página actual
    function actualizarTablaPactos() {
        if (!tablaPactos || !datosFiltrados) return;

        const inicio = (paginaActual - 1) * registrosPorPagina;
        const fin = inicio + registrosPorPagina;
        const pactosPagina = datosFiltrados.slice(inicio, fin);

        // Limpiar la tabla
        tablaPactos.innerHTML = '';

        // Si no hay datos después del filtrado
        if (pactosPagina.length === 0) {
            const tr = document.createElement('tr');
            tr.className = 'no-results-row';
            tr.innerHTML = `
                <td colspan="6" class="text-center py-4">
                    <i class="fas fa-info-circle me-2"></i>
                    No se encontraron pactos con los criterios de búsqueda
                </td>
            `;
            tablaPactos.appendChild(tr);
        } else {
            // Añadir filas correspondientes a la página actual
            pactosPagina.forEach(pacto => {
                const tr = document.createElement('tr');
                tr.className = 'list-table__body-row';
                tr.innerHTML = `
                    <td class="list-table__body-cell" data-label="ID">${pacto.id}</td>
                    <td class="list-table__body-cell" data-label="Producto">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-box me-2 text-primary"></i>
                            <span>${pacto.producto_nombre}</span>
                        </div>
                    </td>
                    <td class="list-table__body-cell" data-label="Tipo Ubicación">
                        ${pacto.tipo_ubicacion === 'Planta' ? 
                          '<span class="badge bg-info">Planta</span>' : 
                          '<span class="badge bg-success">Botiquín</span>'}
                    </td>
                    <td class="list-table__body-cell" data-label="Destino">
                        ${pacto.destino_nombre}
                    </td>
                    <td class="list-table__body-cell" data-label="Cantidad Pactada">
                        <span class="badge bg-primary">${pacto.cantidad_pactada}</span>
                    </td>
                    <td class="list-table__body-cell" data-label="Acciones">
                        <div class="list-table__actions">
                            <button class="list-table__button list-table__button--edit btn-edit-pacto"
                                    data-id="${pacto.id}">
                                <i class="bi bi-pencil-square list-table__button-icon"></i> Editar
                            </button>
                            <button class="list-table__button list-table__button--delete btn-delete-pacto"
                                    data-id="${pacto.id}">
                                <i class="bi bi-trash list-table__button-icon"></i> Eliminar
                            </button>
                        </div>
                    </td>
                `;
                tablaPactos.appendChild(tr);
                
                // Añadir event listeners a los botones de esta fila
                const editBtn = tr.querySelector('.btn-edit-pacto');
                const deleteBtn = tr.querySelector('.btn-delete-pacto');
                
                if (editBtn) {
                    editBtn.addEventListener('click', function() {
                        const pactoId = this.getAttribute('data-id');
                        editarPacto(pactoId);
                    });
                }
                
                if (deleteBtn) {
                    deleteBtn.addEventListener('click', function() {
                        const pactoId = this.getAttribute('data-id');
                        confirmarEliminarPacto(pactoId);
                    });
                }
            });
        }

        // Actualizar información de registros mostrados
        if (inicioRegistros && finRegistros && totalRegistrosElement) {
            inicioRegistros.textContent = datosFiltrados.length > 0 ? inicio + 1 : 0;
            finRegistros.textContent = Math.min(fin, datosFiltrados.length);
            totalRegistrosElement.textContent = datosFiltrados.length;
        }

        // Actualizar el paginador
        actualizarPaginador();
    }

    // Función para actualizar el paginador
    function actualizarPaginador() {
        if (!paginacionPactos) return;

        totalPaginas = Math.ceil(datosFiltrados.length / registrosPorPagina);
        
        // Si no hay registros, establecer al menos una página
        if (totalPaginas === 0) totalPaginas = 1;

        // Ajustar página actual si está fuera de rango
        if (paginaActual > totalPaginas) {
            paginaActual = totalPaginas;
        }

        // Construir los enlaces de paginación
        let paginadorHTML = `
            <li class="page-item ${paginaActual === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-pagina="primera" aria-label="Primera página">
                    <span aria-hidden="true">&laquo;&laquo;</span>
                    <span class="sr-only">Primera</span>
                </a>
            </li>
            <li class="page-item ${paginaActual === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-pagina="anterior" aria-label="Página anterior">
                    <span aria-hidden="true">&laquo;</span>
                    <span class="sr-only">Anterior</span>
                </a>
            </li>
        `;

        // Límites para los números de página (mostrar un máximo de 5 páginas)
        let inicio = Math.max(1, paginaActual - 2);
        let fin = Math.min(totalPaginas, inicio + 4);
        
        // Ajustar inicio si estamos cerca del final
        if (fin === totalPaginas) {
            inicio = Math.max(1, fin - 4);
        }

        // Generar números de página
        for (let i = inicio; i <= fin; i++) {
            paginadorHTML += `
                <li class="page-item ${i === paginaActual ? 'active' : ''}">
                    <a class="page-link" href="#" data-pagina="${i}">${i}</a>
                </li>
            `;
        }

        paginadorHTML += `
            <li class="page-item ${paginaActual === totalPaginas ? 'disabled' : ''}">
                <a class="page-link" href="#" data-pagina="siguiente" aria-label="Página siguiente">
                    <span aria-hidden="true">&raquo;</span>
                    <span class="sr-only">Siguiente</span>
                </a>
            </li>
            <li class="page-item ${paginaActual === totalPaginas ? 'disabled' : ''}">
                <a class="page-link" href="#" data-pagina="ultima" aria-label="Última página">
                    <span aria-hidden="true">&raquo;&raquo;</span>
                    <span class="sr-only">Última</span>
                </a>
            </li>
        `;

        paginacionPactos.innerHTML = paginadorHTML;

        // Agregar eventos a los enlaces de paginación
        document.querySelectorAll('#paginacion-pactos .page-link').forEach(enlace => {
            enlace.addEventListener('click', function(e) {
                e.preventDefault();
                const accion = this.getAttribute('data-pagina');

                switch (accion) {
                    case 'primera':
                        paginaActual = 1;
                        break;
                    case 'anterior':
                        paginaActual = Math.max(1, paginaActual - 1);
                        break;
                    case 'siguiente':
                        paginaActual = Math.min(totalPaginas, paginaActual + 1);
                        break;
                    case 'ultima':
                        paginaActual = totalPaginas;
                        break;
                    default:
                        paginaActual = parseInt(accion);
                }

                actualizarTablaPactos();
            });
        });
    }

    // Buscador de pactos
    if (buscarPacto) {
        buscarPacto.addEventListener('input', function() {
            const termino = this.value.toLowerCase().trim();
            
            if (window.datosPactos) {
                if (termino === '') {
                    // Si no hay término de búsqueda, mostrar todos
                    datosFiltrados = [...window.datosPactos];
                } else {
                    // Filtrar según el término de búsqueda
                    datosFiltrados = window.datosPactos.filter(pacto => {
                        return pacto.producto_nombre.toLowerCase().includes(termino) || 
                               pacto.tipo_ubicacion.toLowerCase().includes(termino) || 
                               pacto.destino_nombre.toLowerCase().includes(termino) ||
                               String(pacto.cantidad_pactada).includes(termino);
                    });
                }
                
                paginaActual = 1; // Volver a la primera página con cada búsqueda
                actualizarTablaPactos();
            }
        });
    }

    // Manejador para cambiar registros por página
    if (registrosPorPaginaSelect) {
        registrosPorPaginaSelect.addEventListener('change', function() {
            registrosPorPagina = parseInt(this.value);
            paginaActual = 1; // Volver a la primera página
            actualizarTablaPactos();
        });
    }

    // Función para editar un pacto
    window.editarPacto = function(idPacto) {
        // Cambiar a la pestaña de agregar/editar
        const tabBtn = document.querySelector('.tab-btn[data-tab="tab-agregar-editar"]');
        if (tabBtn) {
            tabBtn.click();
            
            // Pequeña espera para asegurar que la pestaña se haya cargado
            setTimeout(() => {
                // Activar el formulario "Editar Pacto"
                const editarBtn = document.querySelector('.form-toggle-btn[data-target="editarPactoForm"]');
                if (editarBtn) {
                    editarBtn.click();
                    
                    // Otra pequeña espera para asegurar que el formulario se active
                    setTimeout(() => {
                        // Seleccionar el pacto en el dropdown
                        const selectPacto = document.getElementById('select_pacto');
                        if (selectPacto) {
                            selectPacto.value = idPacto;
                            
                            // Disparar el evento change para cargar los datos del pacto
                            const changeEvent = new Event('change');
                            selectPacto.dispatchEvent(changeEvent);
                        }
                    }, 100);
                }
            }, 100);
        }
    };
    
    // Función para confirmar eliminación de pacto
    window.confirmarEliminarPacto = function(idPacto) {
        document.getElementById('id_pacto_eliminar').value = idPacto;
        
        // Mostrar el modal de confirmación
        const eliminarModal = new bootstrap.Modal(document.getElementById('eliminarPactoModal'));
        eliminarModal.show();
    };

    // Inicializar la tabla y paginador al cargar la página
    if (window.datosPactos && tablaPactos) {
        datosFiltrados = [...window.datosPactos];
        actualizarTablaPactos();
    }
    
    // Manejar botón de nuevo pacto
    const btnNuevoPacto = document.getElementById('btnNuevoPacto');
    if (btnNuevoPacto) {
        btnNuevoPacto.addEventListener('click', function() {
            // Cambiar a la pestaña de agregar/editar
            const tabBtn = document.querySelector('.tab-btn[data-tab="tab-agregar-editar"]');
            if (tabBtn) {
                tabBtn.click();
                
                // Pequeña espera para asegurar que la pestaña se haya cargado
                setTimeout(() => {
                    // Activar el formulario "Crear Pacto" (primero por defecto) 
                    const crearBtn = document.querySelector('.form-toggle-btn[data-target="crearPactoForm"]');
                    if (crearBtn && !crearBtn.classList.contains('active')) {
                        crearBtn.click();
                    }
                }, 100);
            }
        });
    }
});
