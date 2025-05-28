<?php
if (!isset($hospitales) || !isset($plantaController) || !isset($plantas) || !isset($session)) {
    die("Error: No se han proporcionado las variables requeridas.");
}
?>

<div class="table-responsive">
    <table class="list-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Hospital</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($plantas)): ?>
                <tr>
                    <td colspan="4" class="list-table__empty">No hay plantas registradas</td>
                </tr>
            <?php else: ?>
                <?php foreach ($plantas as $planta):
                    $hospitalPlanta = null;
                    foreach ($hospitales as $h) {
                        if ($h->getIdHospital() == $planta->getIdHospital()) {
                            $hospitalPlanta = $h;
                            break;
                        }
                    }
                ?>
                    <tr class="list-table__body-row">
                        <td class="list-table__body-cell" data-label="ID"><?= htmlspecialchars($planta->getIdPlanta()) ?></td>
                        <td class="list-table__body-cell" data-label="Nombre"><?= htmlspecialchars($planta->getNombre()) ?></td>
                        <td class="list-table__body-cell" data-label="Hospital">
                            <?= $hospitalPlanta ? htmlspecialchars($hospitalPlanta->getNombre()) : 'N/A' ?>
                        </td>
                        <td class="list-table__body-cell" data-label="Acciones">
                            <div class="list-table__actions">
                                <button class="list-table__button list-table__button--edit btn-edit-planta" data-id="<?= $planta->getIdPlanta() ?>">
                                    <i class="bi bi-pencil-square list-table__button-icon"></i> Editar
                                </button>
                                <button class="list-table__button list-table__button--delete btn-delete-planta" data-id="<?= $planta->getIdPlanta() ?>">
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

<div id="planta-card-create" class="hospital-card">
    <div class="hospital-card__header hospital-card__header--create">
        <h3 class="hospital-card__title">Nueva Planta</h3>
        <button type="button" class="hospital-card__close">&times;</button>
    </div>
    <div class="hospital-card__body">
        <?php if ($session->hasMessage('modal_error_planta')): ?>
            <div class="hospital-form__error">
                <p><?= $session->getMessage('modal_error_planta') ?></p>
            </div>
            <?php $session->clearMessage('modal_error_planta'); ?>
        <?php endif; ?>
        <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/planta-actions.php" method="post" class="hospital-form" id="form-crear-planta">
            <input type="hidden" name="action" value="crear_planta">
            <div class="hospital-form__group">
                <label for="id-hospital-planta-create" class="hospital-form__label">Hospital:</label>
                <select id="id-hospital-planta-create" name="id_hospital" class="hospital-form__select" required>
                    <option value="">Seleccione un hospital</option>
                    <?php foreach ($hospitales as $hospital): ?>
                        <option value="<?= $hospital->getIdHospital() ?>"><?= htmlspecialchars($hospital->getNombre()) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="hospital-form__group">
                <label for="nombre-planta-create" class="hospital-form__label">Nombre:</label>
                <input type="text" id="nombre-planta-create" name="nombre" class="hospital-form__input" required>
            </div>
            <div class="hospital-card__footer">
                <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel" id="btn-cancel-planta">Cancelar</button>
                <button type="submit" class="hospital-form__button hospital-form__button--primary" id="btn-submit-planta">Registrar Planta</button>
            </div>
        </form>
    </div>
</div>

<?php foreach ($plantas as $planta): ?>
    <div id="planta-card-edit-<?= $planta->getIdPlanta() ?>" class="hospital-card">
        <div class="hospital-card__header hospital-card__header--edit">
            <h3 class="hospital-card__title">Editar Planta</h3>
            <button type="button" class="hospital-card__close">&times;</button>
        </div>
        <div class="hospital-card__body">
            <?php if ($session->hasMessage('modal_error_planta_' . $planta->getIdPlanta())): ?>
                <div class="hospital-form__error">
                    <p><?= $session->getMessage('modal_error_planta_' . $planta->getIdPlanta()) ?></p>
                </div>
                <?php $session->clearMessage('modal_error_planta_' . $planta->getIdPlanta()); ?>
            <?php endif; ?>
            <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/planta-actions.php" method="post" class="hospital-form" id="form-editar-planta-<?= $planta->getIdPlanta() ?>">
                <input type="hidden" name="action" value="editar_planta">
                <input type="hidden" name="id" value="<?= $planta->getIdPlanta() ?>">
                <div class="hospital-form__group">
                    <label for="id-hospital-planta-edit-<?= $planta->getIdPlanta() ?>" class="hospital-form__label">Hospital:</label>
                    <select id="id-hospital-planta-edit-<?= $planta->getIdPlanta() ?>" name="id_hospital" class="hospital-form__select" required>
                        <option value="">Seleccione un hospital</option>
                        <?php foreach ($hospitales as $hospital): ?>
                            <option value="<?= $hospital->getIdHospital() ?>" <?= $hospital->getIdHospital() == $planta->getIdHospital() ? 'selected' : '' ?>>
                                <?= htmlspecialchars($hospital->getNombre()) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="hospital-form__group">
                    <label for="nombre-planta-edit-<?= $planta->getIdPlanta() ?>" class="hospital-form__label">Nombre:</label>
                    <input type="text" id="nombre-planta-edit-<?= $planta->getIdPlanta() ?>" name="nombre" value="<?= htmlspecialchars($planta->getNombre()) ?>" class="hospital-form__input" required>
                </div>
                <div class="hospital-card__footer">
                    <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel" id="btn-cancel-edit-planta-<?= $planta->getIdPlanta() ?>">Cancelar</button>
                    <button type="submit" class="hospital-form__button hospital-form__button--primary" id="btn-submit-edit-planta-<?= $planta->getIdPlanta() ?>">Actualizar Planta</button>
                </div>
            </form>
        </div>
    </div>

    <div id="planta-card-delete-<?= $planta->getIdPlanta() ?>" class="hospital-card">
        <div class="hospital-card__header hospital-card__header--delete">
            <h3 class="hospital-card__title">Eliminar Planta</h3>
            <button type="button" class="hospital-card__close">&times;</button>
        </div>
        <div class="hospital-card__body">
            <h4>¿Estás seguro de que deseas eliminar la planta "<?= htmlspecialchars($planta->getNombre()) ?>"?</h4>
            <p class="text-danger">Esta acción eliminará también todos los botiquines asociados. No se puede deshacer.</p>
            <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/planta-actions.php" method="post" id="form-eliminar-planta-<?= $planta->getIdPlanta() ?>">
                <input type="hidden" name="action" value="eliminar_planta">
                <input type="hidden" name="id" value="<?= $planta->getIdPlanta() ?>">
                <div class="hospital-card__footer">
                    <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel" id="btn-cancel-delete-planta-<?= $planta->getIdPlanta() ?>">Cancelar</button>
                    <button type="submit" class="hospital-form__button hospital-form__button--danger" id="btn-submit-delete-planta-<?= $planta->getIdPlanta() ?>">Confirmar Eliminación</button>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>
