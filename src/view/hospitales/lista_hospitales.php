<?php
session_start();
require_once(__DIR__ . '/../../controller/HospitalController.php');
require_once(__DIR__ . '/../../controller/PlantaController.php');
require_once(__DIR__ . '/../../controller/BotiquinController.php');
include_once(__DIR__ . '/../../util/Session.php');
include_once(__DIR__ . '/../../util/AuthGuard.php');

use controller\HospitalController;
use controller\PlantaController;
use controller\BotiquinController;
use util\Session;
use util\AuthGuard;

$hospitalController = new HospitalController();
$plantaController = new PlantaController();
$botiquinController = new BotiquinController();
$session = new Session();
$authGuard = new AuthGuard();

$authGuard->requireHospitalGestor();

$responseHospitales = $hospitalController->index();
$hospitales = $responseHospitales['error'] ? [] : $responseHospitales['hospitales'];

$responsePlantas = $plantaController->index();
$plantas = $responsePlantas['error'] ? [] : $responsePlantas['plantas'];

$responseBotiquines = $botiquinController->index();
$botiquines = $responseBotiquines['error'] ? [] : $responseBotiquines['botiquines'];

$pageTitle = "Gestión de Ubicaciones";
include_once(__DIR__ . '/../templates/header.php');
?>

<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/card-form.css">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/list.css">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/tabs.css">

<div class="list-container">
    <div class="list-header">
        <h2 class="list-header__title">Gestión de Ubicaciones</h2>
        <div class="list-header__actions">
            <button id="btn-add-hospital" class="list-button list-button--success">
                <i class="bi bi-plus-circle list-button__icon"></i> Nuevo Hospital
            </button>
        </div>
    </div>
    
    <?php if ($session->hasMessage('success')): ?>
        <div class="list-alert list-alert--success">
            <p class="list-alert__message"><?= $session->getMessage('success') ?></p>
            <button type="button" class="list-alert__close">&times;</button>
        </div>
    <?php endif; ?>

    <?php if ($session->hasMessage('error')): ?>
        <div class="list-alert list-alert--error">
            <p class="list-alert__message"><?= $session->getMessage('error') ?></p>
            <button type="button" class="list-alert__close">&times;</button>
        </div>
    <?php endif; ?>

    <div class="tabs-container">
        <div class="tabs-nav">
            <button class="tab-btn active" data-tab="tab-hospitales">Hospitales</button>
            <button class="tab-btn" data-tab="tab-plantas">Plantas</button>
            <button class="tab-btn" data-tab="tab-botiquines">Botiquines</button>
        </div>
        
        <div class="tab-content">
            <!-- Pestaña Hospitales -->
            <div id="tab-hospitales" class="tab-pane active">
                <div class="table-responsive">
                    <table class="list-table">
                        <tbody>
                            <?php if (empty($hospitales)): ?>
                                <tr>
                                    <td colspan="2" class="list-table__empty">No hay hospitales registrados</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($hospitales as $hospital): ?>
                                    <tr class="list-table__body-row">
                                        <td class="list-table__body-cell"><?= htmlspecialchars($hospital->nombre) ?></td>
                                        <td class="list-table__body-cell">
                                            <div class="list-table__actions">
                                                <button class="list-table__button list-table__button--edit btn-edit-hospital" data-id="<?= $hospital->id_hospital ?>">
                                                    <i class="bi bi-pencil-square list-table__button-icon"></i> Editar
                                                </button>
                                                <button class="list-table__button list-table__button--delete btn-delete-hospital" data-id="<?= $hospital->id_hospital ?>">
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
            
            <!-- Pestaña Plantas -->
            <div id="tab-plantas" class="tab-pane">
                <div class="list-header__actions mt-3 mb-3">
                    <button id="btn-add-planta" class="list-button list-button--info">
                        <i class="bi bi-plus-circle list-button__icon"></i> Nueva Planta
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="list-table">
                        <tbody>
                            <?php if (empty($plantas)): ?>
                                <tr>
                                    <td colspan="3" class="list-table__empty">No hay plantas registradas</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($plantas as $planta): ?>
                                    <?php 
                                    $hospitalNombre = "";
                                    foreach ($hospitales as $hospital) {
                                        if ($hospital->id_hospital == $planta->getHospitalId()) {
                                            $hospitalNombre = $hospital->nombre;
                                            break;
                                        }
                                    }
                                    ?>
                                    <tr class="list-table__body-row">
                                        <td class="list-table__body-cell">Planta <?= $planta->getNumero() ?> <?= !empty($planta->getDescripcion()) ? '- ' . htmlspecialchars($planta->getDescripcion()) : '' ?></td>
                                        <td class="list-table__body-cell"><?= htmlspecialchars($hospitalNombre) ?></td>
                                        <td class="list-table__body-cell">
                                            <div class="list-table__actions">
                                                <button class="list-table__button list-table__button--edit btn-edit-planta" data-id="<?= $planta->getId() ?>">
                                                    <i class="bi bi-pencil-square list-table__button-icon"></i> Editar
                                                </button>
                                                <button class="list-table__button list-table__button--delete btn-delete-planta" data-id="<?= $planta->getId() ?>">
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

            <!-- Pestaña Botiquines -->
            <div id="tab-botiquines" class="tab-pane">
                <div class="list-header__actions mt-3 mb-3">
                    <button id="btn-add-botiquin" class="list-button list-button--secondary">
                        <i class="bi bi-plus-circle list-button__icon"></i> Nuevo Botiquín
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="list-table">
                        <tbody>
                            <?php if (empty($botiquines)): ?>
                                <tr>
                                    <td colspan="3" class="list-table__empty">No hay botiquines registrados</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($botiquines as $botiquin): ?>
                                    <?php
                                    $plantaInfo = "";
                                    foreach ($plantas as $planta) {
                                        if ($planta->getId() == $botiquin->planta_id) {
                                            $plantaInfo = "Planta " . $planta->getNumero();
                                            if (!empty($planta->getDescripcion())) {
                                                $plantaInfo .= ' - ' . $planta->getDescripcion();
                                            }
                                            break;
                                        }
                                    }
                                    ?>
                                    <tr class="list-table__body-row">
                                        <td class="list-table__body-cell"><?= htmlspecialchars($botiquin->nombre) ?></td>
                                        <td class="list-table__body-cell"><?= htmlspecialchars($plantaInfo) ?></td>
                                        <td class="list-table__body-cell">
                                            <div class="list-table__actions">
                                                <button class="list-table__button list-table__button--edit btn-edit-botiquin" data-id="<?= $botiquin->id ?>">
                                                    <i class="bi bi-pencil-square list-table__button-icon"></i> Editar
                                                </button>
                                                <button class="list-table__button list-table__button--delete btn-delete-botiquin" data-id="<?= $botiquin->id ?>">
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
        </div>
    </div>
</div>

<div class="hospital-overlay"></div>

<!-- Ventana modal para crear hospital -->
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
        <form id="create-hospital-form" method="POST" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/hospital-actions.php">
            <input type="hidden" name="action" value="crear_hospital">
            <div class="hospital-form__group">
                <label for="nombre-create" class="hospital-form__label">Nombre del Hospital</label>
                <input type="text" class="hospital-form__input" id="nombre-create" name="nombre" required>
            </div>
            <div class="hospital-card__footer">
                <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel">Cancelar</button>
                <button type="submit" class="hospital-form__button hospital-form__button--primary">Registrar Hospital</button>
            </div>
        </form>
    </div>
</div>

<!-- Ventana modal para crear planta -->
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
        <form id="create-planta-form" method="POST" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/hospital-actions.php">
            <input type="hidden" name="action" value="crear_planta">
            <div class="hospital-form__group">
                <label for="numero-planta-create" class="hospital-form__label">Nombre de Planta</label>
                <input type="text" class="hospital-form__input" id="nombre-planta-create" name="nombre" required>
            </div>
            <div class="hospital-form__group">
                <label for="descripcion-planta-create" class="hospital-form__label">Descripción (Opcional)</label>
                <input type="text" class="hospital-form__input" id="descripcion-planta-create" name="descripcion">
            </div>
            <div class="hospital-form__group">
                <label for="hospital-planta-create" class="hospital-form__label">Hospital</label>
                <select class="hospital-form__select" id="hospital-planta-create" name="id_hospital" required>
                    <option value="">Seleccione un hospital</option>
                    <?php foreach ($hospitales as $hospital): ?>
                        <option value="<?= $hospital->id_hospital ?>"><?= htmlspecialchars($hospital->nombre) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="hospital-card__footer">
                <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel">Cancelar</button>
                <button type="submit" class="hospital-form__button hospital-form__button--primary">Registrar Planta</button>
            </div>
        </form>
    </div>
</div>

<!-- Ventana modal para crear botiquin -->
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
        <form id="create-botiquin-form" method="POST" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/hospital-actions.php">
            <input type="hidden" name="action" value="crear_botiquin">
            <div class="hospital-form__group">
                <label for="nombre-botiquin-create" class="hospital-form__label">Nombre del Botiquín</label>
                <input type="text" class="hospital-form__input" id="nombre-botiquin-create" name="nombre" required>
            </div>
            <div class="hospital-form__group">
                <label for="planta-botiquin-create" class="hospital-form__label">Planta</label>
                <select class="hospital-form__select" id="planta-botiquin-create" name="planta_id" required>
                    <option value="">Seleccione una planta</option>
                    <?php foreach ($plantas as $planta): ?>
                        <?php
                        $hospitalNombre = "";
                        foreach ($hospitales as $hospital) {
                            if ($hospital->id_hospital == $planta->getHospitalId()) {
                                $hospitalNombre = $hospital->nombre;
                                break;
                            }
                        }
                        ?>
                        <option value="<?= $planta->getId() ?>">Planta <?= $planta->getNumero() ?> - <?= htmlspecialchars($hospitalNombre) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="hospital-card__footer">
                <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel">Cancelar</button>
                <button type="submit" class="hospital-form__button hospital-form__button--primary">Registrar Botiquín</button>
            </div>
        </form>
    </div>
</div>

<?php if (!empty($hospitales)): ?>
    <?php foreach ($hospitales as $hospital): ?>
        <!-- Ventana modal para editar hospital -->
        <div id="hospital-card-edit-<?= $hospital->id_hospital ?>" class="hospital-card">
            <div class="hospital-card__header hospital-card__header--edit">
                <h3 class="hospital-card__title">Editar Hospital</h3>
                <button type="button" class="hospital-card__close">&times;</button>
            </div>
            <div class="hospital-card__body">
                <?php if ($session->hasMessage('modal_error_hospital_' . $hospital->id_hospital)): ?>
                    <div class="hospital-form__error">
                        <p><?= $session->getMessage('modal_error_hospital_' . $hospital->id_hospital) ?></p>
                    </div>
                    <?php $session->clearMessage('modal_error_hospital_' . $hospital->id_hospital); ?>
                <?php endif; ?>
                <form id="edit-hospital-form-<?= $hospital->id_hospital ?>" method="POST" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/hospital-actions.php">
                    <input type="hidden" name="action" value="editar_hospital">
                    <input type="hidden" name="id" value="<?= $hospital->id_hospital ?>">
                    <div class="hospital-form__group">
                        <label for="nombre-edit-<?= $hospital->id_hospital ?>" class="hospital-form__label">Nombre del Hospital</label>
                        <input type="text" class="hospital-form__input" id="nombre-edit-<?= $hospital->id_hospital ?>" name="nombre" value="<?= htmlspecialchars($hospital->nombre) ?>" required>
                    </div>
                    <div class="hospital-card__footer">
                        <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel">Cancelar</button>
                        <button type="submit" class="hospital-form__button hospital-form__button--primary">Actualizar Hospital</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Ventana modal para eliminar hospital -->
        <div id="hospital-card-delete-<?= $hospital->id_hospital ?>" class="hospital-card">
            <div class="hospital-card__header hospital-card__header--delete">
                <h3 class="hospital-card__title">Eliminar Hospital</h3>
                <button type="button" class="hospital-card__close">&times;</button>
            </div>
            <div class="hospital-card__body">
                <h4>¿Estás seguro de que deseas eliminar el hospital "<?= htmlspecialchars($hospital->nombre) ?>"?</h4>
                <p class="text-danger">Esta acción eliminará también todas las plantas y botiquines asociados.</p>
                <form id="delete-hospital-form-<?= $hospital->id_hospital ?>" method="POST" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/hospital-actions.php">
                    <input type="hidden" name="action" value="eliminar_hospital">
                    <input type="hidden" name="id" value="<?= $hospital->id_hospital ?>">
                    <div class="hospital-card__footer">
                        <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel">Cancelar</button>
                        <button type="submit" class="hospital-form__button hospital-form__button--danger">Confirmar Eliminación</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($plantas)): ?>
    <?php foreach ($plantas as $planta): ?>
        <!-- Ventana modal para editar planta -->
        <div id="planta-card-edit-<?= $planta->getId() ?>" class="hospital-card">
            <div class="hospital-card__header hospital-card__header--edit">
                <h3 class="hospital-card__title">Editar Planta</h3>
                <button type="button" class="hospital-card__close">&times;</button>
            </div>
            <div class="hospital-card__body">
                <?php if ($session->hasMessage('modal_error_planta_' . $planta->getId())): ?>
                    <div class="hospital-form__error">
                        <p><?= $session->getMessage('modal_error_planta_' . $planta->getId()) ?></p>
                    </div>
                    <?php $session->clearMessage('modal_error_planta_' . $planta->getId()); ?>
                <?php endif; ?>
                <form id="edit-planta-form-<?= $planta->getId() ?>" method="POST" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/hospital-actions.php">
                    <input type="hidden" name="action" value="editar_planta">
                    <input type="hidden" name="id" value="<?= $planta->getId() ?>">
                    <div class="hospital-form__group">
                        <label for="numero-planta-edit-<?= $planta->getId() ?>" class="hospital-form__label">Número de Planta</label>
                        <input type="number" class="hospital-form__input" id="numero-planta-edit-<?= $planta->getId() ?>" name="numero" value="<?= $planta->getNumero() ?>" required min="0">
                    </div>
                    <div class="hospital-form__group">
                        <label for="descripcion-planta-edit-<?= $planta->getId() ?>" class="hospital-form__label">Descripción (Opcional)</label>
                        <input type="text" class="hospital-form__input" id="descripcion-planta-edit-<?= $planta->getId() ?>" name="descripcion" value="<?= htmlspecialchars($planta->getDescripcion()) ?>">
                    </div>
                    <div class="hospital-form__group">
                        <label for="hospital-planta-edit-<?= $planta->getId() ?>" class="hospital-form__label">Hospital</label>
                        <select class="hospital-form__select" id="hospital-planta-edit-<?= $planta->getId() ?>" name="hospital_id" required>
                            <option value="">Seleccione un hospital</option>
                            <?php foreach ($hospitales as $hospital): ?>
                                <option value="<?= $hospital->id_hospital ?>" <?= $hospital->id_hospital == $planta->getHospitalId() ? 'selected' : '' ?>><?= htmlspecialchars($hospital->nombre) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="hospital-card__footer">
                        <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel">Cancelar</button>
                        <button type="submit" class="hospital-form__button hospital-form__button--primary">Actualizar Planta</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Ventana modal para eliminar planta -->
        <div id="planta-card-delete-<?= $planta->getId() ?>" class="hospital-card">
            <div class="hospital-card__header hospital-card__header--delete">
                <h3 class="hospital-card__title">Eliminar Planta</h3>
                <button type="button" class="hospital-card__close">&times;</button>
            </div>
            <div class="hospital-card__body">
                <h4>¿Estás seguro de que deseas eliminar la planta "<?= $planta->getNumero() ?>"?</h4>
                <p class="text-danger">Esta acción eliminará también todos los botiquines asociados.</p>
                <form id="delete-planta-form-<?= $planta->getId() ?>" method="POST" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/hospital-actions.php">
                    <input type="hidden" name="action" value="eliminar_planta">
                    <input type="hidden" name="id" value="<?= $planta->getId() ?>">
                    <div class="hospital-card__footer">
                        <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel">Cancelar</button>
                        <button type="submit" class="hospital-form__button hospital-form__button--danger">Confirmar Eliminación</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($botiquines)): ?>
    <?php foreach ($botiquines as $botiquin): ?>
        <!-- Ventana modal para editar botiquin -->
        <div id="botiquin-card-edit-<?= $botiquin->id ?>" class="hospital-card">
            <div class="hospital-card__header hospital-card__header--edit">
                <h3 class="hospital-card__title">Editar Botiquín</h3>
                <button type="button" class="hospital-card__close">&times;</button>
            </div>
            <div class="hospital-card__body">
                <?php if ($session->hasMessage('modal_error_botiquin_' . $botiquin->id)): ?>
                    <div class="hospital-form__error">
                        <p><?= $session->getMessage('modal_error_botiquin_' . $botiquin->id) ?></p>
                    </div>
                    <?php $session->clearMessage('modal_error_botiquin_' . $botiquin->id); ?>
                <?php endif; ?>
                <form id="edit-botiquin-form-<?= $botiquin->id ?>" method="POST" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/hospital-actions.php">
                    <input type="hidden" name="action" value="editar_botiquin">
                    <input type="hidden" name="id" value="<?= $botiquin->id ?>">
                    <div class="hospital-form__group">
                        <label for="nombre-botiquin-edit-<?= $botiquin->id ?>" class="hospital-form__label">Nombre del Botiquín</label>
                        <input type="text" class="hospital-form__input" id="nombre-botiquin-edit-<?= $botiquin->id ?>" name="nombre" value="<?= htmlspecialchars($botiquin->nombre) ?>" required>
                    </div>
                    <div class="hospital-form__group">
                        <label for="planta-botiquin-edit-<?= $botiquin->id ?>" class="hospital-form__label">Planta</label>
                        <select class="hospital-form__select" id="planta-botiquin-edit-<?= $botiquin->id ?>" name="planta_id" required>
                            <option value="">Seleccione una planta</option>
                            <?php foreach ($plantas as $planta): ?>
                                <?php
                                $hospitalNombre = "";
                                foreach ($hospitales as $hospital) {
                                    if ($hospital->id_hospital == $planta->getHospitalId()) {
                                        $hospitalNombre = $hospital->nombre;
                                        break;
                                    }
                                }
                                ?>
                                <option value="<?= $planta->getId() ?>" <?= $planta->getId() == $botiquin->planta_id ? 'selected' : '' ?>>Planta <?= $planta->getNumero() ?> - <?= htmlspecialchars($hospitalNombre) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="hospital-card__footer">
                        <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel">Cancelar</button>
                        <button type="submit" class="hospital-form__button hospital-form__button--primary">Actualizar Botiquín</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Ventana modal para eliminar botiquin -->
        <div id="botiquin-card-delete-<?= $botiquin->id ?>" class="hospital-card">
            <div class="hospital-card__header hospital-card__header--delete">
                <h3 class="hospital-card__title">Eliminar Botiquín</h3>
                <button type="button" class="hospital-card__close">&times;</button>
            </div>
            <div class="hospital-card__body">
                <h4>¿Estás seguro de que deseas eliminar el botiquín "<?= htmlspecialchars($botiquin->nombre) ?>"?</h4>
                <p class="text-danger">Esta acción no se puede deshacer.</p>
                <form id="delete-botiquin-form-<?= $botiquin->id ?>" method="POST" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/hospital-actions.php">
                    <input type="hidden" name="action" value="eliminar_botiquin">
                    <input type="hidden" name="id" value="<?= $botiquin->id ?>">
                    <div class="hospital-card__footer">
                        <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel">Cancelar</button>
                        <button type="submit" class="hospital-form__button hospital-form__button--danger">Confirmar Eliminación</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Eliminamos el script JS en línea y usamos los archivos externos -->
<script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/hospital-cards.js"></script>
<script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/hospital-tabs.js"></script>

<?php include_once(__DIR__ . '/../templates/footer.php'); ?>
