/**
 * Archivo JavaScript para manejar la funcionalidad de los formularios de usuarios
 * Versión sin AJAX - Formularios tradicionales
 */

document.addEventListener('DOMContentLoaded', () => {
    // Inicialización de tooltips y popovers de Bootstrap si están presentes
    if (typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    }

    // Funcionalidad para alternar la visibilidad de la contraseña
    const togglePasswordButtons = document.querySelectorAll('.toggle-password');
    if (togglePasswordButtons) {
        togglePasswordButtons.forEach(button => {
            button.addEventListener('click', function() {
                const input = this.closest('.input-group').querySelector('input');
                const currentType = input.getAttribute('type');
                
                if (currentType === 'password') {
                    input.setAttribute('type', 'text');
                    this.innerHTML = '<i class="fas fa-eye-slash"></i>';
                } else {
                    input.setAttribute('type', 'password');
                    this.innerHTML = '<i class="fas fa-eye"></i>';
                }
            });
        });
    }
    
    // Validación personalizada para contraseñas
    function validarContrasena(contrasena) {
        // Al menos 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial
        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$/;
        return passwordRegex.test(contrasena);
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

    // Manejo del formulario de creación de usuario - Validación Cliente
    const formCrearUsuario = document.getElementById('formCrearUsuario');
    if (formCrearUsuario) {
        formCrearUsuario.addEventListener('submit', function(e) {
            // Obtener valores de los campos
            const nombre = document.getElementById('nombre').value.trim();
            const email = document.getElementById('email').value.trim();
            const rol = document.getElementById('rol').value;
            const contrasena = document.getElementById('contrasena').value;
            const confirmarContrasena = document.getElementById('confirmar_contrasena').value;
            
            // Validaciones básicas
            if (!nombre || !email || !rol || !contrasena || !confirmarContrasena) {
                e.preventDefault();
                alert('Todos los campos son obligatorios');
                return false;
            }
            
            // Validar que las contraseñas coincidan
            if (contrasena !== confirmarContrasena) {
                e.preventDefault();
                alert('Las contraseñas no coinciden');
                return false;
            }
            
            // Validar el formato de la contraseña
            if (!validarContrasena(contrasena)) {
                e.preventDefault();
                alert('La contraseña debe tener al menos 8 caracteres, una letra mayúscula, una minúscula, un número y un carácter especial');
                return false;
            }
            
            // Si todo está bien, el formulario se enviará normalmente
            return true;
        });
    }

    // Manejo del selector de usuario para edición
    const seleccionarUsuario = document.getElementById('seleccionarUsuario');
    const formEditarContainer = document.getElementById('formEditarContainer');
    const mensajeSeleccion = document.getElementById('mensajeSeleccion');
    
    if (seleccionarUsuario && formEditarContainer && mensajeSeleccion) {
        seleccionarUsuario.addEventListener('change', function() {
            const userId = this.value;
            
            if (!userId) {
                mensajeSeleccion.style.display = 'block';
                formEditarContainer.style.display = 'none';
                return;
            }
            
            // Obtener datos del usuario seleccionado
            const userData = window.usuariosData?.[userId];
            
            if (userData) {
                // Asignar datos a los campos del formulario
                document.getElementById('editId').value = userData.id_usuario;
                document.getElementById('editNombre').value = userData.nombre;
                document.getElementById('editEmail').value = userData.email;
                document.getElementById('editRol').value = userData.rol;
                
                const checkboxActivo = document.getElementById('editActivo');
                if (checkboxActivo) {
                    checkboxActivo.checked = userData.activo === '1';
                    const estadoLabel = document.getElementById('estadoLabel');
                    if (estadoLabel) {
                        estadoLabel.textContent = userData.activo === '1' ? 'Activo' : 'Inactivo';
                    }
                }
                
                // Limpiar contraseñas
                document.getElementById('editContrasena').value = '';
                document.getElementById('editConfirmarContrasena').value = '';
                
                // Mostrar formulario y ocultar mensaje
                mensajeSeleccion.style.display = 'none';
                formEditarContainer.style.display = 'block';
            }
        });
    }

    // Manejo del botón para cancelar edición
    const cancelarEdicion = document.getElementById('cancelar_edicion_usuario');
    if (cancelarEdicion) {
        cancelarEdicion.addEventListener('click', function() {
            if (seleccionarUsuario) {
                seleccionarUsuario.value = '';
            }
            
            if (formEditarContainer) {
                formEditarContainer.style.display = 'none';
            }
            
            if (mensajeSeleccion) {
                mensajeSeleccion.style.display = 'block';
            }
        });
    }

    // Manejo del formulario de edición - Validación Cliente
    const formEditarUsuario = document.getElementById('formEditarUsuario');
    if (formEditarUsuario) {
        formEditarUsuario.addEventListener('submit', function(e) {
            // Validaciones básicas para edición
            const id = document.getElementById('editId').value;
            const nombre = document.getElementById('editNombre').value.trim();
            const email = document.getElementById('editEmail').value.trim();
            const rol = document.getElementById('editRol').value;
            
            if (!id || !nombre || !email || !rol) {
                e.preventDefault();
                alert('El nombre, email y rol son obligatorios');
                return false;
            }
            
            // Si hay contraseña, validarla
            const contrasena = document.getElementById('editContrasena').value;
            const confirmarContrasena = document.getElementById('editConfirmarContrasena').value;
            
            if (contrasena || confirmarContrasena) {
                if (contrasena !== confirmarContrasena) {
                    e.preventDefault();
                    alert('Las contraseñas no coinciden');
                    return false;
                }
                
                if (contrasena && !validarContrasena(contrasena)) {
                    e.preventDefault();
                    alert('La contraseña debe tener al menos 8 caracteres, una letra mayúscula, una minúscula, un número y un carácter especial');
                    return false;
                }
            }
            
            // Si todo está bien, el formulario se enviará normalmente
            return true;
        });
    }

    // Actualizar etiqueta de estado al cambiar checkbox
    const editActivo = document.getElementById('editActivo');
    if (editActivo) {
        editActivo.addEventListener('change', function() {
            const estadoLabel = document.getElementById('estadoLabel');
            if (estadoLabel) {
                estadoLabel.textContent = this.checked ? 'Activo' : 'Inactivo';
            }
        });
    }

    // SISTEMA DE PAGINACIÓN PARA USUARIOS
    // Variables para la paginación
    let paginaActual = 1;
    let registrosPorPagina = 10;
    let totalPaginas = Math.ceil(window.totalRegistros / registrosPorPagina);
    let datosFiltrados = window.datosUsuarios ? [...window.datosUsuarios] : [];

    // Referencias a elementos DOM para paginación
    const tablaUsuarios = document.querySelector('#usuariosDataTable tbody');
    const paginacionUsuarios = document.getElementById('paginacion-usuarios');
    const inicioRegistros = document.getElementById('inicio-registros');
    const finRegistros = document.getElementById('fin-registros');
    const totalRegistrosElement = document.getElementById('total-registros');
    const buscarUsuario = document.getElementById('buscarUsuario');
    const registrosPorPaginaSelect = document.getElementById('registrosPorPagina');

    // Función para actualizar la tabla según la página actual
    function actualizarTablaUsuarios() {
        if (!tablaUsuarios || !datosFiltrados) return;

        const inicio = (paginaActual - 1) * registrosPorPagina;
        const fin = inicio + registrosPorPagina;
        const usuariosPagina = datosFiltrados.slice(inicio, fin);

        // Limpiar la tabla
        tablaUsuarios.innerHTML = '';

        // Si no hay datos después del filtrado
        if (usuariosPagina.length === 0) {
            const tr = document.createElement('tr');
            tr.className = 'no-results-row';
            tr.innerHTML = `
                <td colspan="6" class="text-center py-4">
                    <i class="fas fa-info-circle me-2"></i>
                    No se encontraron usuarios con los criterios de búsqueda
                </td>
            `;
            tablaUsuarios.appendChild(tr);
        } else {
            // Añadir filas correspondientes a la página actual
            usuariosPagina.forEach(usuario => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${usuario.id}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span class="ms-2">${usuario.nombre}</span>
                        </div>
                    </td>
                    <td>
                        <a href="mailto:${usuario.email}" class="text-decoration-none">
                            <i class="far fa-envelope me-1"></i>
                            ${usuario.email}
                        </a>
                    </td>
                    <td>
                        <span class="badge ${getBadgeClassForRol(usuario.rol)}">${usuario.rol}</span>
                    </td>
                    <td>
                        <span class="badge ${usuario.activo ? 'bg-success' : 'bg-danger'} status-badge">
                            <i class="fas fa-${usuario.activo ? 'check-circle' : 'times-circle'} me-1"></i>
                            ${usuario.activo ? 'Activo' : 'Inactivo'}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="#" onclick="editarUsuarioDesdeTabla(${usuario.id}); return false;"
                               class="list-table__button list-table__button--edit btn btn-sm"
                               title="Editar usuario">
                                <i class="fas fa-edit list-table__button-icon"></i> Editar
                            </a>
                            <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/usuarios/index.php?tab=asignar-ubicaciones&id=${usuario.id}"
                               class="list-table__button list-table__button--locations btn btn-sm"
                               title="Gestionar ubicaciones">
                                <i class="fas fa-map-marker-alt list-table__button-icon"></i> Ubicaciones
                            </a>
                        </div>
                    </td>
                `;
                tablaUsuarios.appendChild(tr);
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

    // Función para obtener la clase de badge según el rol
    function getBadgeClassForRol(rol) {
        switch (rol) {
            case 'ADMINISTRADOR':
                return 'bg-danger';
            case 'GESTOR_GENERAL':
                return 'bg-primary';
            case 'GESTOR_HOSPITAL':
                return 'bg-warning';
            case 'GESTOR_PLANTA':
                return 'bg-info';
            case 'USUARIO_BOTIQUIN':
                return 'bg-secondary';
            default:
                return 'bg-primary';
        }
    }

    // Función para actualizar el paginador
    function actualizarPaginador() {
        if (!paginacionUsuarios) return;

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

        paginacionUsuarios.innerHTML = paginadorHTML;

        // Agregar eventos a los enlaces de paginación
        document.querySelectorAll('#paginacion-usuarios .page-link').forEach(enlace => {
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

                actualizarTablaUsuarios();
            });
        });
    }

    // Buscador de usuarios
    if (buscarUsuario) {
        buscarUsuario.addEventListener('input', function() {
            const termino = this.value.toLowerCase().trim();
            
            if (window.datosUsuarios) {
                if (termino === '') {
                    // Si no hay término de búsqueda, mostrar todos
                    datosFiltrados = [...window.datosUsuarios];
                } else {
                    // Filtrar según el término de búsqueda
                    datosFiltrados = window.datosUsuarios.filter(usuario => {
                        return usuario.nombre.toLowerCase().includes(termino) || 
                               usuario.email.toLowerCase().includes(termino) || 
                               usuario.rol.toLowerCase().includes(termino);
                    });
                }
                
                paginaActual = 1; // Volver a la primera página con cada búsqueda
                actualizarTablaUsuarios();
            }
        });
    }

    // Manejador para cambiar registros por página
    if (registrosPorPaginaSelect) {
        registrosPorPaginaSelect.addEventListener('change', function() {
            registrosPorPagina = parseInt(this.value);
            paginaActual = 1; // Volver a la primera página
            actualizarTablaUsuarios();
        });
    }

    // Botón para nuevo usuario
    const btnNuevoUsuario = document.getElementById('btnNuevoUsuario');
    if (btnNuevoUsuario) {
        btnNuevoUsuario.addEventListener('click', function() {
            // Cambiar a la pestaña de agregar/editar
            const tabBtn = document.querySelector('.tab-btn[data-tab="tab-crear-editar"]');
            if (tabBtn) {
                tabBtn.click();
                
                // Pequeña espera para asegurar que la pestaña se haya cargado
                setTimeout(() => {
                    // Activar el formulario "Crear Usuario" (primero por defecto) 
                    const crearBtn = document.querySelector('.form-toggle-btn[data-target="crearForm"]');
                    if (crearBtn && !crearBtn.classList.contains('active')) {
                        crearBtn.click();
                    }
                }, 100);
            }
        });
    }

    // Inicializar la tabla y paginador al cargar la página
    if (window.datosUsuarios && tablaUsuarios) {
        datosFiltrados = [...window.datosUsuarios];
        actualizarTablaUsuarios();
    }
});

// Función para editar usuario desde la tabla
function editarUsuarioDesdeTabla(idUsuario) {
    // Cambiar a la pestaña de editar
    const tabBtn = document.querySelector('.tab-btn[data-tab="tab-crear-editar"]');
    if (tabBtn) {
        tabBtn.click();
        
        // Pequeña espera para asegurar que la pestaña se haya cargado
        setTimeout(() => {
            // Activar el formulario "Editar Usuario"
            const editarBtn = document.querySelector('.form-toggle-btn[data-target="editarForm"]');
            if (editarBtn) {
                editarBtn.click();
                
                // Otra pequeña espera para asegurar que el formulario se active
                setTimeout(() => {
                    // Seleccionar el usuario en el dropdown
                    const selectUsuario = document.getElementById('seleccionarUsuario');
                    if (selectUsuario) {
                        selectUsuario.value = idUsuario;
                        
                        // Disparar el evento change para cargar los datos del usuario
                        const changeEvent = new Event('change');
                        selectUsuario.dispatchEvent(changeEvent);
                    }
                }, 100);
            }
        }, 100);
    }
}
