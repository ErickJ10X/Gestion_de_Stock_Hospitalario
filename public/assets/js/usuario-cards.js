document.addEventListener('DOMContentLoaded', function() {
    // Botones para abrir modales
    const modalOpenButtons = document.querySelectorAll('.usuario-card-open');
    modalOpenButtons.forEach(button => {
        button.addEventListener('click', function() {
            const target = this.getAttribute('data-target');
            const modal = document.getElementById(target);
            if (modal) {
                modal.style.display = 'block';
                const overlay = document.querySelector('.usuario-overlay');
                if (overlay) {
                    overlay.style.display = 'block';
                }
                
                // Transferir atributos data al formulario para modales de ubicación
                if (target === 'editar-ubicacion-modal') {
                    const idUsuario = this.getAttribute('data-id-usuario');
                    const tipoUbicacion = this.getAttribute('data-tipo-ubicacion');
                    const idUbicacion = this.getAttribute('data-id-ubicacion');
                    
                    if (idUsuario && tipoUbicacion && idUbicacion) {
                        const editUsuarioId = document.getElementById('edit_usuario_id');
                        const editTipoUbicacionOriginal = document.getElementById('edit_tipo_ubicacion_original');
                        const editUbicacionIdOriginal = document.getElementById('edit_ubicacion_id_original');
                        const editTipoUbicacion = document.getElementById('edit_tipo_ubicacion');
                        const editUbicacionId = document.getElementById('edit_ubicacion_id');
                        
                        if (editUsuarioId) editUsuarioId.value = idUsuario;
                        if (editTipoUbicacionOriginal) editTipoUbicacionOriginal.value = tipoUbicacion;
                        if (editUbicacionIdOriginal) editUbicacionIdOriginal.value = idUbicacion;
                        if (editTipoUbicacion) editTipoUbicacion.value = tipoUbicacion;
                        if (editUbicacionId) editUbicacionId.value = idUbicacion;
                    }
                }
                else if (target === 'eliminar-ubicacion-modal') {
                    const idUsuario = this.getAttribute('data-id-usuario');
                    const tipoUbicacion = this.getAttribute('data-tipo-ubicacion');
                    const idUbicacion = this.getAttribute('data-id-ubicacion');
                    const nombreUsuario = this.getAttribute('data-nombre-usuario');
                    
                    if (idUsuario && tipoUbicacion && idUbicacion) {
                        const deleteUsuarioNombre = document.getElementById('delete-usuario-nombre');
                        const deleteTipoUbicacionTexto = document.getElementById('delete-tipo-ubicacion-texto');
                        const deleteUbicacionId = document.getElementById('delete-ubicacion-id');
                        const deleteUsuarioId = document.getElementById('delete_usuario_id');
                        const deleteTipoUbicacion = document.getElementById('delete_tipo_ubicacion');
                        const deleteUbicacionId2 = document.getElementById('delete_ubicacion_id');
                        
                        if (deleteUsuarioNombre) deleteUsuarioNombre.textContent = nombreUsuario;
                        if (deleteTipoUbicacionTexto) deleteTipoUbicacionTexto.textContent = getTipoUbicacionLabel(tipoUbicacion);
                        if (deleteUbicacionId) deleteUbicacionId.textContent = idUbicacion;
                        if (deleteUsuarioId) deleteUsuarioId.value = idUsuario;
                        if (deleteTipoUbicacion) deleteTipoUbicacion.value = tipoUbicacion;
                        if (deleteUbicacionId2) deleteUbicacionId2.value = idUbicacion;
                    }
                }
            }
        });
    });

    // Botones para cerrar modales
    const modalCloseButtons = document.querySelectorAll('.usuario-card__close, .usuario-form__button--cancel');
    modalCloseButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.usuario-card');
            if (modal) {
                modal.style.display = 'none';
                const overlay = document.querySelector('.usuario-overlay');
                if (overlay) {
                    overlay.style.display = 'none';
                }
            }
        });
    });

    // Cerrar modal al hacer clic en el overlay
    const overlay = document.querySelector('.usuario-overlay');
    if (overlay) {
        overlay.addEventListener('click', function() {
            document.querySelectorAll('.usuario-card').forEach(modal => {
                modal.style.display = 'none';
            });
            this.style.display = 'none';
        });
    }
    
    // Cerrar alertas al hacer clic en el botón de cierre
    const alertCloseButtons = document.querySelectorAll('.list-alert__close');
    alertCloseButtons.forEach(button => {
        button.addEventListener('click', function() {
            const alert = this.closest('.list-alert');
            if (alert) {
                alert.style.display = 'none';
            }
        });
    });
    
    // Validar formulario de usuarios
    const formCrearUsuario = document.getElementById('form-crear-usuario');
    if (formCrearUsuario) {
        formCrearUsuario.addEventListener('submit', function(event) {
            const contrasena = document.getElementById('contrasena-create').value;
            const confirmarContrasena = document.getElementById('confirmar_contrasena-create').value;
            
            if (contrasena !== confirmarContrasena) {
                event.preventDefault();
                alert('Las contraseñas no coinciden');
            }
        });
    }
    
    // Validación de formularios de edición
    document.querySelectorAll('[id^="form-editar-usuario-"]').forEach(form => {
        form.addEventListener('submit', function(event) {
            const idUsuario = this.querySelector('[name="id"]').value;
            const contrasena = document.getElementById(`contrasena-edit-${idUsuario}`).value;
            const confirmarContrasena = document.getElementById(`confirmar_contrasena-edit-${idUsuario}`).value;
            
            if (contrasena !== '' && contrasena !== confirmarContrasena) {
                event.preventDefault();
                alert('Las contraseñas no coinciden');
            }
        });
    });
    
    // Función auxiliar para obtener etiqueta de tipo de ubicación
    function getTipoUbicacionLabel(tipo) {
        switch (tipo) {
            case 'hospital':
                return 'Hospital';
            case 'planta':
                return 'Planta';
            case 'botiquin':
                return 'Botiquín';
            default:
                return tipo;
        }
    }
});
