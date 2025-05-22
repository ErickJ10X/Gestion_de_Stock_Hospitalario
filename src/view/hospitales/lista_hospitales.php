<?php
session_start();
require_once(__DIR__ . '/../../controller/HospitalController.php');
include_once(__DIR__ . '/../../util/Session.php');
include_once(__DIR__ . '/../../util/AuthGuard.php');

use controller\HospitalController;
use util\Session;
use util\AuthGuard;

$hospitalController = new HospitalController();
$session = new Session();
$authGuard = new AuthGuard();

$authGuard->requireHospitalGestor();

$hospitales = $hospitalController->getAllHospitales();

$pageTitle = "Lista de Hospitales";
include_once(__DIR__ . '/../templates/header.php');
?>

<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/card-form.css">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/list.css">

<div class="list-container">
    <div class="list-header">
        <h2 class="list-header__title">Lista de Hospitales</h2>
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

    <div class="list-card">
        <div class="list-card__header">
            <h3 class="list-card__title">Listado de Hospitales</h3>
        </div>
        <div class="list-card__body">
            <table class="list-table">
                <thead class="list-table__head">
                    <tr>
                        <th class="list-table__header">ID</th>
                        <th class="list-table__header">Nombre</th>
                        <th class="list-table__header">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($hospitales)): ?>
                        <tr>
                            <td colspan="3" class="list-table__empty">No hay hospitales registrados</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($hospitales as $hospital): ?>
                            <tr class="list-table__body-row">
                                <td class="list-table__body-cell" data-label="ID"><?= htmlspecialchars($hospital->id) ?></td>
                                <td class="list-table__body-cell" data-label="Nombre"><?= htmlspecialchars($hospital->nombre) ?></td>
                                <td class="list-table__body-cell" data-label="Acciones">
                                    <div class="list-table__actions">
                                        <button class="list-table__button list-table__button--edit btn-edit-hospital" data-id="<?= $hospital->id ?>">
                                            <i class="bi bi-pencil-square list-table__button-icon"></i> Modificar
                                        </button>
                                        <button class="list-table__button list-table__button--delete btn-delete-hospital" data-id="<?= $hospital->id ?>">
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

<div class="hospital-overlay"></div>

<div id="hospital-card-create" class="hospital-card">
    <div class="hospital-card__header hospital-card__header--create">
        <h3 class="hospital-card__title">Nuevo Hospital</h3>
        <button type="button" class="hospital-card__close">&times;</button>
    </div>
    <div class="hospital-card__body">
        <form id="create-hospital-form" method="POST" action="registrar_hospital.php">
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

<?php if (!empty($hospitales)): ?>
    <?php foreach ($hospitales as $hospital): ?>
        <div id="hospital-card-edit-<?= $hospital->id ?>" class="hospital-card">
            <div class="hospital-card__header hospital-card__header--edit">
                <h3 class="hospital-card__title">Editar Hospital</h3>
                <button type="button" class="hospital-card__close">&times;</button>
            </div>
            <div class="hospital-card__body">
                <form id="edit-hospital-form-<?= $hospital->id ?>" method="POST" action="editar_hospital.php?id=<?= $hospital->id ?>">
                    <div class="hospital-form__group">
                        <label for="nombre-edit-<?= $hospital->id ?>" class="hospital-form__label">Nombre del Hospital</label>
                        <input type="text" class="hospital-form__input" id="nombre-edit-<?= $hospital->id ?>" name="nombre" value="<?= htmlspecialchars($hospital->nombre) ?>" required>
                    </div>
                    <div class="hospital-card__footer">
                        <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel">Cancelar</button>
                        <button type="submit" class="hospital-form__button hospital-form__button--primary">Actualizar Hospital</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="hospital-card-delete-<?= $hospital->id ?>" class="hospital-card">
            <div class="hospital-card__header hospital-card__header--delete">
                <h3 class="hospital-card__title">Eliminar Hospital</h3>
                <button type="button" class="hospital-card__close">&times;</button>
            </div>
            <div class="hospital-card__body">
                <h4>¿Estás seguro de que deseas eliminar el hospital "<?= htmlspecialchars($hospital->nombre) ?>"?</h4>
                <p class="text-danger">Esta acción no se puede deshacer.</p>
                <form id="delete-hospital-form-<?= $hospital->id ?>" method="POST" action="eliminar_hospital.php?id=<?= $hospital->id ?>">
                    <input type="hidden" name="confirmar" value="1">
                    <div class="hospital-card__footer">
                        <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel">Cancelar</button>
                        <button type="submit" class="hospital-form__button hospital-form__button--danger">Confirmar Eliminación</button>
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
});
</script>

<script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/hospital-cards.js"></script>

<?php include_once(__DIR__ . '/../templates/footer.php'); ?>
