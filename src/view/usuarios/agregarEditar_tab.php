<?php

use model\enum\RolEnum;

// Verificar que tenemos los datos necesarios
if (!isset($usuarios)) {
    die("Error: No se han proporcionado las variables requeridas.");
}
?>

<div class="usuarios-container">
    <div class="form-toggle-buttons mb-4">
        <button type="button" class="btn form-toggle-btn active" data-target="crearForm">
            <i class="fas fa-user-plus"></i> Crear Usuario
        </button>
        <button type="button" class="btn form-toggle-btn" data-target="editarForm">
            <i class="fas fa-user-edit"></i> Editar Usuario
        </button>
    </div>

    <div class="row">
        <!-- Formulario para crear usuario -->
        <div class="col-md-12 form-section" id="crearForm">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <!-- Eliminar AJAX y usar formulario tradicional para simplificar -->
                    <form id="formCrearUsuario"
                          action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/UsuarioController.php"
                          method="POST" class="card-form">
                        <input type="hidden" name="action" value="crear">
                        <div class="row">

                            <div class="form-group mb-3">
                                <label for="nombre" class="form-label">Nombre completo <span
                                            class="text-danger">*</span></label>
                                <input type="text" id="nombre" name="nombre" class="form-control" required>
                            </div>

                        </div>

                        <div class="row">

                            <div class="form-group mb-3">
                                <label for="email" class="form-label">Email <span
                                            class="text-danger">*</span></label>
                                <input type="email" id="email" name="email" class="form-control" required>
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

                        </div>

                        <div class="row">

                            <div class="form-group mb-3">
                                <label for="contrasena" class="form-label">Contraseña <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" id="contrasena" name="contrasena" class="form-control"
                                           required>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="confirmar_contrasena" class="form-label">Confirmar contraseña <span
                                            class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" id="confirmar_contrasena" name="confirmar_contrasena"
                                           class="form-control" required>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> La contraseña debe tener al menos 8 caracteres,
                                    una letra mayúscula, una minúscula, un número y un carácter especial.
                                </small>
                            </div>
                        </div>

                        <div class="form-group mt-4 text-center">
                            <button type="submit" class="btn btn-success">
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
        <div class="col-md-12 form-section" id="editarForm" style="display: none;">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <?php if (count($usuarios) > 0): ?>
                        <!-- Selector de usuario -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="seleccionarUsuario" class="form-label">Seleccione un usuario</label>
                                    <select id="seleccionarUsuario" class="form-select">
                                        <option value="">Seleccionar usuario</option>
                                        <?php foreach ($usuarios as $usuario): ?>
                                            <option value="<?= $usuario->getIdUsuario() ?>">
                                                <?= htmlspecialchars($usuario->getNombre()) ?>
                                                (<?= htmlspecialchars($usuario->getEmail()) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Seleccione un usuario para editar sus datos</div>
                                </div>
                            </div>
                        </div>

                        <!-- Mensaje de selección inicial -->
                        <div id="mensajeSeleccion" class="alert alert-info text-center">
                            <i class="fas fa-info-circle me-2"></i> Seleccione un usuario para editar
                        </div>

                        <!-- Formulario de edición oculto inicialmente -->
                        <div id="formEditarContainer" style="display: none;">
                            <form id="formEditarUsuario"
                                  action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/UsuarioController.php"
                                  method="POST" class="card-form">
                                <input type="hidden" name="action" value="editar">
                                <input type="hidden" id="editId" name="id" value="">

                                <div class="row">
                                    <div class="form-group mb-3">
                                        <label for="editNombre" class="form-label">Nombre completo <span
                                                    class="text-danger">*</span></label>
                                        <input type="text" id="editNombre" name="nombre" class="form-control"
                                               required>
                                    </div>
                                </div>

                                <div class="row">

                                    <div class="form-group mb-3">
                                        <label for="editEmail" class="form-label">Email <span
                                                    class="text-danger">*</span></label>
                                        <input type="email" id="editEmail" name="email" class="form-control"
                                               required>
                                    </div>


                                    <div class="form-group mb-3">
                                        <label for="editRol" class="form-label">Rol <span
                                                    class="text-danger">*</span></label>
                                        <select id="editRol" name="rol" class="form-select" required>
                                            <option value="">Seleccionar rol</option>
                                            <?php foreach (RolEnum::getValues() as $rolValue): ?>
                                                <option value="<?= $rolValue ?>"><?= $rolValue ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                </div>

                                <!-- Estado del usuario -->
                                <div class="row">

                                    <div class="form-group mb-3">
                                        <label class="form-label">Estado</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="editActivo"
                                                   name="activo" value="1">
                                            <label class="form-check-label" for="editActivo">
                                                <span id="estadoLabel">Activo</span>
                                            </label>
                                        </div>
                                    </div>

                                </div>

                                <div class="row">

                                    <div class="form-group mb-3">
                                        <label for="editContrasena" class="form-label">Contraseña</label>
                                        <div class="input-group">
                                            <input type="password" id="editContrasena" name="contrasena"
                                                   class="form-control">
                                        </div>
                                    </div>


                                    <div class="form-group mb-3">
                                        <label for="editConfirmarContrasena" class="form-label">Confirmar
                                            contraseña</label>
                                        <div class="input-group">
                                            <input type="password" id="editConfirmarContrasena"
                                                   name="confirmar_contrasena" class="form-control">

                                        </div>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle"></i> Dejar en blanco para mantener la
                                            contraseña actual.
                                        </small>
                                    </div>
                                </div>

                                <div class="form-group mt-4 text-center">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save"></i> Actualizar
                                    </button>
                                    <button type="button" id="cancelar_edicion_usuario" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i> No hay usuarios disponibles para editar.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.usuariosData = {};
    <?php foreach ($usuarios as $usuario): ?>
    window.usuariosData[<?= $usuario->getIdUsuario() ?>] = {
        id_usuario: <?= $usuario->getIdUsuario() ?>,
        nombre: <?= json_encode($usuario->getNombre()) ?>,
        email: <?= json_encode($usuario->getEmail()) ?>,
        rol: <?= json_encode($usuario->getRol()) ?>,
        activo: <?= $usuario->isActivo() ? '1' : '0' ?>
    };
    <?php endforeach; ?>

    document.addEventListener('DOMContentLoaded', function() {
        // Referencias a los formularios
        const formCrearUsuario = document.getElementById('formCrearUsuario');
        const formToggleBtns = document.querySelectorAll('.form-toggle-btn');

        // Botones de toggle entre crear y editar
        formToggleBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const target = this.getAttribute('data-target');
                
                formToggleBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                document.querySelectorAll('.form-section').forEach(form => {
                    form.style.display = 'none';
                });
                document.getElementById(target).style.display = 'block';
            });
        });

        // Validación básica del formulario
        if (formCrearUsuario) {
            formCrearUsuario.addEventListener('submit', function(e) {
                const contrasena = document.getElementById('contrasena').value;
                const confirmarContrasena = document.getElementById('confirmar_contrasena').value;
                
                // Verificar que las contraseñas coincidan
                if (contrasena !== confirmarContrasena) {
                    e.preventDefault();
                    alert('Las contraseñas no coinciden.');
                    return false;
                }
                
                // Validar complejidad de contraseña
                if (contrasena.length < 8) {
                    e.preventDefault();
                    alert('La contraseña debe tener al menos 8 caracteres.');
                    return false;
                }
                
                // Verificar que contenga mayúsculas, minúsculas, números y caracteres especiales
                if (!/[A-Z]/.test(contrasena) || !/[a-z]/.test(contrasena) || 
                    !/[0-9]/.test(contrasena) || !/[^A-Za-z0-9]/.test(contrasena)) {
                    e.preventDefault();
                    alert('La contraseña debe contener al menos una letra mayúscula, una minúscula, un número y un carácter especial.');
                    return false;
                }
                
                return true;
            });
        }
        
        // Código para el formulario de edición
        // ...existing code...
    });
</script>