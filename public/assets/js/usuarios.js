/**
 * Archivo JavaScript para manejar la funcionalidad de los formularios de usuarios
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
    
    // Función para mostrar mensajes de alerta en el DOM
    function mostrarMensaje(tipo, texto, contenedor = '.usuarios-container') {
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

    // Manejo del formulario de creación de usuario
    const formCrearUsuario = document.getElementById('formCrearUsuario');
    if (formCrearUsuario) {
        formCrearUsuario.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Obtener valores de los campos
            const nombre = document.getElementById('nombre').value.trim();
            const email = document.getElementById('email').value.trim();
            const rol = document.getElementById('rol').value;
            const contrasena = document.getElementById('contrasena').value;
            const confirmarContrasena = document.getElementById('confirmar_contrasena').value;
            
            // Validaciones básicas
            if (!nombre || !email || !rol || !contrasena || !confirmarContrasena) {
                mostrarMensaje('error', 'Todos los campos son obligatorios');
                return;
            }
            
            // Validar que las contraseñas coincidan
            if (contrasena !== confirmarContrasena) {
                mostrarMensaje('error', 'Las contraseñas no coinciden');
                return;
            }
            
            // Validar el formato de la contraseña
            if (!validarContrasena(contrasena)) {
                mostrarMensaje('error', 'La contraseña debe tener al menos 8 caracteres, una letra mayúscula, una minúscula, un número y un carácter especial');
                return;
            }
            
            // Enviar los datos mediante fetch
            const formData = new FormData(formCrearUsuario);
            
            fetch(formCrearUsuario.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                // Verificar si la respuesta es un JSON válido
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                }
                throw new Error('Respuesta no válida del servidor');
            })
            .then(data => {
                if (data.success) {
                    mostrarMensaje('success', data.message || 'Usuario creado correctamente');
                    formCrearUsuario.reset();
                    
                    // Recargar la página después de un tiempo
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    mostrarMensaje('error', data.message || 'Error al crear el usuario');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarMensaje('error', 'Error al procesar la solicitud');
            });
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

    // Manejo del formulario de edición
    const formEditarUsuario = document.getElementById('formEditarUsuario');
    if (formEditarUsuario) {
        formEditarUsuario.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validaciones básicas para edición
            const id = document.getElementById('editId').value;
            const nombre = document.getElementById('editNombre').value.trim();
            const email = document.getElementById('editEmail').value.trim();
            const rol = document.getElementById('editRol').value;
            
            if (!id || !nombre || !email || !rol) {
                mostrarMensaje('error', 'El nombre, email y rol son obligatorios');
                return;
            }
            
            // Si hay contraseña, validarla
            const contrasena = document.getElementById('editContrasena').value;
            const confirmarContrasena = document.getElementById('editConfirmarContrasena').value;
            
            if (contrasena || confirmarContrasena) {
                if (contrasena !== confirmarContrasena) {
                    mostrarMensaje('error', 'Las contraseñas no coinciden');
                    return;
                }
                
                if (contrasena && !validarContrasena(contrasena)) {
                    mostrarMensaje('error', 'La contraseña debe tener al menos 8 caracteres, una letra mayúscula, una minúscula, un número y un carácter especial');
                    return;
                }
            }
            
            // Enviar formulario
            const formData = new FormData(formEditarUsuario);
            
            // Asegurar que se envíe el estado correcto
            const checkboxActivo = document.getElementById('editActivo');
            if (checkboxActivo && !checkboxActivo.checked) {
                formData.set('activo', '0');
            }
            
            fetch(formEditarUsuario.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                }
                throw new Error('Respuesta no válida del servidor');
            })
            .then(data => {
                if (data.success) {
                    mostrarMensaje('success', data.message || 'Usuario actualizado correctamente');
                    
                    // Actualizar datos en memoria
                    if (window.usuariosData && window.usuariosData[id]) {
                        window.usuariosData[id].nombre = nombre;
                        window.usuariosData[id].email = email;
                        window.usuariosData[id].rol = rol;
                        window.usuariosData[id].activo = checkboxActivo && checkboxActivo.checked ? '1' : '0';
                    }
                    
                    // Recargar después de un tiempo
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    mostrarMensaje('error', data.message || 'Error al actualizar el usuario');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarMensaje('error', 'Error al procesar la solicitud');
            });
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
});
