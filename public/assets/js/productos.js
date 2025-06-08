/**
 * Script para la gestión de productos y catálogos
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

// Almacenar datos de productos y catálogos para acceso rápido
let productosData = {};
let catalogosData = {};

// Inicializar datos cuando cargue el documento
document.addEventListener('DOMContentLoaded', function() {
    // Cargar datos de productos
    if (window.datosProductos) {
        window.datosProductos.forEach(p => {
            productosData[p.id] = p;
        });
    }

    // Cargar datos de catálogos
    if (window.datosCatalogos) {
        window.datosCatalogos.forEach(c => {
            catalogosData[c.id] = c;
        });
    }
});

// Mostrar formulario de edición de producto
function showEditProductoForm(productoId) {
    // Primero activar la pestaña de Agregar/Editar
    activarPestania('tab-agregar-editar');

    setTimeout(() => {
        // Activar botón "Editar Producto" y desactivar los demás
        const formToggleBtns = document.querySelectorAll('.form-toggle-btn');
        formToggleBtns.forEach(btn => btn.classList.remove('active'));
        const btnEditarProducto = document.querySelector('.form-toggle-btn[data-target="editarProductoForm"]');
        if (btnEditarProducto) btnEditarProducto.classList.add('active');

        // Mostrar sección editar producto y ocultar las demás
        const formSections = document.querySelectorAll('.form-section');
        formSections.forEach(section => section.style.display = 'none');
        const editarProductoForm = document.getElementById('editarProductoForm');
        if (editarProductoForm) editarProductoForm.style.display = 'block';

        // Seleccionar producto en el selector
        const select = document.getElementById('select_producto');
        if (select) {
            select.value = productoId;
            // Disparar evento change manualmente para activar el handler
            const event = new Event('change');
            select.dispatchEvent(event);

            // Cargar datos del formulario directamente desde el objeto en memoria
            if (productosData[productoId]) {
                const producto = productosData[productoId];

                // Rellenar el formulario con datos del producto
                const idInput = document.getElementById('editar_producto_id');
                const codigoInput = document.getElementById('editar_codigo_producto');
                const nombreInput = document.getElementById('editar_nombre_producto');
                const descripcionInput = document.getElementById('editar_descripcion_producto');
                const unidadMedidaInput = document.getElementById('editar_unidad_medida');

                if (idInput) idInput.value = producto.id;
                if (codigoInput) codigoInput.value = producto.codigo;
                if (nombreInput) nombreInput.value = producto.nombre;
                if (descripcionInput) descripcionInput.value = producto.descripcion;
                if (unidadMedidaInput) unidadMedidaInput.value = producto.unidad_medida;

                // Mostrar el formulario y ocultar el mensaje
                const mensaje = document.getElementById('mensajeSeleccionProducto');
                const formContainer = document.getElementById('editar_producto_form_container');
                if (mensaje) mensaje.style.display = 'none';
                if (formContainer) formContainer.style.display = 'block';
            }
        }
    }, 300); // Incrementamos el tiempo de espera para asegurar que la pestaña se haya mostrado correctamente
}

// Mostrar formulario para crear un nuevo producto
function showCreateProductoForm() {
    // Primero activar la pestaña de Agregar/Editar
    activarPestania('tab-agregar-editar');

    setTimeout(() => {
        // Activar botón "Crear Producto" y desactivar los demás
        const formToggleBtns = document.querySelectorAll('.form-toggle-btn');
        formToggleBtns.forEach(btn => btn.classList.remove('active'));
        const btnCrearProducto = document.querySelector('.form-toggle-btn[data-target="crearProductoForm"]');
        if (btnCrearProducto) btnCrearProducto.classList.add('active');

        // Mostrar sección crear producto y ocultar las demás
        const formSections = document.querySelectorAll('.form-section');
        formSections.forEach(section => section.style.display = 'none');
        const crearProductoForm = document.getElementById('crearProductoForm');
        if (crearProductoForm) crearProductoForm.style.display = 'block';

        // Limpiar formulario si existe
        const form = document.getElementById('formCrearProducto');
        if (form) form.reset();
    }, 300); // Incrementamos el tiempo de espera
}

// Mostrar formulario de edición de catálogo
function showEditCatalogoForm(catalogoId) {
    // Primero activar la pestaña de Agregar/Editar
    activarPestania('tab-agregar-editar');

    setTimeout(() => {
        // Activar botón "Editar Catálogo" y desactivar los demás
        const formToggleBtns = document.querySelectorAll('.form-toggle-btn');
        formToggleBtns.forEach(btn => btn.classList.remove('active'));
        const btnEditarCatalogo = document.querySelector('.form-toggle-btn[data-target="editarCatalogoForm"]');
        if (btnEditarCatalogo) btnEditarCatalogo.classList.add('active');

        // Mostrar sección editar catálogo y ocultar las demás
        const formSections = document.querySelectorAll('.form-section');
        formSections.forEach(section => section.style.display = 'none');
        const editarCatalogoForm = document.getElementById('editarCatalogoForm');
        if (editarCatalogoForm) editarCatalogoForm.style.display = 'block';

        // Seleccionar catálogo en el selector
        const select = document.getElementById('select_catalogo');
        if (select) {
            select.value = catalogoId;
            // Disparar evento change manualmente para activar el handler
            const event = new Event('change');
            select.dispatchEvent(event);

            // Cargar datos del formulario directamente
            if (catalogosData[catalogoId]) {
                const catalogo = catalogosData[catalogoId];

                // Rellenar el formulario con datos del catálogo
                const idInput = document.getElementById('editar_catalogo_id');
                const productoSelect = document.getElementById('editar_catalogo_producto');
                const plantaSelect = document.getElementById('editar_catalogo_planta');
                const activoCheck = document.getElementById('editar_catalogo_activo');

                if (idInput) idInput.value = catalogo.id;
                if (productoSelect) productoSelect.value = catalogo.id_producto;
                if (plantaSelect) plantaSelect.value = catalogo.id_planta;
                if (activoCheck) activoCheck.checked = catalogo.activo;

                // Mostrar el formulario y ocultar el mensaje
                const mensaje = document.getElementById('mensajeSeleccionCatalogo');
                const formContainer = document.getElementById('editar_catalogo_form_container');
                if (mensaje) mensaje.style.display = 'none';
                if (formContainer) formContainer.style.display = 'block';
            }
        }
    }, 300); // Incrementamos el tiempo de espera
}

// Mostrar formulario para crear un nuevo catálogo
function showCreateCatalogoForm() {
    // Primero activar la pestaña de Agregar/Editar
    activarPestania('tab-agregar-editar');

    setTimeout(() => {
        // Activar botón "Crear Catálogo" y desactivar los demás
        const formToggleBtns = document.querySelectorAll('.form-toggle-btn');
        formToggleBtns.forEach(btn => btn.classList.remove('active'));
        const btnCrearCatalogo = document.querySelector('.form-toggle-btn[data-target="crearCatalogoForm"]');
        if (btnCrearCatalogo) btnCrearCatalogo.classList.add('active');

        // Mostrar sección crear catálogo y ocultar las demás
        const formSections = document.querySelectorAll('.form-section');
        formSections.forEach(section => section.style.display = 'none');
        const crearCatalogoForm = document.getElementById('crearCatalogoForm');
        if (crearCatalogoForm) crearCatalogoForm.style.display = 'block';

        // Limpiar formulario si existe
        const form = document.getElementById('formCrearCatalogo');
        if (form) form.reset();
    }, 300); // Incrementamos el tiempo de espera
}

// Función para confirmar eliminación de producto
function confirmarEliminarProducto(id, nombre) {
    const modal = document.getElementById('eliminarProductoModal');
    if (modal) {
        const nombreSpan = document.getElementById('nombreProductoEliminar');
        const idInput = document.getElementById('id_producto_eliminar');

        if (nombreSpan) nombreSpan.textContent = ' ' + nombre;
        if (idInput) idInput.value = id;

        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    } else {
        if (confirm(`¿Está seguro que desea eliminar el producto ${nombre}? Esta acción no se puede deshacer.`)) {
            window.location.href = `/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/ProductoController.php?action=eliminar&id=${id}`;
        }
    }
}

// Función para confirmar eliminación de catálogo
function confirmarEliminarCatalogo(id, nombreProducto, nombrePlanta) {
    if (confirm(`¿Está seguro que desea eliminar el producto "${nombreProducto}" del catálogo de ${nombrePlanta}? Esta acción no se puede deshacer.`)) {
        window.location.href = `/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/CatalogosController.php?action=eliminar&id=${id}`;
    }
}

// Función para seleccionar un producto para edición
function seleccionarProducto(id) {
    console.log("Seleccionando producto con ID:", id);
    showEditProductoForm(id);
}

// Función para seleccionar un catálogo para edición
function seleccionarCatalogo(id) {
    console.log("Seleccionando catálogo con ID:", id);
    showEditCatalogoForm(id);
}

// Delegar eventos en botones de las tablas
document.addEventListener('DOMContentLoaded', function () {
    console.log("DOM cargado, configurando botones...");

    // Botón nuevo producto
    const btnNuevoProducto = document.getElementById('btnNuevoProducto');
    if (btnNuevoProducto) {
        console.log("Botón nuevo producto encontrado");
        btnNuevoProducto.addEventListener('click', function (e) {
            e.preventDefault();
            console.log("Botón nuevo producto clickeado");
            showCreateProductoForm();
        });
    } else {
        console.log("Botón nuevo producto no encontrado");
    }

    // Botón nuevo catálogo
    const btnNuevoCatalogo = document.getElementById('btnNuevoCatalogo');
    if (btnNuevoCatalogo) {
        console.log("Botón nuevo catálogo encontrado");
        btnNuevoCatalogo.addEventListener('click', function (e) {
            e.preventDefault();
            console.log("Botón nuevo catálogo clickeado");
            showCreateCatalogoForm();
        });
    } else {
        console.log("Botón nuevo catálogo no encontrado");
    }

    // Alternar entre formularios en la pestaña Agregar/Editar
    const formToggleBtns = document.querySelectorAll('.form-toggle-btn');
    formToggleBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            console.log("Botón toggle clickeado:", this.getAttribute('data-target'));
            // Quitar clase active de todos los botones
            formToggleBtns.forEach(b => b.classList.remove('active'));
            // Agregar clase active al botón clickeado
            this.classList.add('active');
            // Mostrar la sección correspondiente y ocultar las demás
            const target = this.getAttribute('data-target');
            const formSections = document.querySelectorAll('.form-section');
            formSections.forEach(section => {
                if (section.id === target) {
                    section.style.display = 'block';
                } else {
                    section.style.display = 'none';
                }
            });
        });
    });

    // Manejar la selección de un producto en el selector
    const selectProducto = document.getElementById('select_producto');
    const mensajeSeleccionProducto = document.getElementById('mensajeSeleccionProducto');
    const editarProductoFormContainer = document.getElementById('editar_producto_form_container');

    if (selectProducto) {
        selectProducto.addEventListener('change', function() {
            const productoId = this.value;

            if (productoId) {
                // Si existe un producto con ese ID en nuestros datos cargados
                if (productosData[productoId]) {
                    const producto = productosData[productoId];

                    // Rellenar el formulario con datos del producto
                    const idInput = document.getElementById('editar_producto_id');
                    const codigoInput = document.getElementById('editar_codigo_producto');
                    const nombreInput = document.getElementById('editar_nombre_producto');
                    const descripcionInput = document.getElementById('editar_descripcion_producto');
                    const unidadMedidaInput = document.getElementById('editar_unidad_medida');

                    if (idInput) idInput.value = producto.id;
                    if (codigoInput) codigoInput.value = producto.codigo;
                    if (nombreInput) nombreInput.value = producto.nombre;
                    if (descripcionInput) descripcionInput.value = producto.descripcion;
                    if (unidadMedidaInput) unidadMedidaInput.value = producto.unidad_medida;

                    // Mostrar el formulario y ocultar el mensaje
                    if (mensajeSeleccionProducto) mensajeSeleccionProducto.style.display = 'none';
                    if (editarProductoFormContainer) editarProductoFormContainer.style.display = 'block';
                } else {
                    console.warn('No se encontraron datos para el producto ID:', productoId);
                }
            } else {
                // Ocultar el formulario y mostrar el mensaje si no se seleccionó ningún producto
                if (mensajeSeleccionProducto) mensajeSeleccionProducto.style.display = 'block';
                if (editarProductoFormContainer) editarProductoFormContainer.style.display = 'none';
            }
        });
    }

    // Manejar la selección de un catálogo en el selector
    const selectCatalogo = document.getElementById('select_catalogo');
    const mensajeSeleccionCatalogo = document.getElementById('mensajeSeleccionCatalogo');
    const editarCatalogoFormContainer = document.getElementById('editar_catalogo_form_container');

    if (selectCatalogo) {
        selectCatalogo.addEventListener('change', function() {
            const catalogoId = this.value;

            if (catalogoId) {
                // Si existe un catálogo con ese ID en nuestros datos cargados
                if (catalogosData[catalogoId]) {
                    const catalogo = catalogosData[catalogoId];

                    // Rellenar el formulario con datos del catálogo
                    const idInput = document.getElementById('editar_catalogo_id');
                    const productoSelect = document.getElementById('editar_catalogo_producto');
                    const plantaSelect = document.getElementById('editar_catalogo_planta');
                    const activoCheck = document.getElementById('editar_catalogo_activo');

                    if (idInput) idInput.value = catalogo.id;
                    if (productoSelect) productoSelect.value = catalogo.id_producto;
                    if (plantaSelect) plantaSelect.value = catalogo.id_planta;
                    if (activoCheck) activoCheck.checked = catalogo.activo;

                    // Mostrar el formulario y ocultar el mensaje
                    if (mensajeSeleccionCatalogo) mensajeSeleccionCatalogo.style.display = 'none';
                    if (editarCatalogoFormContainer) editarCatalogoFormContainer.style.display = 'block';
                } else {
                    console.warn('No se encontraron datos para el catálogo ID:', catalogoId);
                }
            } else {
                // Ocultar el formulario y mostrar el mensaje si no se seleccionó ningún catálogo
                if (mensajeSeleccionCatalogo) mensajeSeleccionCatalogo.style.display = 'block';
                if (editarCatalogoFormContainer) editarCatalogoFormContainer.style.display = 'none';
            }
        });
    }

    // Manejar el botón de cancelar edición de producto
    const cancelarEdicionProducto = document.getElementById('cancelar_edicion_producto');
    if (cancelarEdicionProducto && selectProducto) {
        cancelarEdicionProducto.addEventListener('click', function() {
            selectProducto.value = '';
            if (editarProductoFormContainer) editarProductoFormContainer.style.display = 'none';
            if (mensajeSeleccionProducto) mensajeSeleccionProducto.style.display = 'block';
        });
    }

    // Manejar el botón de cancelar edición de catálogo
    const cancelarEdicionCatalogo = document.getElementById('cancelar_edicion_catalogo');
    if (cancelarEdicionCatalogo && selectCatalogo) {
        cancelarEdicionCatalogo.addEventListener('click', function() {
            selectCatalogo.value = '';
            if (editarCatalogoFormContainer) editarCatalogoFormContainer.style.display = 'none';
            if (mensajeSeleccionCatalogo) mensajeSeleccionCatalogo.style.display = 'block';
        });
    }
});

/**
 * Sistema de paginación para tabla de productos
 */
document.addEventListener('DOMContentLoaded', function() {
    // Configuración inicial productos
    let paginaActualProductos = 1;
    let registrosPorPaginaProductos = 10;
    let filtroTextoProductos = '';

    // Elementos DOM productos
    const tablaProductos = document.getElementById('productosDataTable');
    const tbodyProductos = tablaProductos ? tablaProductos.querySelector('tbody') : null;
    const paginacionProductos = document.getElementById('paginacion-productos');
    const selectRegistrosPorPaginaProductos = document.getElementById('registrosPorPaginaProductos');
    const inputBuscarProducto = document.getElementById('buscarProducto');
    const spanInicioProductos = document.getElementById('inicio-registros-productos');
    const spanFinProductos = document.getElementById('fin-registros-productos');
    const spanTotalProductos = document.getElementById('total-registros-productos');

    // Verificar que tenemos todos los elementos necesarios para productos
    if (tablaProductos && tbodyProductos && window.datosProductos) {
        inicializarPaginacion(
            window.datosProductos,
            tbodyProductos,
            paginacionProductos,
            selectRegistrosPorPaginaProductos,
            inputBuscarProducto,
            spanInicioProductos,
            spanFinProductos,
            spanTotalProductos,
            generarFilaProducto,
            'productos'
        );
    }

    // Configuración inicial catálogos
    let paginaActualCatalogos = 1;
    let registrosPorPaginaCatalogos = 10;
    let filtroTextoCatalogos = '';

    // Elementos DOM catálogos
    const tablaCatalogos = document.getElementById('catalogosDataTable');
    const tbodyCatalogos = tablaCatalogos ? tablaCatalogos.querySelector('tbody') : null;
    const paginacionCatalogos = document.getElementById('paginacion-catalogos');
    const selectRegistrosPorPaginaCatalogos = document.getElementById('registrosPorPaginaCatalogos');
    const inputBuscarCatalogo = document.getElementById('buscarCatalogo');
    const spanInicioCatalogos = document.getElementById('inicio-registros-catalogos');
    const spanFinCatalogos = document.getElementById('fin-registros-catalogos');
    const spanTotalCatalogos = document.getElementById('total-registros-catalogos');

    // Verificar que tenemos todos los elementos necesarios para catálogos
    if (tablaCatalogos && tbodyCatalogos && window.datosCatalogos) {
        inicializarPaginacion(
            window.datosCatalogos,
            tbodyCatalogos,
            paginacionCatalogos,
            selectRegistrosPorPaginaCatalogos,
            inputBuscarCatalogo,
            spanInicioCatalogos,
            spanFinCatalogos,
            spanTotalCatalogos,
            generarFilaCatalogo,
            'catalogos'
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
                if (tipo === 'productos') {
                    return item.nombre.toLowerCase().includes(termino) ||
                        item.codigo.toLowerCase().includes(termino) ||
                        item.descripcion.toLowerCase().includes(termino);
                } else {
                    return item.producto.toLowerCase().includes(termino) ||
                        item.codigo.toLowerCase().includes(termino) ||
                        item.planta.toLowerCase().includes(termino) ||
                        item.hospital.toLowerCase().includes(termino);
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
                            <td colspan="${tipo === 'productos' ? '6' : '7'}" class="text-center py-4">
                                <i class="fas fa-info-circle me-2"></i>
                                No se encontraron ${tipo === 'productos' ? 'productos' : 'catálogos'} registrados
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
                            if (tipo === 'productos') {
                                tablaProductos.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            } else {
                                tablaCatalogos.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            }
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
     * Genera una fila de la tabla de productos
     */
    function generarFilaProducto(producto) {
        return `
            <tr class="list-table__body-row">
                <td class="list-table__body-cell" data-label="ID">${escapeHtml(producto.id)}</td>
                <td class="list-table__body-cell" data-label="Código">${escapeHtml(producto.codigo)}</td>
                <td class="list-table__body-cell" data-label="Nombre">${escapeHtml(producto.nombre)}</td>
                <td class="list-table__body-cell" data-label="Descripción">${escapeHtml(producto.descripcion)}</td>
                <td class="list-table__body-cell" data-label="Unidad de Medida">${escapeHtml(producto.unidad_medida)}</td>
                <td class="list-table__body-cell" data-label="Acciones">
                    <div class="list-table__actions">
                        <button class="list-table__button list-table__button--edit" 
                                onclick="seleccionarProducto(${producto.id})"
                                title="Editar producto">
                            <i class="bi bi-pencil-square list-table__button-icon"></i> Editar
                        </button>
                        <button class="list-table__button list-table__button--delete"
                                onclick="confirmarEliminarProducto(${producto.id}, '${escapeHtml(producto.nombre)}')"
                                title="Eliminar producto">
                            <i class="bi bi-trash list-table__button-icon"></i> Eliminar
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }

    /**
     * Genera una fila de la tabla de catálogos
     */
    function generarFilaCatalogo(catalogo) {
        return `
            <tr class="list-table__body-row ${catalogo.activo ? '' : 'text-muted'}">
                <td class="list-table__body-cell" data-label="ID">${escapeHtml(catalogo.id)}</td>
                <td class="list-table__body-cell" data-label="Producto">${escapeHtml(catalogo.producto)}</td>
                <td class="list-table__body-cell" data-label="Código">${escapeHtml(catalogo.codigo)}</td>
                <td class="list-table__body-cell" data-label="Planta">${escapeHtml(catalogo.planta)}</td>
                <td class="list-table__body-cell" data-label="Hospital">${escapeHtml(catalogo.hospital)}</td>
                <td class="list-table__body-cell" data-label="Estado">
                    ${catalogo.activo
            ? '<span class="badge bg-success">Activo</span>'
            : '<span class="badge bg-danger">Inactivo</span>'}
                </td>
                <td class="list-table__body-cell" data-label="Acciones">
                    <div class="list-table__actions">
                        <button class="list-table__button list-table__button--edit" 
                                onclick="seleccionarCatalogo(${catalogo.id})"
                                title="Editar catálogo">
                            <i class="bi bi-pencil-square list-table__button-icon"></i> Editar
                        </button>
                        <button class="list-table__button list-table__button--delete"
                                onclick="confirmarEliminarCatalogo(${catalogo.id}, '${escapeHtml(catalogo.producto)}', '${escapeHtml(catalogo.planta)}')"
                                title="Eliminar del catálogo">
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
        if (tipo === 'productos') {
            // Los botones ya tienen onclick en el HTML generado
            // Si hay botones adicionales que necesitan event listeners, se agregarían aquí
        } else if (tipo === 'catalogos') {
            // Los botones ya tienen onclick en el HTML generado
            // Si hay botones adicionales que necesitan event listeners, se agregarían aquí
        }
    }
});
