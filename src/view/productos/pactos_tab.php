<?php
if (!isset($pactos) || !isset($session) || !isset($productos) || !isset($catalogos)) {
    die("Error: No se han proporcionado las variables requeridas.");
}
?>

<div class="list-header__actions">
    <button id="btn-add-pacto" class="list-button list-button--success">
        <i class="bi bi-plus-circle"></i> Nuevo
    </button>
</div>

<div class="table-responsive">
    <table class="list-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Productos</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pactos)): ?>
                <tr>
                    <td colspan="5" class="list-table__empty">No hay pactos registrados</td>
                </tr>
            <?php else: ?>
                <?php foreach ($pactos as $pacto): ?>
                    <tr class="list-table__body-row">
                        <td class="list-table__body-cell" data-label="ID"><?= htmlspecialchars($pacto->getIdPacto()) ?></td>
                        <td class="list-table__body-cell" data-label="Nombre"><?= htmlspecialchars($pacto->getNombre()) ?></td>
                        <td class="list-table__body-cell" data-label="Descripción"><?= htmlspecialchars($pacto->getDescripcion()) ?></td>
                        <td class="list-table__body-cell" data-label="Productos">
                            <span class="badge bg-info">
                                <?php 
                                $productosPacto = $pacto->getProductos() ?? [];
                                echo count($productosPacto) . " productos";
                                ?>
                            </span>
                        </td>
                        <td class="list-table__body-cell" data-label="Acciones">
                            <div class="list-table__actions">
                                <button class="list-table__button list-table__button--edit btn-edit-pacto" data-id="<?= $pacto->getIdPacto() ?>">
                                    <i class="bi bi-pencil-square list-table__button-icon"></i> Editar
                                </button>
                                <button class="list-table__button list-table__button--delete btn-delete-pacto" data-id="<?= $pacto->getIdPacto() ?>">
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

<!-- Formulario para crear pacto -->
<div id="pacto-card-create" class="hospital-card">
    <div class="hospital-card__header hospital-card__header--create">
        <h3 class="hospital-card__title">Nuevo Pacto</h3>
        <button type="button" class="hospital-card__close">&times;</button>
    </div>
    <div class="hospital-card__body">
        <?php if ($session->hasMessage('modal_error_pacto')): ?>
            <div class="hospital-form__error">
                <p><?= $session->getMessage('modal_error_pacto') ?></p>
            </div>
            <?php $session->clearMessage('modal_error_pacto'); ?>
        <?php endif; ?>
        <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/productos/pactos-actions.php" method="post" class="hospital-form" id="form-crear-pacto">
            <input type="hidden" name="action" value="crear_pacto">
            <div class="hospital-form__group">
                <label for="nombre-pacto-create" class="hospital-form__label">Nombre:</label>
                <input type="text" id="nombre-pacto-create" name="nombre" class="hospital-form__input" required>
            </div>
            <div class="hospital-form__group">
                <label for="descripcion-pacto-create" class="hospital-form__label">Descripción:</label>
                <textarea id="descripcion-pacto-create" name="descripcion" class="hospital-form__input" rows="3"></textarea>
            </div>
            <div class="hospital-form__group">
                <label class="hospital-form__label">Productos:</label>
                <div class="hospital-form__checkbox-group">
                    <?php foreach ($productos as $producto): ?>
                        <div class="hospital-form__checkbox">
                            <input type="checkbox" id="producto-<?= $producto->getIdProducto() ?>-create" name="productos[]" value="<?= $producto->getIdProducto() ?>" class="hospital-form__checkbox-input">
                            <label for="producto-<?= $producto->getIdProducto() ?>-create" class="hospital-form__checkbox-label">
                                <?= htmlspecialchars($producto->getNombre()) ?> (<?= htmlspecialchars($producto->getCodigo()) ?>)
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="hospital-card__footer">
                <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel" id="btn-cancel-pacto">Cancelar</button>
                <button type="submit" class="hospital-form__button hospital-form__button--primary" id="btn-submit-pacto">Registrar Pacto</button>
            </div>
        </form>
    </div>
</div>

<!-- Formularios para editar y eliminar pactos -->
<?php foreach ($pactos as $pacto): ?>
    <div id="pacto-card-edit-<?= $pacto->getIdPacto() ?>" class="hospital-card">
        <div class="hospital-card__header hospital-card__header--edit">
            <h3 class="hospital-card__title">Editar Pacto</h3>
            <button type="button" class="hospital-card__close">&times;</button>
        </div>
        <div class="hospital-card__body">
            <?php if ($session->hasMessage('modal_error_pacto_' . $pacto->getIdPacto())): ?>
                <div class="hospital-form__error">
                    <p><?= $session->getMessage('modal_error_pacto_' . $pacto->getIdPacto()) ?></p>
                </div>
                <?php $session->clearMessage('modal_error_pacto_' . $pacto->getIdPacto()); ?>
            <?php endif; ?>
            <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/productos/pactos-actions.php" method="post" class="hospital-form" id="form-editar-pacto-<?= $pacto->getIdPacto() ?>">
                <input type="hidden" name="action" value="editar_pacto">
                <input type="hidden" name="id" value="<?= $pacto->getIdPacto() ?>">
                <div class="hospital-form__group">
                    <label for="nombre-pacto-edit-<?= $pacto->getIdPacto() ?>" class="hospital-form__label">Nombre:</label>
                    <input type="text" id="nombre-pacto-edit-<?= $pacto->getIdPacto() ?>" name="nombre" value="<?= htmlspecialchars($pacto->getNombre()) ?>" class="hospital-form__input" required>
                </div>
                <div class="hospital-form__group">
                    <label for="descripcion-pacto-edit-<?= $pacto->getIdPacto() ?>" class="hospital-form__label">Descripción:</label>
                    <textarea id="descripcion-pacto-edit-<?= $pacto->getIdPacto() ?>" name="descripcion" class="hospital-form__input" rows="3"><?= htmlspecialchars($pacto->getDescripcion()) ?></textarea>
                </div>
                <div class="hospital-form__group">
                    <label class="hospital-form__label">Productos:</label>
                    <div class="hospital-form__checkbox-group">
                        <?php 
                        $productosPacto = $pacto->getProductos() ?? [];
                        $productoIds = array_map(function($p) { 
                            return $p->getIdProducto(); 
                        }, $productosPacto);
                        ?>
                        <?php foreach ($productos as $producto): ?>
                            <div class="hospital-form__checkbox">
                                <input type="checkbox" 
                                       id="producto-<?= $producto->getIdProducto() ?>-edit-<?= $pacto->getIdPacto() ?>" 
                                       name="productos[]" 
                                       value="<?= $producto->getIdProducto() ?>" 
                                       class="hospital-form__checkbox-input"
                                       <?= in_array($producto->getIdProducto(), $productoIds) ? 'checked' : '' ?>>
                                <label for="producto-<?= $producto->getIdProducto() ?>-edit-<?= $pacto->getIdPacto() ?>" class="hospital-form__checkbox-label">
                                    <?= htmlspecialchars($producto->getNombre()) ?> (<?= htmlspecialchars($producto->getCodigo()) ?>)
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="hospital-card__footer">
                    <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel" id="btn-cancel-edit-pacto-<?= $pacto->getIdPacto() ?>">Cancelar</button>
                    <button type="submit" class="hospital-form__button hospital-form__button--primary" id="btn-submit-edit-pacto-<?= $pacto->getIdPacto() ?>">Actualizar Pacto</button>
                </div>
            </form>
        </div>
    </div>
    
    <div id="pacto-card-delete-<?= $pacto->getIdPacto() ?>" class="hospital-card">
        <div class="hospital-card__header hospital-card__header--delete">
            <h3 class="hospital-card__title">Eliminar Pacto</h3>
            <button type="button" class="hospital-card__close">&times;</button>
        </div>
        <div class="hospital-card__body">
            <h4>¿Estás seguro de que deseas eliminar este pacto?</h4>
            <p class="text-danger">Esta acción no se puede deshacer.</p>
            <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/productos/pactos-actions.php" method="post" id="form-eliminar-pacto-<?= $pacto->getIdPacto() ?>">
                <input type="hidden" name="action" value="eliminar_pacto">
                <input type="hidden" name="id" value="<?= $pacto->getIdPacto() ?>">
                <div class="hospital-card__footer">
                    <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel" id="btn-cancel-delete-pacto-<?= $pacto->getIdPacto() ?>">Cancelar</button>
                    <button type="submit" class="hospital-form__button hospital-form__button--danger" id="btn-submit-delete-pacto-<?= $pacto->getIdPacto() ?>">Confirmar Eliminación</button>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>
