<?php
if (!isset($hospitales) || !isset($plantas) || !isset($botiquines) || !isset($session)) {
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
            <th>Nombre</th>
            <th>Planta</th>
            <th>Hospital</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($botiquines)): ?>
            <tr>
                <td colspan="5" class="list-table__empty">No hay botiquines registrados</td>
            </tr>
        <?php else: ?>
            <?php foreach ($botiquines as $botiquin):
                $plantaBotiquin = null;
                foreach ($plantas as $p) {
                    if ($p->getIdPlanta() == $botiquin->getIdPlanta()) {
                        $plantaBotiquin = $p;
                        break;
                    }
                }

                $hospitalBotiquin = null;
                if ($plantaBotiquin) {
                    foreach ($hospitales as $h) {
                        if ($h->getIdHospital() == $plantaBotiquin->getIdHospital()) {
                            $hospitalBotiquin = $h;
                            break;
                        }
                    }
                }
                ?>
                <tr class="list-table__body-row">
                    <td class="list-table__body-cell" data-label="ID"><?= htmlspecialchars($botiquin->getIdBotiquines()) ?></td>
                    <td class="list-table__body-cell" data-label="Nombre"><?= htmlspecialchars($botiquin->getNombre()) ?></td>
                    <td class="list-table__body-cell" data-label="Planta">
                        <?= $plantaBotiquin ? htmlspecialchars($plantaBotiquin->getNombre()) : 'N/A' ?>
                    </td>
                    <td class="list-table__body-cell" data-label="Hospital">
                        <?= $hospitalBotiquin ? htmlspecialchars($hospitalBotiquin->getNombre()) : 'N/A' ?>
                    </td>
                    <td class="list-table__body-cell" data-label="Acciones">
                        <div class="list-table__actions">
                            <button class="list-table__button list-table__button--edit btn-edit-botiquin" data-id="<?= $botiquin->getIdBotiquines() ?>">
                                <i class="bi bi-pencil-square list-table__button-icon"></i> Editar
                            </button>
                            <button class="list-table__button list-table__button--delete btn-delete-botiquin" data-id="<?= $botiquin->getIdBotiquines() ?>">
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

<div id="botiquin-card-create" class="hospital-card">
    <div class="hospital-card__header hospital-card__header--create">
        <h3 class="hospital-card__title">Nuevo Botiquín</h3>
        <button type="button" class="hospital-card__close">&times;</button>
    </div>
    <div class="hospital-card__body">
        <?php if ($session->hasMessage('modal_error_botiquin')): ?>
            <div class="hospital-form__error">
                <p><?= $session->getMessage('modal_error_botiquin') ?></p>
            </div>
            <?php $session->clearMessage('modal_error_botiquin'); ?>
        <?php endif; ?>
        <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/botiquin-actions.php" method="post" class="hospital-form" id="form-crear-botiquin">
            <input type="hidden" name="action" value="crear_botiquin">
            <div class="hospital-form__group">
                <label for="id-planta-botiquin-create" class="hospital-form__label">Planta:</label>
                <select id="id-planta-botiquin-create" name="planta_id" class="hospital-form__select" required>
                    <option value="">Seleccione una planta</option>
                    <?php foreach ($plantas as $planta):
                        $hospitalPlanta = null;
                        foreach ($hospitales as $h) {
                            if ($h->getIdHospital() == $planta->getIdHospital()) {
                                $hospitalPlanta = $h;
                                break;
                            }
                        }
                        ?>
                        <option value="<?= $planta->getIdPlanta() ?>">
                            <?= htmlspecialchars($planta->getNombre()) ?>
                            <?= $hospitalPlanta ? ' (' . htmlspecialchars($hospitalPlanta->getNombre()) . ')' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="hospital-form__group">
                <label for="nombre-botiquin-create" class="hospital-form__label">Nombre:</label>
                <input type="text" id="nombre-botiquin-create" name="nombre" class="hospital-form__input" required>
            </div>
            <div class="hospital-card__footer">
                <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel" id="btn-cancel-botiquin">Cancelar</button>
                <button type="submit" class="hospital-form__button hospital-form__button--primary" id="btn-submit-botiquin">Registrar Botiquín</button>
            </div>
        </form>
    </div>
</div>

<?php foreach ($botiquines as $botiquin): ?>
    <div id="botiquin-card-edit-<?= $botiquin->getIdBotiquines() ?>" class="hospital-card">
        <div class="hospital-card__header hospital-card__header--edit">
            <h3 class="hospital-card__title">Editar Botiquín</h3>
            <button type="button" class="hospital-card__close">&times;</button>
        </div>
        <div class="hospital-card__body">
            <?php if ($session->hasMessage('modal_error_botiquin_' . $botiquin->getIdBotiquines())): ?>
                <div class="hospital-form__error">
                    <p><?= $session->getMessage('modal_error_botiquin_' . $botiquin->getIdBotiquines()) ?></p>
                </div>
                <?php $session->clearMessage('modal_error_botiquin_' . $botiquin->getIdBotiquines()); ?>
            <?php endif; ?>
            <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/botiquin-actions.php" method="post" class="hospital-form" id="form-editar-botiquin-<?= $botiquin->getIdBotiquines() ?>">
                <input type="hidden" name="action" value="editar_botiquin">
                <input type="hidden" name="id" value="<?= $botiquin->getIdBotiquines() ?>">
                <div class="hospital-form__group">
                    <label for="id-planta-botiquin-edit-<?= $botiquin->getIdBotiquines() ?>" class="hospital-form__label">Planta:</label>
                    <select id="id-planta-botiquin-edit-<?= $botiquin->getIdBotiquines() ?>" name="planta_id" class="hospital-form__select" required>
                        <option value="">Seleccione una planta</option>
                        <?php foreach ($plantas as $planta):
                            $hospitalPlanta = null;
                            foreach ($hospitales as $h) {
                                if ($h->getIdHospital() == $planta->getIdHospital()) {
                                    $hospitalPlanta = $h;
                                    break;
                                }
                            }
                            ?>
                            <option value="<?= $planta->getIdPlanta() ?>" <?= $planta->getIdPlanta() == $botiquin->getIdPlanta() ? 'selected' : '' ?>>
                                <?= htmlspecialchars($planta->getNombre()) ?>
                                <?= $hospitalPlanta ? ' (' . htmlspecialchars($hospitalPlanta->getNombre()) . ')' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="hospital-form__group">
                    <label for="nombre-botiquin-edit-<?= $botiquin->getIdBotiquines() ?>" class="hospital-form__label">Nombre:</label>
                    <input type="text" id="nombre-botiquin-edit-<?= $botiquin->getIdBotiquines() ?>" name="nombre" value="<?= htmlspecialchars($botiquin->getNombre()) ?>" class="hospital-form__input" required>
                </div>
                <div class="hospital-card__footer">
                    <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel" id="btn-cancel-edit-botiquin-<?= $botiquin->getIdBotiquines() ?>">Cancelar</button>
                    <button type="submit" class="hospital-form__button hospital-form__button--primary" id="btn-submit-edit-botiquin-<?= $botiquin->getIdBotiquines() ?>">Actualizar Botiquín</button>
                </div>
            </form>
        </div>
    </div>

    <div id="botiquin-card-delete-<?= $botiquin->getIdBotiquines() ?>" class="hospital-card">
        <div class="hospital-card__header hospital-card__header--delete">
            <h3 class="hospital-card__title">Eliminar Botiquín</h3>
            <button type="button" class="hospital-card__close">&times;</button>
        </div>
        <div class="hospital-card__body">
            <h4>¿Estás seguro de que deseas eliminar el botiquín "<?= htmlspecialchars($botiquin->getNombre()) ?>"?</h4>
            <p class="text-danger">Esta acción no se puede deshacer.</p>
            <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/botiquin-actions.php" method="post" id="form-eliminar-botiquin-<?= $botiquin->getIdBotiquines() ?>">
                <input type="hidden" name="action" value="eliminar_botiquin">
                <input type="hidden" name="id" value="<?= $botiquin->getIdBotiquines() ?>">
                <div class="hospital-card__footer">
                    <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel" id="btn-cancel-delete-botiquin-<?= $botiquin->getIdBotiquines() ?>">Cancelar</button>
                    <button type="submit" class="hospital-form__button hospital-form__button--danger" id="btn-submit-delete-botiquin-<?= $botiquin->getIdBotiquines() ?>">Confirmar Eliminación</button>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>
