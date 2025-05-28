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

$hospitales = $hospitalController->index()['hospitales'] ?? [];
// Comentamos temporalmente la carga de plantas y botiquines
// $plantas = $plantaController->index()['plantas'] ?? [];
// $botiquines = $botiquinController->index()['botiquines'] ?? [];

$pageTitle = "Hospitales";
include_once(__DIR__ . '/../templates/header.php');
?>

<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/list.css">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/card-form.css">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/tabs.css">

<div class="list-container">
    <div class="list-header">
        <div class="list-header__actions">
            <button id="btn-add-hospital" class="list-button list-button--success">
                <i class="bi bi-hospital"></i> Nuevo Hospital
            </button>
            <!-- Ocultamos botones temporalmente
            <button id="btn-add-planta" class="list-button list-button--primary">
                <i class="bi bi-building"></i> Nueva Planta
            </button>
            <button id="btn-add-botiquin" class="list-button list-button--info">
                <i class="bi bi-box"></i> Nuevo Botiquín
      -      </button>
            -->
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

    <!-- Implementamos el sistema de pestañas -->
    <div class="tabs-container">
        <div class="tabs-nav">
            <button class="tab-btn active" data-tab="tab-hospitales">Hospitales</button>
        </div>
        
        <div class="tab-content">
            <!-- Pestaña Hospitales -->
            <div id="tab-hospitales" class="tab-pane active">
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
                                        <td class="list-table__body-cell" data-label="ID"><?= htmlspecialchars($hospital->id_hospital) ?></td>
                                        <td class="list-table__body-cell" data-label="Nombre"><?= htmlspecialchars($hospital->nombre) ?></td>
                                        <td class="list-table__body-cell" data-label="Plantas">
                                            <?php
                                            $plantasHospital = $plantaController->getByHospital($hospital->id_hospital)['plantas'] ?? [];
                                            $cantidadPlantas = count($plantasHospital);
                                            ?>
                                            <span class="badge bg-info"><?= $cantidadPlantas ?> plantas</span>
                                        </td>
                                        <td class="list-table__body-cell" data-label="Acciones">
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
        </div>
    </div>
</div>

<!-- Overlay para ventanas modales -->
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

<!-- Modales para edición y eliminación de hospitales -->
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
            <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/hospital-actions.php" method="post" class="hospital-form" id="form-editar-hospital-<?= $hospital->id_hospital ?>">
                <input type="hidden" name="action" value="editar_hospital">
                <input type="hidden" name="id" value="<?= $hospital->id_hospital ?>">
                <div class="hospital-form__group">
                    <label for="nombre-hospital-edit-<?= $hospital->id_hospital ?>" class="hospital-form__label">Nombre:</label>
                    <input type="text" id="nombre-hospital-edit-<?= $hospital->id_hospital ?>" name="nombre" value="<?= htmlspecialchars($hospital->nombre) ?>" class="hospital-form__input" required>
                </div>
                <div class="hospital-card__footer">
                    <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel" id="btn-cancel-edit-hospital-<?= $hospital->id_hospital ?>">Cancelar</button>
                    <button type="submit" class="hospital-form__button hospital-form__button--primary" id="btn-submit-edit-hospital-<?= $hospital->id_hospital ?>">Actualizar Hospital</button>
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
            <p class="text-danger">Esta acción eliminará también todas las plantas y botiquines asociados. No se puede deshacer.</p>
            <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/hospital-actions.php" method="post" id="form-eliminar-hospital-<?= $hospital->id_hospital ?>">
                <input type="hidden" name="action" value="eliminar_hospital">
                <input type="hidden" name="id" value="<?= $hospital->id_hospital ?>">
                <div class="hospital-card__footer">
                    <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel" id="btn-cancel-delete-hospital-<?= $hospital->id_hospital ?>">Cancelar</button>
                    <button type="submit" class="hospital-form__button hospital-form__button--danger" id="btn-submit-delete-hospital-<?= $hospital->id_hospital ?>">Confirmar Eliminación</button>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>

<script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/hospital-cards.js"></script>
<script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/hospital-tabs.js"></script>

<?php include_once(__DIR__ . '/../templates/footer.php'); ?>
