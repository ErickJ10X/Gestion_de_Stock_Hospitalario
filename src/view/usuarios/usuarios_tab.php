<?php

use models\enum\RolEnum;

if (!isset($usuarios) || !isset($usuarioController) || !isset($session) || !isset($usuarioUbicacionController)) {
    die("Error: No se han proporcionado las variables requeridas.");
}
?>

<div class="usuarios-container">
    <div class="list-header__actions mb-4">
        <button class="list-button list-button--success usuario-card-open" data-target="usuario-card-create">
            <i class="fas fa-plus"></i> Nuevo Usuario
        </button>
    </div>

    <div class="card shadow">
        <div class="section-title bg-primary text-white">
            Lista de Usuarios
        </div>
        <div class="table-responsive">
            <table class="list-table" id="usuariosDataTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($usuarios as $usuario) {
                        $badgeClass = $usuario->getActivo() ? 'bg-success' : 'bg-danger';
                        $badgeText = $usuario->getActivo() ? 'Activo' : 'Inactivo';
                        $rolBadgeClass = getBadgeColorForRole($usuario->getRol());
                        ?>
                        <tr class="list-table__body-row">
                            <td class="list-table__body-cell"><?= $usuario->getIdUsuario() ?></td>
                            <td class="list-table__body-cell"><?= htmlspecialchars($usuario->getNombre()) ?></td>
                            <td class="list-table__body-cell"><?= htmlspecialchars($usuario->getEmail()) ?></td>
                            <td class="list-table__body-cell">
                                <span class="badge bg-<?= $rolBadgeClass ?>"><?= htmlspecialchars($usuario->getRol()) ?></span>
                            </td>
                            <td class="list-table__body-cell">
                                <span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span>
                            </td>
                            <td class="list-table__body-cell">
                                <div class="list-table__actions">
                                    <button class="list-table__button list-table__button--info usuario-card-open" 
                                            data-target="ubicaciones-card-<?= $usuario->getIdUsuario() ?>" 
                                            title="Ver Ubicaciones">
                                        <i class="fas fa-map-marker-alt list-table__button-icon"></i> Ver
                                    </button>
                                    <button class="list-table__button list-table__button--edit usuario-card-open" 
                                            data-target="usuario-card-edit-<?= $usuario->getIdUsuario() ?>" 
                                            title="Editar">
                                        <i class="fas fa-edit list-table__button-icon"></i> Editar
                                    </button>
                                    <button class="list-table__button list-table__button--delete usuario-card-open" 
                                            data-target="usuario-card-delete-<?= $usuario->getIdUsuario() ?>" 
                                            title="Eliminar">
                                        <i class="fas fa-trash list-table__button-icon"></i> Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Ventana modal para crear usuario -->
<div id="usuario-card-create" class="usuario-card">
    <div class="usuario-card__header usuario-card__header--create">
        <h3 class="usuario-card__title">Nuevo Usuario</h3>
        <button type="button" class="usuario-card__close">&times;</button>
    </div>
    <div class="usuario-card__body">
        <?php if ($session->hasMessage('modal_error')): ?>
            <div class="usuario-form__error">
                <p><?= $session->getMessage('modal_error') ?></p>
            </div>
            <?php $session->clearMessage('modal_error'); ?>
        <?php endif; ?>
        <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/usuarios/usuario-actions.php"
              method="post" class="usuario-form" id="form-crear-usuario">
            <input type="hidden" name="action" value="crear">
            <div class="usuario-form__group">
                <label for="nombre-create" class="usuario-form__label">Nombre:</label>
                <input type="text" id="nombre-create" name="nombre" class="usuario-form__input" required>
            </div>
            <div class="usuario-form__group">
                <label for="email-create" class="usuario-form__label">Email:</label>
                <input type="email" id="email-create" name="email" class="usuario-form__input" required>
            </div>
            <div class="usuario-form__group">
                <label for="contrasena-create" class="usuario-form__label">Contraseña:</label>
                <input type="password" id="contrasena-create" name="contrasena" class="usuario-form__input" required>
            </div>
            <div class="usuario-form__group">
                <label for="confirmar_contrasena-create" class="usuario-form__label">Confirmar Contraseña:</label>
                <input type="password" id="confirmar_contrasena-create" name="confirmar_contrasena"
                       class="usuario-form__input" required>
            </div>
            <div class="usuario-form__group">
                <label for="rol-create" class="usuario-form__label">Rol:</label>
                <select id="rol-create" name="rol" class="usuario-form__select" required>
                    <?php foreach (RolEnum::getValues() as $rol): ?>
                        <option value="<?= htmlspecialchars($rol) ?>"><?= htmlspecialchars($rol) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="usuario-card__footer">
                <button type="button"
                        class="usuario-form__button usuario-form__button--secondary usuario-form__button--cancel">
                    Cancelar
                </button>
                <button type="submit" class="usuario-form__button usuario-form__button--primary">Registrar Usuario
                </button>
            </div>
        </form>
    </div>
</div>

<?php foreach ($usuarios as $usuario): ?>
    <!-- Ventana modal para editar usuario -->
    <div id="usuario-card-edit-<?= $usuario->getIdUsuario() ?>" class="usuario-card">
        <div class="usuario-card__header usuario-card__header--edit">
            <h3 class="usuario-card__title">Editar Usuario</h3>
            <button type="button" class="usuario-card__close">&times;</button>
        </div>
        <div class="usuario-card__body">
            <?php if ($session->hasMessage('modal_error_edit_' . $usuario->getIdUsuario())): ?>
                <div class="usuario-form__error">
                    <p><?= $session->getMessage('modal_error_edit_' . $usuario->getIdUsuario()) ?></p>
                </div>
                <?php $session->clearMessage('modal_error_edit_' . $usuario->getIdUsuario()); ?>
            <?php endif; ?>
            <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/usuarios/usuario-actions.php"
                  method="post" class="usuario-form" id="form-editar-usuario-<?= $usuario->getIdUsuario() ?>">
                <input type="hidden" name="action" value="editar">
                <input type="hidden" name="id" value="<?= $usuario->getIdUsuario() ?>">
                <div class="usuario-form__group">
                    <label for="nombre-edit-<?= $usuario->getIdUsuario() ?>" class="usuario-form__label">Nombre:</label>
                    <input type="text" id="nombre-edit-<?= $usuario->getIdUsuario() ?>" name="nombre"
                           value="<?= htmlspecialchars($usuario->getNombre()) ?>" class="usuario-form__input" required>
                </div>
                <div class="usuario-form__group">
                    <label for="email-edit-<?= $usuario->getIdUsuario() ?>" class="usuario-form__label">Email:</label>
                    <input type="email" id="email-edit-<?= $usuario->getIdUsuario() ?>" name="email"
                           value="<?= htmlspecialchars($usuario->getEmail()) ?>" class="usuario-form__input" required>
                </div>
                <div class="usuario-form__group">
                    <label for="contrasena-edit-<?= $usuario->getIdUsuario() ?>" class="usuario-form__label">Contraseña
                        (dejar en blanco para mantener la actual):</label>
                    <input type="password" id="contrasena-edit-<?= $usuario->getIdUsuario() ?>" name="contrasena"
                           class="usuario-form__input">
                </div>
                <div class="usuario-form__group">
                    <label for="confirmar_contrasena-edit-<?= $usuario->getIdUsuario() ?>" class="usuario-form__label">Confirmar
                        Contraseña:</label>
                    <input type="password" id="confirmar_contrasena-edit-<?= $usuario->getIdUsuario() ?>"
                           name="confirmar_contrasena" class="usuario-form__input">
                </div>
                <div class="usuario-form__group">
                    <label for="rol-edit-<?= $usuario->getIdUsuario() ?>" class="usuario-form__label">Rol:</label>
                    <select id="rol-edit-<?= $usuario->getIdUsuario() ?>" name="rol" class="usuario-form__select"
                            required>
                        <?php foreach (RolEnum::getValues() as $rol): ?>
                            <option value="<?= htmlspecialchars($rol) ?>" <?= $rol === $usuario->getRol() ? 'selected' : '' ?>><?= htmlspecialchars($rol) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="usuario-form__group">
                    <label for="activo-edit-<?= $usuario->getIdUsuario() ?>" class="usuario-form__label">Estado:</label>
                    <select id="activo-edit-<?= $usuario->getIdUsuario() ?>" name="activo" class="usuario-form__select"
                            required>
                        <option value="1" <?= $usuario->getActivo() ? 'selected' : '' ?>>Activo</option>
                        <option value="0" <?= !$usuario->getActivo() ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
                <div class="usuario-card__footer">
                    <button type="button"
                            class="usuario-form__button usuario-form__button--secondary usuario-form__button--cancel">
                        Cancelar
                    </button>
                    <button type="submit" class="usuario-form__button usuario-form__button--primary">Actualizar
                        Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Ventana modal para eliminar usuario -->
    <div id="usuario-card-delete-<?= $usuario->getIdUsuario() ?>" class="usuario-card">
        <div class="usuario-card__header usuario-card__header--delete">
            <h3 class="usuario-card__title">Eliminar Usuario</h3>
            <button type="button" class="usuario-card__close">&times;</button>
        </div>
        <div class="usuario-card__body">
            <h4>¿Estás seguro de que deseas eliminar al usuario "<?= htmlspecialchars($usuario->getNombre()) ?>"?</h4>
            <p class="text-danger">Esta acción no se puede deshacer.</p>
            <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/usuarios/usuario-actions.php"
                  method="post">
                <input type="hidden" name="action" value="eliminar">
                <input type="hidden" name="id" value="<?= $usuario->getIdUsuario() ?>">
                <div class="usuario-card__footer">
                    <button type="button"
                            class="usuario-form__button usuario-form__button--secondary usuario-form__button--cancel">
                        Cancelar
                    </button>
                    <button type="submit" class="usuario-form__button usuario-form__button--danger">Confirmar
                        Eliminación
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Ventana modal para ver ubicaciones -->
    <div id="ubicaciones-card-<?= $usuario->getIdUsuario() ?>" class="usuario-card">
        <div class="usuario-card__header usuario-card__header--view">
            <h3 class="usuario-card__title">Ubicaciones de <?= htmlspecialchars($usuario->getNombre()) ?></h3>
            <button type="button" class="usuario-card__close">&times;</button>
        </div>
        <div class="usuario-card__body">
            <?php
            $ubicacionesUsuario = $usuarioUbicacionController->getUbicacionesByUsuario($usuario->getIdUsuario());
            $ubicacionesData = $ubicacionesUsuario['success'] ? $ubicacionesUsuario['data'] : [];
            
            if (empty($ubicacionesData)):
                ?>
                <div class="alert alert-warning">
                    Este usuario no tiene ubicaciones asignadas.
                </div>
            <?php else: ?>
                <ul class="ubicaciones-list">
                    <?php foreach ($ubicacionesData as $ubicacion): ?>
                        <li class="ubicaciones-list__item">
                            <span class="ubicaciones-list__badge badge-<?= $ubicacion->getTipoUbicacion() ?>">
                                <?= htmlspecialchars(getTipoUbicacion($ubicacion->getTipoUbicacion())) ?>
                            </span>
                            <span class="ubicaciones-list__name">ID: <?= $ubicacion->getIdUbicacion() ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <div class="usuario-card__footer">
                <button type="button"
                        class="usuario-form__button usuario-form__button--secondary usuario-form__button--cancel">Cerrar
                </button>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Inicializando script en usuarios_tab.php...');
    
    // Inicializar DataTable si está disponible
    if (typeof $.fn.DataTable !== 'undefined') {
        try {
            $('#usuariosDataTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
                },
                responsive: true,
                order: [[0, 'asc']]
            });
            console.log('DataTable inicializado correctamente');
        } catch (error) {
            console.error('Error al inicializar DataTable:', error);
        }
    } else {
        console.warn('DataTable no está disponible');
    }
});
</script>
