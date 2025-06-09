/**
 * Script para la gestión de lecturas de stock
 */

// Variables globales
let datosLecturas = [];
let datosProximasLecturas = [];
let paginaActualLecturas = 1;
let registrosPorPaginaLecturas = 10;
let filtroTextoLecturas = '';
let filtroBotiquin = '';

// Inicializar cuando el documento esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Cargar datos iniciales desde el objeto window si existen
    if (window.datosLecturas) {
        datosLecturas = window.datosLecturas;
    }

    if (window.datosProximasLecturas) {
        datosProximasLecturas = window.datosProximasLecturas;
    }

    // Configurar paginación para la tabla de lecturas
    inicializarPaginacionLecturas();
    
    // Configurar filtros de botiquín en la pestaña de historial
    configurarFiltrosBotiquin();
    
    // Configurar filtros para próximas lecturas
    configurarFiltrosProximasLecturas();
    
    // Inicializar eventos para el formulario de registro
    inicializarFormularioRegistro();
    
    // Manejar alertas
    const alertCloseButtons = document.querySelectorAll('.list-alert__close');
    alertCloseButtons.forEach(button => {
        button.addEventListener('click', function() {
            this.parentElement.style.display = 'none';
        });
    });

    // Inicializar modales de Bootstrap
    var modalTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="modal"]'))
    var modalList = modalTriggerList.map(function(modalTriggerEl) {
        return new bootstrap.Modal(document.querySelector(modalTriggerEl.getAttribute('data-bs-target')));
    });
});

/**
 * Inicializa la paginación para la tabla de lecturas históricas
 */
function inicializarPaginacionLecturas() {
    // Elementos DOM para la tabla de lecturas
    const tablaLecturas = document.getElementById('tabla-lecturas');
    if (!tablaLecturas) return;
    
    const tbodyLecturas = tablaLecturas.querySelector('tbody');
    const paginacionLecturas = document.getElementById('paginacion-lecturas');
    const selectRegistrosPorPagina = document.getElementById('registrosPorPaginaLecturas');
    const inputBuscar = document.getElementById('buscarLectura');
    const spanInicio = document.getElementById('inicio-registros-lecturas');
    const spanFin = document.getElementById('fin-registros-lecturas');
    const spanTotal = document.getElementById('total-registros-lecturas');
    
    // Manejador para cambio en registros por página
    if (selectRegistrosPorPagina) {
        selectRegistrosPorPagina.addEventListener('change', function() {
            registrosPorPaginaLecturas = parseInt(this.value);
            paginaActualLecturas = 1;
            actualizarTablaLecturas();
        });
    }
    
    // Manejador para buscar
    if (inputBuscar) {
        inputBuscar.addEventListener('input', debounce(function() {
            filtroTextoLecturas = this.value;
            paginaActualLecturas = 1;
            actualizarTablaLecturas();
        }, 300));
    }
    
    // Primera actualización
    actualizarTablaLecturas();
    
    /**
     * Actualiza la tabla con los datos filtrados y paginados
     */
    function actualizarTablaLecturas() {
        if (!tbodyLecturas) return;
        
        const datosFiltrados = filtrarDatosLecturas();
        const totalRegistros = datosFiltrados.length;
        
        // Calcular índices para la paginación
        const inicio = (paginaActualLecturas - 1) * registrosPorPaginaLecturas;
        let fin = inicio + registrosPorPaginaLecturas;
        if (fin > totalRegistros) fin = totalRegistros;
        
        // Obtener los registros para esta página
        const registrosPagina = datosFiltrados.slice(inicio, fin);
        
        // Actualizar la tabla
        if (registrosPagina.length === 0) {
            tbodyLecturas.innerHTML = `
                <tr>
                    <td colspan="7" class="list-table__empty">No hay lecturas registradas o que coincidan con el filtro</td>
                </tr>
            `;
        } else {
            tbodyLecturas.innerHTML = registrosPagina.map(generarFilaLectura).join('');
        }
        
        // Actualizar contadores
        if (spanInicio) spanInicio.textContent = totalRegistros > 0 ? inicio + 1 : 0;
        if (spanFin) spanFin.textContent = fin;
        if (spanTotal) spanTotal.textContent = totalRegistros;
        
        // Actualizar paginación
        actualizarPaginacionLecturas(totalRegistros);
    }
    
    /**
     * Filtra los datos de lecturas según los criterios actuales
     */
    function filtrarDatosLecturas() {
        let resultado = datosLecturas;
        
        // Filtrar por botiquín si hay uno seleccionado
        if (filtroBotiquin) {
            resultado = resultado.filter(lectura => lectura.botiquin_id == filtroBotiquin);
        }
        
        // Filtrar por texto de búsqueda
        if (filtroTextoLecturas) {
            const termino = filtroTextoLecturas.toLowerCase();
            resultado = resultado.filter(lectura => 
                lectura.botiquin_nombre.toLowerCase().includes(termino) ||
                lectura.producto_nombre.toLowerCase().includes(termino) ||
                lectura.usuario.toLowerCase().includes(termino)
            );
        }
        
        return resultado;
    }
    
    /**
     * Genera HTML para los controles de paginación
     */
    function actualizarPaginacionLecturas(totalRegistros) {
        if (!paginacionLecturas) return;
        
        const totalPaginas = Math.ceil(totalRegistros / registrosPorPaginaLecturas);
        
        // Limpiar paginación anterior
        paginacionLecturas.innerHTML = '';
        
        // Si no hay registros o solo hay una página, no mostramos paginación
        if (totalRegistros === 0 || totalPaginas === 1) {
            return;
        }
        
        // Botón primera página
        const liPrimera = document.createElement('li');
        liPrimera.className = `page-item ${paginaActualLecturas === 1 ? 'disabled' : ''}`;
        liPrimera.innerHTML = `
            <a class="page-link" href="#" data-pagina="1" aria-label="Primera página">
                <span aria-hidden="true">&laquo;&laquo;</span>
                <span class="sr-only">Primera</span>
            </a>
        `;
        paginacionLecturas.appendChild(liPrimera);
        
        // Botón anterior
        const liAnterior = document.createElement('li');
        liAnterior.className = `page-item ${paginaActualLecturas === 1 ? 'disabled' : ''}`;
        liAnterior.innerHTML = `
            <a class="page-link" href="#" data-pagina="${paginaActualLecturas - 1}" aria-label="Página anterior">
                <span aria-hidden="true">&laquo;</span>
                <span class="sr-only">Anterior</span>
            </a>
        `;
        paginacionLecturas.appendChild(liAnterior);
        
        // Determinar qué números mostrar (5 números máximo)
        let inicio = Math.max(1, paginaActualLecturas - 2);
        let fin = Math.min(totalPaginas, inicio + 4);
        
        // Ajustar inicio si estamos cerca del final
        if (fin === totalPaginas) {
            inicio = Math.max(1, totalPaginas - 4);
        }
        
        // Números de página
        for (let i = inicio; i <= fin; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${i === paginaActualLecturas ? 'active' : ''}`;
            li.innerHTML = `<a class="page-link" href="#" data-pagina="${i}">${i}</a>`;
            paginacionLecturas.appendChild(li);
        }
        
        // Botón siguiente
        const liSiguiente = document.createElement('li');
        liSiguiente.className = `page-item ${paginaActualLecturas === totalPaginas ? 'disabled' : ''}`;
        liSiguiente.innerHTML = `
            <a class="page-link" href="#" data-pagina="${paginaActualLecturas + 1}" aria-label="Página siguiente">
                <span aria-hidden="true">&raquo;</span>
                <span class="sr-only">Siguiente</span>
            </a>
        `;
        paginacionLecturas.appendChild(liSiguiente);
        
        // Botón última página
        const liUltima = document.createElement('li');
        liUltima.className = `page-item ${paginaActualLecturas === totalPaginas ? 'disabled' : ''}`;
        liUltima.innerHTML = `
            <a class="page-link" href="#" data-pagina="${totalPaginas}" aria-label="Última página">
                <span aria-hidden="true">&raquo;&raquo;</span>
                <span class="sr-only">Última</span>
            </a>
        `;
        paginacionLecturas.appendChild(liUltima);
        
        // Agregar manejador de eventos a los botones de paginación
        paginacionLecturas.querySelectorAll('.page-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                if (this.parentNode.classList.contains('disabled')) return;
                
                const pagina = parseInt(this.getAttribute('data-pagina'));
                if (!isNaN(pagina) && pagina !== paginaActualLecturas) {
                    paginaActualLecturas = pagina;
                    actualizarTablaLecturas();
                }
            });
        });
    }
}

/**
 * Configura los filtros de botiquín para la tabla de lecturas históricas
 */
function configurarFiltrosBotiquin() {
    const filtroBotiquinSelect = document.getElementById('filtro-botiquin');
    const btnFiltrar = document.getElementById('btn-filtrar');
    const btnResetFiltros = document.getElementById('btn-reset-filtros');
    
    if (!filtroBotiquinSelect || !btnFiltrar || !btnResetFiltros) return;
    
    btnFiltrar.addEventListener('click', function() {
        filtroBotiquin = filtroBotiquinSelect.value;
        paginaActualLecturas = 1;
        actualizarTablaLecturas();
    });
    
    btnResetFiltros.addEventListener('click', function() {
        filtroBotiquinSelect.value = '';
        filtroBotiquin = '';
        paginaActualLecturas = 1;
        actualizarTablaLecturas();
    });
}

/**
 * Genera una fila HTML para la tabla de lecturas
 */
function generarFilaLectura(lectura) {
    return `
        <tr class="list-table__body-row" data-botiquin="${lectura.botiquin_id}">
            <td class="list-table__body-cell" data-label="ID">${lectura.id}</td>
            <td class="list-table__body-cell" data-label="Botiquín">${escapeHtml(lectura.botiquin_nombre)}</td>
            <td class="list-table__body-cell" data-label="Producto">${escapeHtml(lectura.producto_nombre)}</td>
            <td class="list-table__body-cell" data-label="Cantidad">${lectura.cantidad}</td>
            <td class="list-table__body-cell" data-label="Fecha">${lectura.fecha}</td>
            <td class="list-table__body-cell" data-label="Usuario">${escapeHtml(lectura.usuario)}</td>
            <td class="list-table__body-cell" data-label="Acciones">
                <div class="list-table__actions">
                    <button class="list-table__button list-table__button--edit btn-view-lectura"
                            onclick="verDetalleLectura(${lectura.id})">
                        <i class="bi bi-eye list-table__button-icon"></i> Ver
                    </button>
                </div>
            </td>
        </tr>
    `;
}

/**
 * Función para ver el detalle de una lectura - ahora usa datos precargados, sin AJAX
 */
function verDetalleLectura(lecturaId) {
    const detalleContent = document.getElementById('detalle-lectura-content');
    if (!detalleContent) return;

    // Abrir el modal
    var myModal = new bootstrap.Modal(document.getElementById('modal-detalle-lectura'));
    myModal.show();

    // Buscar los datos precargados
    if (window.detallesLecturas && window.detallesLecturas[lecturaId]) {
        const lectura = window.detallesLecturas[lecturaId];

        // Formato de la fecha
        const fechaObj = new Date(lectura.fecha_lectura);
        const fechaFormateada = fechaObj.toLocaleDateString('es-ES') + ' ' + fechaObj.toLocaleTimeString('es-ES');

        let html = `
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th>ID Lectura:</th>
                        <td>${lectura.id_lectura}</td>
                    </tr>
                    <tr>
                        <th>Botiquín:</th>
                        <td>${lectura.nombre_botiquin || 'Desconocido'}</td>
                    </tr>
                    <tr>
                        <th>Producto:</th>
                        <td>${lectura.codigo_producto || ''} - ${lectura.nombre_producto || 'Desconocido'}</td>
                    </tr>
                    <tr>
                        <th>Cantidad disponible:</th>
                        <td>${lectura.cantidad_disponible}</td>
                    </tr>
                    <tr>
                        <th>Fecha de lectura:</th>
                        <td>${fechaFormateada}</td>
                    </tr>
                    <tr>
                        <th>Registrado por:</th>
                        <td>${lectura.nombre_usuario || lectura.registrado_por}</td>
                    </tr>
                    <tr>
                        <th>Unidad de medida:</th>
                        <td>${lectura.unidad_medida || 'No especificada'}</td>
                    </tr>
                </table>
            </div>
        `;

        detalleContent.innerHTML = html;
    } else {
        detalleContent.innerHTML = `<div class="alert alert-danger">No se encontraron detalles para esta lectura</div>`;
    }
}

/**
 * Configura los filtros para próximas lecturas
 */
function configurarFiltrosProximasLecturas() {
    const filtroBotiquin = document.getElementById('filtro-botiquin-proximas');
    const btnFiltrar = document.getElementById('btn-filtrar-proximas');
    const btnResetFiltros = document.getElementById('btn-reset-filtros-proximas');
    const tbodyProximas = document.getElementById('proximas-lecturas-body');
    
    if (!filtroBotiquin || !btnFiltrar || !btnResetFiltros || !tbodyProximas) return;
    
    btnFiltrar.addEventListener('click', function() {
        const botiquinId = filtroBotiquin.value;
        filtrarProximasLecturas(botiquinId);
    });
    
    btnResetFiltros.addEventListener('click', function() {
        filtroBotiquin.value = '';
        filtrarProximasLecturas('');
    });
    
    // Inicializar paginación
    const paginaProxima = document.getElementById('paginacion-proximas');
    const selectRegistrosPorPaginaProximas = document.getElementById('registrosPorPaginaProximas');
    const buscarProximaLectura = document.getElementById('buscarProximaLectura');
    
    // Configurar eventos
    if (selectRegistrosPorPaginaProximas) {
        selectRegistrosPorPaginaProximas.addEventListener('change', function() {
            const botiquinId = filtroBotiquin.value;
            filtrarProximasLecturas(botiquinId);
        });
    }
    
    if (buscarProximaLectura) {
        buscarProximaLectura.addEventListener('input', debounce(function() {
            const botiquinId = filtroBotiquin.value;
            filtrarProximasLecturas(botiquinId, this.value);
        }, 300));
    }
}

/**
 * Filtra las próximas lecturas según los criterios
 */
function filtrarProximasLecturas(botiquinId, textoBusqueda = '') {
    // Esta función filtra los datos cargados previamente
    if (!window.datosProximasLecturas) return;
    
    const tbody = document.getElementById('proximas-lecturas-body');
    if (!tbody) return;
    
    // Filtrar los datos
    let datosProximas = [...window.datosProximasLecturas];
    
    // Filtrar por botiquín
    if (botiquinId) {
        datosProximas = datosProximas.filter(lectura => lectura.id_botiquin == botiquinId);
    }
    
    // Filtrar por texto
    if (textoBusqueda) {
        const termino = textoBusqueda.toLowerCase();
        datosProximas = datosProximas.filter(lectura => 
            lectura.nombre_botiquin.toLowerCase().includes(termino) ||
            lectura.nombre_producto.toLowerCase().includes(termino) ||
            lectura.codigo_producto.toLowerCase().includes(termino)
        );
    }
    
    // Actualizar la tabla
    actualizarTablaProximasLecturas(datosProximas);
}

/**
 * Actualiza la tabla de próximas lecturas con los datos filtrados
 */
function actualizarTablaProximasLecturas(datos) {
    const tbody = document.getElementById('proximas-lecturas-body');
    const spanInicio = document.getElementById('inicio-registros-proximas');
    const spanFin = document.getElementById('fin-registros-proximas');
    const spanTotal = document.getElementById('total-registros-proximas');
    
    if (!tbody) return;
    
    if (datos.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center">
                    <div class="alert alert-info my-3">
                        No hay próximas lecturas programadas que coincidan con los filtros
                    </div>
                </td>
            </tr>
        `;
        
        if (spanInicio) spanInicio.textContent = '0';
        if (spanFin) spanFin.textContent = '0';
        if (spanTotal) spanTotal.textContent = '0';
        return;
    }
    
    // Actualizar contadores
    if (spanInicio) spanInicio.textContent = '1';
    if (spanFin) spanFin.textContent = Math.min(datos.length, 10);
    if (spanTotal) spanTotal.textContent = datos.length;
    
    // Generar filas
    let html = '';
    
    datos.forEach(lectura => {
        // Calcular el estado basado en la fecha próxima
        let estado = '';
        let estadoClass = '';

        const hoy = new Date();
        const fechaProxima = new Date(lectura.fecha_proxima_lectura);

        if (fechaProxima < hoy) {
            estado = 'Atrasada';
            estadoClass = 'bg-danger';
        } else {
            // Calcular días de diferencia
            const diffDays = Math.ceil((fechaProxima - hoy) / (1000 * 60 * 60 * 24));

            if (diffDays <= 2) {
                estado = 'Urgente';
                estadoClass = 'bg-warning';
            } else if (diffDays <= 7) {
                estado = 'Próxima';
                estadoClass = 'bg-info';
            } else {
                estado = 'Programada';
                estadoClass = 'bg-success';
            }
        }

        // Formatear fechas
        const fechaUltimaLectura = new Date(lectura.ultima_fecha_lectura);
        const fechaUltimaFormateada = fechaUltimaLectura.toLocaleDateString('es-ES');

        const fechaProximaFormateada = fechaProxima.toLocaleDateString('es-ES');
        
        html += `
            <tr class="list-table__body-row" data-botiquin-id="${lectura.id_botiquin}">
                <td class="list-table__body-cell" data-label="Botiquín">${escapeHtml(lectura.nombre_botiquin)}</td>
                <td class="list-table__body-cell" data-label="Producto">${escapeHtml(lectura.codigo_producto + ' - ' + lectura.nombre_producto)}</td>
                <td class="list-table__body-cell" data-label="Última lectura">${fechaUltimaFormateada}</td>
                <td class="list-table__body-cell" data-label="Cantidad">${lectura.cantidad_disponible}</td>
                <td class="list-table__body-cell" data-label="Próxima lectura">${fechaProximaFormateada}</td>
                <td class="list-table__body-cell" data-label="Estado">
                    <span class="badge ${estadoClass} text-white">${estado}</span>
                </td>
                <td class="list-table__body-cell" data-label="Acciones">
                    <div class="list-table__actions">
                        <button class="list-table__button list-table__button--edit btn-registrar-lectura"
                                data-botiquin-id="${lectura.id_botiquin}"
                                data-producto-id="${lectura.id_producto}"
                                onclick="registrarNuevaLectura(${lectura.id_botiquin}, ${lectura.id_producto})">
                            <i class="bi bi-clipboard-plus list-table__button-icon"></i> Registrar
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

/**
 * Inicializa el formulario de registro de lecturas
 */
function inicializarFormularioRegistro() {
    const botiquinSelect = document.getElementById('id_botiquin');
    const productoSelect = document.getElementById('id_producto');
    const productoLoading = document.getElementById('producto-loading');
    const productoError = document.getElementById('producto-error');
    
    if (!botiquinSelect) return;

    botiquinSelect.addEventListener('change', function() {
        const botiquinId = this.value;
        productoSelect.disabled = true;
        productoSelect.innerHTML = '<option value="">Seleccione un producto</option>';

        if (!botiquinId) {
            return;
        }

        // Mostrar indicador de carga
        productoLoading.style.display = 'inline';
        productoError.style.display = 'none';

        // Usar datos precargados en lugar de hacer una petición AJAX
        if (window.productosPorBotiquin && window.productosPorBotiquin[botiquinId]) {
            const productos = window.productosPorBotiquin[botiquinId];
            productoLoading.style.display = 'none';

            if (productos.length > 0) {
                productos.forEach(producto => {
                    const option = document.createElement('option');
                    option.value = producto.id;
                    option.textContent = `${producto.codigo} - ${producto.nombre}`;
                    productoSelect.appendChild(option);
                });
                productoSelect.disabled = false;
            } else {
                productoError.textContent = 'No hay productos asociados a este botiquín';
                productoError.style.display = 'inline';
            }
        } else {
            productoLoading.style.display = 'none';
            productoError.textContent = 'No se encontraron productos para este botiquín';
            productoError.style.display = 'inline';
        }
    });

    // Validación del formulario antes de enviar
    const form = document.getElementById('formRegistrarLectura');
    if (form) {
        form.addEventListener('submit', function(event) {
            const botiquinId = botiquinSelect.value;
            const productoId = productoSelect.value;
            const cantidad = document.getElementById('cantidad_disponible').value;
            const fecha = document.getElementById('fecha_lectura').value;

            let isValid = true;
            let errorMessage = '';

            if (!botiquinId) {
                errorMessage = 'Debe seleccionar un botiquín';
                isValid = false;
            } else if (!productoId) {
                errorMessage = 'Debe seleccionar un producto';
                isValid = false;
            } else if (!cantidad || cantidad < 0) {
                errorMessage = 'La cantidad debe ser un número positivo';
                isValid = false;
            } else if (!fecha) {
                errorMessage = 'Debe especificar una fecha y hora';
                isValid = false;
            }

            if (!isValid) {
                event.preventDefault();
                alert(errorMessage);
            }
        });
    }

    // Limpieza de mensajes de error al hacer reset
    const resetButton = document.querySelector('button[type="reset"]');
    if (resetButton) {
        resetButton.addEventListener('click', function() {
            productoSelect.disabled = true;
            productoSelect.innerHTML = '<option value="">Seleccione un producto</option>';
            productoError.style.display = 'none';
            productoLoading.style.display = 'none';
        });
    }
}

/**
 * Función para ir a la pestaña de registrar lectura con datos precargados
 */
function registrarNuevaLectura(botiquinId, productoId) {
    // Cambiar a la pestaña de registrar lectura
    document.querySelector('.tab-btn[data-tab="tab-registrar-lectura"]').click();

    // Esperar a que la pestaña se muestre y luego preseleccionar los valores
    setTimeout(() => {
        // Seleccionar el botiquín
        const botiquinSelect = document.getElementById('id_botiquin');
        if (botiquinSelect) {
            botiquinSelect.value = botiquinId;

            // Disparar el evento change para cargar los productos
            const event = new Event('change');
            botiquinSelect.dispatchEvent(event);

            // Esperar a que los productos se carguen y luego seleccionar el producto
            setTimeout(() => {
                const productoSelect = document.getElementById('id_producto');
                if (productoSelect) {
                    productoSelect.value = productoId;
                }
            }, 300); // Dar tiempo para que se carguen los productos
        }
    }, 100);
}

/**
 * Función para retrasar la ejecución (debounce)
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
 * Escapa HTML para evitar inyección de código
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
