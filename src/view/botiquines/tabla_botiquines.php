<?php
session_start();
require_once __DIR__ . '/../../controller/BotiquinController.php';
require_once __DIR__ . '/../../util/Session.php';
require_once __DIR__ . '/../../util/AuthGuard.php';
require_once __DIR__ . '/../../model/service/PlantaService.php';

use controller\BotiquinController;
use util\Session;
use util\AuthGuard;

$authGuard = new AuthGuard();
$authGuard->requireHospitalGestor();

$botiquinesController = new BotiquinController();
$botiquinesWithPlantas = $botiquinesController->getBotiquinesWithPlantas();
$plantas = $botiquinesController->getPlantas();

$session = new Session();

include(__DIR__ . '/../templates/header.php');
?>

<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/card-form.css">

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col">
            <h2 class="mb-3">Gestión de Botiquines</h2>
        </div>
        <div class="col text-end">
            <button id="btn-add-botiquin" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nuevo Botiquín
            </button>
        </div>
    </div>

    <?php if ($session->hasMessage('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $session->getMessage('success'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($session->hasMessage('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $session->getMessage('error'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title mb-0">Listado de Botiquines</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Planta</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($botiquinesWithPlantas)): ?>
                            <tr>
                                <td colspan="4" class="text-center">No hay botiquines registrados</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($botiquinesWithPlantas as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['botiquin']->getId()); ?></td>
                                    <td><?php echo htmlspecialchars($item['botiquin']->getNombre()); ?></td>
                                    <td><?php echo $item['planta'] ? htmlspecialchars($item['planta']->getNombre()) : 'N/A'; ?></td>
                                    <td>
                                        <button class="btn btn-warning btn-sm btn-action btn-edit-botiquin" data-id="<?php echo $item['botiquin']->getId(); ?>">
                                            <i class="bi bi-pencil"></i> Editar
                                        </button>
                                        <button class="btn btn-danger btn-sm btn-action btn-delete-botiquin" data-id="<?php echo $item['botiquin']->getId(); ?>">
                                            <i class="bi bi-trash"></i> Eliminar
                                        </button>
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

<div class="botiquin-overlay"></div>

<div id="botiquin-card-create" class="botiquin-card">
    <div class="botiquin-card__header botiquin-card__header--create">
        <h3 class="botiquin-card__title">Nuevo Botiquín</h3>
        <button type="button" class="botiquin-card__close">&times;</button>
    </div>
    <div class="botiquin-card__body">
        <form id="create-botiquin-form" method="POST" action="procesar_botiquin.php">
            <input type="hidden" name="action" value="create">
            <div class="botiquin-form__group">
                <label for="nombre-create" class="botiquin-form__label">Nombre del Botiquín</label>
                <input type="text" class="botiquin-form__input" id="nombre-create" name="nombre" required>
            </div>
            <div class="botiquin-form__group">
                <label for="planta_id-create" class="botiquin-form__label">Planta</label>
                <select class="botiquin-form__select" id="planta_id-create" name="planta_id" required>
                    <option value="">Seleccionar planta</option>
                    <?php foreach ($plantas as $planta): ?>
                        <option value="<?php echo $planta->getId(); ?>">
                            <?php echo htmlspecialchars($planta->getNombre()); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="botiquin-card__footer">
                <button type="button" class="botiquin-form__button botiquin-form__button--secondary botiquin-form__button--cancel">Cancelar</button>
                <button type="submit" class="botiquin-form__button botiquin-form__button--primary">Registrar Botiquín</button>
            </div>
        </form>
    </div>
</div>

<?php if (!empty($botiquinesWithPlantas)): ?>
    <?php foreach ($botiquinesWithPlantas as $item): ?>
        <div id="botiquin-card-edit-<?php echo $item['botiquin']->getId(); ?>" class="botiquin-card">
            <div class="botiquin-card__header botiquin-card__header--edit">
                <h3 class="botiquin-card__title">Editar Botiquín</h3>
                <button type="button" class="botiquin-card__close">&times;</button>
            </div>
            <div class="botiquin-card__body">
                <form id="edit-botiquin-form-<?php echo $item['botiquin']->getId(); ?>" method="POST" action="procesar_botiquin.php">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo $item['botiquin']->getId(); ?>">
                    <div class="botiquin-form__group">
                        <label for="nombre-edit-<?php echo $item['botiquin']->getId(); ?>" class="botiquin-form__label">Nombre del Botiquín</label>
                        <input type="text" class="botiquin-form__input" id="nombre-edit-<?php echo $item['botiquin']->getId(); ?>" name="nombre" value="<?php echo htmlspecialchars($item['botiquin']->getNombre()); ?>" required>
                    </div>
                    <div class="botiquin-form__group">
                        <label for="planta_id-edit-<?php echo $item['botiquin']->getId(); ?>" class="botiquin-form__label">Planta</label>
                        <select class="botiquin-form__select" id="planta_id-edit-<?php echo $item['botiquin']->getId(); ?>" name="planta_id" required>
                            <option value="">Seleccionar planta</option>
                            <?php foreach ($plantas as $planta): ?>
                                <option value="<?php echo $planta->getId(); ?>" <?php echo ($item['botiquin']->getPlantaId() == $planta->getId()) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($planta->getNombre()); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="botiquin-card__footer">
                        <button type="button" class="botiquin-form__button botiquin-form__button--secondary botiquin-form__button--cancel">Cancelar</button>
                        <button type="submit" class="botiquin-form__button botiquin-form__button--primary">Actualizar Botiquín</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="botiquin-card-delete-<?php echo $item['botiquin']->getId(); ?>" class="botiquin-card">
            <div class="botiquin-card__header botiquin-card__header--delete">
                <h3 class="botiquin-card__title">Eliminar Botiquín</h3>
                <button type="button" class="botiquin-card__close">&times;</button>
            </div>
            <div class="botiquin-card__body">
                <h4>¿Estás seguro de que deseas eliminar el botiquín "<?php echo htmlspecialchars($item['botiquin']->getNombre()); ?>"?</h4>
                <p class="text-danger">Esta acción no se puede deshacer.</p>
                <form id="delete-botiquin-form-<?php echo $item['botiquin']->getId(); ?>" method="POST" action="procesar_botiquin.php">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?php echo $item['botiquin']->getId(); ?>">
                    <div class="botiquin-card__footer">
                        <button type="button" class="botiquin-form__button botiquin-form__button--secondary botiquin-form__button--cancel">Cancelar</button>
                        <button type="submit" class="botiquin-form__button botiquin-form__button--danger">Confirmar Eliminación</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php include(__DIR__ . '/../templates/footer.php'); ?>
    
<script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/botiquin-cards.js"></script>
