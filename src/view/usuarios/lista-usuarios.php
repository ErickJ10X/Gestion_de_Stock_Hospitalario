<?php
session_start();
require_once(__DIR__ . '/../../controller/UsuarioController.php');
require_once(__DIR__ . '/../../model/enum/RolEnum.php');
include_once(__DIR__ . '/../../util/Session.php');
include_once(__DIR__ . '/../../util/AuthGuard.php');

use controller\UsuarioController;
use model\enum\RolEnum;
use util\Session;
use util\AuthGuard;

$usuarioController = new UsuarioController();
$session = new Session();
$authGuard = new AuthGuard();

$authGuard->requireAdministrador();

$usuarios = $usuarioController->getAllUsers();

$pageTitle = "Usuarios";
include_once(__DIR__ . '/../templates/header.php');
?>

<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/list.css">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/card-form.css">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/tabs.css">

<div class="list-container">
    <div class="list-header">
        <div class="list-header__actions">
            <button id="btn-add-usuario" class="list-button list-button--success">
                <i class="bi bi-person-plus"></i> Nuevo Usuario
            </button>
        </div>
    </div>
    
    <?php if ($session->hasMessage('success')): ?>
        <div class="list-alert list-alert--success">
            <p class="list-alert__message"><?= $session->getMessage('success') ?></p>
            <button type="button" class="list-alert__close">&times;</button>
        </div>
        <?php $session->clearMessage('success'); ?>
    <?php endif; ?>
    
    <?php if ($session->hasMessage('error')): ?>
        <div class="list-alert list-alert--error">
            <p class="list-alert__message"><?= $session->getMessage('error') ?></p>
            <button type="button" class="list-alert__close">&times;</button>
        </div>
        <?php $session->clearMessage('error'); ?>
    <?php endif; ?>

    <div class="tabs-container">
        <div class="tabs-nav">
            <button class="tab-btn active" data-tab="tab-lista">Lista</button>
            <button class="tab-btn" data-tab="tab-ubicaciones">Ubicaciones</button>
            <button class="tab-btn" data-tab="tab-roles">Roles</button>
        </div>
        
        <div class="tab-content">
            <!-- Pestaña Lista -->
            <div id="tab-lista" class="tab-pane active">
                <div class="table-responsive">
                    <table class="list-table">
                        <tbody>
                            <?php if (empty($usuarios)): ?>
                                <tr>
                                    <td colspan="7" class="list-table__empty">No hay usuarios registrados</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($usuarios as $index => $usuario): ?>
                                    <tr class="list-table__body-row">
                                        <td class="list-table__body-cell" data-label="Nombre"><?= htmlspecialchars($usuario->getNombre()) ?></td>
                                        <td class="list-table__body-cell" data-label="Email"><?= htmlspecialchars($usuario->getEmail()) ?></td>
                                        <td class="list-table__body-cell" data-label="Rol">
                                            <span class="badge bg-<?= getBadgeColorForRole($usuario->getRol()) ?>">
                                                <?= htmlspecialchars($usuario->getRol()) ?>
                                            </span>
                                        </td>
                                        <td class="list-table__body-cell" data-label="Estado">
                                            <span class="badge <?= $usuario->getActivo() ? 'bg-success' : 'bg-danger' ?>">
                                                <?= $usuario->getActivo() ? 'Activo' : 'Inactivo' ?>
                                            </span>
                                        </td>
                                        <td class="list-table__body-cell" data-label="Ubicaciones">
                                            <?php 
                                            $ubicaciones = $usuarioController->getUbicacionesUsuario($usuario->getIdUsuario());
                                            if (empty($ubicaciones)): 
                                            ?>
                                                <span class="badge bg-warning">Sin ubicaciones</span>
                                            <?php else: ?>
                                                <button class="list-table__button list-table__button--view btn-view-ubicaciones" data-id="<?= $usuario->getIdUsuario() ?>">
                                                    <i class="bi bi-geo-alt list-table__button-icon"></i> Ver (<?= count($ubicaciones) ?>)
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                        <td class="list-table__body-cell" data-label="Acciones">
                                            <div class="list-table__actions">
                                                <button class="list-table__button list-table__button--edit btn-edit-usuario" data-id="<?= $usuario->getIdUsuario() ?>">
                                                    <i class="bi bi-pencil-square list-table__button-icon"></i> Editar
                                                </button>
                                                <button class="list-table__button list-table__button--view btn-asign-ubicacion" data-id="<?= $usuario->getIdUsuario() ?>">
                                                    <i class="bi bi-geo-alt list-table__button-icon"></i> Ubicaciones
                                                </button>
                                                <button class="list-table__button list-table__button--delete btn-delete-usuario" data-id="<?= $usuario->getIdUsuario() ?>">
                                                    <i class="bi bi-trash list-table__button-icon"></i> Eliminar
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Pestaña Ubicaciones -->
            <div id="tab-ubicaciones" class="tab-pane">
                <div class="ubicaciones-grid">
                    <?php
                    // Agrupar usuarios por ubicación
                    $ubicacionesData = [];
                    foreach ($usuarios as $usuario) {
                        $userUbicaciones = $usuarioController->getUbicacionesUsuario($usuario->getIdUsuario());
                        foreach ($userUbicaciones as $ubicacion) {
                            if (!isset($ubicacionesData[$ubicacion['tipo']][$ubicacion['id']])) {
                                $ubicacionesData[$ubicacion['tipo']][$ubicacion['id']] = [
                                    'nombre' => $ubicacion['nombre'],
                                    'usuarios' => []
                                ];
                            }
                            $ubicacionesData[$ubicacion['tipo']][$ubicacion['id']]['usuarios'][] = [
                                'id' => $usuario->getIdUsuario(),
                                'nombre' => $usuario->getNombre(),
                                'email' => $usuario->getEmail()
                            ];
                        }
                    }

                    $tiposUbicacion = ['hospital', 'planta', 'botiquin'];
                    
                    foreach ($tiposUbicacion as $tipo):
                        if (isset($ubicacionesData[$tipo])):
                    ?>
                        <div class="ubicacion-section">
                            <h3 class="ubicacion-section-title"><?= ucfirst(getTipoUbicacion($tipo)) ?>s</h3>
                            <div class="ubicacion-cards">
                                <?php foreach ($ubicacionesData[$tipo] as $id => $ubicacion): ?>
                                    <div class="ubicacion-card">
                                        <div class="ubicacion-card-header ubicacion-card-header--<?= $tipo ?>">
                                            <?= htmlspecialchars($ubicacion['nombre']) ?>
                                        </div>
                                        <div class="ubicacion-card-body">
                                            <div class="ubicacion-card-count">
                                                <span class="count-number"><?= count($ubicacion['usuarios']) ?></span>
                                                <span class="count-label">Usuarios</span>
                                            </div>
                                            <div class="ubicacion-card-users">
                                                <?php foreach ($ubicacion['usuarios'] as $i => $user): ?>
                                                    <?php if ($i < 3): ?>
                                                        <div class="user-item">
                                                            <i class="bi bi-person-circle"></i>
                                                            <span><?= htmlspecialchars($user['nombre']) ?></span>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                                <?php if (count($ubicacion['usuarios']) > 3): ?>
                                                    <div class="user-item user-item--more">
                                                        <span>+<?= count($ubicacion['usuarios']) - 3 ?> más</span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php 
                        endif;
                    endforeach; 
                    
                    if (empty($ubicacionesData)):
                    ?>
                        <div class="empty-message">No hay ubicaciones asignadas a usuarios</div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Pestaña Roles -->
            <div id="tab-roles" class="tab-pane">
                <div class="roles-grid">
                    <?php
                    $roleUsers = [];
                    foreach (RolEnum::getValues() as $rol) {
                        $roleUsers[$rol] = [];
                    }
                    
                    // Agrupar usuarios por rol
                    foreach ($usuarios as $usuario) {
                        $rol = $usuario->getRol();
                        if (isset($roleUsers[$rol])) {
                            $roleUsers[$rol][] = $usuario;
                        }
                    }
                    
                    foreach ($roleUsers as $rol => $users):
                    ?>
                        <div class="role-card">
                            <div class="role-card-header bg-<?= getBadgeColorForRole($rol) ?>">
                                <?= htmlspecialchars($rol) ?>
                                <span class="role-count"><?= count($users) ?></span>
                            </div>
                            <div class="role-card-body">
                                <?php if (empty($users)): ?>
                                    <div class="role-empty">No hay usuarios con este rol</div>
                                <?php else: ?>
                                    <ul class="role-users-list">
                                        <?php foreach ($users as $usuario): ?>
                                            <li class="role-user-item">
                                                <div class="role-user-name">
                                                    <i class="bi bi-person"></i>
                                                    <?= htmlspecialchars($usuario->getNombre()) ?>
                                                </div>
                                                <div class="role-user-email"><?= htmlspecialchars($usuario->getEmail()) ?></div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Overlay para ventanas modales -->
<div class="usuario-overlay"></div>

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
        <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/usuarios/usuario-actions.php" method="post" class="usuario-form" id="form-crear-usuario">
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
                <input type="password" id="confirmar_contrasena-create" name="confirmar_contrasena" class="usuario-form__input" required>
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
                <button type="button" class="usuario-form__button usuario-form__button--secondary usuario-form__button--cancel">Cancelar</button>
                <button type="submit" class="usuario-form__button usuario-form__button--primary">Registrar Usuario</button>
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
            <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/usuarios/usuario-actions.php" method="post" class="usuario-form" id="form-editar-usuario-<?= $usuario->getIdUsuario() ?>">
                <input type="hidden" name="action" value="editar">
                <input type="hidden" name="id" value="<?= $usuario->getIdUsuario() ?>">
                <div class="usuario-form__group">
                    <label for="nombre-edit-<?= $usuario->getIdUsuario() ?>" class="usuario-form__label">Nombre:</label>
                    <input type="text" id="nombre-edit-<?= $usuario->getIdUsuario() ?>" name="nombre" value="<?= htmlspecialchars($usuario->getNombre()) ?>" class="usuario-form__input" required>
                </div>
                <div class="usuario-form__group">
                    <label for="email-edit-<?= $usuario->getIdUsuario() ?>" class="usuario-form__label">Email:</label>
                    <input type="email" id="email-edit-<?= $usuario->getIdUsuario() ?>" name="email" value="<?= htmlspecialchars($usuario->getEmail()) ?>" class="usuario-form__input" required>
                </div>
                <div class="usuario-form__group">
                    <label for="contrasena-edit-<?= $usuario->getIdUsuario() ?>" class="usuario-form__label">Contraseña (dejar en blanco para mantener la actual):</label>
                    <input type="password" id="contrasena-edit-<?= $usuario->getIdUsuario() ?>" name="contrasena" class="usuario-form__input">
                </div>
                <div class="usuario-form__group">
                    <label for="confirmar_contrasena-edit-<?= $usuario->getIdUsuario() ?>" class="usuario-form__label">Confirmar Contraseña:</label>
                    <input type="password" id="confirmar_contrasena-edit-<?= $usuario->getIdUsuario() ?>" name="confirmar_contrasena" class="usuario-form__input">
                </div>
                <div class="usuario-form__group">
                    <label for="rol-edit-<?= $usuario->getIdUsuario() ?>" class="usuario-form__label">Rol:</label>
                    <select id="rol-edit-<?= $usuario->getIdUsuario() ?>" name="rol" class="usuario-form__select" required>
                        <?php foreach (RolEnum::getValues() as $rol): ?>
                            <option value="<?= htmlspecialchars($rol) ?>" <?= $rol === $usuario->getRol() ? 'selected' : '' ?>><?= htmlspecialchars($rol) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="usuario-form__group">
                    <label for="activo-edit-<?= $usuario->getIdUsuario() ?>" class="usuario-form__label">Estado:</label>
                    <select id="activo-edit-<?= $usuario->getIdUsuario() ?>" name="activo" class="usuario-form__select" required>
                        <option value="1" <?= $usuario->getActivo() ? 'selected' : '' ?>>Activo</option>
                        <option value="0" <?= !$usuario->getActivo() ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
                <div class="usuario-card__footer">
                    <button type="button" class="usuario-form__button usuario-form__button--secondary usuario-form__button--cancel">Cancelar</button>
                    <button type="submit" class="usuario-form__button usuario-form__button--primary">Actualizar Usuario</button>
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
            <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/usuarios/usuario-actions.php" method="post">
                <input type="hidden" name="action" value="eliminar">
                <input type="hidden" name="id" value="<?= $usuario->getIdUsuario() ?>">
                <div class="usuario-card__footer">
                    <button type="button" class="usuario-form__button usuario-form__button--secondary usuario-form__button--cancel">Cancelar</button>
                    <button type="submit" class="usuario-form__button usuario-form__button--danger">Confirmar Eliminación</button>
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
            $ubicaciones = $usuarioController->getUbicacionesUsuario($usuario->getIdUsuario());
            if (empty($ubicaciones)): 
            ?>
                <div class="alert alert--warning">
                    Este usuario no tiene ubicaciones asignadas.
                </div>
            <?php else: ?>
                <ul class="ubicaciones-list">
                    <?php foreach ($ubicaciones as $ubicacion): ?>
                        <li class="ubicaciones-list__item">
                            <span class="ubicaciones-list__name"><?= htmlspecialchars($ubicacion['nombre']) ?></span>
                            <span class="ubicaciones-list__badge badge-<?= $ubicacion['tipo'] ?>">
                                <?= htmlspecialchars(getTipoUbicacion($ubicacion['tipo'])) ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <div class="usuario-card__footer">
                <button type="button" class="usuario-form__button usuario-form__button--secondary usuario-form__button--cancel">Cerrar</button>
                <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/usuarios/asignar-ubicaciones.php?id=<?= $usuario->getIdUsuario() ?>" class="usuario-form__button usuario-form__button--primary">Gestionar Ubicaciones</a>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php
function getBadgeColorForRole($role) {
    switch ($role) {
        case RolEnum::ADMINISTRADOR:
            return 'danger';
        case RolEnum::GESTOR_GENERAL:
            return 'primary';
        case RolEnum::GESTOR_HOSPITAL:
            return 'success';
        case RolEnum::GESTOR_PLANTA:
            return 'info';
        case RolEnum::USUARIO_BOTIQUIN:
            return 'secondary';
        default:
            return 'dark';
    }
}

function getTipoUbicacion($tipo) {
    switch ($tipo) {
        case 'hospital':
            return 'Hospital';
        case 'planta':
            return 'Planta';
        case 'botiquin':
            return 'Botiquín';
        default:
            return ucfirst($tipo);
    }
}
?>

<script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/usuario-cards.js"></script>
<script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/usuario-tabs.js"></script>

<?php include_once(__DIR__ . '/../templates/footer.php'); ?>
