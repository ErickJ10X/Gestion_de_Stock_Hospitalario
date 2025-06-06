/**
 * Script para la gestión de hospitales y plantas
 */

// Cambia la pestaña activa y muestra el panel correspondiente
function activarPestania(tabId) {
    // Quitar clase active de todos los botones y paneles
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));

    // Activar el botón y el panel correspondiente
    const btn = document.querySelector(`.tab-btn[data-tab="${tabId}"]`);
    const pane = document.getElementById(tabId);
    if (btn) btn.classList.add('active');
    if (pane) pane.classList.add('active');
}

// Almacenar datos de hospitales y plantas para acceso rápido
let hospitalesData = {};
let plantasData = {};

// Inicializar datos cuando cargue el documento
document.addEventListener('DOMContentLoaded', function() {
    // Cargar datos de hospitales
    if (window.datosHospitales) {
        window.datosHospitales.forEach(h => {
            hospitalesData[h.id] = h;
        });
    }

    // Cargar datos de plantas
    if (window.datosPlantas) {
        window.datosPlantas.forEach(p => {
            plantasData[p.id] = p;
        });
    }
});

// Mostrar formulario de edición de hospital
function showEditHospitalForm(hospitalId) {
    activarPestania('tab-agregar-editar');
    
    setTimeout(() => {
        // Activar botón "Editar Hospital" y desactivar los demás
        const formToggleBtns = document.querySelectorAll('.form-toggle-btn');
        formToggleBtns.forEach(btn => btn.classList.remove('active'));
        const btnEditarHospital = document.querySelector('.form-toggle-btn[data-target="editarHospitalForm"]');
        if (btnEditarHospital) btnEditarHospital.classList.add('active');
        
        // Mostrar sección editar hospital y ocultar las demás
        const formSections = document.querySelectorAll('.form-section');
        formSections.forEach(section => section.style.display = 'none');
        const editarHospitalForm = document.getElementById('editarHospitalForm');
        if (editarHospitalForm) editarHospitalForm.style.display = '';
        
        // Seleccionar hospital en el selector
        const select = document.getElementById('select_hospital');
        if (select) {
            select.value = hospitalId;
            // Disparar evento change manualmente para activar el handler
            const event = new Event('change');
            select.dispatchEvent(event);
        
            // Cargar datos del formulario directamente desde el objeto en memoria
            if (hospitalesData[hospitalId]) {
                const hospital = hospitalesData[hospitalId];
                
                // Rellenar el formulario con datos del hospital
                const idInput = document.getElementById('editar_hospital_id');
                const nombreInput = document.getElementById('editar_nombre_hospital');
                const ubicacionInput = document.getElementById('editar_ubicacion_hospital');
                
                if (idInput) idInput.value = hospital.id;
                if (nombreInput) nombreInput.value = hospital.nombre;
                if (ubicacionInput && hospital.ubicacion) ubicacionInput.value = hospital.ubicacion;
                
                // Mostrar el formulario y ocultar el mensaje
                const mensaje = document.getElementById('mensajeSeleccionHospital');
                const formContainer = document.getElementById('editar_hospital_form_container');
                if (mensaje) mensaje.style.display = 'none';
                if (formContainer) formContainer.style.display = 'block';
            }
        }
    }, 100);
}

// Mostrar formulario para crear un nuevo hospital
function showCreateHospitalForm() {
    activarPestania('tab-agregar-editar');
    
    setTimeout(() => {
        // Activar botón "Crear Hospital" y desactivar los demás
        const formToggleBtns = document.querySelectorAll('.form-toggle-btn');
        formToggleBtns.forEach(btn => btn.classList.remove('active'));
        const btnCrearHospital = document.querySelector('.form-toggle-btn[data-target="crearHospitalForm"]');
        if (btnCrearHospital) btnCrearHospital.classList.add('active');
        
        // Mostrar sección crear hospital y ocultar las demás
        const formSections = document.querySelectorAll('.form-section');
        formSections.forEach(section => section.style.display = 'none');
        const crearHospitalForm = document.getElementById('crearHospitalForm');
        if (crearHospitalForm) crearHospitalForm.style.display = '';

        // Limpiar formulario si existe
        const form = document.getElementById('formCrearHospital');
        if (form) form.reset();
    }, 100);
}

// Mostrar formulario de edición de planta
function showEditPlantaForm(plantaId) {
    activarPestania('tab-agregar-editar');
    
    setTimeout(() => {
        // Activar botón "Editar Planta" y desactivar los demás
        const formToggleBtns = document.querySelectorAll('.form-toggle-btn');
        formToggleBtns.forEach(btn => btn.classList.remove('active'));
        const btnEditarPlanta = document.querySelector('.form-toggle-btn[data-target="editarPlantaForm"]');
        if (btnEditarPlanta) btnEditarPlanta.classList.add('active');
        
        // Mostrar sección editar planta y ocultar las demás
        const formSections = document.querySelectorAll('.form-section');
        formSections.forEach(section => section.style.display = 'none');
        const editarPlantaForm = document.getElementById('editarPlantaForm');
        if (editarPlantaForm) editarPlantaForm.style.display = '';
        
        // Seleccionar planta en el selector
        const select = document.getElementById('select_planta');
        if (select) {
            select.value = plantaId;
            // Disparar evento change manualmente para activar el handler
            const event = new Event('change');
            select.dispatchEvent(event);
            
            // Cargar datos del formulario directamente
            if (plantasData[plantaId]) {
                const planta = plantasData[plantaId];
                
                // Rellenar el formulario con datos de la planta
                const idInput = document.getElementById('editar_planta_id');
                const nombreInput = document.getElementById('editar_nombre_planta');
                const hospitalSelect = document.getElementById('editar_hospital_planta');
                
                if (idInput) idInput.value = planta.id;
                if (nombreInput) nombreInput.value = planta.nombre;
                if (hospitalSelect) hospitalSelect.value = planta.idHospital;
                
                // Mostrar el formulario y ocultar el mensaje
                const mensaje = document.getElementById('mensajeSeleccionPlanta');
                const formContainer = document.getElementById('editar_planta_form_container');
                if (mensaje) mensaje.style.display = 'none';
                if (formContainer) formContainer.style.display = 'block';
            }
        }
    }, 100);
}

// Mostrar formulario para crear una nueva planta
function showCreatePlantaForm() {
    activarPestania('tab-agregar-editar');
    
    setTimeout(() => {
        // Activar botón "Crear Planta" y desactivar los demás
        const formToggleBtns = document.querySelectorAll('.form-toggle-btn');
        formToggleBtns.forEach(btn => btn.classList.remove('active'));
        const btnCrearPlanta = document.querySelector('.form-toggle-btn[data-target="crearPlantaForm"]');
        if (btnCrearPlanta) btnCrearPlanta.classList.add('active');
        
        // Mostrar sección crear planta y ocultar las demás
        const formSections = document.querySelectorAll('.form-section');
        formSections.forEach(section => section.style.display = 'none');
        const crearPlantaForm = document.getElementById('crearPlantaForm');
        if (crearPlantaForm) crearPlantaForm.style.display = '';

        // Limpiar formulario si existe
        const form = document.getElementById('formCrearPlanta');
        if (form) form.reset();
    }, 100);
}

// Delegar eventos en botones de las tablas
document.addEventListener('DOMContentLoaded', function () {
    // Botón nuevo hospital
    const btnNuevoHospital = document.getElementById('btnNuevoHospital');
    if (btnNuevoHospital) {
        btnNuevoHospital.addEventListener('click', function (e) {
            e.preventDefault();
            showCreateHospitalForm();
        });
    }
    
    // Botón nueva planta
    const btnNuevaPlanta = document.getElementById('btnNuevaPlanta');
    if (btnNuevaPlanta) {
        btnNuevaPlanta.addEventListener('click', function (e) {
            e.preventDefault();
            showCreatePlantaForm();
        });
    }

    // Botones editar hospital
    document.querySelectorAll('.btn-edit-hospital').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const hospitalId = this.getAttribute('data-id');
            showEditHospitalForm(hospitalId);
        });
    });

    // Botones eliminar hospital
    document.querySelectorAll('.btn-delete-hospital').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const hospitalId = this.getAttribute('data-id');
            if (confirm('¿Está seguro que desea eliminar este hospital? Esta acción no se puede deshacer.')) {
                // Redireccionar a la acción de eliminación
                window.location.href = `/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/HospitalController.php?action=eliminar&id=${hospitalId}`;
            }
        });
    });

    // Botones editar planta
    document.querySelectorAll('.btn-edit-planta').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const plantaId = this.getAttribute('data-id');
            showEditPlantaForm(plantaId);
        });
    });

    // Botones eliminar planta
    document.querySelectorAll('.btn-delete-planta').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const plantaId = this.getAttribute('data-id');
            if (confirm('¿Está seguro que desea eliminar esta planta? Esta acción no se puede deshacer.')) {
                // Redireccionar a la acción de eliminación
                window.location.href = `/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/PlantaController.php?action=eliminar&id=${plantaId}`;
            }
        });
    });

    // Alternar entre formularios en la pestaña Agregar/Editar
    const formToggleBtns = document.querySelectorAll('.form-toggle-btn');
    const formSections = document.querySelectorAll('.form-section');
    
    formToggleBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Quitar clase active de todos los botones
            formToggleBtns.forEach(b => b.classList.remove('active'));
            // Agregar clase active al botón clickeado
            this.classList.add('active');
            // Mostrar la sección correspondiente y ocultar las demás
            const target = this.getAttribute('data-target');
            formSections.forEach(section => {
                if (section.id === target) {
                    section.style.display = '';
                } else {
                    section.style.display = 'none';
                }
            });
        });
    });

    // Manejar la selección de un hospital en el selector
    const selectHospital = document.getElementById('select_hospital');
    const mensajeSeleccionHospital = document.getElementById('mensajeSeleccionHospital');
    const editarHospitalFormContainer = document.getElementById('editar_hospital_form_container');
    
    if (selectHospital) {
        selectHospital.addEventListener('change', function() {
            const hospitalId = this.value;
            
            if (hospitalId) {
                // Si existe un hospital con ese ID en nuestros datos cargados
                if (hospitalesData[hospitalId]) {
                    const hospital = hospitalesData[hospitalId];
                    
                    // Rellenar el formulario con datos del hospital
                    const idInput = document.getElementById('editar_hospital_id');
                    const nombreInput = document.getElementById('editar_nombre_hospital');
                    const ubicacionInput = document.getElementById('editar_ubicacion_hospital');
                    
                    if (idInput) idInput.value = hospital.id;
                    if (nombreInput) nombreInput.value = hospital.nombre;
                    if (ubicacionInput) ubicacionInput.value = hospital.ubicacion || '';
                    
                    // Mostrar el formulario y ocultar el mensaje
                    if (mensajeSeleccionHospital) mensajeSeleccionHospital.style.display = 'none';
                    if (editarHospitalFormContainer) editarHospitalFormContainer.style.display = 'block';
                } else {
                    console.warn('No se encontraron datos para el hospital ID:', hospitalId);
                }
            } else {
                // Ocultar el formulario y mostrar el mensaje si no se seleccionó ningún hospital
                if (mensajeSeleccionHospital) mensajeSeleccionHospital.style.display = 'block';
                if (editarHospitalFormContainer) editarHospitalFormContainer.style.display = 'none';
            }
        });
    }
    
    // Manejar la selección de una planta en el selector
    const selectPlanta = document.getElementById('select_planta');
    const mensajeSeleccionPlanta = document.getElementById('mensajeSeleccionPlanta');
    const editarPlantaFormContainer = document.getElementById('editar_planta_form_container');
    
    if (selectPlanta) {
        selectPlanta.addEventListener('change', function() {
            const plantaId = this.value;
            
            if (plantaId) {
                // Si existe una planta con ese ID en nuestros datos cargados
                if (plantasData[plantaId]) {
                    const planta = plantasData[plantaId];
                    
                    // Rellenar el formulario con datos de la planta
                    const idInput = document.getElementById('editar_planta_id');
                    const nombreInput = document.getElementById('editar_nombre_planta');
                    const hospitalSelect = document.getElementById('editar_hospital_planta');
                    
                    if (idInput) idInput.value = planta.id;
                    if (nombreInput) nombreInput.value = planta.nombre;
                    if (hospitalSelect) hospitalSelect.value = planta.idHospital;
                    
                    // Mostrar el formulario y ocultar el mensaje
                    if (mensajeSeleccionPlanta) mensajeSeleccionPlanta.style.display = 'none';
                    if (editarPlantaFormContainer) editarPlantaFormContainer.style.display = 'block';
                } else {
                    console.warn('No se encontraron datos para la planta ID:', plantaId);
                }
            } else {
                // Ocultar el formulario y mostrar el mensaje si no se seleccionó ninguna planta
                if (mensajeSeleccionPlanta) mensajeSeleccionPlanta.style.display = 'block';
                if (editarPlantaFormContainer) editarPlantaFormContainer.style.display = 'none';
            }
        });
    }
    
    // Manejar el botón de cancelar edición de hospital
    const cancelarEdicionHospital = document.getElementById('cancelar_edicion_hospital');
    if (cancelarEdicionHospital && selectHospital) {
        cancelarEdicionHospital.addEventListener('click', function() {
            selectHospital.value = '';
            if (editarHospitalFormContainer) editarHospitalFormContainer.style.display = 'none';
            if (mensajeSeleccionHospital) mensajeSeleccionHospital.style.display = 'block';
        });
    }
    
    // Manejar el botón de cancelar edición de planta
    const cancelarEdicionPlanta = document.getElementById('cancelar_edicion_planta');
    if (cancelarEdicionPlanta && selectPlanta) {
        cancelarEdicionPlanta.addEventListener('click', function() {
            selectPlanta.value = '';
            if (editarPlantaFormContainer) editarPlantaFormContainer.style.display = 'none';
            if (mensajeSeleccionPlanta) mensajeSeleccionPlanta.style.display = 'block';
        });
    }
});

/**
 * Sistema de paginación para tabla de hospitales
 */
document.addEventListener('DOMContentLoaded', function() {
    // Configuración inicial hospitales
    let paginaActualHospitales = 1;
    let registrosPorPaginaHospitales = 10;
    let filtroTextoHospitales = '';

    // Elementos DOM hospitales
    const tablaHospitales = document.getElementById('hospitalesDataTable');
    const tbodyHospitales = tablaHospitales ? tablaHospitales.querySelector('tbody') : null;
    const paginacionHospitales = document.getElementById('paginacion-hospitales');
    const selectRegistrosPorPaginaHospitales = document.getElementById('registrosPorPaginaHospitales');
    const inputBuscarHospital = document.getElementById('buscarHospital');
    const spanInicioHospitales = document.getElementById('inicio-registros-hospitales');
    const spanFinHospitales = document.getElementById('fin-registros-hospitales');
    const spanTotalHospitales = document.getElementById('total-registros-hospitales');

    // Verificar que tenemos todos los elementos necesarios para hospitales
    if (tablaHospitales && tbodyHospitales && window.datosHospitales) {
        inicializarPaginacion(
            window.datosHospitales,
            tbodyHospitales,
            paginacionHospitales,
            selectRegistrosPorPaginaHospitales,
            inputBuscarHospital,
            spanInicioHospitales,
            spanFinHospitales,
            spanTotalHospitales,
            generarFilaHospital,
            'hospitales'
        );
    }

    // Configuración inicial plantas
    let paginaActualPlantas = 1;
    let registrosPorPaginaPlantas = 10;
    let filtroTextoPlantas = '';

    // Elementos DOM plantas
    const tablaPlantas = document.getElementById('plantasDataTable');
    const tbodyPlantas = tablaPlantas ? tablaPlantas.querySelector('tbody') : null;
    const paginacionPlantas = document.getElementById('paginacion-plantas');
    const selectRegistrosPorPaginaPlantas = document.getElementById('registrosPorPaginaPlantas');
    const inputBuscarPlanta = document.getElementById('buscarPlanta');
    const spanInicioPlantas = document.getElementById('inicio-registros-plantas');
    const spanFinPlantas = document.getElementById('fin-registros-plantas');
    const spanTotalPlantas = document.getElementById('total-registros-plantas');

    // Verificar que tenemos todos los elementos necesarios para plantas
    if (tablaPlantas && tbodyPlantas && window.datosPlantas) {
        inicializarPaginacion(
            window.datosPlantas,
            tbodyPlantas,
            paginacionPlantas,
            selectRegistrosPorPaginaPlantas,
            inputBuscarPlanta,
            spanInicioPlantas,
            spanFinPlantas,
            spanTotalPlantas,
            generarFilaPlanta,
            'plantas'
        );
    }

    /**
     * Inicializa el sistema de paginación para una tabla
     */
    function inicializarPaginacion(
        datos,
        tbody,
        paginacionContainer,
        selectRegistrosPorPagina,
        inputBuscar,
        spanInicio,
        spanFin,
        spanTotal,
        generadorFila,
        tipo
    ) {
        // Variables de estado para esta paginación
        const estado = {
            paginaActual: 1,
            registrosPorPagina: 10,
            filtroTexto: ''
        };

        /**
         * Filtra los datos según el texto de búsqueda
         */
        function filtrarDatos() {
            if (!estado.filtroTexto) {
                return datos;
            }

            const termino = estado.filtroTexto.toLowerCase();
            return datos.filter(item => {
                if (tipo === 'hospitales') {
                    return item.nombre.toLowerCase().includes(termino);
                } else {
                    return item.nombre.toLowerCase().includes(termino) || 
                           item.nombreHospital.toLowerCase().includes(termino);
                }
            });
        }

        /**
         * Actualiza la tabla con los datos de la página actual
         */
        function actualizarTabla() {
            // Agregar clase loading para transición
            tbody.classList.add('loading');

            // Pequeño retraso para permitir que la transición sea visible
            setTimeout(() => {
                const datosFiltrados = filtrarDatos();
                const totalRegistros = datosFiltrados.length;

                // Calcular indices
                const inicio = (estado.paginaActual - 1) * estado.registrosPorPagina;
                let fin = inicio + estado.registrosPorPagina;
                if (fin > totalRegistros) fin = totalRegistros;

                // Obtener los registros de la página actual
                const registros = datosFiltrados.slice(inicio, fin);

                // Actualizar HTML de la tabla
                if (registros.length === 0) {
                    tbody.innerHTML = `
                        <tr class="no-results-row">
                            <td colspan="4" class="text-center py-4">
                                <i class="fas fa-info-circle me-2"></i>
                                No se encontraron ${tipo === 'hospitales' ? 'hospitales' : 'plantas'} registrados
                            </td>
                        </tr>
                    `;
                } else {
                    tbody.innerHTML = registros.map(generadorFila).join('');
                }

                // Actualizar contadores
                if (spanInicio) spanInicio.textContent = totalRegistros > 0 ? inicio + 1 : 0;
                if (spanFin) spanFin.textContent = fin;
                if (spanTotal) spanTotal.textContent = totalRegistros;

                // Actualizar paginación
                actualizarPaginacion(totalRegistros);

                // Reactivar eventos en los botones de la tabla
                activarEventosBotones(tipo);

                // Quitar clase loading
                tbody.classList.remove('loading');
            }, 150);
        }

        /**
         * Genera los botones de paginación
         */
        function actualizarPaginacion(totalRegistros) {
            if (!paginacionContainer) return;

            const totalPaginas = Math.ceil(totalRegistros / estado.registrosPorPagina);

            // Limpiar paginación anterior
            paginacionContainer.innerHTML = '';

            // Si no hay registros, no mostramos paginación
            if (totalRegistros === 0) {
                return;
            }

            // Botón primera página
            const liPrimera = document.createElement('li');
            liPrimera.className = `page-item ${estado.paginaActual === 1 ? 'disabled' : ''}`;
            liPrimera.innerHTML = `
                <a class="page-link" href="#" data-pagina="primera" aria-label="Primera página">
                    <span aria-hidden="true">&laquo;&laquo;</span>
                    <span class="sr-only">Primera</span>
                </a>
            `;
            paginacionContainer.appendChild(liPrimera);

            // Botón anterior
            const liAnterior = document.createElement('li');
            liAnterior.className = `page-item ${estado.paginaActual === 1 ? 'disabled' : ''}`;
            liAnterior.innerHTML = `
                <a class="page-link" href="#" data-pagina="anterior" aria-label="Página anterior">
                    <span aria-hidden="true">&laquo;</span>
                    <span class="sr-only">Anterior</span>
                </a>
            `;
            paginacionContainer.appendChild(liAnterior);

            // Determinar qué números mostrar (mostraremos 5 números máximo alrededor de la página actual)
            let inicio = Math.max(1, estado.paginaActual - 2);
            let fin = Math.min(totalPaginas, inicio + 4);

            // Si estamos cerca del final, ajustar el inicio
            if (fin === totalPaginas) {
                inicio = Math.max(1, totalPaginas - 4);
            }

            // Números de página
            for (let i = inicio; i <= fin; i++) {
                const li = document.createElement('li');
                li.className = `page-item ${i === estado.paginaActual ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="#" data-pagina="${i}">${i}</a>`;
                paginacionContainer.appendChild(li);
            }

            // Botón siguiente
            const liSiguiente = document.createElement('li');
            liSiguiente.className = `page-item ${estado.paginaActual === totalPaginas ? 'disabled' : ''}`;
            liSiguiente.innerHTML = `
                <a class="page-link" href="#" data-pagina="siguiente" aria-label="Página siguiente">
                    <span aria-hidden="true">&raquo;</span>
                    <span class="sr-only">Siguiente</span>
                </a>
            `;
            paginacionContainer.appendChild(liSiguiente);

            // Botón última página
            const liUltima = document.createElement('li');
            liUltima.className = `page-item ${estado.paginaActual === totalPaginas ? 'disabled' : ''}`;
            liUltima.innerHTML = `
                <a class="page-link" href="#" data-pagina="ultima" aria-label="Última página">
                    <span aria-hidden="true">&raquo;&raquo;</span>
                    <span class="sr-only">Última</span>
                </a>
            `;
            paginacionContainer.appendChild(liUltima);

            // Agregar manejador de eventos a todos los links de paginación
            paginacionContainer.querySelectorAll('.page-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (this.parentNode.classList.contains('disabled')) return;

                    const accion = this.getAttribute('data-pagina');
                    const totalPaginas = Math.ceil(filtrarDatos().length / estado.registrosPorPagina);
                    let paginaAnterior = estado.paginaActual;

                    // Determinar la nueva página según la acción
                    switch(accion) {
                        case 'primera':
                            estado.paginaActual = 1;
                            break;
                        case 'anterior':
                            if (estado.paginaActual > 1) estado.paginaActual--;
                            break;
                        case 'siguiente':
                            if (estado.paginaActual < totalPaginas) estado.paginaActual++;
                            break;
                        case 'ultima':
                            estado.paginaActual = totalPaginas;
                            break;
                        default:
                            // Si es un número de página
                            if (!isNaN(parseInt(accion))) {
                                estado.paginaActual = parseInt(accion);
                            }
                            break;
                    }

                    // Solo actualizar si realmente cambiamos de página
                    if (paginaAnterior !== estado.paginaActual) {
                        // Hacer scroll a la parte superior de la tabla si es necesario
                        if (window.innerWidth < 768) {
                            tablaHospitales.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                        actualizarTabla();
                    }
                });
            });
        }

        // Manejar cambio en registros por página
        if (selectRegistrosPorPagina) {
            selectRegistrosPorPagina.addEventListener('change', function() {
                estado.registrosPorPagina = parseInt(this.value);
                estado.paginaActual = 1; // Volver a la primera página
                actualizarTabla();
            });
        }

        // Manejar búsqueda
        if (inputBuscar) {
            inputBuscar.addEventListener('input', debounce(function() {
                estado.filtroTexto = this.value;
                estado.paginaActual = 1; // Volver a la primera página
                actualizarTabla();
            }, 300));
        }

        // Inicializar la tabla
        actualizarTabla();
    }

    /**
     * Función para retrasar la ejecución de una función (debounce)
     */
    function debounce(func, wait) {
        let timeout;
        return function() {
            const context = this, args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), wait);
        };
    }

    /**
     * Genera una fila de la tabla de hospitales
     */
    function generarFilaHospital(hospital) {
        return `
            <tr class="list-table__body-row">
                <td class="list-table__body-cell" data-label="ID">${escapeHtml(hospital.id)}</td>
                <td class="list-table__body-cell" data-label="Nombre">${escapeHtml(hospital.nombre)}</td>
                <td class="list-table__body-cell" data-label="Plantas">
                    <span class="badge bg-info">${hospital.plantas} plantas</span>
                </td>
                <td class="list-table__body-cell" data-label="Acciones">
                    <div class="list-table__actions">
                        <button class="list-table__button list-table__button--edit btn-edit-hospital" data-id="${hospital.id}">
                            <i class="bi bi-pencil-square list-table__button-icon"></i> Editar
                        </button>
                        <button class="list-table__button list-table__button--delete btn-delete-hospital" data-id="${hospital.id}">
                            <i class="bi bi-trash list-table__button-icon"></i> Eliminar
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }

    /**
     * Genera una fila de la tabla de plantas
     */
    function generarFilaPlanta(planta) {
        return `
            <tr class="list-table__body-row">
                <td class="list-table__body-cell" data-label="ID">${escapeHtml(planta.id)}</td>
                <td class="list-table__body-cell" data-label="Nombre">${escapeHtml(planta.nombre)}</td>
                <td class="list-table__body-cell" data-label="Hospital">
                    ${escapeHtml(planta.nombreHospital)}
                </td>
                <td class="list-table__body-cell" data-label="Acciones">
                    <div class="list-table__actions">
                        <button class="list-table__button list-table__button--edit btn-edit-planta" data-id="${planta.id}">
                            <i class="bi bi-pencil-square list-table__button-icon"></i> Editar
                        </button>
                        <button class="list-table__button list-table__button--delete btn-delete-planta" data-id="${planta.id}">
                            <i class="bi bi-trash list-table__button-icon"></i> Eliminar
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }

    /**
     * Escapa caracteres HTML por seguridad
     */
    function escapeHtml(texto) {
        if (texto === undefined || texto === null) return '';
        return texto
            .toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    /**
     * Activar eventos en los botones de edición y eliminación
     */
    function activarEventosBotones(tipo) {
        if (tipo === 'hospitales') {
            // Botones editar hospital
            document.querySelectorAll('.btn-edit-hospital').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const hospitalId = this.getAttribute('data-id');
                    showEditHospitalForm(hospitalId);
                });
            });

            // Botones eliminar hospital
            document.querySelectorAll('.btn-delete-hospital').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const hospitalId = this.getAttribute('data-id');
                    if (confirm('¿Está seguro que desea eliminar este hospital? Esta acción no se puede deshacer.')) {
                        window.location.href = `/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/HospitalController.php?action=eliminar&id=${hospitalId}`;
                    }
                });
            });
        } else if (tipo === 'plantas') {
            // Botones editar planta
            document.querySelectorAll('.btn-edit-planta').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const plantaId = this.getAttribute('data-id');
                    showEditPlantaForm(plantaId);
                });
            });

            // Botones eliminar planta
            document.querySelectorAll('.btn-delete-planta').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const plantaId = this.getAttribute('data-id');
                    if (confirm('¿Está seguro que desea eliminar esta planta? Esta acción no se puede deshacer.')) {
                        window.location.href = `/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/PlantaController.php?action=eliminar&id=${plantaId}`;
                    }
                });
            });
        }
    }
});
