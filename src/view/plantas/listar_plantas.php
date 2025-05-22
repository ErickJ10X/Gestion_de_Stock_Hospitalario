<?php
session_start();
require_once(__DIR__ . '/../../controller/PlantaController.php');
require_once(__DIR__ . '/../../controller/HospitalController.php');
include_once(__DIR__ . '/../../util/Session.php');
include_once(__DIR__ . '/../../util/AuthGuard.php');

$plantaController = new \controller\PlantaController();
$hospitalController = new \controller\HospitalController();
$session = new \util\Session();
$authGuard = new \util\AuthGuard();

$authGuard->requirePlantaGestor();

try {
    $plantas = $plantaController->getAllPlantas();
    $hospitalesMap = [];
    $hospitales = $hospitalController->getAllHospitales();
    
    // Crear un mapa de hospitales para mostrar nombres en lugar de IDs
    foreach ($hospitales as $hospital) {
        $hospitalesMap[$hospital->getId()] = $hospital->getNombre();
    }
} catch (Exception $e) {
    $plantas = [];
    $hospitales = [];
    $session->setMessage('error', 'Error al cargar los datos: ' . $e->getMessage());
}

$pageTitle = "Listado de Plantas";
include_once(__DIR__ . '/../templates/header.php');
?>

<!-- Incluir el archivo CSS para las tarjetas -->
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/card-form.css">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/list.css">

<div class="list-container">
    <div class="list-header">
        <h2 class="list-header__title">Gestión de Plantas</h2>
        <div class="list-header__actions">
            <button id="btn-add-planta" class="list-button list-button--primary">
                <i class="bi bi-plus-circle list-button__icon"></i> Nueva Planta
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

    <div class="list-card">
        <div class="list-card__header">
            <h3 class="list-card__title">Listado de Plantas</h3>
        </div>
        <div class="list-card__body">
            <table class="list-table">
                <thead class="list-table__head">
                    <tr>
                        <th class="list-table__header">ID</th>
                        <th class="list-table__header">Nombre</th>
                        <th class="list-table__header">Hospital</th>
                        <th class="list-table__header">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($plantas)): ?>
                        <tr>
                            <td colspan="4" class="list-table__empty">No hay plantas registradas</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($plantas as $planta): ?>
                            <tr class="list-table__body-row">
                                <td class="list-table__body-cell" data-label="ID"><?= htmlspecialchars($planta->getId()) ?></td>
                                <td class="list-table__body-cell" data-label="Nombre"><?= htmlspecialchars($planta->getNombre()) ?></td>
                                <td class="list-table__body-cell" data-label="Hospital">
                                    <?= isset($hospitalesMap[$planta->getHospitalId()]) ? htmlspecialchars($hospitalesMap[$planta->getHospitalId()]) : 'N/A' ?>
                                </td>
                                <td class="list-table__body-cell" data-label="Acciones">
                                    <div class="list-table__actions">
                                        <button class="list-table__button list-table__button--edit btn-edit-planta" data-id="<?= $planta->getId() ?>">
                                            <i class="bi bi-pencil-square list-table__button-icon"></i> Modificar
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
</div>

<div class="planta-overlay"></div>

<div id="planta-card-create" class="planta-card">
    <div class="planta-card__header planta-card__header--create">
        <h3 class="planta-card__title">Nueva Planta</h3>
        <button type="button" class="planta-card__close">&times;</button>
    </div>
    <div class="planta-card__body">
        <form id="create-planta-form" method="POST" action="registrar_planta.php">
            <div class="planta-form__group">
                <label for="nombre-create" class="planta-form__label">Nombre de la Planta</label>
                <input type="text" class="planta-form__input" id="nombre-create" name="nombre" required>
            </div>
            <div class="planta-form__group">
                <label for="hospital_id-create" class="planta-form__label">Hospital</label>
                <select class="planta-form__select" id="hospital_id-create" name="hospital_id" required>
                    <option value="">Seleccionar hospital</option>
                    <?php foreach ($hospitales as $hospital): ?>
                        <option value="<?= $hospital->getId() ?>"><?= htmlspecialchars($hospital->getNombre()) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="planta-card__footer">
                <button type="button" class="planta-form__button planta-form__button--secondary planta-form__button--cancel">Cancelar</button>
                <button type="submit" class="planta-form__button planta-form__button--primary">Registrar Planta</button>
            </div>
        </form>
    </div>
</div>

<?php if (!empty($plantas)): ?>
    <?php foreach ($plantas as $planta): ?>
        <div id="planta-card-edit-<?= $planta->getId() ?>" class="planta-card">
            <div class="planta-card__header planta-card__header--edit">
                <h3 class="planta-card__title">Editar Planta</h3>
                <button type="button" class="planta-card__close">&times;</button>
            </div>
            <div class="planta-card__body">
                <form id="edit-planta-form-<?= $planta->getId() ?>" method="POST" action="editar_planta.php">
                    <input type="hidden" name="id" value="<?= $planta->getId() ?>">
                    <div class="planta-form__group">
                        <label for="nombre-edit-<?= $planta->getId() ?>" class="planta-form__label">Nombre de la Planta</label>
                        <input type="text" class="planta-form__input" id="nombre-edit-<?= $planta->getId() ?>" name="nombre" value="<?= htmlspecialchars($planta->getNombre()) ?>" required>
                    </div>
                    <div class="planta-form__group">
                        <label for="hospital_id-edit-<?= $planta->getId() ?>" class="planta-form__label">Hospital</label>
                        <select class="planta-form__select" id="hospital_id-edit-<?= $planta->getId() ?>" name="hospital_id" required>
                            <option value="">Seleccionar hospital</option>
                            <?php foreach ($hospitales as $hospital): ?>
                                <option value="<?= $hospital->getId() ?>" <?= $planta->getHospitalId() == $hospital->getId() ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($hospital->getNombre()) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="planta-card__footer">
                        <button type="button" class="planta-form__button planta-form__button--secondary planta-form__button--cancel">Cancelar</button>
                        <button type="submit" class="planta-form__button planta-form__button--primary">Actualizar Planta</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="planta-card-delete-<?= $planta->getId() ?>" class="planta-card">
            <div class="planta-card__header planta-card__header--delete">
                <h3 class="planta-card__title">Eliminar Planta</h3>
                <button type="button" class="planta-card__close">&times;</button>
            </div>
            <div class="planta-card__body">
                <h4>¿Estás seguro de que deseas eliminar la planta "<?= htmlspecialchars($planta->getNombre()) ?>"?</h4>
                <p class="text-danger">Esta acción no se puede deshacer.</p>
                <form id="delete-planta-form-<?= $planta->getId() ?>" method="POST" action="eliminar_planta.php">
                    <input type="hidden" name="id" value="<?= $planta->getId() ?>">
                    <input type="hidden" name="confirmar" value="1">
                    <div class="planta-card__footer">
                        <button type="button" class="planta-form__button planta-form__button--secondary planta-form__button--cancel">Cancelar</button>
                        <button type="submit" class="planta-form__button planta-form__button--danger">Confirmar Eliminación</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cerrar alertas
    const alertCloseButtons = document.querySelectorAll('.list-alert__close');
    alertCloseButtons.forEach(button => {
        button.addEventListener('click', function() {
            const alert = this.closest('.list-alert');
            alert.style.display = 'none';
        });
    });
    
    // Botón para agregar planta
    const btnAddPlanta = document.getElementById('btn-add-planta');
    const plantaOverlay = document.querySelector('.planta-overlay');
    const plantaCardCreate = document.getElementById('planta-card-create');
    const plantaCloseButtons = document.querySelectorAll('.planta-card__close, .planta-form__button--cancel');
    
    // Botones para editar plantas
    const btnEditPlantas = document.querySelectorAll('.btn-edit-planta');
    
    // Botones para eliminar plantas
    const btnDeletePlantas = document.querySelectorAll('.btn-delete-planta');
    
    // Abrir modal de creación
    btnAddPlanta.addEventListener('click', function() {
        plantaOverlay.style.display = 'block';
        plantaCardCreate.style.display = 'block';
    });
    
    // Cerrar modales
    plantaCloseButtons.forEach(button => {
        button.addEventListener('click', function() {
            plantaOverlay.style.display = 'none';
            document.querySelectorAll('.planta-card').forEach(card => {
                card.style.display = 'none';
            });
        });
    });
    
    // Abrir modal de edición
    btnEditPlantas.forEach(button => {
        button.addEventListener('click', function() {
            const plantaId = this.getAttribute('data-id');
            plantaOverlay.style.display = 'block';
            document.getElementById(`planta-card-edit-${plantaId}`).style.display = 'block';
        });
    });
    
    // Abrir modal de eliminación
    btnDeletePlantas.forEach(button => {
        button.addEventListener('click', function() {
            const plantaId = this.getAttribute('data-id');
            plantaOverlay.style.display = 'block';
            document.getElementById(`planta-card-delete-${plantaId}`).style.display = 'block';
        });
    });
});
</script>

<?php include_once(__DIR__ . '/../templates/footer.php'); ?>
