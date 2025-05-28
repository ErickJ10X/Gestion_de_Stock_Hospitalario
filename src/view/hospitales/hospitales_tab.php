<?php
if (!isset($hospitales) || !isset($plantaController) || !isset($session)) {
    die("Error: No se han proporcionado las variables requeridas.");
}
?>

<div class="table-responsive">
    <table class="list-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Plantas</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($hospitales)): ?>
                <tr>
                    <td colspan="4" class="list-table__empty">No hay hospitales registrados</td>
                </tr>
            <?php else: ?>
                <?php foreach ($hospitales as $hospital): ?>
                    <tr class="list-table__body-row">
                        <td class="list-table__body-cell" data-label="ID"><?= htmlspecialchars($hospital->getIdHospital()) ?></td>
                        <td class="list-table__body-cell" data-label="Nombre"><?= htmlspecialchars($hospital->getNombre()) ?></td>
                        <td class="list-table__body-cell" data-label="Plantas">
                            <?php
                            $plantasHospital = $plantaController->getByHospital($hospital->getIdHospital())['plantas'] ?? [];
                            $cantidadPlantas = count($plantasHospital);
                            ?>
                            <span class="badge bg-info"><?= $cantidadPlantas ?> plantas</span>
                        </td>
                        <td class="list-table__body-cell" data-label="Acciones">
                            <div class="list-table__actions">
                                <button class="list-table__button list-table__button--edit btn-edit-hospital" data-id="<?= $hospital->getIdHospital() ?>">
                                    <i class="bi bi-pencil-square list-table__button-icon"></i> Editar
                                </button>
                                <button class="list-table__button list-table__button--delete btn-delete-hospital" data-id="<?= $hospital->getIdHospital() ?>">
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

<div class="hospital-overlay"></div>

<div id="hospital-card-create" class="hospital-card">
    <div class="hospital-card__header hospital-card__header--create">
        <h3 class="hospital-card__title">Nuevo Hospital</h3>
        <button type="button" class="hospital-card__close">&times;</button>
    </div>
    <div class="hospital-card__body">
        <?php if ($session->hasMessage('modal_error_hospital')): ?>
            <div class="hospital-form__error">
                <p><?= $session->getMessage('modal_error_hospital') ?></p>
            </div>
            <?php $session->clearMessage('modal_error_hospital'); ?>
        <?php endif; ?>
        <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/hospital-actions.php" method="post" class="hospital-form" id="form-crear-hospital">
            <input type="hidden" name="action" value="crear_hospital">
            <div class="hospital-form__group">
                <label for="nombre-hospital-create" class="hospital-form__label">Nombre:</label>
                <input type="text" id="nombre-hospital-create" name="nombre" class="hospital-form__input" required>
            </div>
            <div class="hospital-card__footer">
                <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel" id="btn-cancel-hospital">Cancelar</button>
                <button type="submit" class="hospital-form__button hospital-form__button--primary" id="btn-submit-hospital">Registrar Hospital</button>
            </div>
        </form>
    </div>
</div>

<?php foreach ($hospitales as $hospital): ?>
    <div id="hospital-card-edit-<?= $hospital->getIdHospital() ?>" class="hospital-card">
        <div class="hospital-card__header hospital-card__header--edit">
            <h3 class="hospital-card__title">Editar Hospital</h3>
            <button type="button" class="hospital-card__close">&times;</button>
        </div>
        <div class="hospital-card__body">
            <?php if ($session->hasMessage('modal_error_hospital_' . $hospital->getIdHospital())): ?>
                <div class="hospital-form__error">
                    <p><?= $session->getMessage('modal_error_hospital_' . $hospital->getIdHospital()) ?></p>
                </div>
                <?php $session->clearMessage('modal_error_hospital_' . $hospital->getIdHospital()); ?>
            <?php endif; ?>
            <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/hospital-actions.php" method="post" class="hospital-form" id="form-editar-hospital-<?= $hospital->getIdHospital() ?>">
                <input type="hidden" name="action" value="editar_hospital">
                <input type="hidden" name="id" value="<?= $hospital->getIdHospital() ?>">
                <div class="hospital-form__group">
                    <label for="nombre-hospital-edit-<?= $hospital->getIdHospital() ?>" class="hospital-form__label">Nombre:</label>
                    <input type="text" id="nombre-hospital-edit-<?= $hospital->getIdHospital() ?>" name="nombre" value="<?= htmlspecialchars($hospital->getNombre()) ?>" class="hospital-form__input" required>
                </div>
                <div class="hospital-card__footer">
                    <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel" id="btn-cancel-edit-hospital-<?= $hospital->getIdHospital() ?>">Cancelar</button>
                    <button type="submit" class="hospital-form__button hospital-form__button--primary" id="btn-submit-edit-hospital-<?= $hospital->getIdHospital() ?>">Actualizar Hospital</button>
                </div>
            </form>
        </div>
    </div>
    
    <div id="hospital-card-delete-<?= $hospital->getIdHospital() ?>" class="hospital-card">
        <div class="hospital-card__header hospital-card__header--delete">
            <h3 class="hospital-card__title">Eliminar Hospital</h3>
            <button type="button" class="hospital-card__close">&times;</button>
        </div>
        <div class="hospital-card__body">
            <h4>¿Estás seguro de que deseas eliminar el hospital "<?= htmlspecialchars($hospital->getNombre()) ?>"?</h4>
            <p class="text-danger">Esta acción eliminará también todas las plantas y botiquines asociados. No se puede deshacer.</p>
            <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/hospital-actions.php" method="post" id="form-eliminar-hospital-<?= $hospital->getIdHospital() ?>">
                <input type="hidden" name="action" value="eliminar_hospital">
                <input type="hidden" name="id" value="<?= $hospital->getIdHospital() ?>">
                <div class="hospital-card__footer">
                    <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel" id="btn-cancel-delete-hospital-<?= $hospital->getIdHospital() ?>">Cancelar</button>
                    <button type="submit" class="hospital-form__button hospital-form__button--danger" id="btn-submit-delete-hospital-<?= $hospital->getIdHospital() ?>">Confirmar Eliminación</button>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>
