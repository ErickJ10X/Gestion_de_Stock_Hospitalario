/**
 * Script para la gestión de etiquetas
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

// Almacenar datos de etiquetas para acceso rápido
let etiquetasData = {};

// Inicializar datos cuando cargue el documento
document.addEventListener('DOMContentLoaded', function() {
    // Cargar datos de etiquetas en memoria
    if (window.datosEtiquetas) {
        window.datosEtiquetas.forEach(e => {
            etiquetasData[e.id] = e;
        });
    }

    // Configurar el modal de vista previa
    configurarModalEtiqueta();

    // Manejar eventos de pestañas
    const tabButtons = document.querySelectorAll('.tab-btn');
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            activarPestania(tabId);
        });
    });

    // Sistema de paginación
    inicializarPaginacionEtiquetas();
    
    // Asegurarse de que las vistas previas solo estén en los modales
    ocultarVistasPreviasNoModal();
});

/**
 * Ocultar todas las vistas previas que no estén dentro de un modal
 */
function ocultarVistasPreviasNoModal() {
    const previewContainers = document.querySelectorAll('.preview-container');
    previewContainers.forEach(container => {
        if (!container.closest('.modal')) {
            container.style.display = 'none';
        }
    });
}

/**
 * Configura el comportamiento del modal de vista previa de etiqueta
 */
function configurarModalEtiqueta() {
    const verEtiquetaModal = document.getElementById('verEtiquetaModal');

    if (verEtiquetaModal) {
        // Usar Bootstrap 5 event para el modal
        verEtiquetaModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const tipo = button.getAttribute('data-tipo');
            const prioridad = button.getAttribute('data-prioridad');
            const producto = button.getAttribute('data-producto');
            const referencia = button.getAttribute('data-referencia');
            const reposicion = button.getAttribute('data-reposicion');

            // Actualizar contenido del modal
            const modalEtiquetaPreview = document.getElementById('modal-etiqueta-preview');
            const modalColorIndicator = document.getElementById('modal-color-indicator');
            const modalTipoPrioridad = document.getElementById('modal-tipo-prioridad');
            const modalProducto = document.getElementById('modal-producto');
            const modalReferencia = document.getElementById('modal-referencia');
            const modalReposicion = document.getElementById('modal-reposicion');
            const modalFecha = document.getElementById('modal-fecha');

            // Actualizar título del modal según prioridad
            const modalTitle = document.getElementById('verEtiquetaModalLabel');
            if (modalTitle) {
                const iconClass = prioridad === 'Urgente' ? 'text-danger' : 'text-primary';
                modalTitle.innerHTML = `<i class="fas fa-tag ${iconClass} me-2"></i> Etiqueta ${prioridad} - ${tipo}`;
            }

            // Actualizar contenido
            modalTipoPrioridad.textContent = prioridad + ' ' + tipo;
            modalProducto.textContent = producto || '-';
            modalReferencia.textContent = referencia || '-';
            modalReposicion.textContent = reposicion || '-';
            modalFecha.textContent = new Date().toLocaleDateString('es-ES');

            // Actualizar estilo según prioridad
            if (prioridad === 'Normal') {
                modalEtiquetaPreview.className = 'etiqueta-preview normal modal-etiqueta-preview';
                modalColorIndicator.className = 'color-indicator blue';
            } else {
                modalEtiquetaPreview.className = 'etiqueta-preview urgente modal-etiqueta-preview';
                modalColorIndicator.className = 'color-indicator red';
            }

            // Configurar botón de imprimir
            const btnImprimirModal = document.getElementById('btn-imprimir-modal');
            if (btnImprimirModal) {
                btnImprimirModal.onclick = function() {
                    window.location.href = 'imprimir_etiqueta.php?id=' + id;
                };

                // Si la etiqueta está impresa, deshabilitar el botón de imprimir
                if (etiquetasData[id] && etiquetasData[id].impresa) {
                    btnImprimirModal.disabled = true;
                    btnImprimirModal.classList.add('disabled');
                    btnImprimirModal.innerHTML = '<i class="bi bi-printer"></i> Ya impresa';
                } else {
                    btnImprimirModal.disabled = false;
                    btnImprimirModal.classList.remove('disabled');
                    btnImprimirModal.innerHTML = '<i class="bi bi-printer"></i> Imprimir Etiqueta';
                }
            }
        });

        // Reiniciar el modal cuando se cierre
        verEtiquetaModal.addEventListener('hidden.bs.modal', function() {
            const btnImprimirModal = document.getElementById('btn-imprimir-modal');
            if (btnImprimirModal) {
                btnImprimirModal.disabled = false;
                btnImprimirModal.classList.remove('disabled');
            }
        });
    }

    // Configurar el modal de vista previa en la pantalla de generación
    const previewEtiquetaModal = document.getElementById('previewEtiquetaModal');
    if (previewEtiquetaModal) {
        previewEtiquetaModal.addEventListener('show.bs.modal', function() {
            // Actualizar datos de la vista previa antes de mostrar el modal
            const tipo = document.getElementById('tipo').value;
            const prioridad = document.getElementById('prioridad').value;
            
            // Actualizar título del modal según tipo seleccionado
            const previewModalLabel = document.getElementById('previewEtiquetaModalLabel');
            if (previewModalLabel) {
                const iconClass = prioridad === 'Urgente' ? 'text-danger' : 'text-primary';
                previewModalLabel.innerHTML = `<i class="fas fa-eye ${iconClass} me-2"></i> Vista previa - ${prioridad} ${tipo}`;
            }
        });
    }
}

/**
 * Inicializa el sistema de paginación para la tabla de etiquetas
 */
function inicializarPaginacionEtiquetas() {
    // Configuración inicial
    let paginaActual = 1;
    let registrosPorPagina = 10;
    let filtroTexto = '';

    // Elementos DOM
    const tablaEtiquetas = document.getElementById('etiquetasDataTable');
    const tbodyEtiquetas = tablaEtiquetas ? tablaEtiquetas.querySelector('tbody') : null;
    const paginacionEtiquetas = document.getElementById('paginacion-etiquetas');
    const selectRegistrosPorPagina = document.getElementById('registrosPorPaginaEtiquetas');
    const inputBuscarEtiqueta = document.getElementById('buscarEtiqueta');
    const spanInicio = document.getElementById('inicio-registros-etiquetas');
    const spanFin = document.getElementById('fin-registros-etiquetas');
    const spanTotal = document.getElementById('total-registros-etiquetas');

    // Si no tenemos los elementos necesarios, salimos
    if (!tablaEtiquetas || !tbodyEtiquetas || !window.datosEtiquetas) {
        return;
    }

    /**
     * Filtra las etiquetas según el texto de búsqueda
     */
    function filtrarEtiquetas() {
        if (!filtroTexto) {
            return window.datosEtiquetas;
        }

        const termino = filtroTexto.toLowerCase();
        return window.datosEtiquetas.filter(etiqueta => {
            return (
                etiqueta.id.toString().includes(termino) ||
                etiqueta.producto.toLowerCase().includes(termino) ||
                etiqueta.reposicion.toString().includes(termino) ||
                etiqueta.tipo.toLowerCase().includes(termino) ||
                etiqueta.prioridad.toLowerCase().includes(termino)
            );
        });
    }

    /**
     * Actualiza la tabla con los datos de la página actual
     */
    function actualizarTablaEtiquetas() {
        // Agregar clase loading para transición
        if (tbodyEtiquetas) {
            tbodyEtiquetas.classList.add('loading');
        }

        // Pequeño retraso para permitir que la transición sea visible
        setTimeout(() => {
            const etiquetasFiltradas = filtrarEtiquetas();
            const totalEtiquetas = etiquetasFiltradas.length;

            // Calcular índices
            const inicio = (paginaActual - 1) * registrosPorPagina;
            let fin = inicio + registrosPorPagina;
            if (fin > totalEtiquetas) fin = totalEtiquetas;

            // Obtener las etiquetas de la página actual
            const etiquetasPagina = etiquetasFiltradas.slice(inicio, fin);

            // Actualizar HTML de la tabla
            if (etiquetasPagina.length === 0) {
                tbodyEtiquetas.innerHTML = `
                    <tr class="no-results-row">
                        <td colspan="7" class="text-center py-4">
                            <i class="fas fa-info-circle me-2"></i>
                            No se encontraron etiquetas registradas
                        </td>
                    </tr>
                `;
            } else {
                tbodyEtiquetas.innerHTML = etiquetasPagina.map(generarFilaEtiqueta).join('');
            }

            // Actualizar contadores
            if (spanInicio) spanInicio.textContent = totalEtiquetas > 0 ? inicio + 1 : 0;
            if (spanFin) spanFin.textContent = fin;
            if (spanTotal) spanTotal.textContent = totalEtiquetas;

            // Actualizar paginación
            actualizarPaginacion(totalEtiquetas);

            // Reactivar eventos en los botones del modal
            activarEventosBotonesModal();

            // Quitar clase loading
            if (tbodyEtiquetas) {
                tbodyEtiquetas.classList.remove('loading');
            }
        }, 150);
    }

    /**
     * Genera los botones de paginación
     */
    function actualizarPaginacion(totalRegistros) {
        if (!paginacionEtiquetas) return;

        const totalPaginas = Math.ceil(totalRegistros / registrosPorPagina);

        // Limpiar paginación anterior
        paginacionEtiquetas.innerHTML = '';

        // Si no hay registros, no mostramos paginación
        if (totalRegistros === 0) {
            return;
        }

        // Botón primera página
        const liPrimera = document.createElement('li');
        liPrimera.className = `page-item ${paginaActual === 1 ? 'disabled' : ''}`;
        liPrimera.innerHTML = `
            <a class="page-link" href="#" data-pagina="primera" aria-label="Primera página">
                <span aria-hidden="true">&laquo;&laquo;</span>
                <span class="sr-only">Primera</span>
            </a>
        `;
        paginacionEtiquetas.appendChild(liPrimera);

        // Botón anterior
        const liAnterior = document.createElement('li');
        liAnterior.className = `page-item ${paginaActual === 1 ? 'disabled' : ''}`;
        liAnterior.innerHTML = `
            <a class="page-link" href="#" data-pagina="anterior" aria-label="Página anterior">
                <span aria-hidden="true">&laquo;</span>
                <span class="sr-only">Anterior</span>
            </a>
        `;
        paginacionEtiquetas.appendChild(liAnterior);

        // Determinar qué números mostrar (mostraremos 5 números máximo alrededor de la página actual)
        let inicio = Math.max(1, paginaActual - 2);
        let fin = Math.min(totalPaginas, inicio + 4);

        // Si estamos cerca del final, ajustar el inicio
        if (fin === totalPaginas) {
            inicio = Math.max(1, totalPaginas - 4);
        }

        // Números de página
        for (let i = inicio; i <= fin; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${i === paginaActual ? 'active' : ''}`;
            li.innerHTML = `<a class="page-link" href="#" data-pagina="${i}">${i}</a>`;
            paginacionEtiquetas.appendChild(li);
        }

        // Botón siguiente
        const liSiguiente = document.createElement('li');
        liSiguiente.className = `page-item ${paginaActual === totalPaginas ? 'disabled' : ''}`;
        liSiguiente.innerHTML = `
            <a class="page-link" href="#" data-pagina="siguiente" aria-label="Página siguiente">
                <span aria-hidden="true">&raquo;</span>
                <span class="sr-only">Siguiente</span>
            </a>
        `;
        paginacionEtiquetas.appendChild(liSiguiente);

        // Botón última página
        const liUltima = document.createElement('li');
        liUltima.className = `page-item ${paginaActual === totalPaginas ? 'disabled' : ''}`;
        liUltima.innerHTML = `
            <a class="page-link" href="#" data-pagina="ultima" aria-label="Última página">
                <span aria-hidden="true">&raquo;&raquo;</span>
                <span class="sr-only">Última</span>
            </a>
        `;
        paginacionEtiquetas.appendChild(liUltima);

        // Agregar manejador de eventos a todos los links de paginación
        paginacionEtiquetas.querySelectorAll('.page-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                if (this.parentNode.classList.contains('disabled')) return;

                const accion = this.getAttribute('data-pagina');
                const totalPaginas = Math.ceil(filtrarEtiquetas().length / registrosPorPagina);
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
                        tablaEtiquetas.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                    actualizarTablaEtiquetas();
                }
            });
        });
    }

    /**
     * Genera una fila HTML para una etiqueta
     */
    function generarFilaEtiqueta(etiqueta) {
        const tipoClase = etiqueta.tipo === 'RFID' ? 'bg-info' : 'bg-secondary';
        const prioridadClase = etiqueta.prioridad === 'Urgente' ? 'bg-danger' : 'bg-primary';
        const estadoClase = etiqueta.impresa ? 'bg-success' : 'bg-warning text-dark';
        const estadoTexto = etiqueta.impresa ? 'Impresa' : 'Pendiente';

        // Botón de imprimir solo para etiquetas no impresas
        const botonImprimir = !etiqueta.impresa ? `
            <form method="POST" class="d-inline">
                <input type="hidden" name="id_etiqueta" value="${etiqueta.id}">
                <input type="hidden" name="accion" value="imprimir">
                <button type="submit" class="list-table__button list-table__button--print">
                    <i class="bi bi-printer list-table__button-icon"></i> Imprimir
                </button>
            </form>
        ` : '';

        return `
            <tr class="list-table__body-row">
                <td class="list-table__body-cell" data-label="ID">${escapeHtml(etiqueta.id)}</td>
                <td class="list-table__body-cell" data-label="Producto">${escapeHtml(etiqueta.producto)}</td>
                <td class="list-table__body-cell" data-label="Reposición">${escapeHtml(etiqueta.reposicion)}</td>
                <td class="list-table__body-cell" data-label="Tipo">
                    <span class="badge ${tipoClase}">${escapeHtml(etiqueta.tipo)}</span>
                </td>
                <td class="list-table__body-cell" data-label="Prioridad">
                    <span class="badge ${prioridadClase}">${escapeHtml(etiqueta.prioridad)}</span>
                </td>
                <td class="list-table__body-cell" data-label="Estado">
                    <span class="badge ${estadoClase}">${estadoTexto}</span>
                </td>
                <td class="list-table__body-cell" data-label="Acciones">
                    <div class="list-table__actions">
                        <button class="list-table__button list-table__button--view btn-view-etiqueta"
                                data-bs-toggle="modal" 
                                data-bs-target="#verEtiquetaModal"
                                data-id="${etiqueta.id}"
                                data-tipo="${etiqueta.tipo}"
                                data-prioridad="${etiqueta.prioridad}"
                                data-producto="${escapeHtml(etiqueta.producto)}"
                                data-reposicion="${etiqueta.reposicion}">
                            <i class="bi bi-eye list-table__button-icon"></i> Ver
                        </button>
                        
                        ${botonImprimir}
                        
                        <form method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar esta etiqueta?')">
                            <input type="hidden" name="id_etiqueta" value="${etiqueta.id}">
                            <input type="hidden" name="accion" value="eliminar">
                            <button type="submit" class="list-table__button list-table__button--delete">
                                <i class="bi bi-trash list-table__button-icon"></i> Eliminar
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        `;
    }

    /**
     * Activa eventos para botones del modal
     */
    function activarEventosBotonesModal() {
        // Los eventos del modal ya están manejados por el script en index.php
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

    // Manejar cambio en registros por página
    if (selectRegistrosPorPagina) {
        selectRegistrosPorPagina.addEventListener('change', function() {
            registrosPorPagina = parseInt(this.value);
            paginaActual = 1; // Volver a la primera página
            actualizarTablaEtiquetas();
        });
    }

    // Manejar búsqueda
    if (inputBuscarEtiqueta) {
        inputBuscarEtiqueta.addEventListener('input', debounce(function() {
            filtroTexto = this.value;
            paginaActual = 1; // Volver a la primera página
            actualizarTablaEtiquetas();
        }, 300));
    }

    // Inicializar la tabla
    actualizarTablaEtiquetas();

    // Exportar funciones
    window.etiquetasModule = {
        actualizarTabla: actualizarTablaEtiquetas,
        cambiarFiltro: (nuevoFiltro) => {
            filtroTexto = nuevoFiltro;
            paginaActual = 1;
            actualizarTablaEtiquetas();
        },
        cambiarPagina: (nuevaPagina) => {
            paginaActual = nuevaPagina;
            actualizarTablaEtiquetas();
        }
    };
}

// Cerrar alertas
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.list-alert__close').forEach(function(closeBtn) {
        closeBtn.addEventListener('click', function() {
            this.parentElement.style.display = 'none';
        });
    });
});