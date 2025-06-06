/**
 * Archivo JavaScript para manejar la funcionalidad de los formularios de botiquines
 */

document.addEventListener('DOMContentLoaded', () => {
    // Inicialización de tooltips y popovers de Bootstrap si están presentes
    if (typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    }

    // Cerrar mensajes de alerta
    const alertCloseButtons = document.querySelectorAll('.list-alert__close');
    if (alertCloseButtons) {
        alertCloseButtons.forEach(button => {
            button.addEventListener('click', function() {
                const alert = this.closest('.list-alert');
                if (alert) {
                    alert.style.display = 'none';
                }
            });
        });
    }
    
    // Función para mostrar mensajes de alerta en el DOM
    function mostrarMensaje(tipo, texto, contenedor = '.botiquines-container') {
        const containerEl = document.querySelector(contenedor);
        if (!containerEl) return;
        
        const alertaEl = document.createElement('div');
        alertaEl.className = `alert alert-${tipo === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
        alertaEl.innerHTML = `
            ${texto}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        containerEl.insertBefore(alertaEl, containerEl.firstChild);
        
        // Eliminar automáticamente después de 5 segundos
        setTimeout(() => {
            alertaEl.classList.remove('show');
            setTimeout(() => alertaEl.remove(), 300);
        }, 5000);
    }

    // Configuración de la alternancia entre formularios
    const formToggleButtons = document.querySelectorAll('.form-toggle-btn');
    if (formToggleButtons.length) {
        formToggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Desactivar todos los botones
                formToggleButtons.forEach(btn => btn.classList.remove('active'));
                // Activar este botón
                this.classList.add('active');
                
                // Ocultar todos los formularios
                document.querySelectorAll('.form-section').forEach(form => {
                    form.style.display = 'none';
                });
                
                // Mostrar el formulario correspondiente
                const targetForm = document.getElementById(this.getAttribute('data-target'));
                if (targetForm) {
                    targetForm.style.display = 'block';
                }
            });
        });
    }

    // Manejar cambio de hospital en el formulario de creación
    const hospitalBotiquinCreate = document.getElementById('hospital_botiquin_create');
    const plantaBotiquinCreate = document.getElementById('planta_botiquin_create');
    
    if (hospitalBotiquinCreate && plantaBotiquinCreate) {
        hospitalBotiquinCreate.addEventListener('change', function() {
            actualizarPlantas(this.value, plantaBotiquinCreate);
        });
    }
    
    // Manejar cambio de hospital en el formulario de edición
    const hospitalBotiquinEdit = document.getElementById('hospital_botiquin_edit');
    const plantaBotiquinEdit = document.getElementById('planta_botiquin_edit');
    
    if (hospitalBotiquinEdit && plantaBotiquinEdit) {
        hospitalBotiquinEdit.addEventListener('change', function() {
            actualizarPlantas(this.value, plantaBotiquinEdit);
        });
    }
    
    // Función para actualizar las plantas según el hospital seleccionado
    function actualizarPlantas(idHospital, selectPlanta) {
        // Limpiar el select de planta
        selectPlanta.innerHTML = '';
        
        if (!idHospital) {
            // Si no hay hospital seleccionado, mostrar mensaje por defecto
            const optionDefault = document.createElement('option');
            optionDefault.value = '';
            optionDefault.textContent = 'Primero seleccione un hospital';
            selectPlanta.appendChild(optionDefault);
            selectPlanta.disabled = true;
            return;
        }
        
        // Habilitar el select
        selectPlanta.disabled = false;
        
        // Obtener las plantas del hospital seleccionado
        const plantas = window.plantasData[idHospital] || [];
        
        if (plantas.length === 0) {
            // Si no hay plantas para este hospital
            const optionNoData = document.createElement('option');
            optionNoData.value = '';
            optionNoData.textContent = 'No hay plantas para este hospital';
            selectPlanta.appendChild(optionNoData);
            return;
        }
        
        // Agregar opción por defecto
        const optionSelectPlanta = document.createElement('option');
        optionSelectPlanta.value = '';
        optionSelectPlanta.textContent = 'Seleccione una planta';
        selectPlanta.appendChild(optionSelectPlanta);
        
        // Agregar las plantas al select
        plantas.forEach(planta => {
            const option = document.createElement('option');
            option.value = planta.id;
            option.textContent = planta.nombre;
            selectPlanta.appendChild(option);
        });
    }
    
    // Manejar selección de botiquín para editar
    const selectBotiquin = document.getElementById('select_botiquin');
    const editarBotiquinFormContainer = document.getElementById('editar_botiquin_form_container');
    const mensajeSeleccion = document.getElementById('mensajeSeleccion');
    
    if (selectBotiquin && editarBotiquinFormContainer && mensajeSeleccion) {
        selectBotiquin.addEventListener('change', function() {
            const idBotiquin = this.value;
            
            if (!idBotiquin) {
                // Si no se selecciona botiquín, mostrar mensaje y ocultar formulario
                mensajeSeleccion.style.display = 'block';
                editarBotiquinFormContainer.style.display = 'none';
                return;
            }
            
            // Obtener datos del botiquín seleccionado
            const botiquinData = window.botiquinesData[idBotiquin];
            
            if (botiquinData) {
                // Asignar ID al formulario
                document.getElementById('editar_botiquin_id').value = botiquinData.id;
                
                // Asignar nombre
                document.getElementById('nombre_botiquin_edit').value = botiquinData.nombre;
                
                // Obtener hospital de la planta
                const idHospital = window.hospitalPorPlanta[botiquinData.planta_id];
                
                // Seleccionar hospital
                const hospitalSelect = document.getElementById('hospital_botiquin_edit');
                hospitalSelect.value = idHospital;
                
                // Actualizar plantas y seleccionar la correcta
                actualizarPlantas(idHospital, plantaBotiquinEdit);
                
                // Pequeña espera para que se carguen las plantas
                setTimeout(() => {
                    plantaBotiquinEdit.value = botiquinData.planta_id;
                }, 100);
                
                // Mostrar formulario y ocultar mensaje
                mensajeSeleccion.style.display = 'none';
                editarBotiquinFormContainer.style.display = 'block';
            }
        });
    }
    
    // Manejar botón cancelar edición
    const cancelarEdicionBotiquin = document.getElementById('cancelar_edicion_botiquin');
    if (cancelarEdicionBotiquin) {
        cancelarEdicionBotiquin.addEventListener('click', function() {
            // Resetear el select
            if (selectBotiquin) {
                selectBotiquin.value = '';
            }
            
            // Ocultar formulario y mostrar mensaje
            if (editarBotiquinFormContainer) {
                editarBotiquinFormContainer.style.display = 'none';
            }
            
            if (mensajeSeleccion) {
                mensajeSeleccion.style.display = 'block';
            }
        });
    }

    // SISTEMA DE PAGINACIÓN PARA BOTIQUINES
    // Variables para la paginación
    let paginaActual = 1;
    let registrosPorPagina = 10;
    let totalPaginas = Math.ceil(window.totalRegistros / registrosPorPagina);
    let datosFiltrados = window.datosBotiquines ? [...window.datosBotiquines] : [];

    // Referencias a elementos DOM para paginación
    const tablaBotiquines = document.querySelector('#botiquinesDataTable tbody');
    const paginacionBotiquines = document.getElementById('paginacion-botiquines');
    const inicioRegistros = document.getElementById('inicio-registros');
    const finRegistros = document.getElementById('fin-registros');
    const totalRegistrosElement = document.getElementById('total-registros');
    const buscarBotiquin = document.getElementById('buscarBotiquin');
    const registrosPorPaginaSelect = document.getElementById('registrosPorPagina');

    // Función para actualizar la tabla según la página actual
    function actualizarTablaBotiquines() {
        if (!tablaBotiquines || !datosFiltrados) return;

        const inicio = (paginaActual - 1) * registrosPorPagina;
        const fin = inicio + registrosPorPagina;
        const botiquinesPagina = datosFiltrados.slice(inicio, fin);

        // Limpiar la tabla
        tablaBotiquines.innerHTML = '';

        // Si no hay datos después del filtrado
        if (botiquinesPagina.length === 0) {
            const tr = document.createElement('tr');
            tr.className = 'no-results-row';
            tr.innerHTML = `
                <td colspan="5" class="text-center py-4">
                    <i class="fas fa-info-circle me-2"></i>
                    No se encontraron botiquines con los criterios de búsqueda
                </td>
            `;
            tablaBotiquines.appendChild(tr);
        } else {
            // Añadir filas correspondientes a la página actual
            botiquinesPagina.forEach(botiquin => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${botiquin.id}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-first-aid me-2 text-success"></i>
                            <span class="ms-2">${botiquin.nombre}</span>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-info">
                            <i class="fas fa-clinic-medical me-1"></i>
                            ${botiquin.planta_nombre}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-primary">
                            <i class="fas fa-hospital me-1"></i>
                            ${botiquin.hospital_nombre}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/botiquin/?id=${botiquin.id}" 
                                class="btn btn-sm btn-info"
                                title="Ver botiquín">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                            <button type="button"
                                   class="btn btn-sm btn-primary"
                                   onclick="editarBotiquin(${botiquin.id})" 
                                   title="Editar botiquín">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                        </div>
                    </td>
                `;
                tablaBotiquines.appendChild(tr);
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
        if (!paginacionBotiquines) return;

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

        paginacionBotiquines.innerHTML = paginadorHTML;

        // Agregar eventos a los enlaces de paginación
        document.querySelectorAll('#paginacion-botiquines .page-link').forEach(enlace => {
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

                actualizarTablaBotiquines();
            });
        });
    }

    // Buscador de botiquines
    if (buscarBotiquin) {
        buscarBotiquin.addEventListener('input', function() {
            const termino = this.value.toLowerCase().trim();
            
            if (window.datosBotiquines) {
                if (termino === '') {
                    // Si no hay término de búsqueda, mostrar todos
                    datosFiltrados = [...window.datosBotiquines];
                } else {
                    // Filtrar según el término de búsqueda
                    datosFiltrados = window.datosBotiquines.filter(botiquin => {
                        return botiquin.nombre.toLowerCase().includes(termino) || 
                               botiquin.planta_nombre.toLowerCase().includes(termino) || 
                               botiquin.hospital_nombre.toLowerCase().includes(termino);
                    });
                }
                
                paginaActual = 1; // Volver a la primera página con cada búsqueda
                actualizarTablaBotiquines();
            }
        });
    }

    // Manejador para cambiar registros por página
    if (registrosPorPaginaSelect) {
        registrosPorPaginaSelect.addEventListener('change', function() {
            registrosPorPagina = parseInt(this.value);
            paginaActual = 1; // Volver a la primera página
            actualizarTablaBotiquines();
        });
    }

    // Inicializar la tabla y paginador al cargar la página
    if (window.datosBotiquines && tablaBotiquines) {
        datosFiltrados = [...window.datosBotiquines];
        actualizarTablaBotiquines();
    }
    
    // Función global para navegar a la pestaña de crear botiquín
    window.irACrearBotiquin = function() {
        // Cambiar a la pestaña de agregar/editar
        const tabBtn = document.querySelector('.tab-btn[data-tab="tab-agregar-editar"]');
        if (tabBtn) {
            // Hacer clic en la pestaña para cambiar a ella
            tabBtn.click();
            
            // Pequeña espera para asegurar que la pestaña se haya cargado
            setTimeout(() => {
                // Activar el formulario "Crear Botiquín" (primero por defecto) 
                const crearBtn = document.querySelector('.form-toggle-btn[data-target="crearForm"]');
                if (crearBtn && !crearBtn.classList.contains('active')) {
                    crearBtn.click();
                }
            }, 100);
        }
    };
    
    // Función global para editar botiquín
    window.editarBotiquin = function(idBotiquin) {
        // Cambiar a la pestaña de agregar/editar
        const tabBtn = document.querySelector('.tab-btn[data-tab="tab-agregar-editar"]');
        if (tabBtn) {
            // Hacer clic en la pestaña para cambiar a ella
            tabBtn.click();
            
            // Pequeña espera para asegurar que la pestaña se haya cargado
            setTimeout(() => {
                // Activar el formulario "Editar Botiquín"
                const editarBtn = document.querySelector('.form-toggle-btn[data-target="editarForm"]');
                if (editarBtn) {
                    editarBtn.click();
                    
                    // Otra pequeña espera para asegurar que el formulario se active
                    setTimeout(() => {
                        // Seleccionar el botiquín en el dropdown
                        if (selectBotiquin) {
                            selectBotiquin.value = idBotiquin;
                            
                            // Disparar el evento change para cargar los datos del botiquín
                            const changeEvent = new Event('change');
                            selectBotiquin.dispatchEvent(changeEvent);
                        }
                    }, 100);
                }
            }, 100);
        }
    };
    
    // Asegurarse de que el botón de nuevo botiquín funcione correctamente
    const btnNuevoBotiquin = document.getElementById('btnNuevoBotiquin');
    if (btnNuevoBotiquin) {
        btnNuevoBotiquin.addEventListener('click', function(e) {
            e.preventDefault();
            window.irACrearBotiquin();
        });
    }
});
