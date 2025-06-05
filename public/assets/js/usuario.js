/**
 * usuario.js - Script para gestionar la funcionalidad de usuarios
 * 
 * Este archivo contiene todas las funciones relacionadas con la gestión de usuarios:
 * - Navegación entre pestañas
 * - Carga de datos de usuarios para edición
 * - Carga de ubicaciones de usuarios
 * - Inicialización de DataTables para la lista de usuarios
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Inicializando script de usuarios...');
    
    // Inicializar DataTables para la tabla de usuarios
    initUsuariosDataTable();
    
    // Inicializar manejadores de eventos para la tabla de usuarios
    initUsuariosTableHandlers();
    
    // Inicializar eventos para mostrar/ocultar contraseñas
    initPasswordToggle();
    
    // Inicializar el toggle de los formularios
    initFormToggleButtons();

    // Inicializar formularios con Ajax
    initFormHandlers();

    // Inicializar lógica de edición tipo almacenes SIN AJAX
    initUsuarioEditarDropdown();
    
    // Cargar ubicaciones si ya hay un usuario seleccionado en la URL
    autocargarUbicacionesDesdeURL();
});

/**
 * Inicializa los formularios para envío con Ajax
 */
function initFormHandlers() {
    const formCrearUsuario = document.getElementById('formCrearUsuario');
    const formEditarUsuario = document.getElementById('formEditarUsuario');

    if (formCrearUsuario) {
        formCrearUsuario.addEventListener('submit', function(e) {
            e.preventDefault();
            submitFormWithAjax(this, 'crear');
        });
    }

    if (formEditarUsuario) {
        formEditarUsuario.addEventListener('submit', function(e) {
            e.preventDefault();
            submitFormWithAjax(this, 'editar');
        });
    }
}

/**
 * Envía un formulario mediante Ajax
 * @param {HTMLFormElement} form - El formulario a enviar
 * @param {string} actionType - Tipo de acción ('crear', 'editar', etc.)
 */
function submitFormWithAjax(form, actionType) {
    const formData = new FormData(form);
    
    // Mostrar indicador de carga
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Éxito
            mostrarAlerta('success', data.message || 'Operación completada con éxito');
            
            // Esperar un momento para mostrar el mensaje antes de recargar los datos
            setTimeout(() => {
                if (actionType === 'crear') {
                    form.reset();
                    refreshUserTable();
                } else if (actionType === 'editar') {
                    // Si estamos en edición, volvemos a la lista de usuarios
                    const tabUsuariosBtn = document.querySelector('[data-tab="tab-usuarios"]');
                    if (tabUsuariosBtn) {
                        tabUsuariosBtn.click();
                        refreshUserTable();
                    }
                }
            }, 1500);
        } else {
            // Error
            mostrarAlerta('error', data.message || 'Error al procesar la solicitud');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarAlerta('error', 'Error de conexión al procesar la solicitud');
    })
    .finally(() => {
        // Restaurar el botón
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    });
}

/**
 * Inicializa DataTables con paginación para la tabla de usuarios
 */
function initUsuariosDataTable() {
    const usuariosTable = document.getElementById('usuariosDataTable');
    
    if (usuariosTable && typeof $.fn.DataTable !== 'undefined') {
        try {
            console.log('Inicializando DataTable para usuarios...');
            
            $(usuariosTable).DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json',
                    search: "Buscar:",
                    lengthMenu: "Mostrar _MENU_ registros por página",
                    zeroRecords: "No se encontraron registros",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    infoEmpty: "Mostrando 0 a 0 de 0 registros",
                    infoFiltered: "(filtrado de _MAX_ registros totales)",
                    paginate: {
                        first: "Primero",
                        last: "Último",
                        next: "Siguiente",
                        previous: "Anterior"
                    }
                },
                pagingType: "full_numbers",
                responsive: true,
                order: [[0, 'asc']],
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
                pageLength: 10,
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                     "<'row'<'col-sm-12'tr>>" +
                     "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                drawCallback: function() {
                    console.log('DataTable redibujada');
                }
            });
            console.log('DataTable inicializado correctamente');
        } catch (error) {
            console.error('Error al inicializar DataTable:', error);
        }
    } else {
        console.warn('DataTable no está disponible o no se encontró la tabla de usuarios');
    }
}

/**
 * Refresca la tabla de usuarios
 */
function refreshUserTable() {
    if ($.fn.DataTable.isDataTable('#usuariosDataTable')) {
        console.log('Recargando tabla de usuarios...');
        // Intentamos recargar los datos mediante API
        reloadUserData();
    } else {
        console.log('La tabla no está inicializada como DataTable, recargando página...');
        window.location.reload();
    }
}

/**
 * Recarga los datos de usuarios desde la API
 */
function reloadUserData() {
    fetch('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/UsuarioController.php?action=getAll', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && $.fn.DataTable.isDataTable('#usuariosDataTable')) {
            // Recargar la página para simplificar (en una versión más avanzada podrías actualizar solo la tabla)
            window.location.reload();
        } else {
            console.error('Error al cargar datos de usuarios:', data.message);
        }
    })
    .catch(error => {
        console.error('Error en la solicitud de usuarios:', error);
    });
}

/**
 * Inicializa los manejadores de eventos para los botones en la tabla de usuarios
 */
function initUsuariosTableHandlers() {
    // Delegación de eventos para los botones de editar y ubicaciones
    const tablaUsuarios = document.querySelector('#usuariosDataTable tbody');
    
    if (tablaUsuarios) {
        tablaUsuarios.addEventListener('click', function(e) {
            // Manejar botón de editar
            if (e.target.closest('.list-table__button--edit')) {
                e.preventDefault();
                const button = e.target.closest('.list-table__button--edit');
                const userId = button.getAttribute('data-user-id');
                console.log('Click en botón editar, user ID:', userId);
                if (userId) {
                    navigateToEditTab(userId);
                } else {
                    console.error('No se encontró el ID de usuario en el botón de editar');
                }
            }
            
            // Manejar botón de ubicaciones
            if (e.target.closest('.list-table__button--locations')) {
                e.preventDefault();
                const button = e.target.closest('.list-table__button--locations');
                const userId = button.getAttribute('data-user-id');
                console.log('Click en botón ubicaciones, user ID:', userId);
                if (userId) {
                    navigateToLocationsTab(userId);
                } else {
                    console.error('No se encontró el ID de usuario en el botón de ubicaciones');
                }
            }
        });
    } else {
        console.warn('No se encontró la tabla de usuarios');
    }
}

/**
 * Inicializa los botones de toggle para cambiar entre formularios
 */
function initFormToggleButtons() {
    const toggleButtons = document.querySelectorAll('.form-toggle-btn');
    const formSections = document.querySelectorAll('.form-section');
    
    if (toggleButtons.length && formSections.length) {
        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                console.log(`Cambiando a formulario: ${targetId}`);
                
                // Desactivar todos los botones y ocultar todos los formularios
                toggleButtons.forEach(btn => btn.classList.remove('active'));
                formSections.forEach(section => section.style.display = 'none');
                
                // Activar el botón seleccionado y mostrar el formulario correspondiente
                this.classList.add('active');
                const targetForm = document.getElementById(targetId);
                if (targetForm) {
                    targetForm.style.display = 'block';
                    
                    // Si es el formulario de edición, asegúrate de que el select tenga el foco
                    if (targetId === 'editarForm') {
                        const selectUsuario = document.getElementById('seleccionarUsuario');
                        if (selectUsuario) {
                            setTimeout(() => selectUsuario.focus(), 100);
                        }
                    }
                }
            });
        });
        
        console.log('Botones de toggle de formularios inicializados');
    }
}

/**
 * Inicializa el comportamiento de edición de usuario tipo almacenes (dropdown + autollenado, sin AJAX)
 */
function initUsuarioEditarDropdown() {
    const selectUsuario = document.getElementById('seleccionarUsuario');
    const formEditarContainer = document.getElementById('formEditarContainer');
    const mensajeSeleccion = document.getElementById('mensajeSeleccion');
    if (!selectUsuario) return;

    // Mapa de usuarios para acceso rápido (rellenado por PHP en la vista)
    if (typeof window.usuariosData === 'undefined') {
        window.usuariosData = {};
        // Si no existe, intentar construirlo desde la tabla (opcional)
    }

    selectUsuario.addEventListener('change', function() {
        const userId = this.value;
        if (userId && window.usuariosData[userId]) {
            // Ocultar mensaje, mostrar formulario
            if (mensajeSeleccion) mensajeSeleccion.style.display = 'none';
            if (formEditarContainer) formEditarContainer.style.display = 'block';
            // Llenar el formulario con los datos del usuario (sin AJAX)
            fillEditForm(window.usuariosData[userId]);
        } else {
            // Mostrar mensaje, ocultar formulario
            if (mensajeSeleccion) {
                mensajeSeleccion.innerHTML = '<i class="fas fa-info-circle me-2"></i> Seleccione un usuario para editar';
                mensajeSeleccion.style.display = 'block';
            }
            if (formEditarContainer) formEditarContainer.style.display = 'none';
        }
    });

    // Botón cancelar edición (si existe)
    const cancelarBtn = document.getElementById('cancelar_edicion_usuario');
    if (cancelarBtn) {
        cancelarBtn.addEventListener('click', function() {
            selectUsuario.value = '';
            if (mensajeSeleccion) {
                mensajeSeleccion.innerHTML = '<i class="fas fa-info-circle me-2"></i> Seleccione un usuario para editar';
                mensajeSeleccion.style.display = 'block';
            }
            if (formEditarContainer) formEditarContainer.style.display = 'none';
        });
    }
}

/**
 * Navega a la pestaña de edición y carga los datos del usuario sin recargar la página (sin AJAX)
 * @param {string} userId - ID del usuario a editar
 */
function navigateToEditTab(userId) {
    console.log('Navegando a la pestaña de edición para el usuario ID:', userId);

    try {
        // 1. Cambiar a la pestaña de edición
        const tabButton = document.querySelector('[data-tab="tab-crear-editar"]');
        if (tabButton) {
            // Simular clic en la pestaña
            tabButton.click();
            
            // 2. Esperar brevemente para que se muestre la pestaña
            setTimeout(() => {
                // 3. Cambiar al formulario de edición (no el de creación)
                const editToggleButton = document.querySelector('[data-target="editarForm"]');
                if (editToggleButton) {
                    editToggleButton.click();
                    
                    // 4. Esperar a que se muestre el formulario de edición
                    setTimeout(() => {
                        // 5. Seleccionar el usuario en el dropdown
                        const selectUsuario = document.getElementById('seleccionarUsuario');
                        if (selectUsuario) {
                            console.log('Seleccionando usuario ID:', userId);
                            selectUsuario.value = userId;
                            
                            // Disparar evento change para que se comporte igual que almacenes
                            const event = new Event('change');
                            selectUsuario.dispatchEvent(event);
                        } else {
                            console.error('No se encontró el selector de usuario');
                        }
                    }, 100);
                } else {
                    console.error('No se encontró el botón para el formulario de edición');
                }
            }, 100);
        } else {
            console.error('No se encontró el botón de la pestaña de edición');
        }
    } catch (error) {
        console.error('Error al navegar a la pestaña de edición:', error);
        mostrarAlerta('error', 'Error: No se pudo cargar la pestaña de edición');
    }
}

/**
 * Rellena el formulario de edición con los datos del usuario (sin AJAX)
 * @param {Object} usuario - Datos del usuario
 */
function fillEditForm(usuario) {
    try {
        console.log('Rellenando formulario con datos:', usuario);
        
        // Asegurar que tenemos los elementos del formulario
        const formElements = {
            id: document.getElementById('editId'),
            nombre: document.getElementById('editNombre'),
            email: document.getElementById('editEmail'),
            rol: document.getElementById('editRol'),
            activo: document.getElementById('editActivo'),
            estadoLabel: document.getElementById('estadoLabel'),
            contrasena: document.getElementById('editContrasena'),
            confirmarContrasena: document.getElementById('editConfirmarContrasena')
        };
        
        // Verificar que se encontraron todos los elementos
        for (const [key, element] of Object.entries(formElements)) {
            if (!element) {
                console.error(`No se encontró el elemento '${key}' en el formulario`);
            }
        }
        
        // Rellenar los campos
        if (formElements.id) formElements.id.value = usuario.id_usuario;
        if (formElements.nombre) formElements.nombre.value = usuario.nombre;
        if (formElements.email) formElements.email.value = usuario.email;
        if (formElements.rol) formElements.rol.value = usuario.rol;
        
        // Establecer el estado activo
        const activo = usuario.activo === true || usuario.activo === 1 || usuario.activo === '1';
        
        if (formElements.activo) {
            formElements.activo.checked = activo;
        }
        
        if (formElements.estadoLabel) {
            formElements.estadoLabel.textContent = activo ? 'Activo' : 'Inactivo';
        }
        
        // Limpiar campos de contraseña
        if (formElements.contrasena) formElements.contrasena.value = '';
        if (formElements.confirmarContrasena) formElements.confirmarContrasena.value = '';
        
        console.log('Formulario rellenado exitosamente');
    } catch (error) {
        console.error('Error al rellenar formulario:', error);
        mostrarAlerta('error', 'Error al rellenar el formulario');
    }
}

/**
 * Navega a la pestaña de ubicaciones y carga los datos del usuario
 * @param {string} userId - ID del usuario
 */
function navigateToLocationsTab(userId) {
    console.log('Navegando a la pestaña de ubicaciones para el usuario ID:', userId);
    
    try {
        // Activar la pestaña de ubicaciones
        const tabButton = document.querySelector('[data-tab="tab-ubicaciones"]');
        if (tabButton) {
            // Hacer clic en la pestaña para activarla
            tabButton.click();
            
            // Esperar a que la pestaña se muestre antes de seleccionar el usuario
            setTimeout(() => {
                const selectUsuario = document.getElementById('usuario_id');
                console.log('Select ubicación usuario encontrado:', selectUsuario ? 'SÍ' : 'NO');
                
                if (selectUsuario) {
                    // Cambiar el valor del select
                    selectUsuario.value = userId;
                    console.log('Valor del select cambiado a:', userId);
                    
                    // Disparar el evento change manualmente para cargar las ubicaciones
                    const event = new Event('change', { bubbles: true });
                    selectUsuario.dispatchEvent(event);
                    console.log('Evento change disparado');
                } else {
                    console.error('No se encontró el selector de usuario para ubicaciones');
                    mostrarAlerta('error', 'Error interno: No se encontró el selector de usuario');
                }
            }, 300); // Aumentamos el tiempo de espera para asegurar que la pestaña se haya cargado
        } else {
            console.error('No se encontró el botón de la pestaña de ubicaciones');
            mostrarAlerta('error', 'Error interno: No se pudo navegar a la pestaña de ubicaciones');
        }
    } catch (error) {
        console.error('Error al navegar a la pestaña de ubicaciones:', error);
        mostrarAlerta('error', 'Error: No se pudo cargar la pestaña de ubicaciones');
    }
}

/**
 * Inicializa los toggles para mostrar/ocultar contraseñas
 */
function initPasswordToggle() {
    const toggleButtons = document.querySelectorAll('.toggle-password');
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
}

/**
 * Autocarga las ubicaciones para un usuario específico si la URL contiene un parámetro de usuario
 */
function autocargarUbicacionesDesdeURL() {
    const urlParams = new URLSearchParams(window.location.search);
    const userId = urlParams.get('user_id');
    
    if (userId) {
        console.log('Autodetectado usuario ID en URL:', userId);
        setTimeout(() => {
            navigateToLocationsTab(userId);
        }, 500);
    }
}

/**
 * Muestra una alerta en la página
 * @param {string} tipo - Tipo de alerta ('success' o 'error')
 * @param {string} mensaje - Mensaje a mostrar
 */
function mostrarAlerta(tipo, mensaje) {
    const alertClass = tipo === 'success' ? 'list-alert--success' : 'list-alert--error';
    const iconClass = tipo === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `list-alert ${alertClass}`;
    alertDiv.innerHTML = `
        <p class="list-alert__message"><i class="fas ${iconClass} me-2"></i> ${mensaje}</p>
        <button type="button" class="list-alert__close">&times;</button>
    `;
    
    const container = document.querySelector('.list-container');
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);
        
        // Configurar el botón de cerrar
        const closeButton = alertDiv.querySelector('.list-alert__close');
        if (closeButton) {
            closeButton.addEventListener('click', function() {
                alertDiv.remove();
            });
        }
        
        // Auto-eliminar después de 5 segundos
        setTimeout(() => {
            alertDiv.classList.add('list-alert--fade-out');
            setTimeout(() => alertDiv.remove(), 500);
        }, 5000);
    }
}

/**
 * Función para cancelar la edición de un usuario y limpiar el formulario
 */
function cancelarEditarUsuario() {
    const selectUsuario = document.getElementById('seleccionarUsuario');
    const mensajeSeleccion = document.getElementById('mensajeSeleccion');
    const formEditarContainer = document.getElementById('formEditarContainer');
    
    // Resetear el selector de usuario
    if (selectUsuario) selectUsuario.value = '';
    
    // Mostrar mensaje de selección
    if (mensajeSeleccion) {
        mensajeSeleccion.innerHTML = '<i class="fas fa-info-circle me-2"></i> Seleccione un usuario para editar';
        mensajeSeleccion.style.display = 'block';
    }
    
    // Ocultar formulario
    if (formEditarContainer) {
        formEditarContainer.style.display = 'none';
    }
}
