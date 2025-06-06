<?php

use model\enum\RolEnum;

// Verificar que tenemos los datos necesarios
if (!isset($usuarios) || !isset($roles)) {
    die("Error: No se han proporcionado las variables requeridas.");
}

// Determinar si estamos en modo edición
$modoEdicion = isset($usuario_editar) && $usuario_editar !== null;
?>

<div class="usuarios-container">
    <div class="form-toggle-buttons mb-4">
        <button type="button" class="btn form-toggle-btn <?= !$modoEdicion ? 'active' : '' ?>" data-target="crearForm">
            <i class="fas fa-user-plus"></i> Crear Usuario
        </button>
        <button type="button" class="btn form-toggle-btn <?= $modoEdicion ? 'active' : '' ?>" data-target="editarForm">
            <i class="fas fa-user-edit"></i> Editar Usuario
        </button>
    </div>

    <div class="row">
        <!-- Formulario para crear usuario -->
        <div class="col-md-12 form-section" id="crearForm" <?= $modoEdicion ? 'style="display: none;"' : '' ?>>
            <div class="card shadow mb-4">

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
                                <?php foreach ($roles as $rolValue): ?>
                                    <option value="<?= $rolValue ?>"><?= $rolValue ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                    </div>

                    <div class="row">

                        <div class="form-group mb-3">
                            <label for="contrasena" class="form-label">Contraseña <span
                                        class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" id="contrasena" name="contrasena" class="form-control"
                                       required>
                                <button class="btn btn-outline-secondary toggle-password" type="button">
                                    <i class="far fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="confirmar_contrasena" class="form-label">Confirmar contraseña <span
                                        class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" id="confirmar_contrasena" name="confirmar_contrasena"
                                       class="form-control" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button">
                                    <i class="far fa-eye"></i>
                                </button>
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

        <!-- Formulario para editar usuario -->
        <div class="col-md-12 form-section" id="editarForm" <?= !$modoEdicion ? 'style="display: none;"' : '' ?>>
            <div class="card shadow mb-4">
                <div class="card-body">
                    <?php if (count($usuarios) > 0): ?>
                        <!-- Selector de usuario - Mostrar solo si no estamos editando desde URL -->
                        <?php if (!$modoEdicion): ?>
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

                            <!-- Mensaje de selección inicial - Solo si no estamos editando desde URL -->
                            <div id="mensajeSeleccion" class="alert alert-info text-center">
                                <i class="fas fa-info-circle me-2"></i> Seleccione un usuario para editar
                            </div>
                        <?php endif; ?>

                        <!-- Formulario de edición -->
                        <div id="formEditarContainer" <?= !$modoEdicion ? 'style="display: none;"' : '' ?>>
                            <form id="formEditarUsuario"
                                  action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/UsuarioController.php"
                                  method="POST" class="card-form">
                                <input type="hidden" name="action" value="editar">
                                <input type="hidden" id="editId" name="id"
                                       value="<?= $modoEdicion ? $usuario_editar->getIdUsuario() : '' ?>">

                                <div class="row">

                                    <div class="form-group mb-3">
                                        <label for="editNombre" class="form-label">Nombre completo <span
                                                    class="text-danger">*</span></label>
                                        <input type="text" id="editNombre" name="nombre" class="form-control"
                                               value="<?= $modoEdicion ? htmlspecialchars($usuario_editar->getNombre()) : '' ?>"
                                               required>
                                    </div>

                                </div>

                                <div class="row">

                                    <div class="form-group mb-3">
                                        <label for="editEmail" class="form-label">Email <span
                                                    class="text-danger">*</span></label>
                                        <input type="email" id="editEmail" name="email" class="form-control"
                                               value="<?= $modoEdicion ? htmlspecialchars($usuario_editar->getEmail()) : '' ?>"
                                               required>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="editRol" class="form-label">Rol <span
                                                    class="text-danger">*</span></label>
                                        <select id="editRol" name="rol" class="form-select" required>
                                            <option value="">Seleccionar rol</option>
                                            <?php foreach ($roles as $rolValue): ?>
                                                <option value="<?= $rolValue ?>" <?= $modoEdicion && $usuario_editar->getRol() === $rolValue ? 'selected' : '' ?>>
                                                    <?= $rolValue ?>
                                                </option>
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
                                                   name="activo"
                                                   value="1" <?= $modoEdicion && $usuario_editar->isActivo() ? 'checked' : '' ?>>
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
                                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                                <i class="far fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="editConfirmarContrasena" class="form-label">Confirmar
                                            contraseña</label>
                                        <div class="input-group">
                                            <input type="password" id="editConfirmarContrasena"
                                                   name="confirmar_contrasena" class="form-control">
                                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                                <i class="far fa-eye"></i>
                                            </button>
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
                                    <a href="index.php" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
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

<!-- Inicialización de datos de usuarios para JS -->
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
</script>
<!-- Incluir el JS centralizado de usuarios -->
<script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/usuarios.js"></script>