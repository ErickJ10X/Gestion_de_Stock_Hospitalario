<?php
use model\enum\RolEnum;

// Verificar que tenemos los datos necesarios
if (!isset($usuarios)) {
    die("Error: No se han proporcionado las variables requeridas.");
}
?>

<div class="usuarios-container">
    <div class="row">
        <!-- Formulario para crear usuario -->
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="section-title bg-primary text-white">
                    Crear Usuario
                </div>
                <div class="card-body">
                    <form id="formCrearUsuario" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/UsuarioHandler.php" method="POST" class="card-form">
                        <input type="hidden" name="action" value="crear">
                        
                        <div class="form-group mb-3">
                            <label for="nombre" class="form-label">Nombre completo <span class="text-danger">*</span></label>
                            <input type="text" id="nombre" name="nombre" class="form-control" required>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="contrasena" class="form-label">Contraseña <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" id="contrasena" name="contrasena" class="form-control" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <small class="text-muted">
                                La contraseña debe tener al menos 8 caracteres, una letra mayúscula, una minúscula, un número y un carácter especial.
                            </small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="confirmar_contrasena" class="form-label">Confirmar contraseña <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" id="confirmar_contrasena" name="confirmar_contrasena" class="form-control" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="rol" class="form-label">Rol <span class="text-danger">*</span></label>
                            <select id="rol" name="rol" class="form-select" required>
                                <option value="">Seleccionar rol</option>
                                <?php foreach (RolEnum::getValues() as $rolValue): ?>
                                    <option value="<?= $rolValue ?>"><?= $rolValue ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar
                            </button>
                            <button type="reset" class="btn btn-secondary">
                                <i class="fas fa-broom"></i> Limpiar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Formulario para editar usuario -->
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="section-title bg-warning text-dark">
                    Editar Usuario
                </div>
                <div class="card-body">
                    <?php if (count($usuarios) > 0): ?>
                        <div class="form-group mb-3">
                            <label for="seleccionarUsuario" class="form-label">Seleccione un usuario</label>
                            <select id="seleccionarUsuario" class="form-select">
                                <option value="">Seleccionar usuario</option>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <option value="<?= $usuario->getIdUsuario() ?>">
                                        <?= htmlspecialchars($usuario->getNombre()) ?> (<?= htmlspecialchars($usuario->getEmail()) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div id="formEditarContainer" style="display: none;">
                            <form id="formEditarUsuario" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/UsuarioHandler.php" method="POST" class="card-form">
                                <input type="hidden" name="action" value="editar">
                                <input type="hidden" id="editId" name="id" value="">
                                
                                <div class="form-group mb-3">
                                    <label for="editNombre" class="form-label">Nombre completo <span class="text-danger">*</span></label>
                                    <input type="text" id="editNombre" name="nombre" class="form-control" required>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="editEmail" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" id="editEmail" name="email" class="form-control" required>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="editContrasena" class="form-label">Contraseña</label>
                                    <div class="input-group">
                                        <input type="password" id="editContrasena" name="contrasena" class="form-control">
                                        <button class="btn btn-outline-secondary toggle-password" type="button">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted">
                                        Dejar en blanco para mantener la contraseña actual.
                                    </small>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="editConfirmarContrasena" class="form-label">Confirmar contraseña</label>
                                    <div class="input-group">
                                        <input type="password" id="editConfirmarContrasena" name="confirmar_contrasena" class="form-control">
                                        <button class="btn btn-outline-secondary toggle-password" type="button">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="editRol" class="form-label">Rol <span class="text-danger">*</span></label>
                                    <select id="editRol" name="rol" class="form-select" required>
                                        <option value="">Seleccionar rol</option>
                                        <?php foreach (RolEnum::getValues() as $rolValue): ?>
                                            <option value="<?= $rolValue ?>"><?= $rolValue ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label class="form-label">Estado</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="editActivo" name="activo" value="1" checked>
                                        <label class="form-check-label" for="editActivo">
                                            <span id="estadoLabel">Activo</span>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save"></i> Actualizar
                                    </button>
                                    <button type="button" class="btn btn-secondary" id="cancelarEdicion">
                                        <i class="fas fa-times"></i> Cancelar
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <div id="mensajeSeleccion" class="alert alert-info text-center">
                            Seleccione un usuario para editar
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            No hay usuarios disponibles para editar.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle para mostrar/ocultar contraseñas
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
    
    // Manejo del selector de usuarios para edición
    const seleccionarUsuario = document.getElementById('seleccionarUsuario');
    const formEditarContainer = document.getElementById('formEditarContainer');
    const mensajeSeleccion = document.getElementById('mensajeSeleccion');
    const editActivo = document.getElementById('editActivo');
    const estadoLabel = document.getElementById('estadoLabel');
    const cancelarEdicion = document.getElementById('cancelarEdicion');
    
    if (seleccionarUsuario) {
        seleccionarUsuario.addEventListener('change', function() {
            const usuarioId = this.value;
            
            if (usuarioId) {
                // Ocultar mensaje y mostrar formulario
                mensajeSeleccion.style.display = 'none';
                formEditarContainer.style.display = 'block';
                
                // Buscar el usuario seleccionado en el array de usuarios
                fetch(`/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/UsuarioApi.php?action=getById&id=${usuarioId}`)
                    .then(response => response.json())
                    .then(usuario => {
                        if (usuario && usuario.success) {
                            const data = usuario.data;
                            
                            // Llenar el formulario con los datos del usuario
                            document.getElementById('editId').value = data.id_usuario;
                            document.getElementById('editNombre').value = data.nombre;
                            document.getElementById('editEmail').value = data.email;
                            document.getElementById('editRol').value = data.rol;
                            
                            // Establecer el estado activo
                            const activo = data.activo === true || data.activo === 1 || data.activo === '1';
                            editActivo.checked = activo;
                            estadoLabel.textContent = activo ? 'Activo' : 'Inactivo';
                            
                            // Limpiar campos de contraseña
                            document.getElementById('editContrasena').value = '';
                            document.getElementById('editConfirmarContrasena').value = '';
                        } else {
                            console.error('Error al obtener datos del usuario:', usuario ? usuario.message : 'Respuesta vacía');
                        }
                    })
                    .catch(error => {
                        console.error('Error al cargar datos del usuario:', error);
                    });
            } else {
                // Mostrar mensaje y ocultar formulario
                mensajeSeleccion.style.display = 'block';
                formEditarContainer.style.display = 'none';
            }
        });
    }
    
    // Actualizar etiqueta de estado al cambiar el switch
    if (editActivo) {
        editActivo.addEventListener('change', function() {
            estadoLabel.textContent = this.checked ? 'Activo' : 'Inactivo';
        });
    }
    
    // Cancelar edición
    if (cancelarEdicion) {
        cancelarEdicion.addEventListener('click', function() {
            seleccionarUsuario.value = '';
            mensajeSeleccion.style.display = 'block';
            formEditarContainer.style.display = 'none';
        });
    }
});
</script>
