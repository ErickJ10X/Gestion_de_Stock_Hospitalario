/**
 * Sistema de paginación para tablas con datos cargados en el cliente
 */
document.addEventListener('DOMContentLoaded', function() {
    // Configuración inicial
    let paginaActual = 1;
    let registrosPorPagina = 10;
    let filtroTexto = '';
    
    // Elementos DOM
    const tabla = document.getElementById('usuariosDataTable');
    const tbody = tabla ? tabla.querySelector('tbody') : null;
    const paginacionContainer = document.getElementById('paginacion-usuarios');
    const selectRegistrosPorPagina = document.getElementById('registrosPorPagina');
    const inputBuscar = document.getElementById('buscarUsuario');
    const spanInicio = document.getElementById('inicio-registros');
    const spanFin = document.getElementById('fin-registros');
    const spanTotal = document.getElementById('total-registros');
    
    // Verificar que tenemos todos los elementos necesarios
    if (!tabla || !tbody || !paginacionContainer || !window.datosUsuarios) {
        console.error('No se encontraron los elementos necesarios para la paginación');
        return;
    }
    
    /**
     * Función para generar una fila de usuario
     */
    function generarFilaUsuario(usuario) {
        const badgeClass = usuario.activo ? 'bg-success' : 'bg-danger';
        const badgeText = usuario.activo ? 'Activo' : 'Inactivo';
        const iconClass = usuario.activo ? 'check-circle' : 'times-circle';
        
        let rolBadgeClass = 'primary';
        switch (usuario.rol) {
            case 'ADMINISTRADOR':
                rolBadgeClass = 'danger';
                break;
            case 'GESTOR_GENERAL':
                rolBadgeClass = 'primary';
                break;
            case 'GESTOR_HOSPITAL':
                rolBadgeClass = 'warning';
                break;
            case 'GESTOR_PLANTA':
                rolBadgeClass = 'info';
                break;
            case 'USUARIO_BOTIQUIN':
                rolBadgeClass = 'secondary';
                break;
        }
        
        return `
            <tr>
                <td>${usuario.id}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <span class="ms-2">${escapeHtml(usuario.nombre)}</span>
                    </div>
                </td>
                <td>
                    <a href="mailto:${escapeHtml(usuario.email)}" class="text-decoration-none">
                        <i class="far fa-envelope me-1"></i>
                        ${escapeHtml(usuario.email)}
                    </a>
                </td>
                <td>
                    <span class="badge bg-${rolBadgeClass}">${escapeHtml(usuario.rol)}</span>
                </td>
                <td>
                    <span class="badge ${badgeClass} status-badge">
                        <i class="fas fa-${iconClass} me-1"></i>
                        ${badgeText}
                    </span>
                </td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="#" 
                            class="list-table__button list-table__button--edit btn btn-sm"
                            title="Editar usuario">
                            <i class="fas fa-edit list-table__button-icon"></i> Editar
                        </a>
                        
                        <button type="button"
                                class="list-table__button list-table__button--locations btn btn-sm"
                                title="Gestionar ubicaciones">
                            <i class="fas fa-map-marker-alt list-table__button-icon"></i> Ubicaciones
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
            return window.datosUsuarios;
        }
        
        const termino = filtroTexto.toLowerCase();
        return window.datosUsuarios.filter(usuario => {
            return usuario.nombre.toLowerCase().includes(termino) ||
                   usuario.email.toLowerCase().includes(termino) ||
                   usuario.rol.toLowerCase().includes(termino);
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
                        <td colspan="6" class="text-center py-4">
                            <i class="fas fa-info-circle me-2"></i>
                            No se encontraron usuarios registrados
                        </td>
                    </tr>
                `;
            } else {
                tbody.innerHTML = registros.map(generarFilaUsuario).join('');
            }
            
            // Actualizar contadores
            if (spanInicio) spanInicio.textContent = totalRegistros > 0 ? inicio + 1 : 0;
            if (spanFin) spanFin.textContent = fin;
            if (spanTotal) spanTotal.textContent = totalRegistros;
            
            // Actualizar paginación
            actualizarPaginacion(totalRegistros);
            
            // Reactivar eventos en los botones de la tabla
            activarEventosBotones();
            
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
     * Activar eventos en los botones de edición y ubicaciones
     */
    function activarEventosBotones() {
        document.querySelectorAll('.list-table__button--edit').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const userId = this.closest('tr').querySelector('td').textContent.trim();
                if (typeof showEditUserTab === 'function') {
                    showEditUserTab(userId);
                }
            });
        });

        document.querySelectorAll('.list-table__button--locations').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const userId = this.closest('tr').querySelector('td').textContent.trim();
                if (typeof showUserLocationsTab === 'function') {
                    showUserLocationsTab(userId);
                }
            });
        });
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
});
