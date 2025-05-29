<?php
if (!isset($almacenes) || !isset($session)) {
    die("Error: No se han proporcionado las variables requeridas.");
}
?>

<div class="list-header__actions">
    <button id="btn-add-hospital" class="list-button list-button--success">
        <i class="bi bi-plus-circle"></i> Nuevo
    </button>
</div>

<div class="table-responsive">
    <table class="list-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tipo</th>
                <th>Planta</th>
                <th>Hospital</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($almacenes)): ?>
                <tr>
                    <td colspan="5" class="list-table__empty">No hay almacenes registrados</td>
                </tr>
            <?php else: ?>
                <?php foreach ($almacenes as $almacen): ?>
                    <tr class="list-table__body-row">
                        <td class="list-table__body-cell" data-label="ID"><?= htmlspecialchars($almacen->getIdAlmacen()) ?></td>
                        <td class="list-table__body-cell" data-label="Tipo"><?= htmlspecialchars($almacen->getTipo()) ?></td>
                        <td class="list-table__body-cell" data-label="Planta"><?= htmlspecialchars($almacen->getIdPlanta()) ?></td>
                        <td class="list-table__body-cell" data-label="Hospital"><?= htmlspecialchars($almacen->getIdHospital()) ?></td>
                        <td class="list-table__body-cell" data-label="Acciones">
                            <div class="list-table__actions">
                                <button class="list-table__button list-table__button--edit btn-edit-almacen" data-id="<?= $almacen->getIdAlmacen() ?>">
                                    <i class="bi bi-pencil-square list-table__button-icon"></i> Editar
                                </button>
                                <button class="list-table__button list-table__button--delete btn-delete-almacen" data-id="<?= $almacen->getIdAlmacen() ?>">
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

<div id="almacen-card-create" class="hospital-card">
    <div class="hospital-card__header hospital-card__header--create">
        <h3 class="hospital-card__title">Nuevo Almacén</h3>
        <button type="button" class="hospital-card__close">&times;</button>
    </div>
    <div class="hospital-card__body">
        <?php if ($session->hasMessage('modal_error_almacen')): ?>
            <div class="hospital-form__error">
                <p><?= $session->getMessage('modal_error_almacen') ?></p>
            </div>
            <?php $session->clearMessage('modal_error_almacen'); ?>
        <?php endif; ?>
        <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/almacen-actions.php" method="post" class="hospital-form" id="form-crear-almacen">
            <input type="hidden" name="action" value="crear_almacen">
            <div class="hospital-form__group">
                <label for="tipo-almacen-create" class="hospital-form__label">Tipo:</label>
                <input type="text" id="tipo-almacen-create" name="tipo" class="hospital-form__input" required>
            </div>
            <div class="hospital-form__group">
                <label for="planta-id-create" class="hospital-form__label">ID de Planta:</label>
                <input type="number" id="planta-id-create" name="planta_id" class="hospital-form__input" required>
            </div>
            <div class="hospital-form__group">
                <label for="hospital-id-create" class="hospital-form__label">ID de Hospital:</label>
                <input type="number" id="hospital-id-create" name="id_hospital" class="hospital-form__input" required>
            </div>
            <div class="hospital-card__footer">
                <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel" id="btn-cancel-almacen">Cancelar</button>
                <button type="submit" class="hospital-form__button hospital-form__button--primary" id="btn-submit-almacen">Registrar Almacén</button>
            </div>
        </form>
    </div>
</div>

<?php foreach ($almacenes as $almacen): ?>
    <div id="almacen-card-edit-<?= $almacen->getIdAlmacen() ?>" class="hospital-card">
        <div class="hospital-card__header hospital-card__header--edit">
            <h3 class="hospital-card__title">Editar Almacén</h3>
            <button type="button" class="hospital-card__close">&times;</button>
        </div>
        <div class="hospital-card__body">
            <?php if ($session->hasMessage('modal_error_almacen_' . $almacen->getIdAlmacen())): ?>
                <div class="hospital-form__error">
                    <p><?= $session->getMessage('modal_error_almacen_' . $almacen->getIdAlmacen()) ?></p>
                </div>
                <?php $session->clearMessage('modal_error_almacen_' . $almacen->getIdAlmacen()); ?>
            <?php endif; ?>
            <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/almacen-actions.php" method="post" class="hospital-form" id="form-editar-almacen-<?= $almacen->getIdAlmacen() ?>">
                <input type="hidden" name="action" value="editar_almacen">
                <input type="hidden" name="id" value="<?= $almacen->getIdAlmacen() ?>">
                <div class="hospital-form__group">
                    <label for="tipo-almacen-edit-<?= $almacen->getIdAlmacen() ?>" class="hospital-form__label">Tipo:</label>
                    <input type="text" id="tipo-almacen-edit-<?= $almacen->getIdAlmacen() ?>" name="tipo" value="<?= htmlspecialchars($almacen->getTipo()) ?>" class="hospital-form__input" required>
                </div>
                <div class="hospital-form__group">
                    <label for="planta-id-edit-<?= $almacen->getIdAlmacen() ?>" class="hospital-form__label">ID de Planta:</label>
                    <input type="number" id="planta-id-edit-<?= $almacen->getIdAlmacen() ?>" name="planta_id" value="<?= htmlspecialchars($almacen->getIdPlanta()) ?>" class="hospital-form__input" required>
                </div>
                <div class="hospital-form__group">
                    <label for="hospital-id-edit-<?= $almacen->getIdAlmacen() ?>" class="hospital-form__label">ID de Hospital:</label>
                    <input type="number" id="hospital-id-edit-<?= $almacen->getIdAlmacen() ?>" name="id_hospital" value="<?= htmlspecialchars($almacen->getIdHospital()) ?>" class="hospital-form__input" required>
                </div>
                <div class="hospital-card__footer">
                    <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel" id="btn-cancel-edit-almacen-<?= $almacen->getIdAlmacen() ?>">Cancelar</button>
                    <button type="submit" class="hospital-form__button hospital-form__button--primary" id="btn-submit-edit-almacen-<?= $almacen->getIdAlmacen() ?>">Actualizar Almacén</button>
                </div>
            </form>
        </div>
    </div>
    
    <div id="almacen-card-delete-<?= $almacen->getIdAlmacen() ?>" class="hospital-card">
        <div class="hospital-card__header hospital-card__header--delete">
            <h3 class="hospital-card__title">Eliminar Almacén</h3>
            <button type="button" class="hospital-card__close">&times;</button>
        </div>
        <div class="hospital-card__body">
            <h4>¿Estás seguro de que deseas eliminar este almacén?</h4>
            <p class="text-danger">Esta acción no se puede deshacer.</p>
            <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/almacen-actions.php" method="post" id="form-eliminar-almacen-<?= $almacen->getIdAlmacen() ?>">
                <input type="hidden" name="action" value="eliminar_almacen">
                <input type="hidden" name="id" value="<?= $almacen->getIdAlmacen() ?>">
                <div class="hospital-card__footer">
                    <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel" id="btn-cancel-delete-almacen-<?= $almacen->getIdAlmacen() ?>">Cancelar</button>
                    <button type="submit" class="hospital-form__button hospital-form__button--danger" id="btn-submit-delete-almacen-<?= $almacen->getIdAlmacen() ?>">Confirmar Eliminación</button>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>
