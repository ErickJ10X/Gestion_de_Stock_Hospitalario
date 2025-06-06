/**
 * Archivo JavaScript para gestionar las funcionalidades de los almacenes
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

// Mostrar formulario de edición y seleccionar almacén
function editarAlmacen(id) {
    // Cambiar a la pestaña de agregar/editar
    activarPestania('tab-agregar-editar');
    
    // Activar el formulario de edición
    document.querySelector('.form-toggle-btn[data-target="editarForm"]').click();
    
    // Seleccionar el almacén en el dropdown
    const selectAlmacen = document.getElementById('select_almacen');
    if (selectAlmacen) {
        selectAlmacen.value = id;
        
        // Disparar el evento change para cargar los datos
        const event = new Event('change');
        selectAlmacen.dispatchEvent(event);
    }
}

// Función para eliminar un almacén
function eliminarAlmacen(id) {
    // Mostrar confirmación antes de eliminar
    if (confirm('¿Está seguro de que desea eliminar este almacén? Esta acción no se puede deshacer.')) {
        // Enviar solicitud para eliminar el almacén
        fetch('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/AlmacenesController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=eliminar&id=${id}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mostrar mensaje de éxito
                alert('Almacén eliminado correctamente');
                // Recargar la página para actualizar la lista
                window.location.reload();
            } else {
                // Mostrar mensaje de error
                alert('Error al eliminar el almacén: ' + (data.message || 'Se produjo un error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al procesar la solicitud');
        });
    }
}

// Inicialización cuando se carga el DOM
document.addEventListener('DOMContentLoaded', function() {
    // Alternar entre formularios de crear y editar
    document.querySelectorAll('.form-toggle-btn').forEach(button => {
        button.addEventListener('click', function() {
            const target = this.getAttribute('data-target');
            
            // Quitar clase active de todos los botones
            document.querySelectorAll('.form-toggle-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Ocultar todas las secciones de formulario
            document.querySelectorAll('.form-section').forEach(section => {
                section.style.display = 'none';
            });
            
            // Activar el botón seleccionado
            this.classList.add('active');
            
            // Mostrar la sección correspondiente
            const formSection = document.getElementById(target);
            if (formSection) {
                formSection.style.display = 'block';
            }
        });
    });
    
    // Formulario para crear almacén
    const formCrearAlmacen = document.getElementById('formCrearAlmacen');
    if (formCrearAlmacen) {
        formCrearAlmacen.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validar formulario
            if (!validarFormularioAlmacen(this)) {
                return false;
            }
            
            // Asegurarse de que el hospital_id se establezca correctamente
            const hospitalIdCreate = document.getElementById('hospital_id_create');
            const hospitalAlmacenCreate = document.getElementById('hospital_almacen_create');
            if (hospitalIdCreate && hospitalAlmacenCreate) {
                hospitalIdCreate.value = hospitalAlmacenCreate.value;
            }
            
            // Enviar formulario mediante fetch
            const formData = new FormData(this);
            
            fetch('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/AlmacenesController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostrar mensaje de éxito
                    alert('Almacén creado correctamente');
                    // Recargar la página o actualizar la lista
                    window.location.reload();
                } else {
                    // Mostrar mensaje de error
                    alert('Error al crear el almacén: ' + (data.message || 'Se produjo un error desconocido'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al procesar la solicitud');
            });
        });
    }
    
    // Formulario para editar almacén
    const formEditarAlmacen = document.getElementById('formEditarAlmacen');
    if (formEditarAlmacen) {
        formEditarAlmacen.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validar formulario
            if (!validarFormularioAlmacen(this)) {
                return false;
            }
            
            // Asegurarse de que el hospital_id se establezca correctamente
            const hospitalIdEdit = document.getElementById('hospital_id_edit');
            const hospitalAlmacenEdit = document.getElementById('hospital_almacen_edit');
            if (hospitalIdEdit && hospitalAlmacenEdit) {
                hospitalIdEdit.value = hospitalAlmacenEdit.value;
            }
            
            // Enviar el formulario tradicionalmente (sin fetch/ajax)
            this.submit();
        });
    }
    
    // Manejar cambio de hospital en el formulario de creación
    const hospitalAlmacenCreate = document.getElementById('hospital_almacen_create');
    const plantaAlmacenCreate = document.getElementById('planta_almacen_create');
    const hospitalIdCreate = document.getElementById('hospital_id_create');
    
    if (hospitalAlmacenCreate) {
        hospitalAlmacenCreate.addEventListener('change', function() {
            const hospitalId = this.value;
            actualizarPlantas(hospitalId, plantaAlmacenCreate);
            if (hospitalIdCreate) {
                hospitalIdCreate.value = hospitalId;
            }
        });
    }
    
    // Manejar cambio de hospital en el formulario de edición
    const hospitalAlmacenEdit = document.getElementById('hospital_almacen_edit');
    const plantaAlmacenEdit = document.getElementById('planta_almacen_edit');
    const hospitalIdEdit = document.getElementById('hospital_id_edit');
    
    if (hospitalAlmacenEdit) {
        hospitalAlmacenEdit.addEventListener('change', function() {
            const hospitalId = this.value;
            actualizarPlantas(hospitalId, plantaAlmacenEdit);
            if (hospitalIdEdit) {
                hospitalIdEdit.value = hospitalId;
            }
        });
    }
    
    // Manejar la selección de un almacén para editar
    const selectAlmacen = document.getElementById('select_almacen');
    const editarAlmacenFormContainer = document.getElementById('editar_almacen_form_container');
    const editarAlmacenPlaceholder = document.getElementById('editar_almacen_placeholder');
    
    if (selectAlmacen) {
        selectAlmacen.addEventListener('change', function() {
            const almacenId = this.value;
            
            if (almacenId && typeof almacenesData !== 'undefined') {
                const almacen = almacenesData[almacenId];
                if (almacen) {
                    // Llenar el formulario con los datos del almacén
                    const editarAlmacenId = document.getElementById('editar_almacen_id');
                    const tipoAlmacenEdit = document.getElementById('tipo_almacen_edit');
                    
                    if (editarAlmacenId) editarAlmacenId.value = almacen.id;
                    if (tipoAlmacenEdit) tipoAlmacenEdit.value = almacen.tipo;
                    
                    // Establecer el hospital
                    const hospitalId = almacen.hospital_id;
                    if (hospitalAlmacenEdit) {
                        hospitalAlmacenEdit.value = hospitalId;
                        if (hospitalIdEdit) hospitalIdEdit.value = hospitalId;
                    }
                    
                    // Cargar y seleccionar la planta
                    if (plantaAlmacenEdit) {
                        actualizarPlantas(hospitalId, plantaAlmacenEdit, almacen.planta_id);
                    }
                    
                    // Mostrar el formulario y ocultar el placeholder
                    if (editarAlmacenFormContainer) editarAlmacenFormContainer.style.display = 'block';
                    if (editarAlmacenPlaceholder) editarAlmacenPlaceholder.style.display = 'none';
                } else {
                    console.error('Error: No se encontraron datos para el almacén seleccionado');
                }
            } else {
                // Ocultar el formulario y mostrar el placeholder
                if (editarAlmacenFormContainer) editarAlmacenFormContainer.style.display = 'none';
                if (editarAlmacenPlaceholder) editarAlmacenPlaceholder.style.display = 'block';
            }
        });
    }
    
    // Manejar el botón de cancelar edición
    const cancelarEdicionAlmacen = document.getElementById('cancelar_edicion_almacen');
    if (cancelarEdicionAlmacen && selectAlmacen) {
        cancelarEdicionAlmacen.addEventListener('click', function() {
            selectAlmacen.value = '';
            const event = new Event('change');
            selectAlmacen.dispatchEvent(event);
        });
    }
    
    // Botón para agregar un nuevo almacén
    const btnAddAlmacen = document.getElementById('btn-add-almacen');
    if (btnAddAlmacen) {
        btnAddAlmacen.addEventListener('click', function() {
            activarPestania('tab-agregar-editar');
            // Activar el formulario de creación
            document.querySelector('.form-toggle-btn[data-target="crearForm"]').click();
        });
    }

    // Inicializar la paginación
    inicializarPaginacion();
});

// Función para actualizar las plantas según el hospital seleccionado
function actualizarPlantas(hospitalId, plantaSelect, selectedPlantaId = null) {
    if (!plantaSelect) return;
    
    plantaSelect.innerHTML = '<option value="">Seleccione una planta</option>';
    
    if (hospitalId && typeof plantasData !== 'undefined') {
        plantaSelect.disabled = false;
        const plantas = plantasData[hospitalId] || [];
        
        plantas.forEach(planta => {
            const option = document.createElement('option');
            option.value = planta.id;
            option.textContent = planta.nombre;
            if (selectedPlantaId && planta.id == selectedPlantaId) {
                option.selected = true;
            }
            plantaSelect.appendChild(option);
        });
    } else {
        plantaSelect.disabled = true;
    }
}

// Función para validar el formulario de almacén
function validarFormularioAlmacen(form) {
    // Validar que se seleccionó un hospital
    const hospitalId = form.querySelector('#hospital_almacen_create') ? 
                       form.querySelector('#hospital_almacen_create').value : 
                       form.querySelector('#hospital_almacen_edit').value;
    
    if (!hospitalId) {
        alert('Por favor, seleccione un hospital');
        return false;
    }
    
    // Validar que se seleccionó una planta (si es necesario)
    const plantaId = form.querySelector('[name="planta_id"]').value;
    const tipoAlmacen = form.querySelector('[name="tipo"]').value;
    
    if (tipoAlmacen === 'Planta' && !plantaId) {
        alert('Para almacenes de tipo Planta, debe seleccionar una planta');
        return false;
    }
    
    return true;
}

// Función para cerrar alertas
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.list-alert__close').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.list-alert').remove();
        });
    });
});

/**
 * Sistema de paginación para tablas con datos cargados en el cliente
 */
function inicializarPaginacion() {
    // Configuración inicial
    let paginaActual = 1;
    let registrosPorPagina = 10;
    let filtroTexto = '';

    // Elementos DOM
    const tabla = document.getElementById('almacenesDataTable');
    const tbody = tabla ? tabla.querySelector('tbody') : null;
    const paginacionContainer = document.getElementById('paginacion-almacenes');
    const selectRegistrosPorPagina = document.getElementById('registrosPorPagina');
    const inputBuscar = document.getElementById('buscarAlmacen');
    const spanInicio = document.getElementById('inicio-registros');
    const spanFin = document.getElementById('fin-registros');
    const spanTotal = document.getElementById('total-registros');

    // Verificar que tenemos todos los elementos necesarios
    if (!tabla || !tbody || !paginacionContainer || !window.datosAlmacenes) {
        console.error('No se encontraron los elementos necesarios para la paginación');
        return;
    }

    /**
     * Función para generar una fila de almacén
     */
    function generarFilaAlmacen(almacen) {
        const tipoClase = almacen.tipo === 'General' ? 'almacen-type--general' : 'almacen-type--planta';
        
        return `
            <tr class="list-table__body-row">
                <td class="list-table__body-cell" data-label="ID">${almacen.id}</td>
                <td class="list-table__body-cell" data-label="Tipo">
                    <span class="almacen-type ${tipoClase}">
                        ${escapeHtml(almacen.tipo)}
                    </span>
                </td>
                <td class="list-table__body-cell" data-label="Planta">
                    ${escapeHtml(almacen.planta_nombre)}
                </td>
                <td class="list-table__body-cell" data-label="Hospital">
                    ${escapeHtml(almacen.hospital_nombre)}
                </td>
                <td class="list-table__body-cell" data-label="Acciones">
                    <div class="list-table__actions">
                        <button class="list-table__button list-table__button--edit btn-edit-almacen" 
                                onclick="editarAlmacen(${almacen.id})">
                            <i class="bi bi-pencil-square list-table__button-icon"></i> Editar
                        </button>
                        <button class="list-table__button list-table__button--delete btn-delete-almacen"
                                onclick="eliminarAlmacen(${almacen.id})">
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
        if (!texto) return '';
        return texto
            .toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    /**
     * Filtra los datos según el texto de búsqueda
     */
    function filtrarDatos() {
        if (!filtroTexto) {
            return window.datosAlmacenes;
        }

        const termino = filtroTexto.toLowerCase();
        return window.datosAlmacenes.filter(almacen => {
            return almacen.tipo.toLowerCase().includes(termino) ||
                almacen.planta_nombre.toLowerCase().includes(termino) ||
                almacen.hospital_nombre.toLowerCase().includes(termino);
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
            const inicio = (paginaActual - 1) * registrosPorPagina;
            let fin = inicio + registrosPorPagina;
            if (fin > totalRegistros) fin = totalRegistros;

            // Obtener los registros de la página actual
            const registros = datosFiltrados.slice(inicio, fin);

            // Actualizar HTML de la tabla
            if (registros.length === 0) {
                tbody.innerHTML = `
                    <tr class="no-results-row">
                        <td colspan="5" class="text-center py-4">
                            <i class="fas fa-info-circle me-2"></i>
                            No se encontraron almacenes registrados
                        </td>
                    </tr>
                `;
            } else {
                tbody.innerHTML = registros.map(generarFilaAlmacen).join('');
            }

            // Actualizar contadores
            if (spanInicio) spanInicio.textContent = totalRegistros > 0 ? inicio + 1 : 0;
            if (spanFin) spanFin.textContent = fin;
            if (spanTotal) spanTotal.textContent = totalRegistros;

            // Actualizar paginación
            actualizarPaginacion(totalRegistros);

            // Quitar clase loading
            tbody.classList.remove('loading');
        }, 150);
    }

    /**
     * Genera los botones de paginación
     */
    function actualizarPaginacion(totalRegistros) {
        const totalPaginas = Math.ceil(totalRegistros / registrosPorPagina);

        // Si no hay registros o solo hay una página
        if (totalRegistros === 0 || totalPaginas === 1) {
            paginacionContainer.querySelectorAll('.page-item').forEach(item => {
                item.classList.add('disabled');
            });
            return;
        }

        // Actualizar estado de los botones primera/anterior
        const btnPrimera = paginacionContainer.querySelector('[data-pagina="primera"]').parentNode;
        const btnAnterior = paginacionContainer.querySelector('[data-pagina="anterior"]').parentNode;

        if (paginaActual === 1) {
            btnPrimera.classList.add('disabled');
            btnAnterior.classList.add('disabled');
        } else {
            btnPrimera.classList.remove('disabled');
            btnAnterior.classList.remove('disabled');
        }

        // Actualizar estado de los botones siguiente/última
        const btnSiguiente = paginacionContainer.querySelector('[data-pagina="siguiente"]').parentNode;
        const btnUltima = paginacionContainer.querySelector('[data-pagina="ultima"]').parentNode;

        if (paginaActual === totalPaginas) {
            btnSiguiente.classList.add('disabled');
            btnUltima.classList.add('disabled');
        } else {
            btnSiguiente.classList.remove('disabled');
            btnUltima.classList.remove('disabled');
        }

        // Generar números de página
        // Eliminar números existentes (excepto primera/anterior/siguiente/última)
        const botonesAEliminar = Array.from(paginacionContainer.querySelectorAll('.page-item')).filter(item => {
            const link = item.querySelector('a');
            return link && link.dataset.pagina && !isNaN(parseInt(link.dataset.pagina));
        });

        botonesAEliminar.forEach(btn => btn.remove());

        // Agregar nuevos números
        const btnSiguienteElement = paginacionContainer.querySelector('[data-pagina="siguiente"]').parentNode;

        // Determinar qué números mostrar (mostraremos 5 números máximo alrededor de la página actual)
        let inicio = Math.max(1, paginaActual - 2);
        let fin = Math.min(totalPaginas, inicio + 4);

        // Si estamos cerca del final, ajustar el inicio
        if (fin === totalPaginas) {
            inicio = Math.max(1, totalPaginas - 4);
        }

        for (let i = inicio; i <= fin; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${i === paginaActual ? 'active' : ''}`;

            const a = document.createElement('a');
            a.className = 'page-link';
            a.href = '#';
            a.textContent = i;
            a.dataset.pagina = i;

            li.appendChild(a);
            paginacionContainer.insertBefore(li, btnSiguienteElement);
        }
    }

    /**
     * Manejar clics en la paginación
     */
    paginacionContainer.addEventListener('click', function(event) {
        const target = event.target.closest('a');
        if (!target || target.parentNode.classList.contains('disabled')) return;

        event.preventDefault();

        const accion = target.dataset.pagina;
        const datosFiltrados = filtrarDatos();
        const totalPaginas = Math.ceil(datosFiltrados.length / registrosPorPagina);

        let paginaAnterior = paginaActual;

        // Determinar la nueva página según la acción
        switch(accion) {
            case 'primera':
                paginaActual = 1;
                break;
            case 'anterior':
                if (paginaActual > 1) paginaActual--;
                break;
            case 'siguiente':
                if (paginaActual < totalPaginas) paginaActual++;
                break;
            case 'ultima':
                paginaActual = totalPaginas;
                break;
            default:
                // Si es un número de página
                if (!isNaN(parseInt(accion))) {
                    paginaActual = parseInt(accion);
                }
                break;
        }

        // Solo actualizar si realmente cambiamos de página
        if (paginaAnterior !== paginaActual) {
            // Hacer scroll a la parte superior de la tabla si es necesario
            if (window.innerWidth < 768) {
                tabla.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
            actualizarTabla();
        }
    });

    /**
     * Manejar cambio en registros por página
     */
    if (selectRegistrosPorPagina) {
        selectRegistrosPorPagina.addEventListener('change', function() {
            registrosPorPagina = parseInt(this.value);
            paginaActual = 1; // Volver a la primera página
            actualizarTabla();
        });
    }

    /**
     * Manejar búsqueda
     */
    if (inputBuscar) {
        // Usamos keyup para que se dispare después de que el usuario haya terminado de escribir
        inputBuscar.addEventListener('input', debounce(function() {
            filtroTexto = this.value;
            paginaActual = 1; // Volver a la primera página
            actualizarTabla();
        }, 300));
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

    // Inicializar la tabla
    actualizarTabla();
}